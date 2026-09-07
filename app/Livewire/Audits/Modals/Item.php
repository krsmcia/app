<?php

namespace App\Livewire\Audits\Modals;

use App\Models\Item as ItemModel;
use App\Models\PurchaseWorkflowItem;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class Item extends Component
{
    use WithPagination;

    public $itemModal = false;

    public $selectedItemId = null;
    public $selectedItem = null;

    #[On('open-item')]
    public function openModal($itemId = null)
    {
        if (!$itemId) {
            return;
        }

        $this->selectedItemId = $itemId;

        $this->resetPage('purchaseHistoryPage');

        $this->selectedItem = ItemModel::query()
            ->with([
                'itemVendors.vendor',
            ])
            ->findOrFail($itemId);

        $this->itemModal = true;
    }

    public function getCompletedPurchaseItemsProperty()
    {
        return PurchaseWorkflowItem::query()
            ->where('status', 'completed')
            ->whereHas('purchaseItem', function ($query) {
                $query->where('item_id', $this->selectedItemId);
            })
            ->with([
                'purchaseWorkflow.purchaseRequest.user',
                'purchaseItem',
            ])
            ->latest()
            ->paginate(
                10,
                ['*'],
                'purchaseHistoryPage'
            );
    }

    public function render()
    {
        return view('livewire.audits.modals.item');
    }
}