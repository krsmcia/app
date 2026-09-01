<?php

namespace App\Livewire\Procurements;

use App\Models\Item;
use App\Models\Vendor;
use App\Models\ItemVendor;
use App\Models\PurchaseWorkflow;
use Livewire\Component;

class Requests extends Component
{
    public bool $showVendorModal = false;
    public ?int $vendorItemId = null;
    public ?Item $vendorItem = null;
    public string $vendorSearch = '';
    public array $vendorSearchResults = [];
    public ?int $selectedItemId = null;
    public string $selectedItemName = '';
    public array $vendorForms = [];

    public function openVendorModal(int $itemId): void
    {
        $item = Item::query()
            ->with('itemVendors.vendor')
            ->findOrFail($itemId);

        // 이전 모달 상태 완전히 초기화
        $this->vendorSearch = '';
        $this->vendorSearchResults = [];

        $this->vendorItemId = $item->id;
        $this->vendorItem = $item;

        $this->selectedItemId = $item->id;
        $this->selectedItemName = $item->item_name ?? 'Unnamed Item';

        $this->vendorForms = [];

        foreach ($item->itemVendors as $itemVendor) {
            $this->vendorForms[$itemVendor->id] = [
                'vendor_id' => $itemVendor->vendor_id,
                'vendor_name' => $itemVendor->vendor?->name ?? '',
                'vendor_sku' => $itemVendor->vendor_sku,
                'unit_price' => $itemVendor->unit_price,
                'minimum_order_qty' => $itemVendor->minimum_order_qty,
                'lead_time' => $itemVendor->lead_time,
                'is_preferred' => (bool) $itemVendor->is_preferred,
            ];
        }

        $this->showVendorModal = true;
    }

    public function closeVendorModal(): void
    {
        $this->showVendorModal = false;

        $this->vendorItemId = null;
        $this->vendorItem = null;

        $this->selectedItemId = null;
        $this->selectedItemName = '';

        $this->vendorSearch = '';
        $this->vendorSearchResults = [];
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
    public function removeVendor(int $itemVendorId): void
    {
        if (!$this->vendorItemId) {
            return;
        }

        $itemVendor = ItemVendor::query()
            ->where('id', $itemVendorId)
            ->where('item_id', $this->vendorItemId)
            ->firstOrFail();

        // Primary Vendor는 삭제 불가
        if ($itemVendor->is_preferred) {
            return;
        }

        $itemVendor->delete();

        $this->reloadVendorItem();
    }
    public function updatedVendorSearch(): void
    {
        $this->searchVendors();
    }
    public function searchVendors(): void
    {
        $search = trim($this->vendorSearch);

        if (strlen($search) < 2 || ! $this->vendorItemId) {
            $this->vendorSearchResults = [];
            return;
        }

        $attachedVendorIds = $this->vendorItem
            ? $this->vendorItem->itemVendors
                ->pluck('vendor_id')
                ->all()
            : [];

        $this->vendorSearchResults = Vendor::query()
            ->where('is_active', true)
            ->when(
                !empty($attachedVendorIds),
                fn ($query) => $query->whereNotIn('id', $attachedVendorIds)
            )
            ->where(function ($query) use ($search) {
                $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(fn (Vendor $vendor) => [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'code' => $vendor->code,
            ])
            ->toArray();
    }
    public function addVendor(int $vendorId): void
    {
        if (! $this->vendorItemId) {
            return;
        }

        $item = Item::findOrFail($this->vendorItemId);

        // Vendor가 실제로 존재하고 활성 상태인지 확인
        $vendor = Vendor::query()
            ->where('id', $vendorId)
            ->where('is_active', true)
            ->firstOrFail();

        // 이미 연결되어 있으면 추가하지 않음
        if (
            $item->vendors()
                ->where('vendor_id', $vendor->id)
                ->exists()
        ) {
            return;
        }

        // 첫 번째 Vendor라면 자동으로 Preferred
        $isFirstVendor = ! $item->vendors()->exists();

        $item->vendors()->attach($vendor->id, [
            'vendor_sku' => null,
            'unit_price' => null,
            'minimum_order_qty' => 1,
            'lead_time' => null,
            'is_preferred' => $isFirstVendor,
        ]);

        $this->reloadVendorItem();

        $this->vendorSearch = '';

        $this->vendorSearchResults = [];
    }
    private function reloadVendorItem(): void
    {
        if (!$this->vendorItemId) {
            return;
        }

        $this->vendorItem = Item::query()
            ->with('itemVendors.vendor')
            ->findOrFail($this->vendorItemId);

        $this->vendorForms = $this->vendorItem->itemVendors
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
        $workflows->each(function ($workflow) {
            foreach ($workflow->purchaseWorkflowItems as $workflowItem) {
                $item = $workflowItem->purchaseItem;

                $workflowItem->preferred_vendor =
                    $item?->item
                        ?->itemVendors
                        ->firstWhere('is_preferred', true);
            }

            $workflow->can_approve =
                $workflow->purchaseWorkflowItems->isNotEmpty()
                && $workflow->purchaseWorkflowItems->every(
                    fn ($workflowItem) => $workflowItem->preferred_vendor
                        && filled($workflowItem->preferred_vendor->unit_price)
                        && (float) $workflowItem->preferred_vendor->unit_price > 0
                );

            $workflow->procurement_total =
                $workflow->purchaseWorkflowItems->sum(function ($workflowItem) {
                    $item = $workflowItem->purchaseItem;
                    $vendor = $workflowItem->preferred_vendor;

                    return $vendor?->unit_price !== null
                        ? $item->quantity * $vendor->unit_price
                        : 0;
                });
        });
        return view('livewire.procurements.requests', [
            'workflows' => $workflows,
        ]);
    }
}