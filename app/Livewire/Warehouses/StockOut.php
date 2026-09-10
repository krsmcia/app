<?php

namespace App\Livewire\Warehouses;

use App\Models\Item;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StockOut extends Component
{
    public $warehouse;
    public function mount($code)
    {
        $this->warehouse = Warehouse::where('code', $code)->firstOrFail();
    }
    public function findItemByBarcode(string $barcode): void
    {
        $item = Item::where('barcode', trim($barcode))->first();
        if (!$item) {
            $this->dispatch(
                'item-not-found',
                message: "Barcode [{$barcode}] was not found."
            );

            return;
        }
        $this->dispatch(
            'item-found',
            item: [
                'id' => $item->id,
                'barcode' => $item->barcode,
                'name' => $item->name,
                'sku' => $item->sku,
                'image_url' => $item->imageUrl,
            ]
        );
    }
    public function save(array $items): void
    {
        if (empty($items)) {
            $this->dispatch(
                'stock-out-error',
                message: 'No items to save.'
            );

            return;
        }
        try {
            DB::transaction(function () use ($items) {
                foreach ($items as $item) {
                    $itemId = (int) ($item['id'] ?? 0);
                    $quantity = (float) ($item['quantity'] ?? 0);
                    if ($itemId <= 0 || $quantity <= 0) {
                        continue;
                    }
                    $stock = Stock::where('item_id', $itemId)
                        ->where('warehouse_id', $this->warehouse->id)
                        ->lockForUpdate()
                        ->first();
                    if (!$stock) {
                        throw new \RuntimeException(
                            "Stock not found for item ID: {$itemId}."
                        );
                    }
                    $availableQuantity =
                        $stock->quantity - $stock->reserved_quantity;
                    if ($availableQuantity < $quantity) {
                        throw new \RuntimeException(
                            "Stock out failed for {$stock->item->name}. " .
                            "Available: {$availableQuantity}, " .
                            "Requested: {$quantity}."
                        );
                    }
                    $stock->quantity -= $quantity;
                    $stock->save();
                    StockMovement::create([
                        'item_id' => $itemId,
                        'warehouse_id' => $this->warehouse->id,
                        'type' => 'out',
                        'quantity' => $quantity,
                        'balance_after' => $stock->quantity,
                        'warehouse_user_id' => auth()->id(),
                        'user_id' => auth()->id(),
                        'is_confirmed' => true,
                        'remark' => 'Stock out',
                    ]);
                }
            });
            $this->dispatch(
                'stock-out-saved',
                message: 'Stock out completed successfully.'
            );
        } catch (\Throwable $e) {
            $this->dispatch(
                'stock-out-error',
                message: $e->getMessage()
            );
        }
    }
    public function render()
    {
        return view('livewire.warehouses.stock-out');
    }
}