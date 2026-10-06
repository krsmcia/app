<?php

namespace App\Livewire\Audits\Modals;

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
        $this->movementUserSearch = '';
        $this->movementUserId = null;
        $this->movementUserResults = [];
        $this->resetPage();
        $this->showModal = true;
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
        return view('livewire.audits.modals.stock-movements',['movements' => $movements]);
    }
}
