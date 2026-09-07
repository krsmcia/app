<?php

namespace App\Livewire\Audits;

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
    public bool $denyModal = false;
    public ?int $denyWorkflowItemId = null;
    public string $denyComment = '';
    /**
     * ---------------------------------------------------------
     * Approve single item
     * ---------------------------------------------------------
     */
    public function approveItem(int $workflowItemId): void
    {
        $workflowItem = PurchaseWorkflowItem::query()
            ->with('purchaseWorkflow')
            ->whereKey($workflowItemId)
            ->where('status', 'pending')
            ->whereHas('purchaseWorkflow', function ($query) {
                $query
                    ->where('step', 'audit')
                    ->where('status', 'pending');
            })
            ->firstOrFail();
        DB::transaction(function () use ($workflowItem) {
            $workflowItem->update([
                'status' => 'approved',
                'acted_at' => now(),
            ]);
            $workflowItem->purchaseActions()->create([
                'action' => 'approved',
                'acted_by' => Auth::id(),
                'acted_at' => now(),
            ]);
            $this->completeAuditWorkflowIfFinished(
                $workflowItem->purchaseWorkflow
            );
        });
        $this->dispatch('approval-updated');
    }

    public function openDenyModal(int $workflowItemId): void
    {
        $this->denyWorkflowItemId = $workflowItemId;
        $this->denyComment = '';
        $this->resetValidation();
        $this->denyModal = true;
    }


    /**
     * ---------------------------------------------------------
     * Deny single item
     * ---------------------------------------------------------
     */
    public function deny(): void
    {
        $this->validate([
            'denyComment' => ['required', 'string', 'max:2000'],
        ], [
            'denyComment.required' => 'Please provide a reason for denial.',
        ]);
        if (!$this->denyWorkflowItemId) {
            return;
        }
        $workflowItem = PurchaseWorkflowItem::query()
            ->with('purchaseWorkflow')
            ->whereKey($this->denyWorkflowItemId)
            ->where('status', 'pending')
            ->whereHas('purchaseWorkflow', function ($query) {
                $query
                    ->where('step', 'audit')
                    ->where('status', 'pending');
            })
            ->firstOrFail();
        DB::transaction(function () use ($workflowItem) {
            $workflowItem->update([
                'status' => 'denied',
                'acted_at' => now(),
            ]);
            $workflowItem->purchaseActions()->create([
                'action' => 'denied',
                'acted_by' => Auth::id(),
                'comment' => trim($this->denyComment),
                'acted_at' => now(),
            ]);
            $this->completeAuditWorkflowIfFinished(
                $workflowItem->purchaseWorkflow
            );
        });
        // Modal state 초기화
        $this->denyModal = false;
        $this->denyWorkflowItemId = null;
        $this->denyComment = '';
        $this->dispatch('approval-updated');
    }


    /**
     * ---------------------------------------------------------
     * Approve all pending items
     * ---------------------------------------------------------
     */
    public function approve(int $workflowId): void
    {
        $workflow = PurchaseWorkflow::query()
            ->with([
                'purchaseRequest',
                'purchaseWorkflowItems.purchaseItem',
            ])
            ->whereKey($workflowId)
            ->where('step', 'audit')
            ->where('status', 'pending')
            ->firstOrFail();
        DB::transaction(function () use ($workflow) {
            $pendingItems = $workflow->purchaseWorkflowItems
                ->filter(fn ($item) => $item->status === 'pending');
            if ($pendingItems->isEmpty()) {
                return;
            }
            foreach ($pendingItems as $workflowItem) {

                $workflowItem->update([
                    'status' => 'approved',
                    'acted_at' => now(),
                ]);

                $workflowItem->purchaseActions()->create([
                    'action' => 'approved',
                    'acted_by' => Auth::id(),
                    'acted_at' => now(),
                ]);
            }

            /*
             * Audit workflow 완료
             */
            $workflow->update([
                'status' => 'completed',
                'acted_at' => now(),
            ]);

            /*
             * 다음 단계 생성
             *
             * approved 된 item만 다음 workflow로 전달
             */
            $this->createNextWorkflow($workflow);
        });

        $this->dispatch('approval-updated');
    }


    /**
     * ---------------------------------------------------------
     * Deny all pending items
     * ---------------------------------------------------------
     */
    public function denyAll(int $workflowId): void
    {
        $workflow = PurchaseWorkflow::query()
            ->with([
                'purchaseRequest',
                'purchaseWorkflowItems',
            ])
            ->whereKey($workflowId)
            ->where('step', 'audit')
            ->where('status', 'pending')
            ->firstOrFail();

        DB::transaction(function () use ($workflow) {

            $pendingItems = $workflow->purchaseWorkflowItems
                ->filter(fn ($item) => $item->status === 'pending');

            if ($pendingItems->isEmpty()) {
                return;
            }

            foreach ($pendingItems as $workflowItem) {

                $workflowItem->update([
                    'status' => 'denied',
                    'acted_at' => now(),
                ]);

                $workflowItem->purchaseActions()->create([
                    'action' => 'denied',
                    'acted_by' => Auth::id(),
                    'acted_at' => now(),
                ]);
            }

            /*
             * 모든 item이 거절되었으므로
             * 다음 단계 workflow는 생성하지 않는다.
             */
            $workflow->update([
                'status' => 'completed',
                'acted_at' => now(),
            ]);
        });

        $this->dispatch('approval-updated');
    }


    /**
     * ---------------------------------------------------------
     * Complete audit workflow when no pending items remain
     * ---------------------------------------------------------
     */
    private function completeAuditWorkflowIfFinished(
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

        $this->createNextWorkflow($workflow);
    }


    /**
     * ---------------------------------------------------------
     * Create next workflow
     * ---------------------------------------------------------
     */
    private function createNextWorkflow(
        PurchaseWorkflow $workflow
    ): void {

        $purchaseRequest = $workflow->purchaseRequest;

        /*
         * Audit에서 승인된 item만 다음 단계로 전달
         */
        $approvedItems = $workflow->purchaseWorkflowItems()
            ->where('status', 'approved')
            ->get();

        /*
         * 승인된 item이 없으면 다음 단계 생성하지 않음
         */
        if ($approvedItems->isEmpty()) {
            return;
        }

        /*
         * 다음 단계
         *
         * 현재 workflow 구조에 맞게
         * 다음 단계 이름을 변경하면 된다.
         */
        $nextWorkflow = $purchaseRequest->purchaseWorkflows()->create([
            'step' => 'head',
            'status' => 'pending',
        ]);

        foreach ($approvedItems as $workflowItem) {

            $nextWorkflow->purchaseWorkflowItems()->create([
                'purchase_item_id' => $workflowItem->purchase_item_id,
                'status' => 'pending',
            ]);
        }
    }


    /**
     * ---------------------------------------------------------
     * Render
     * ---------------------------------------------------------
     */
    public function render()
    {
        $requests = PurchaseRequest::query()
            ->with([
                'user',
                'department',

                'purchaseWorkflows' => function ($query) {
                    $query
                        ->where('step', 'audit')
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
                    ->where('step', 'audit')
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

        return view('livewire.audits.requests', [
            'requests' => $requests,
        ]);
    }
}