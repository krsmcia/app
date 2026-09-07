<?php

namespace App\Livewire\Warehouses\Modals;

use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class StockMovements extends Component
{
    use WithPagination, WithoutUrlPagination; 

    public bool $showModal = false;

    public ?int $stockId = null;

    public ?Stock $stock = null;

    public string $type = 'in';
    public string $quantity = '';
    public string $remark = '';

    public function mount(): void
    {
        $this->resetForm();
    }
    #[On('stock-movement')]
    public function openStockMovementModal($stockId): void
    {
        $this->stockId = (int) $stockId;

        $this->stock = Stock::with([
            'item',
            'warehouse',
        ])->findOrFail($this->stockId);
        $this->resetForm();
        // 다른 Stock을 열었을 때 항상 1페이지부터
        $this->resetPage();
        $this->showModal = true;
    }
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset([
            'stockId',
            'stock',
            'type',
            'quantity',
            'remark',
        ]);
        $this->resetPage();
    }
    public function saveMovement(): void
    {
        $this->validate([
            'type' => [
                'required',
                'in:in,out,adjustment,return',
            ],
            'quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'remark' => [
                'required_if:type,adjustment',
                'nullable',
                'string',
                'max:255',
            ],
        ]);
        DB::transaction(function () {
            $stock = Stock::query()
                ->whereKey($this->stockId)
                ->lockForUpdate()
                ->firstOrFail();
            $quantity = (float) $this->quantity;
            $newBalance = match ($this->type) {
                'in', 'return' => $stock->quantity + $quantity,
                'out' => $stock->quantity - $quantity,
                'adjustment' => $quantity,
            };
            if ($newBalance < 0) {
                $this->addError(
                    'quantity',
                    'Insufficient stock quantity.'
                );
                throw new \RuntimeException('Insufficient stock.');
            }
            $stock->update([
                'quantity' => $newBalance,
            ]);
            StockMovement::create([
                'item_id' => $stock->item_id,
                'warehouse_id' => $stock->warehouse_id,
                'type' => $this->type,
                'quantity' => $quantity,
                'balance_after' => $newBalance,
                'user_id' => auth()->id(),
                'remark' => $this->remark ?: null,
            ]);
            $this->stock = $stock->fresh([
                'item',
                'warehouse',
            ]);
        });
        $this->dispatch('stock-updated');
        $this->resetForm();
        // 새 movement가 추가됐으므로 최신 기록이 있는 1페이지로
        $this->resetPage();
        session()->flash(
            'success',
            'Stock movement recorded successfully.'
        );
    }

    private function resetForm(): void
    {
        $this->type = 'in';
        $this->quantity = '';
        $this->remark = '';

        $this->resetValidation();
    }

    public function render()
    {
        $movements = StockMovement::query()
            ->with('user')
            ->when(
                $this->stock,
                fn ($query) => $query
                    ->where('item_id', $this->stock->item_id)
                    ->where('warehouse_id', $this->stock->warehouse_id)
            )
            ->latest()
            ->paginate(10);

        return view(
            'livewire.warehouses.modals.stock-movements',
            [
                'movements' => $movements,
            ]
        );
    }
}