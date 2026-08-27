<?php

namespace App\Livewire\Procurements;

use App\Models\Item;
use App\Models\ItemVendor;
use App\Models\PurchaseWorkflow;
use Livewire\Component;

class Requests extends Component
{
    public bool $showVendorModal = false;

    public ?int $selectedItemId = null;

    public string $selectedItemName = '';

    public array $vendorForms = [];

    public function openVendorModal(int $itemId): void
    {
        $item = Item::query()->with('itemVendors.vendor')->findOrFail($itemId);

        $this->selectedItemId = $item->id;

        $this->selectedItemName = $item->item_name ?? 'Unnamed Item';

        $this->vendorForms = $item->itemVendors
            ->mapWithKeys(function ($itemVendor) {
                return [
                    $itemVendor->id => [
                        'vendor_id' => $itemVendor->vendor_id,
                        'vendor_name' => $itemVendor->vendor->name,
                        'vendor_sku' => $itemVendor->vendor_sku,
                        'unit_price' => $itemVendor->unit_price,
                        'minimum_order_qty' => $itemVendor->minimum_order_qty,
                        'lead_time' => $itemVendor->lead_time,
                        'is_preferred' => (bool) $itemVendor->is_preferred,
                    ],
                ];
            })
            ->toArray();

        $this->showVendorModal = true;
    }

    public function closeVendorModal(): void
    {
        $this->showVendorModal = false;

        $this->selectedItemId = null;
        $this->selectedItemName = '';
        $this->vendorForms = [];
    }

    public function setPrimaryVendor(int $itemVendorId): void
    {
        foreach ($this->vendorForms as $id => &$vendor) {
            $vendor['is_preferred'] = ((int) $id === $itemVendorId);
        }

        unset($vendor);
    }

    public function saveVendors(): void
    {
        if (!$this->selectedItemId) {
            return;
        }

        $primaryVendorId = collect($this->vendorForms)
            ->filter(fn ($vendor) => !empty($vendor['is_preferred']))
            ->keys()
            ->first();

        ItemVendor::query()
            ->where('item_id', $this->selectedItemId)
            ->update([
                'is_preferred' => false,
            ]);

        foreach ($this->vendorForms as $itemVendorId => $data) {

            ItemVendor::query()
                ->where('id', $itemVendorId)
                ->where('item_id', $this->selectedItemId)
                ->update([
                    'vendor_sku' => filled($data['vendor_sku'] ?? null)
                        ? trim($data['vendor_sku'])
                        : null,

                    'unit_price' => filled($data['unit_price'] ?? null)
                        ? $data['unit_price']
                        : null,

                    'minimum_order_qty' => filled($data['minimum_order_qty'] ?? null)
                        ? (int) $data['minimum_order_qty']
                        : 1,

                    'lead_time' => filled($data['lead_time'] ?? null)
                        ? (int) $data['lead_time']
                        : null,

                    'is_preferred' => ((int) $itemVendorId === (int) $primaryVendorId),
                ]);
        }

        $this->closeVendorModal();
    }

    public function render()
    {
        $workflows = PurchaseWorkflow::query()
            ->with([
                'purchaseRequest.user',
                'purchaseRequest.department',
                'purchaseWorkflowItems.purchaseItem.item.primaryImage',
                'purchaseWorkflowItems.purchaseItem.item.itemVendors.vendor',
            ])
            ->where('step', 'procurement')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('livewire.procurements.requests', [
            'workflows' => $workflows,
        ]);
    }
}