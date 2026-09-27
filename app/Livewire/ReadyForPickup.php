<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class ReadyForPickup extends Component
{
    public function render()
    {
        $purchase_actions = auth()->user()
            ->purchaseActions()
            ->where('action', 'purchased')
            ->doesntHave('receivedItemPhotos')
            ->with([
                'purchaseWorkflowItem.purchaseItem.item.primaryImage',
                'purchaseWorkflowItem.purchaseItem.purchaseRequest.user',
                'purchaseWorkflowItem.purchaseItem.purchaseRequest.department',
            ])
            ->latest()
            ->paginate(10);
        $purchase_actions->getCollection()->transform(function ($purchaseAction) {
            $purchaseItem = $purchaseAction->purchaseWorkflowItem?->purchaseItem;
            $quantity = (float) ($purchaseItem?->quantity ?? 0);
            $purchaseAction->quantity = $quantity;
            $purchaseAction->vendor_name_display = $purchaseItem?->vendor_name;
            return $purchaseAction;
        });
        return view('livewire.ready-for-pickup', ['purchase_actions' => $purchase_actions,]);
    }
}
