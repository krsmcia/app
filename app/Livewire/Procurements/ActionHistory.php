<?php

namespace App\Livewire\Procurements;

use App\Models\PurchaseRequest;
use Livewire\Component;
use Livewire\WithPagination;

class ActionHistory extends Component
{
    use WithPagination;

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
                            'purchaseWorkflowItems.purchaseActions' => function ($query) {
                                $query
                                    ->with('actedBy')
                                    ->orderByDesc('acted_at');
                            },

                            'purchaseWorkflowItems.purchaseItem.item.primaryImage',
                            'purchaseWorkflowItems.purchaseItem.itemVendor.vendor',
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

        $requests->getCollection()->transform(function ($request) {
            // 완료된 review-prices 워크플로 선택
            $workflow = $request->purchaseWorkflows
                ->sortByDesc('id')
                ->first();

            $request->setRelation('reviewPricesWorkflow', $workflow);

            if (!$workflow) {
                $request->review_prices_total = 0;

                return $request;
            }

            $items = $workflow->purchaseWorkflowItems;

            $request->setRelation('reviewPricesItems', $items);

            $request->review_prices_total = max(
                0,
                $items->sum(function ($workflowItem) {
                    $purchaseItem = $workflowItem->purchaseItem;

                    if (!$purchaseItem) {
                        return 0;
                    }

                    $quantity = (float) ($purchaseItem->quantity ?? 0);
                    $unitPrice = (float) ($purchaseItem->unit_price ?? 0);
                    $discount = (float) ($purchaseItem->discount ?? 0);
                    $shippingFee = (float) ($purchaseItem->shipping_fee ?? 0);

                    return ($quantity * $unitPrice) - $discount + $shippingFee;
                })
            );

            return $request;
        });

        return view('livewire.procurements.action-history', [
            'requests' => $requests,
        ]);
    }
}