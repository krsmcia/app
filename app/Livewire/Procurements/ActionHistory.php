<?php

namespace App\Livewire\Procurements;

use Livewire\Component;
use App\Models\PurchaseRequest;

class ActionHistory extends Component
{
    public function render()
    {
        $requests = PurchaseRequest::query()
            ->with([
                'user',
                'department',
                'purchaseWorkflows' => function ($query) {
                    $query
                        ->where('step', 'review-prices')
                        ->where('status', 'completed')
                        ->with([
                            'purchaseWorkflowItems' => function ($query) {
                                $query
                                    ->with([
                                        'purchaseActions' => function ($query) {
                                            $query
                                                ->with('actedBy')
                                                ->orderByDesc('acted_at');
                                        },
                                        'purchaseItem.item.primaryImage',
                                        'purchaseItem.itemVendor.vendor',
                                    ]);
                            },
                        ]);
                },
            ])
            ->whereHas('purchaseWorkflows', function ($query) {
                $query
                    ->where('step', 'review-prices')
                    ->where('status', 'completed');
            })
            ->latest()
            ->paginate(12);
        $requests->getCollection()->transform(
            function ($request) {
                $workflow = $request->purchaseWorkflows->first();
                $request->review_prices_workflow = $workflow;
                if (!$workflow) {
                    $request->review_prices_total = 0;
                    return $request;
                }
                $request->items = $workflow->purchaseWorkflowItems;
                $request->review_prices_total = $workflow->purchaseWorkflowItems
                    ->sum(function ($workflowItem) {
                        $purchaseItem = $workflowItem->purchaseItem;
                        $quantity = (int) ($purchaseItem->quantity ?? 0);
                        $unitPrice = (float) ($purchaseItem->unit_price ?? 0);
                        $amount = (float) ($quantity * $unitPrice);
                        $discount = (float) ($purchaseItem->discount ?? 0);
                        $shippingFee = (float) ($purchaseItem->shipping_fee ?? 0);
                        return $amount - $discount + $shippingFee;
                    });
                $request->review_prices_total = max(0, $request->review_prices_total);
                return $request;
            }
        );
        return view('livewire.procurements.action-history', [
            'requests' => $requests,
        ]);
    }
}
