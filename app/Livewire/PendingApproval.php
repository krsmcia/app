<?php

namespace App\Livewire;

use App\Models\PurchaseWorkflowItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PendingApproval extends Component
{
    public ?ApprovalRole $approvalRole = null;
    public function approve(int $workflowItemId): void
    {
        $workflowItem = $this->findApprovableItem($workflowItemId);

        abort_unless($workflowItem, 403);

        $this->processAction($workflowItem, 'approved');
    }

    public function reject(int $workflowItemId): void
    {
        $workflowItem = $this->findApprovableItem($workflowItemId);

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
        int $workflowItemId
    ): ?PurchaseWorkflowItem {
        $user = Auth::user();

        $step = $this->approvalStep($user);

        if (!$step) {
            return null;
        }

        return PurchaseWorkflowItem::query()
            ->with([
                'purchaseItem.item.primaryImage',
                'purchaseWorkflow.purchaseRequest.user',
                'purchaseWorkflow.purchaseRequest.department',
                'purchaseItem',
            ])
            ->whereKey($workflowItemId)
            ->where('status', 'pending')
            ->whereHas('purchaseWorkflow', function ($query) use ($user, $step) {
                $query
                    ->where('step', $step)
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
            'supervisor' => null,
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

        $nextWorkflow = $purchaseRequest->purchaseWorkflow()->create([
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
    public function approveUserItems(int $userId): void
    {
        $this->processUserItems($userId, 'approved');
    }

    public function rejectUserItems(int $userId): void
    {
        $this->processUserItems($userId, 'rejected');
    }
    private function processUserItems(
        int $userId,
        string $action
    ): void {
        $user = Auth::user();
        $step = $this->approvalStep($user);
        abort_unless($step, 403);
        DB::transaction(function () use (
            $user,
            $userId,
            $step,
            $action
        ) {
            $items = PurchaseWorkflowItem::query()
                ->where('status', 'pending')
                ->whereHas('purchaseWorkflow', function ($query) use (
                    $user,
                    $userId,
                    $step
                ) {
                    $query
                        ->where('step', $step)
                        ->where('status', 'pending')
                        ->whereHas('purchaseRequest', function ($query) use (
                            $user,
                            $userId
                        ) {
                            $query
                                ->where(
                                    'department_id',
                                    $user->current_department_id
                                )
                                ->where('user_id', $userId);
                        });
                })
                ->lockForUpdate()
                ->get();
            abort_if($items->isEmpty(), 404);
            /*
            * workflow 별로 처리해야 한다.
            * 한 User가 여러 PurchaseRequest를 가지고 있을 수 있기 때문.
            */
            $workflowIds = $items
                ->pluck('purchase_workflow_id')
                ->unique();
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
            foreach ($workflowIds as $workflowId) {
                $workflow = $items
                    ->where('purchase_workflow_id', $workflowId)
                    ->first()
                    ->purchaseWorkflow;
                $hasPending = $workflow
                    ->purchaseWorkflowItems()
                    ->where('status', 'pending')
                    ->exists();
                if ($hasPending) {
                    continue;
                }
                $workflow->update([
                    'status' => 'completed',
                    'acted_at' => now(),
                ]);
                $this->createNextWorkflow($workflow);
            }
        });
        $this->dispatch('approval-updated');
    }
    public function render()
    {
        $user = Auth::user();

        $step = $this->approvalStep($user);

        $groupedItems = collect();

        if ($step && $user->current_department_id) {
            $groupedItems = PurchaseWorkflowItem::query()
                ->with([
                    'purchaseItem.item.primaryImage',
                    'purchaseWorkflow.purchaseRequest.user',
                    'purchaseWorkflow.purchaseRequest.department',
                    'purchaseItem',
                ])
                ->where('status', 'pending')
                ->whereHas('purchaseWorkflow', function ($query) use ($user, $step) {
                    $query
                        ->where('step', $step)
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
                ->latest()
                ->get()
                ->groupBy(function ($item) {
                    return $item->purchaseWorkflow
                        ->purchaseRequest
                        ->user_id;
                });
        }

        return view('livewire.pending-approval', [
            'groupedItems' => $groupedItems,
            'approvalStep' => $step,
        ]);
    }
}