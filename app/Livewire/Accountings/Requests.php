<?php

namespace App\Livewire\Accountings;

use App\Models\PurchaseRequest;
use App\Models\PurchaseWorkflow;
use App\Models\PurchaseWorkflowItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Requests extends Component
{
    use WithPagination;
    public bool $remarkModal = false;
    public string $remark = '';
    public ?int $releaseWorkflowItemId = null;
    public function openRemarkModal($workflowItemId)
    {
        $this->releaseWorkflowItemId = $workflowItemId;
        $this->remark = '';
        $this->resetValidation();
        $this->remarkModal = true;
    }
    public function complete()
    {
        $this->validate([
            'remark' => 'required|string|max:500',
        ]);

        if (!$this->releaseWorkflowItemId) {
            return;
        }

        $workflowItem = PurchaseWorkflowItem::query()
            ->with([
                'purchaseWorkflow',
                'purchaseItem',
            ])
            ->whereKey($this->releaseWorkflowItemId)
            ->where('status', 'pending')
            ->whereHas('purchaseWorkflow', function ($query) {
                $query
                    ->where('step', 'accounting')
                    ->where('status', 'pending');
            })
            ->firstOrFail();

        DB::transaction(function () use ($workflowItem) {

            $purchaseItem = $workflowItem->purchaseItem;

            $isCash = strcasecmp(
                trim($purchaseItem->disbursement_type_name ?? ''),
                'cash'
            ) === 0;

            $workflowItem->update([
                'status' => $isCash ? 'fund released' : 'purchased',
                'acted_at' => now(),
            ]);

            // 실제 구매 상태 기록
            $workflowItem->purchaseActions()->create([
                'action' => $isCash ? 'pending' : 'purchased',
                'acted_by' => Auth::id(),
                'acted_at' => now(),
                'comment' => $this->remark,
            ]);

            // Accounting workflow가 모두 처리됐는지 확인
            $this->completeAccountingWorkflowIfFinished(
                $workflowItem->purchaseWorkflow
            );
        });

        $this->reset([
            'remarkModal',
            'remark',
            'releaseWorkflowItemId',
        ]);

        $this->dispatch('approval-updated');
    }
    //purchase fulfillment
    public function releaseCash(int $workflowItemId): void
    {
        $workflowItem = PurchaseWorkflowItem::query()
            ->with('purchaseWorkflow')
            ->whereKey($workflowItemId)
            ->where('status', 'pending')
            ->whereHas('purchaseWorkflow', function ($query) {
                $query
                    ->where('step', 'accounting')
                    ->where('status', 'pending');
            })
            ->firstOrFail();
        DB::transaction(function () use ($workflowItem) {
            $workflowItem->update([
                'status' => 'fund released',
                'acted_at' => now(),
            ]);
            $workflowItem->purchaseActions()->create([
                'action' => 'fund released',
                'acted_by' => Auth::id(),
                'acted_at' => now(),
            ]);
            // Cash 지급은 아직 실제 구매 전
            // purchase_items.status = pending 유지
            $this->completeAccountingWorkflowIfFinished(
                $workflowItem->purchaseWorkflow,
                'purchase fulfillment'
            );
        });
        $this->dispatch('approval-updated');
    }
    private function completeAccountingWorkflowIfFinished(
        PurchaseWorkflow $workflow
    ): void {
        $hasPendingItems = $workflow->purchaseWorkflowItems()
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingItems) {
            return;
        }
        $workflow->update([
            'status' => 'completed',
            'acted_at' => now(),
        ]);
        $this->createFundReleasedWorkflow($workflow);
    }
    private function createFundReleasedWorkflow(PurchaseWorkflow $workflow): void
    {
        $purchaseRequest = $workflow->purchaseRequest;
        $cashItems = $workflow->purchaseWorkflowItems()
            ->with('purchaseItem')
            ->where('status', 'fund released')
            ->get()
            ->filter(function ($workflowItem) {
                return strcasecmp(
                    trim($workflowItem->purchaseItem->disbursement_type_name ?? ''),
                    'cash'
                ) === 0;
            });
        if ($cashItems->isEmpty()) {
            return;
        }
        // fund released workflow 생성
        $nextWorkflow = $purchaseRequest->purchaseWorkflows()->create([
            'step' => 'fund released',
            'status' => 'pending',
        ]);
        // 모든 cash item을 pending으로 생성
        foreach ($cashItems as $workflowItem) {
            $nextWorkflow->purchaseWorkflowItems()->create([
                'purchase_item_id' => $workflowItem->purchase_item_id,
                'status' => 'pending',
            ]);
        }
    }
    private function createNextWorkflow(
        PurchaseWorkflow $workflow,
        string $step
    ): void {
        $purchaseRequest = $workflow->purchaseRequest;

        $approvedItems = $workflow->purchaseWorkflowItems()
            ->where('status', 'approved')
            ->get();

        if ($approvedItems->isEmpty()) {
            return;
        }

        /*
        * Fund Release
        * → 실제 구매 완료
        * → 별도의 pending workflow를 만들 필요 없음
        */
        if ($step === 'fund release') {
            return;
        }

        /*
        * Purchase Fulfillment
        * → 아직 구매 완료가 아니므로
        * → 다음 단계 workflow 생성
        */
        $nextWorkflow = $purchaseRequest->purchaseWorkflows()->create([
            'step' => $step,
            'status' => 'pending',
        ]);

        foreach ($approvedItems as $workflowItem) {
            $nextWorkflow->purchaseWorkflowItems()->create([
                'purchase_item_id' => $workflowItem->purchase_item_id,
                'status' => 'pending',
            ]);
        }
    }
    public function render()
    {
        $requests = PurchaseRequest::query()
            ->with([
                'user',
                'department',
                'purchaseWorkflows' => function ($query) {
                    $query
                        ->where('step', 'accounting')
                        ->where('status', 'pending')
                        ->with([
                            'purchaseWorkflowItems' => function ($query) {
                                $query
                                    ->where('status', 'pending')
                                    ->with([
                                        'purchaseItem.item.primaryImage',
                                        'purchaseItem.itemVendor.vendor',
                                    ]);
                            },
                        ]);
                },
            ])
            ->whereHas('purchaseWorkflows', function ($query) {
                $query
                    ->where('step', 'accounting')
                    ->where('status', 'pending')
                    ->whereHas('purchaseWorkflowItems', function ($query) {
                        $query->where('status', 'pending');
                    });
            })
            ->latest()
            ->paginate(12);
        $requests->getCollection()->transform(
            function ($request) {
                $workflow = $request->purchaseWorkflows->first();
                $request->audit_workflow = $workflow;
                if (!$workflow) {
                    $request->audit_total = 0;

                    return $request;
                }
                /*
                 * Audit 화면에는 pending item만 존재
                 */
                $request->items = $workflow->purchaseWorkflowItems;
                /*
                 * 중요:
                 *
                 * Audit에서는 vendor의 현재 가격을 다시 계산하지 않는다.
                 *
                 * Procurement 단계에서 purchase_items.amount에
                 * 확정된 snapshot 가격이 저장되어 있기 때문이다.
                 */
                $request->audit_total = $workflow->purchaseWorkflowItems
                    ->sum(function ($workflowItem) {

                        $purchaseItem = $workflowItem->purchaseItem;

                        return (float) ($purchaseItem->amount ?? 0);
                    });
                return $request;
            }
        );
        return view('livewire.accountings.requests', [
            'requests' => $requests,
        ]);
    }
}
