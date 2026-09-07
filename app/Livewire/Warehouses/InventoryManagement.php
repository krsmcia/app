<?php

namespace App\Livewire\Warehouses;

use App\Models\Item;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class InventoryManagement extends Component
{
    use WithPagination;

    public string $search = '';
    public string $warehouseId = '';
    public string $stockStatus = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'warehouseId' => ['except' => ''],
        'stockStatus' => ['except' => ''],
    ];

    /*
    |--------------------------------------------------------------------------
    | Movement History Modal
    |--------------------------------------------------------------------------
    */

    public bool $movementModal = false;
    public ?int $selectedStockId = null;
    public $movements = [];

    public array $movementHistory = [];
    public ?Stock $selectedStock = null;

    public bool $addItemModal = false;
    public string $addItemId = '';
    public string $addWarehouseId = '';
    public string $addQuantity = '0';
    public string $addReorderPoint = '0';

    public string $addItemSearch = '';
    public $addItemResults = [];

    protected function addItemRules(): array
    {
        return [
            'addItemId' => ['required', 'integer', 'exists:items,id'],
            'addWarehouseId' => ['required', 'integer', 'exists:warehouses,id'],
            'addQuantity' => ['required', 'numeric', 'min:0'],
            'addReorderPoint' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedWarehouseId(): void
    {
        $this->resetPage();
    }

    public function updatedStockStatus(): void
    {
        $this->resetPage();
    }


    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'warehouseId',
            'stockStatus',
        ]);

        $this->resetPage();
    }


    /*
    |--------------------------------------------------------------------------
    | Movement History
    |--------------------------------------------------------------------------
    */
    public function openMovementModal(int $stockId): void
    {
        $this->selectedStock = Stock::query()
            ->with([
                'item',
                'warehouse',
            ])
            ->findOrFail($stockId);

        $this->movements = StockMovement::query()
            ->where('item_id', $this->selectedStock->item_id)
            ->where('warehouse_id', $this->selectedStock->warehouse_id)
            ->with('user')
            ->latest()
            ->get();

        $this->movementModal = true;
    }
    public function closeMovementModal(): void
    {
        $this->reset([
            'movementModal',
            'selectedStockId',
            'movements',
        ]);
    }

    public function openAddItemModal(): void
    {
        $this->reset([
            'addItemId',
            'addWarehouseId',
            'addQuantity',
            'addReorderPoint',
            'addItemSearch',
            'addItemResults',
        ]);

        $this->addQuantity = '0';
        $this->addReorderPoint = '0';

        $this->addItemModal = true;
    }
    public function updatedAddItemSearch(): void
    {
        $search = trim($this->addItemSearch);

        if ($search === '' || strlen($search) < 2) {
            $this->addItemResults = [];

            return;
        }

        $this->addItemResults = Item::query()
            ->select(['id', 'name', 'sku'])
            ->where(function ($query) use ($search) {
                $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->toArray();
    }
    public function selectAddItem(int $itemId): void
    {
        $item = Item::query()
            ->select(['id', 'name', 'sku'])
            ->find($itemId);

        if (!$item) {
            return;
        }

        $this->addItemId = $item->id;
        $this->addItemSearch = $item->name . ' (' . $item->sku . ')';
        $this->addItemResults = [];
    }
    public function addItem(): void
    {
        $this->validate($this->addItemRules());

        $exists = Stock::query()
            ->where('item_id', $this->addItemId)
            ->where('warehouse_id', $this->addWarehouseId)
            ->exists();

        if ($exists) {
            $this->addError(
                'addItemId',
                'This item already exists in the selected warehouse.'
            );

            return;
        }

        DB::transaction(function () {
            $quantity = (float) $this->addQuantity;

            $stock = Stock::create([
                'item_id' => $this->addItemId,
                'warehouse_id' => $this->addWarehouseId,
                'quantity' => $quantity,
                'reserved_quantity' => 0,
                'reorder_point' => $this->addReorderPoint,
            ]);

            if ($quantity > 0) {
                StockMovement::create([
                    'item_id' => $stock->item_id,
                    'warehouse_id' => $stock->warehouse_id,
                    'type' => 'in',
                    'quantity' => $quantity,
                    'balance_after' => $quantity,
                    'reference_type' => 'initial_stock',
                    'reference_id' => $stock->id,
                    'user_id' => auth()->id(),
                    'remark' => 'Initial stock',
                ]);
            }
        });

        $this->reset([
            'addItemModal',
            'addItemId',
            'addWarehouseId',
            'addQuantity',
            'addReorderPoint',
            'addItemSearch',
            'addItemResults',
        ]);

        $this->resetPage();

        session()->flash('success', 'Item added to inventory.');
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
        return view('livewire.warehouses.inventory-management', [
            'stocks' => $stocks,
            'warehouses' => $warehouses,
        ]);
    }
}