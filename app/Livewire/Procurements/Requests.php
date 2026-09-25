<?php

namespace App\Livewire\Procurements;

use App\Models\Item;
use App\Models\Vendor;
use App\Models\ItemVendor;
use App\Models\PurchaseRequest;
use App\Models\PurchaseWorkflow;
use App\Models\PurchaseWorkflowItem;
use App\Models\DisbursementType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

use Livewire\Component;

class Requests extends Component
{
    use WithPagination;

    public bool $showVendorModal = false;
    public ?int $vendorItemId = null;
    public ?Item $vendorItem = null;
    public string $vendorSearch = '';
    public array $vendorSearchResults = [];
    public ?int $selectedItemId = null;
    public string $selectedItemName = '';
    public array $vendorForms = [];
    public array $requestDiscounts = [];
    public array $itemAdjustments = [];

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
                'disbursement_type_id' => $itemVendor->disbursement_type_id,
                'payment_details' => $itemVendor->payment_details,
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

        // 모든 Vendor 데이터 validation
        foreach ($this->vendorForms as $itemVendorId => $data) {

            $validated = validator(
                $data,
                [
                    'vendor_sku' => [
                        'nullable',
                        'string',
                        'max:100',
                    ],

                    'unit_price' => [
                        'nullable',
                        'numeric',
                        'min:0',
                    ],

                    'minimum_order_qty' => [
                        'required',
                        'integer',
                        'min:1',
                    ],

                    'lead_time' => [
                        'nullable',
                        'integer',
                        'min:0',
                    ],

                    'disbursement_type_id' => [
                        'required',
                        'integer',
                        'exists:disbursement_types,id',
                    ],

                    'payment_details' => [
                        'nullable',
                        'string',
                        'max:65535',
                    ],
                ]
            )->validate();

            ItemVendor::query()
                ->where('id', $itemVendorId)
                ->where('item_id', $this->selectedItemId)
                ->update([
                    'vendor_sku' => filled($validated['vendor_sku'] ?? null)
                        ? trim($validated['vendor_sku'])
                        : null,

                    'unit_price' => filled($validated['unit_price'] ?? null)
                        ? $validated['unit_price']
                        : null,

                    'minimum_order_qty' => (int) $validated['minimum_order_qty'],

                    'lead_time' => filled($validated['lead_time'] ?? null)
                        ? (int) $validated['lead_time']
                        : null,

                    'disbursement_type_id' => (int) $validated['disbursement_type_id'],

                    'payment_details' => filled($validated['payment_details'] ?? null)
                        ? trim($validated['payment_details'])
                        : null,
                ]);
        }

        // Preferred Vendor 처리
        ItemVendor::query()
            ->where('item_id', $this->selectedItemId)
            ->update([
                'is_preferred' => false,
            ]);

        if ($primaryVendorId) {
            ItemVendor::query()
                ->where('id', $primaryVendorId)
                ->where('item_id', $this->selectedItemId)
                ->update([
                    'is_preferred' => true,
                ]);
        }

        $this->reloadVendorItem();

