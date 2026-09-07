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

    public string $movementUserSearch = '';
    public ?int $movementUserId = null;
    public array $movementUserResults = [];

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
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset([
            'stockId',
            'stock',
            'type',
            'quantity',
            'remark',
            'movementUserSearch',
            'movementUserId',
            'movementUserResults',
        ]);
        $this->resetPage();
    }
    public function updatedMovementUserSearch(): void
    {
        $search = trim($this->movementUserSearch);

        $this->movementUserId = null;

        if ($search === '' || strlen($search) < 2) {
            $this->movementUserResults = [];

            return;
        }

        $this->movementUserResults = \App\Models\User::query()
            ->select(['id', 'name', 'email'])
            ->where(function ($query) use ($search) {
                $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->toArray();
    }
    public function selectMovementUser(int $userId): void
    {
        $user = \App\Models\User::query()
            ->select(['id', 'name', 'email'])
            ->find($userId);

        if (!$user) {
            return;
        }

        $this->movementUserId = $user->id;

        $this->movementUserSearch = $user->name;

        $this->movementUserResults = [];
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
            'movementUserId' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
        ]);

        if (in_array($this->type, ['out', 'return']) && !$this->movementUserId) {
            $this->addError(
                'movementUserId',
                'Please select a user.'
            );

            return;
        }
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
                'warehouse_user_id' => auth()->id(),
                'user_id' => in_array($this->type, ['out', 'return'])
                    ? $this->movementUserId
                    : auth()->id(),
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