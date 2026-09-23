<?php

namespace App\Services;

use App\Models\PurchaseWorkflow;

class PurchaseWorkflowService
{
    public function completeAccountingWorkflowIfFinished(
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
    public function completeFundReleasedWorkflowIfFinished(
        PurchaseWorkflow $workflow
    ): void {
        $hasPendingItems = $workflow->purchaseWorkflowItems()
            ->whereNotIn('status', ['ordered', 'purchased'])
            ->exists();
        if ($hasPendingItems) {
            return;
        }
        $workflow->update([
            'status' => 'completed',
            'acted_at' => now(),
        ]);
    }
    private function createFundReleasedWorkflow(
        PurchaseWorkflow $workflow
    ): void {
        $purchaseRequest = $workflow->purchaseRequest;
        $alreadyExists = $purchaseRequest->purchaseWorkflows()
            ->where('step', 'fund released')
            ->exists();
        if ($alreadyExists) {
            return;
        }
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
        $nextWorkflow = $purchaseRequest->purchaseWorkflows()->create([
            'step' => 'fund released',
            'status' => 'pending',
        ]);
        foreach ($cashItems as $workflowItem) {
            $nextWorkflow->purchaseWorkflowItems()->create([
                'purchase_item_id' => $workflowItem->purchase_item_id,
                'status' => 'pending',
            ]);
        }
    }
}