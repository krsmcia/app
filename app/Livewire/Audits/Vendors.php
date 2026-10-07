<?php

namespace App\Livewire\Audits;

use App\Models\Vendor;
use Livewire\Component;
use Livewire\WithPagination;

class Vendors extends Component
{
    use WithPagination;
    public string $search = '';
    public string $typeFilter = '';
    public string $statusFilter = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';
    public function render()
    {
        $vendors = Vendor::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($query) {
                    $query
                        ->where('code', 'like', "%{$this->search}%")
                        ->orWhere('name', 'like', "%{$this->search}%")
                        ->orWhere('legal_name', 'like', "%{$this->search}%")
                        ->orWhere('contact_person', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('address', 'like', "%{$this->search}%");
                });
            })
            ->when($this->typeFilter !== '', function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where(
                    'is_active',
                    $this->statusFilter === 'active'
                );
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);
        return view('livewire.audits.vendors',['vendors' => $vendors,]);
    }
}
