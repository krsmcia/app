<?php

namespace App\Livewire\Warehouses;

use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;

class Warehouses extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';

    public bool $showModal = false;
    public bool $editing = false;

    public ?int $warehouseId = null;

    public string $code = '';
    public string $name = '';
    public string $description = '';
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:warehouses,code,' . ($this->warehouseId ?? 'NULL'),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'boolean',
            ],
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();

        $this->editing = false;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $warehouse = Warehouse::findOrFail($id);

        $this->warehouseId = $warehouse->id;
        $this->code = $warehouse->code;
        $this->name = $warehouse->name;
        $this->description = $warehouse->description ?? '';
        $this->is_active = $warehouse->is_active;

        $this->editing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editing) {
            $warehouse = Warehouse::findOrFail($this->warehouseId);

            $warehouse->update($validated);

            $message = 'Warehouse updated successfully.';
        } else {
            Warehouse::create($validated);

            $message = 'Warehouse created successfully.';
        }

        $this->closeModal();

        $this->dispatch('warehouse-saved', message: $message);
    }

    public function delete(int $id): void
    {
        $warehouse = Warehouse::findOrFail($id);

        $warehouse->delete();

        $this->dispatch(
            'warehouse-deleted',
            message: 'Warehouse deleted successfully.'
        );
    }

    public function toggleStatus(int $id): void
    {
        $warehouse = Warehouse::findOrFail($id);

        $warehouse->update([
            'is_active' => ! $warehouse->is_active,
        ]);
    }

    public function closeModal(): void
    {
        $this->showModal = false;

        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm(): void
    {
        $this->warehouseId = null;
        $this->code = '';
        $this->name = '';
        $this->description = '';
        $this->is_active = true;
        $this->editing = false;
    }

    public function render()
    {
        $warehouses = Warehouse::query()
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query
                        ->where('code', 'like', "%{$this->search}%")
                        ->orWhere('name', 'like', "%{$this->search}%");
                });
            })
            ->when(
                $this->statusFilter !== 'all',
                fn ($query) => $query->where(
                    'is_active',
                    $this->statusFilter === 'active'
                )
            )
            ->latest()
            ->paginate(20);
        return view('livewire.warehouses.warehouses', [
            'warehouses' => $warehouses,
        ]);
    }
}