        session()->flash(
            'success',
            'Vendor information updated successfully.'
        );
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
            'disbursement_type_id' => null,
            'payment_details' => null,
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
                        'disbursement_type_id' => $itemVendor->disbursement_type_id,
                        'payment_details' => $itemVendor->payment_details,
                        'is_preferred' => (bool) $itemVendor->is_preferred,
                    ],
                ];
            })
            ->toArray();
    }
    
    public function approve(
        int $workflowId,
        $requestDiscount,
        array $items
    ): void {
        $workflow = PurchaseWorkflow::query()
            ->with([
                'purchaseRequest',
                'purchaseWorkflowItems.purchaseItem',
                'purchaseWorkflowItems.purchaseItem.item.itemVendors.vendor',
                'purchaseWorkflowItems.purchaseItem.item.itemVendors.disbursementType',
            ])
            ->whereKey($workflowId)
            ->where('step', 'procurement')
            ->where('status', 'pending')
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Request Discount
        |--------------------------------------------------------------------------
        */

        $requestDiscount = (float) str_replace(
            ',',
            '',
            $requestDiscount ?? 0
        );

        validator(
            [
                'discount' => $requestDiscount,
            ],
            [
                'discount' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],
            ]
        )->validate();

        DB::transaction(function () use (
            $workflow,
            $requestDiscount,
            $items
        ) {
            $totalAmount = 0;

            foreach ($workflow->purchaseWorkflowItems as $workflowItem) {

                // 현재 Procurement 단계에서는 pending item만 처리
                if ($workflowItem->status !== 'pending') {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Alpine에서 승인 시 전달된 값
                |--------------------------------------------------------------------------
                */

                $itemData = $items[$workflowItem->id] ?? [];

                $shippingFee = (float) str_replace(
                    ',',
                    '',
                    $itemData['shippingFee'] ?? 0
                );

                $itemDiscount = (float) str_replace(
                    ',',
                    '',
                    $itemData['discount'] ?? 0
                );

                /*
                |--------------------------------------------------------------------------
                | Purchase Item
                |--------------------------------------------------------------------------
                */

                $purchaseItem = $workflowItem->purchaseItem;
                $item = $purchaseItem->item;

                /*
                |--------------------------------------------------------------------------
                | Preferred Vendor
                |--------------------------------------------------------------------------
                */

                $itemVendor = $item->itemVendors
                    ->firstWhere('is_preferred', true);

                /*
                |--------------------------------------------------------------------------
                | Vendor / Price / Disbursement Type 확인
                |--------------------------------------------------------------------------
                */

                if (
                    !$itemVendor ||
                    !$itemVendor->vendor ||
                    !filled($itemVendor->unit_price) ||
                    (float) $itemVendor->unit_price <= 0 ||
                    !$itemVendor->disbursement_type_id
                ) {
                    abort(
                        422,
                        "Vendor or price is not set for item: {$purchaseItem->item_name}"
                    );
                }

                $unitPrice = (float) $itemVendor->unit_price;

                /*
                |--------------------------------------------------------------------------
                | 기본 아이템 금액
                |--------------------------------------------------------------------------
                */

                $amount = $purchaseItem->quantity * $unitPrice;

                /*
                |--------------------------------------------------------------------------
                | Item Discount 검증
                |--------------------------------------------------------------------------
                |
                | Discount는 Amount + Shipping보다 클 수 없음
                |
                */

                if ($itemDiscount > ($amount + $shippingFee)) {
                    abort(
                        422,
                        "Item discount cannot be greater than the item amount."
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Item별 최종 금액
                |--------------------------------------------------------------------------
                */

                $itemTotal = max(
                    0,
                    $amount + $shippingFee - $itemDiscount
                );

                /*
                |--------------------------------------------------------------------------
                | Purchase Item Snapshot
                |--------------------------------------------------------------------------
                */

                $purchaseItem->update([
                    'item_vendor_id' => $itemVendor->id,
                    'item_name' => $item->name,
                    'sku' => $item->sku,
                    'vendor_name' => $itemVendor->vendor->name,
                    'vendor_sku' => $itemVendor->vendor_sku,
                    'unit_price' => $unitPrice,
                    'shipping_fee' => $shippingFee,
                    'discount' => $itemDiscount,
                    'disbursement_type_name' => $itemVendor->disbursementType->name,
                    'payment_details' => $itemVendor->payment_details,
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

                // Item별 계산 결과를 전체 합계에 추가
                $totalAmount += $itemTotal;
            }

            /*
            |--------------------------------------------------------------------------
            | Request Discount 검증
            |--------------------------------------------------------------------------
            |
            | 전체 Item 금액보다 Request Discount가 클 수 없음
            |
            */

            if ($requestDiscount > $totalAmount) {
                abort(
                    422,
                    'Request discount cannot be greater than the total purchase amount.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Request Discount 적용
            |--------------------------------------------------------------------------
            */

            $totalAmount = max(
                0,
                $totalAmount - $requestDiscount
            );

            /*
            |--------------------------------------------------------------------------
            | Purchase Request
            |--------------------------------------------------------------------------
            */

            $workflow->purchaseRequest->update([
                'discount' => $requestDiscount,
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
        $nextWorkflow = $purchaseRequest->purchaseWorkflows()->create([
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
        $requests = PurchaseRequest::query()
            ->with([
                'user',
                'department',
                'purchaseWorkflows.purchaseWorkflowItems.purchaseItem.item.primaryImage',
                'purchaseWorkflows.purchaseWorkflowItems.purchaseItem.item.itemVendors.vendor',
            ])
            ->whereHas('purchaseWorkflows', function ($query) {
                $query
                    ->where('step', 'procurement')
                    ->where('status', 'pending');
            })
            ->latest()
            ->paginate(12);
        $requests->getCollection()->each(function ($request) {
            $workflow = $request->purchaseWorkflows
                ->firstWhere('step', 'procurement');
            if (!$workflow) {
                return;
            }
            // Request discount 초기값
            if (!array_key_exists($request->id, $this->requestDiscounts)) {
                $this->requestDiscounts[$request->id] = $request->discount ?? 0;
            }
            foreach ($workflow->purchaseWorkflowItems as $workflowItem) {
                $item = $workflowItem->purchaseItem;
                if (!array_key_exists($workflowItem->id, $this->itemAdjustments)) {
                    $this->itemAdjustments[$workflowItem->id] = [
                        'shipping_fee' => $item?->shipping_fee ?? 0,
                        'discount' => $item?->discount ?? 0,
                    ];
                }
                $workflowItem->preferred_vendor =
                    $item?->item
                        ?->itemVendors
                        ->firstWhere('is_preferred', true);
            }
            $workflow->can_approve =
                $workflow->purchaseWorkflowItems->isNotEmpty()
                && $workflow->purchaseWorkflowItems->every(
                    fn ($workflowItem) =>
                        $workflowItem->preferred_vendor
                        && filled($workflowItem->preferred_vendor->unit_price)
                        && (float) $workflowItem->preferred_vendor->unit_price > 0
                        && filled($workflowItem->preferred_vendor->disbursement_type_id)
                );
            $itemsTotal = $workflow->purchaseWorkflowItems->sum(function ($workflowItem) {
                $item = $workflowItem->purchaseItem;
                $vendor = $workflowItem->preferred_vendor;
                if (!$vendor?->unit_price) {
                    return 0;
                }
                $amount = $item->quantity * (float) $vendor->unit_price;
                $shippingFee = (float) (
                    $this->itemAdjustments[$workflowItem->id]['shipping_fee'] ?? 0
                );
                $discount = (float) (
                    $this->itemAdjustments[$workflowItem->id]['discount'] ?? 0
                );
                return max(0, $amount + $shippingFee - $discount);
            });
            $itemsTotal = $workflow->purchaseWorkflowItems->sum(function ($workflowItem) {
                $item = $workflowItem->purchaseItem;
                $vendor = $workflowItem->preferred_vendor;

                if (!$vendor?->unit_price) {
                    return 0;
                }

                $amount = $item->quantity * (float) $vendor->unit_price;

                $shippingFee = (float) (
                    $this->itemAdjustments[$workflowItem->id]['shipping_fee'] ?? 0
                );

                $discount = (float) (
                    $this->itemAdjustments[$workflowItem->id]['discount'] ?? 0
                );

                return max(
                    0,
                    $amount + $shippingFee - $discount
                );
            });

            $requestDiscount = (float) (
                $this->requestDiscounts[$request->id] ?? 0
            );

            $workflow->procurement_total = max(
                0,
                $itemsTotal - $requestDiscount
            );
        });

        $disbursementTypes = DisbursementType::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.procurements.requests', [
            'requests' => $requests,
            'disbursementTypes' => $disbursementTypes,
        ]);
    }
}