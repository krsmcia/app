<?php

namespace App\Livewire\Procurements;

use App\Models\Item;
use App\Models\Vendor;
use App\Models\ItemVendor;
use App\Models\PurchaseWorkflow;
use App\Models\PurchaseWorkflowItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    public function approve(int $workflowId): void
    {
        $workflow = PurchaseWorkflow::query()
            ->with([
                'purchaseRequest',
                'purchaseWorkflowItems.purchaseItem',
                'purchaseWorkflowItems.purchaseItem.item.itemVendors.vendor',
            ])
            ->whereKey($workflowId)
            ->where('step', 'procurement')
            ->where('status', 'pending')
            ->firstOrFail();

        DB::transaction(function () use ($workflow) {

            $totalAmount = 0;

            foreach ($workflow->purchaseWorkflowItems as $workflowItem) {
                
                // 현재 Procurement 단계에서는 pending item만 처리
                if ($workflowItem->status !== 'pending') {
                    continue;
                }
                $purchaseItem = $workflowItem->purchaseItem;
                $item = $purchaseItem->item;
                // Preferred Vendor 가져오기
                $itemVendor = $item->itemVendors
                    ->firstWhere('is_preferred', true);
                // Vendor 또는 가격이 없으면 승인 불가
                if (
                    !$itemVendor ||
                    !$itemVendor->vendor ||
                    !filled($itemVendor->unit_price) ||
                    (float) $itemVendor->unit_price <= 0
                ) {
                    abort(
                        422,
                        "Vendor or price is not set for item: {$purchaseItem->item_name}"
                    );
                }
                $unitPrice = (float) $itemVendor->unit_price;
                $amount = $purchaseItem->quantity * $unitPrice;
                /*
                |--------------------------------------------------------------------------
                | Purchase Item Snapshot
                |--------------------------------------------------------------------------
                |
                | ItemVendor의 현재 정보를 purchase_items에 확정 저장
                |
                */
                $purchaseItem->update([
                    'item_vendor_id' => $itemVendor->id,
                    'item_name' => $item->name,
                    'sku' => $item->sku,
                    'vendor_name' => $itemVendor->vendor->name,
                    'vendor_sku' => $itemVendor->vendor_sku,
                    'unit_price' => $unitPrice,
                    'amount' => $amount,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Procurement Workflow Item
                |--------------------------------------------------------------------------
                */

                $workflowItem->update([
                    'status' => 'approved',
                    'acted_at' => now(),
                ]);

                $workflowItem->purchaseActions()->create([
                    'action' => 'approved',
                    'acted_by' => Auth::id(),
                    'acted_at' => now(),
                ]);

                $totalAmount += $amount;
            }

            /*
            |--------------------------------------------------------------------------
            | Purchase Request Total
            |--------------------------------------------------------------------------
            */

            $workflow->purchaseRequest->update([
                'total_amount' => $totalAmount,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Procurement Workflow 완료
            |--------------------------------------------------------------------------
            */

            $workflow->update([
                'status' => 'completed',
                'acted_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | 다음 단계 → Audit
            |--------------------------------------------------------------------------
            */

            $this->createAuditWorkflow($workflow);
        });

        $this->dispatch('approval-updated');
    }
    private function createAuditWorkflow(PurchaseWorkflow $workflow): void
    {
        $purchaseRequest = $workflow->purchaseRequest;

        $nextWorkflow = $purchaseRequest->purchaseWorkflow()->create([
            'step' => 'audit',
            'status' => 'pending',
        ]);

        $approvedItems = $workflow->purchaseWorkflowItems()
            ->where('status', 'approved')
            ->get();

        foreach ($approvedItems as $workflowItem) {
            $nextWorkflow->purchaseWorkflowItems()->create([
                'purchase_item_id' => $workflowItem->purchase_item_id,
                'status' => 'pending',
            ]);
        }
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