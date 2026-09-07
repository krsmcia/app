<?php

namespace App\Livewire\Procurements;

use App\Models\PurchaseWorkflowItem;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class Approved extends Component
{
    use WithPagination, WithoutUrlPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $items = PurchaseWorkflowItem::query()
            ->with([
                'purchaseWorkflow.purchaseRequest.user',
                'purchaseWorkflow.purchaseRequest.department',
                'purchaseItem.item.primaryImage',
                'purchaseItem.itemVendor.vendor',
            ])
            ->where('status', 'approved')
            ->whereHas('purchaseWorkflow', function (Builder $query) {
                $query
                    ->where('step', 'procurement')
                    ->where('status', 'completed');
            })
            ->when($this->search !== '', function (Builder $query) {
                $search = '%' . trim($this->search) . '%';

                $query->whereHas('purchaseItem.item', function (Builder $query) use ($search) {
                    $query
                        ->where('name', 'like', $search)
                        ->orWhere('sku', 'like', $search);
                });
            })
            ->latest('acted_at')
            ->paginate(20);

        return view('livewire.procurements.approved', [
            'items' => $items,
        ]);
    }
}