<?php

namespace App\Livewire\Audits\Modals;

use App\Models\Vendor;
use App\Models\PurchaseWorkflowItem;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Vendors extends Component
{
    use WithPagination;

    public $selectedVendorId = null;
    public $selectedVendorName = '';
    public $vendorModal = false;

    public $itemSearch = '';
    public $dateFrom = '';
    public $dateTo = '';

    #[On('open-vendor')]
    public function openModal($vendorId = null)
    {
        if (!$vendorId) {
            return;
        }

        $vendor = Vendor::findOrFail($vendorId);

        $this->selectedVendorId = $vendor->id;
        $this->selectedVendorName = $vendor->name;

        // 이전 검색조건 초기화
        $this->reset([
            'itemSearch',
            'dateFrom',
            'dateTo',
        ]);

        $this->resetPage();

        $this->vendorModal = true;
    }

    public function updatedItemSearch()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }
    public function clearFilters()
    {
        $this->reset([
            'itemSearch',
            'dateFrom',
            'dateTo',
        ]);

        $this->resetPage();
    }
    public function render()
    {
        $vendorPurchaseItems = collect();

        if ($this->selectedVendorId) {
            $query = PurchaseWorkflowItem::query()
                ->where('status', 'completed')
                ->whereHas('purchaseItem.itemVendor', function ($query) {
                    $query->where('vendor_id', $this->selectedVendorId);
                })
                ->with([
                    'purchaseItem',
                ]);

            // Item name search
            if (trim($this->itemSearch) !== '') {
                $search = trim($this->itemSearch);

                $query->whereHas('purchaseItem', function ($query) use ($search) {
                    $query->where('item_name', 'like', '%' . $search . '%');
                });
            }

            // Date from
            if ($this->dateFrom !== '') {
                $query->whereDate('acted_at', '>=', $this->dateFrom);
            }

            // Date to
            if ($this->dateTo !== '') {
                $query->whereDate('acted_at', '<=', $this->dateTo);
            }

            $vendorPurchaseItems = $query
                ->latest('acted_at')
                ->paginate(10);
        }

        return view('livewire.audits.modals.vendors', [
            'vendorPurchaseItems' => $vendorPurchaseItems,
        ]);
    }
}