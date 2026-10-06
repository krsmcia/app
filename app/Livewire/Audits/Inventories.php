<?php

namespace App\Livewire\Audits;

use App\Models\Warehouse;
use App\Models\Stock;
use Livewire\WithPagination;

use Livewire\Component;

class Inventories extends Component
{
    use WithPagination;
    public string $search = '';
    public string $warehouseId = '';
    public string $stockStatus = '';

    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'warehouseId',
            'stockStatus',
        ]);

        $this->resetPage();
    }

    public function render()
    {
        $warehouses = Warehouse::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $stocks = Stock::query()
            ->with([
                'item.primaryImage',
                'warehouse',
            ])
            ->when(
                $this->search !== '',
                function ($query) {
                    $search = trim($this->search);

                    $query->whereHas('item', function ($query) use ($search) {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
                }
            )
            ->when(
                $this->warehouseId !== '',
                function ($query) {
                    $query->where(
                        'warehouse_id',
                        $this->warehouseId
                    );
                }
            )
            ->when(
                $this->stockStatus === 'out',
                function ($query) {
                    $query->where('quantity', '<=', 0);
                }
            )
            ->when(
                $this->stockStatus === 'low',
                function ($query) {
                    $query
                        ->where('quantity', '>', 0)
                        ->whereColumn(
                            'quantity',
                            '<=',
                            'reorder_point'
                        );
                }
            )
            ->when(
                $this->stockStatus === 'available',
                function ($query) {
                    $query->whereColumn(
                        'quantity',
                        '>',
                        'reorder_point'
                    );
                }
            )
            ->latest()
            ->paginate(15);
        return view('livewire.audits.inventories', [
            'stocks' => $stocks,
            'warehouses' => $warehouses,
        ]);
    }
}
