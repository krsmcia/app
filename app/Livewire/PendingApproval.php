<?php

namespace App\Livewire;

use App\Models\PurchaseRequest;
use App\Models\PurchaseWorkflowItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

use Livewire\WithPagination;

class PendingApproval extends Component
{
    use WithPagination;
    public ?ApprovalRole $approvalRole = null;
    public function approve(int $purchaseItemId): void
    {
        $workflowItem = $this->findApprovableItem($purchaseItemId);
        abort_unless($workflowItem, 403);
        $this->processAction($workflowItem, 'approved');
    }

    public function reject(int $purchaseItemId): void
    {
        $workflowItem = $this->findApprovableItem($purchaseItemId);
        abort_unless($workflowItem, 403);
        $this->processAction($workflowItem, 'rejected');
    }
    /**
     * Return the workflow step this user is allowed to approve.
     *
     * staff       -> no approval
     * team-leader -> team-leader
     * supervisor  -> supervisor
     * audit dept  -> audit
     * head        -> head
     */
    private function approvalStep($user): ?string
    {
        return match (true) {
            $user->hasRole('team-leader') => 'team-leader',
            $user->hasRole('supervisor') => 'supervisor',
            default => null,
        };
    }

    private function findApprovableItem(
        int $purchaseItemId
    ): ?PurchaseWorkflowItem {
        $user = Auth::user();

        $workflowStep = $this->approvalStep($user);

        if (!$workflowStep) {
            return null;
        }

        return PurchaseWorkflowItem::query()
            ->with([
                'purchaseItem.item.primaryImage',
                'purchaseWorkflow.purchaseRequest.user',
                'purchaseWorkflow.purchaseRequest.department',
                'purchaseItem',
            ])
            ->where('purchase_item_id', $purchaseItemId)
            ->where('status', 'pending')
            ->whereHas('purchaseWorkflow', function ($query) use (
                $workflowStep,
                $user
            ) {
                $query
                    ->where('step', $workflowStep)
                    ->where('status', 'pending')
                    ->whereHas('purchaseRequest', function ($query) use ($user) {
                        $query
                            ->where(
                                'department_id',
                                $user->current_department_id
                            )
                            ->where('user_id', '!=', $user->id);
                    });
            })
            ->first();
    }

    private function processAction(
        PurchaseWorkflowItem $workflowItem,
        string $action
    ): void {
        $user = Auth::user();

        DB::transaction(function () use ($workflowItem, $action, $user) {
            $workflow = $workflowItem->purchaseWorkflow;

            $workflowItem->update([
                'status' => $action,
                'acted_at' => now(),
            ]);

            $workflowItem->purchaseActions()->create([
                'action' => $action,
                'acted_by' => $user->id,
                'acted_at' => now(),
            ]);

            $hasPending = $workflow->purchaseWorkflowItems()
                ->where('status', 'pending')
                ->exists();

            if ($hasPending) {
                return;
            }

            // 현재 단계의 모든 item이 처리됨
            $workflow->update([
                'status' => 'completed',
                'acted_at' => now(),
            ]);

            $this->createNextWorkflow($workflow);
        });

        $this->dispatch('approval-updated');
    }

    private function createNextWorkflow($workflow): void
    {
        $nextStep = match ($workflow->step) {
            'team-leader' => 'supervisor',
            'supervisor' => 'procurement',
            default => null,
        };

        if (!$nextStep) {
            return;
        }

        $approvedItemIds = $workflow->purchaseWorkflowItems()
            ->where('status', 'approved')
            ->pluck('purchase_item_id');

        if ($approvedItemIds->isEmpty()) {
            return;
        }

        $purchaseRequest = $workflow->purchaseRequest;

        $nextWorkflow = $purchaseRequest->purchaseWorkflows()->create([
            'step' => $nextStep,
            'status' => 'pending',
        ]);

        foreach ($approvedItemIds as $purchaseItemId) {
            $nextWorkflow->purchaseWorkflowItems()->create([
                'purchase_item_id' => $purchaseItemId,
                'status' => 'pending',
            ]);
        }
    }

    private function workflowStatus(
        PurchaseWorkflowItem $workflowItem
    ): string {
        $workflow = $workflowItem->purchaseWorkflow;

        $hasPending = $workflow->purchaseWorkflowItems()
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return 'pending';
        }

        $hasRejected = $workflow->purchaseWorkflowItems()
            ->where('status', 'rejected')
            ->exists();

