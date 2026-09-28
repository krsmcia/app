<?php

namespace App\Livewire;

use App\Models\PurchaseAction;
use Livewire\Component;
use Livewire\WithPagination;

class ReadyForPickup extends Component
{
    use WithPagination;
    public function render()
    {
        $purchase_actions = PurchaseAction::query()
            ->where('action', 'purchased')
            ->whereHas(
                'purchaseWorkflowItem.purchaseItem.purchaseRequest',
                fn ($query) => $query->where('user_id', auth()->id())
            )
            ->whereHas('receivedItemPhotos')
            ->doesntHave('itemHandoverPhotos')
            ->with([
                'purchaseWorkflowItem.purchaseItem.item.primaryImage',
                'purchaseWorkflowItem.purchaseItem.purchaseRequest.user',
                'purchaseWorkflowItem.purchaseItem.purchaseRequest.department',

                // acted_by가 User FK라면
                'actedBy',
            ])
            ->latest()
            ->paginate(10);

        $purchase_actions->getCollection()->transform(function ($purchaseAction) {
            $purchaseItem = $purchaseAction->purchaseWorkflowItem?->purchaseItem;

            $purchaseAction->quantity = (float) ($purchaseItem?->quantity ?? 0);
            $purchaseAction->vendor_name_display = $purchaseItem?->vendor_name;

            return $purchaseAction;
        });

        // 실제 물건을 가지고 있는 직원(acted_by) 기준
        $groupedPurchaseActions = $purchase_actions
            ->getCollection()
            ->groupBy('acted_by');

        return view('livewire.ready-for-pickup', [
            'purchase_actions' => $purchase_actions,
            'groupedPurchaseActions' => $groupedPurchaseActions,
        ]);
    }
}
