<?php

namespace App\Livewire\Warehouses;

use App\Models\Item;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StockIn extends Component
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
                'stock-in-error',
                message: 'No items to save.'
            );

            return;
        }

        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                $itemId = (int) ($item['id'] ?? 0);
                $quantity = (float) ($item['quantity'] ?? 0);

                if ($itemId <= 0 || $quantity <= 0) {
                    continue;
                }

                /*
                 * 현재 재고 row를 잠금
                 */
                $stock = Stock::where('item_id', $itemId)
                    ->where('warehouse_id', $this->warehouse->id)
                    ->lockForUpdate()
                    ->first();

                /*
                 * 아직 해당 창고에 재고 row가 없다면 생성
                 */
                if (!$stock) {
                    $stock = Stock::create([
                        'item_id' => $itemId,
                        'warehouse_id' => $this->warehouse->id,
                        'quantity' => 0,
                        'reserved_quantity' => 0,
                        'reorder_point' => 0,
                    ]);
                }

                /*
                 * 현재 재고 증가
                 */
                $stock->quantity += $quantity;
                $stock->save();

                /*
                 * 재고 변동 이력 기록
                 */
                StockMovement::create([
                    'item_id' => $itemId,
                    'warehouse_id' => $this->warehouse->id,
                    'type' => 'in',
                    'quantity' => $quantity,
                    'balance_after' => $stock->quantity,
                    'warehouse_user_id' => auth()->id(),
                    'user_id' => auth()->id(),
                    'is_confirmed' => true,
                    'remark' => 'Stock in',
                ]);
            }
        });

        $this->dispatch(
            'stock-in-saved',
            message: 'Stock in completed successfully.'
        );
    }

    public function render()
    {
        return view('livewire.warehouses.stock-in');
    }
}