        return $hasRejected ? 'rejected' : 'approved';
    }
    public function approveRequestItems(int $requestId): void
    {
        $this->processRequestItems($requestId, 'approved');
    }

    public function rejectRequestItems(int $requestId): void
    {
        $this->processRequestItems($requestId, 'rejected');
    }
    private function processRequestItems(
        int $requestId,
        string $action
    ): void {
        $user = Auth::user();

        $step = $this->approvalStep($user);

        abort_unless($step, 403);

        DB::transaction(function () use (
            $user,
            $requestId,
            $step,
            $action
        ) {

            /*
            * 현재 사용자가 승인할 수 있는
            * 해당 Request의 현재 workflow item만 가져온다.
            *
            * 조건:
            * 1. item status = pending
            * 2. workflow step = 현재 승인 단계
            * 3. workflow status = pending
            * 4. request가 현재 사용자의 department
            * 5. 자기 자신의 request가 아님
            */
            $items = PurchaseWorkflowItem::query()
                ->where('status', 'pending')

                ->whereHas('purchaseWorkflow', function ($query) use (
                    $user,
                    $requestId,
                    $step
                ) {
                    $query
                        ->where('step', $step)
                        ->where('status', 'pending')

                        ->whereHas('purchaseRequest', function ($query) use (
                            $user,
                            $requestId
                        ) {
                            $query
                                ->whereKey($requestId)
                                ->where(
                                    'department_id',
                                    $user->current_department_id
                                )
                                ->where(
                                    'user_id',
                                    '!=',
                                    $user->id
                                );
                        });
                })

                ->lockForUpdate()
                ->get();

            /*
            * 처리할 pending item이 없다면
            * 이미 승인/거절된 request라는 뜻이다.
            */
            abort_if($items->isEmpty(), 404);

            foreach ($items as $item) {

                $item->update([
                    'status' => $action,
                    'acted_at' => now(),
                ]);

                $item->purchaseActions()->create([
                    'action' => $action,
                    'acted_by' => $user->id,
                    'acted_at' => now(),
                ]);
            }

            /*
            * 현재 workflow
            */
            $workflow = $items->first()->purchaseWorkflow;

            /*
            * 아직 처리하지 않은 item이 있는지 확인
            */
            $hasPending = $workflow
                ->purchaseWorkflowItems()
                ->where('status', 'pending')
                ->exists();

            if ($hasPending) {
                return;
            }

            /*
            * 현재 workflow의 모든 item이 처리됨
            */
            $workflow->update([
                'status' => 'completed',
                'acted_at' => now(),
            ]);

            /*
            * 승인된 item만 다음 단계로 전달
            *
            * rejected item은 절대 다음 단계로 넘어가지 않는다.
            */
            $this->createNextWorkflow($workflow);
        });

        $this->dispatch('approval-updated');
    }
    public function render()
    {
        $user = Auth::user();
        $approvalStep = $this->approvalStep($user);
        $requests = collect();
        if ($approvalStep && $user->current_department_id) {
            $requests = PurchaseRequest::query()
                ->where('department_id', $user->current_department_id)
                ->where('user_id', '!=', $user->id)
                // 현재 승인 단계의 pending workflow가 존재해야 함
                ->whereHas('purchaseWorkflows', function ($query) use ($approvalStep) {
                    $query
                        ->where('step', $approvalStep)
                        ->where('status', 'pending')
                        ->whereHas('purchaseWorkflowItems', function ($query) {
                            $query->where('status', 'pending');
                        });
                })
                ->with([
                    'user',
                    'department',
                    // ⭐ 현재 승인 단계에서 pending인 item만 로딩
                    'purchaseItems' => function ($query) use ($approvalStep) {
                        $query->whereHas('purchaseWorkflowItems', function ($query) use ($approvalStep) {
                            $query
                                ->where('status', 'pending')
                                ->whereHas('purchaseWorkflow', function ($query) use ($approvalStep) {
                                    $query
                                        ->where('step', $approvalStep)
                                        ->where('status', 'pending');
                                });
                        })
                        ->with([
                            'item.primaryImage',
                            'purchaseWorkflowItems' => function ($query) use ($approvalStep) {
                                $query
                                    ->where('status', 'pending')
                                    ->whereHas('purchaseWorkflow', function ($query) use ($approvalStep) {
                                        $query
                                            ->where('step', $approvalStep)
                                            ->where('status', 'pending');
                                    });
                            },
                        ]);
                    },
                    'purchaseWorkflows',
                ])
                ->latest()
                ->paginate(12);
        }

        return view('livewire.pending-approval', [
            'requests' => $requests,
            'approvalStep' => $approvalStep,
        ]);
    }
}