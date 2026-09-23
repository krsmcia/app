<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">
            Procurement Requests
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Purchase requests waiting for procurement processing.
        </p>
    </div>
    <div class="space-y-4">
        @if(count($requests)>0)
            @forelse ($requests as $request)
                @php
                    $workflow = $request->purchaseWorkflows
                        ->firstWhere('step', 'procurement');
                @endphp
                <div
                    x-data="{
                        requestDiscount: {{ (float) ($requestDiscounts[$request->id] ?? 0) }},

                        items: {
                            @foreach ($workflow->purchaseWorkflowItems as $workflowItem)
                                {{ $workflowItem->id }}: {
                                    amount: {{ (float) (
                                        $workflowItem->purchaseItem?->quantity
                                        * ($workflowItem->preferred_vendor?->unit_price ?? 0)
                                    ) }},
                                    shippingFee: {{ (float) ($itemAdjustments[$workflowItem->id]['shipping_fee'] ?? 0) }},
                                    discount: {{ (float) ($itemAdjustments[$workflowItem->id]['discount'] ?? 0) }},
                                },
                            @endforeach
                        },

                        number(value) {
                            return parseFloat(
                                String(value ?? 0).replace(/,/g, '')
                            ) || 0;
                        },

                        get subtotal() {
                            return Object.values(this.items).reduce((sum, item) => {
                                const amount = this.number(item.amount);
                                const shippingFee = this.number(item.shippingFee);
                                const discount = this.number(item.discount);

                                return sum + Math.max(
                                    0,
                                    amount + shippingFee - discount
                                );
                            }, 0);
                        },

                        get total() {
                            return Math.max(
                                0,
                                this.subtotal - this.number(this.requestDiscount)
                            );
                        },

                        money(value) {
                            return this.number(value).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        },
                        validateRequestDiscount() {
                            const discount = this.number(this.requestDiscount);
                            const subtotal = this.number(this.subtotal);

                            if (discount > subtotal) {
                                alert(
                                    `Request discount cannot be greater than ${this.money(subtotal)}.`
                                );

                                this.requestDiscount = '';
                            }
                        },
                        
                    }"
                    class="rounded-lg border border-gray-200 bg-white shadow-sm"
                >
                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <h2 class="font-semibold text-gray-900">
                                    {{ $request->request_no }}
                                </h2>
                                <span class="rounded-full bg-yellow-50 px-2.5 py-1 text-xs font-medium text-yellow-700">
                                    Pending
                                </span>
                            </div>
                            <div class="mt-1 text-sm text-gray-500">
                                Requested by
                                <span class="font-medium text-gray-700">
                                    {{ $request->user->name }}
                                </span>
                                @if ($request->department)
                                    · {{ $request->department->name }}
                                @endif
                            </div>
                        </div>
                        <div class="text-right text-sm text-gray-500">
                            {{ $request->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>
                    {{-- Items --}}
                    <div class="divide-y divide-gray-100">
                        @foreach ($workflow->purchaseWorkflowItems as $workflowItem)
                            @php
                                $item = $workflowItem->purchaseItem;
                                $preferredVendor = $workflowItem->preferred_vendor;

                                $unitPrice = $preferredVendor?->unit_price !== null
                                    ? (float) $preferredVendor->unit_price
                                    : 0;

                                $shippingFee = (float) (
                                    $itemAdjustments[$workflowItem->id]['shipping_fee'] ?? 0
                                );

                                $itemDiscount = (float) (
                                    $itemAdjustments[$workflowItem->id]['discount'] ?? 0
                                );

                                $itemAmount = $item && $unitPrice > 0
                                    ? $item->quantity * $unitPrice
                                    : 0;

                                $itemTotal = max(
                                    0,
                                    $itemAmount + $shippingFee - $itemDiscount
                                );
                            @endphp

                            <div
                                class="px-4 py-4 sm:px-5"
                                wire:key="workflow-{{ $workflowItem->id }}"
                            >
                                {{-- =========================================================
                                    Desktop: Everything in ONE ROW
                                ========================================================== --}}
                                <div
                                    @if ($preferredVendor)
                                        x-data="{
                                            get itemData() {
                                                return items[{{ $workflowItem->id }}];
                                            },

                                            number(value) {
                                                return parseFloat(
                                                    String(value ?? 0).replace(/,/g, '')
                                                ) || 0;
                                            },

                                            get total() {
                                                const amount = this.number(this.itemData.amount);
                                                const shippingFee = this.number(this.itemData.shippingFee);
                                                const discount = this.number(this.itemData.discount);

                                                return Math.max(
                                                    0,
                                                    amount + shippingFee - discount
                                                );
                                            },

                                            money(value) {
                                                return this.number(value).toLocaleString('en-US', {
                                                    minimumFractionDigits: 2,
                                                    maximumFractionDigits: 2
                                                });
                                            },

                                            validateDiscount() {
                                                const amount = this.number(this.itemData.amount);
                                                const shippingFee = this.number(this.itemData.shippingFee);
                                                const discount = this.number(this.itemData.discount);

                                                const maxDiscount = amount + shippingFee;

                                                if (discount > maxDiscount) {
                                                    alert(
                                                        `Item discount cannot be greater than ${this.money(maxDiscount)}.`
                                                    );
                                                    this.itemData.shippingFee = '';
                                                    this.itemData.discount = '';
                                                }
                                            }
                                        }"
                                    @endif
                                >
                                    {{-- Desktop --}}
                                    <div
                                        class="hidden xl:grid xl:grid-cols-[56px_minmax(180px,1.7fr)_minmax(120px,1.2fr)_90px_100px_100px_110px_110px_120px] sm:items-center sm:gap-3"
                                    >
                                        {{-- Image --}}
                                        <div
                                            class="h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-gray-100"
                                        >
                                            <img
                                                src="{{ $item?->item?->primaryImage
                                                    ? Storage::url($item->item->primaryImage->path)
                                                    : asset('images/default-item.png') }}"
                                                alt="{{ $item?->item?->item_name }}"
                                                class="h-full w-full object-cover"
                                            >
                                        </div>
                                        {{-- Item Info --}}
                                        <div class="min-w-0">
                                            <div class="truncate text-sm font-semibold text-gray-900">
                                                {{ $item?->item?->item_name }}
                                            </div>
                                            <div class="mt-1 flex items-center gap-2 text-xs text-gray-500">
                                                <span>
                                                    SKU:
                                                    <span class="font-medium text-gray-600">
                                                        {{ $item?->item?->sku }}
                                                    </span>
                                                </span>
                                                <span class="text-gray-300">•</span>
                                                <span>
                                                    Qty:
                                                    <span class="font-semibold text-gray-700">
                                                        {{ $item?->quantity }}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                        {{-- Vendor --}}
                                        <div class="min-w-0">
                                            <div class="text-[10px] font-medium uppercase tracking-wider text-gray-400">
                                                Vendor
                                            </div>
                                            @if ($preferredVendor)
                                                <div
                                                    class="mt-0.5 truncate text-sm font-semibold text-gray-800"
                                                    title="{{ $preferredVendor->vendor->name }}"
                                                >
                                                    {{ $preferredVendor->vendor->name }}
                                                </div>
                                            @else
                                                <div class="mt-0.5 text-sm text-gray-400">
                                                    No vendor
                                                </div>
                                            @endif
                                        </div>
                                        {{-- Unit Price --}}
                                        <div>
                                            <div class="text-[10px] text-gray-400">
                                                Unit Price
                                            </div>
                                            @if ($unitPrice > 0)
                                                <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                                    {{ number_format($unitPrice, 2) }}
                                                </div>
                                            @else
                                                <div class="mt-0.5 text-sm font-medium text-red-600">
                                                    No price
                                                </div>
                                            @endif
                                        </div>
                                        {{-- Amount --}}
                                        <div>
                                            <div class="text-[10px] text-gray-400">
                                                Amount
                                            </div>

                                            <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                                {{ number_format($itemAmount, 2) }}
                                            </div>
                                        </div>

                                        {{-- Item Total --}}
                                        <div>
                                            <div class="text-[10px] text-gray-400">
                                                Item Total
                                            </div>

                                            @if ($preferredVendor)
                                                <div
                                                    x-text="money(total)"
                                                    class="mt-0.5 text-sm font-semibold text-gray-900"
                                                ></div>
                                            @else
                                                <div class="mt-0.5 text-sm text-gray-400">
                                                    —
                                                </div>
                                            @endif
                                        </div>
                                        {{-- Shipping Fee --}}
                                        <div>
                                            @if ($preferredVendor && $unitPrice > 0)
                                                <label class="block text-[10px] font-medium text-gray-400">
                                                    Shipping
                                                </label>

                                                <input
                                                    type="tel"
                                                    x-mask:dynamic="$money($input, '.', ',', 2)"
                                                    x-model="itemData.shippingFee"
                                                    @input="validateDiscount()"
                                                    class="mt-1 block w-full rounded-md border-gray-300
                                                        px-2 py-1.5 text-right text-sm shadow-sm
                                                        focus:border-indigo-500 focus:ring-indigo-500"
                                                    placeholder="0.00"
                                                >
                                            @else
                                                <div class="text-[10px] text-gray-400">
                                                    Shipping
                                                </div>

                                                <div class="mt-1 text-sm text-gray-300">
                                                    —
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Discount --}}
                                        <div>
                                            @if ($preferredVendor && $unitPrice > 0)
                                                <label class="block text-[10px] font-medium text-gray-400">
                                                    Discount
                                                </label>
                                                <input
                                                    type="tel"
                                                    x-mask:dynamic="$money($input, '.', ',', 2)"
                                                    x-model="itemData.discount"
                                                    @input="validateDiscount()"
                                                    class="mt-1 block w-full rounded-md border-gray-300
                                                        px-2 py-1.5 text-right text-sm shadow-sm
                                                        focus:border-indigo-500 focus:ring-indigo-500"
                                                    placeholder="0.00"
                                                >
                                            @else
                                                <div class="text-[10px] text-gray-400">
                                                    Discount
                                                </div>
                                                <div class="mt-1 text-sm text-gray-300">
                                                    —
                                                </div>
                                            @endif
                                        </div>
                                        {{-- Manage Vendors --}}
                                        <div class="flex justify-end">
                                            <button
                                                type="button"
                                                wire:click="openVendorModal({{ $item->item_id }})"
                                                class="inline-flex items-center justify-center rounded-md border
                                                    border-gray-300 bg-white px-3 py-2 text-xs font-medium
                                                    text-gray-700 shadow-sm transition hover:bg-gray-50
                                                    whitespace-nowrap"
                                            >
                                                Manage Vendors
                                            </button>
                                        </div>
                                    </div>
                                    {{-- =========================================================
                                        Mobile
                                    ========================================================== --}}
                                    <div class="xl:hidden space-y-4">
                                        {{-- Top: Image + Item + Vendors --}}
                                        <div class="flex items-start gap-3">
                                            {{-- Image --}}
                                            <div
                                                class="h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-gray-100"
                                            >
                                                <img
                                                    src="{{ $item?->item?->primaryImage
                                                        ? Storage::url($item->item->primaryImage->path)
                                                        : asset('images/default-item.png') }}"
                                                    alt="{{ $item?->item?->item_name }}"
                                                    class="h-full w-full object-cover"
                                                >
                                            </div>
                                            {{-- Item Info --}}
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $item?->item?->item_name }}
                                                </div>

                                                <div class="mt-1 text-xs text-gray-500">
                                                    SKU:
                                                    <span class="font-medium">
                                                        {{ $item?->item?->sku }}
                                                    </span>

                                                    <span class="mx-1 text-gray-300">•</span>

                                                    Qty:
                                                    <span class="font-semibold text-gray-700">
                                                        {{ $item?->quantity }}
                                                    </span>
                                                </div>

                                                <div class="mt-2">
                                                    @if ($preferredVendor)
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="text-[11px] text-gray-400">
                                                                Vendor
                                                            </span>

                                                            <span class="truncate text-xs font-medium text-gray-700">
                                                                {{ $preferredVendor->vendor->name }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <span class="text-xs text-gray-400">
                                                            No vendor selected
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Manage Vendors --}}
                                            <button
                                                type="button"
                                                wire:click="openVendorModal({{ $item->item_id }})"
                                                class="shrink-0 rounded-md border border-gray-300
                                                    bg-white px-2.5 py-2 text-xs font-medium
                                                    text-gray-700 shadow-sm hover:bg-gray-50"
                                            >
                                                Vendors
                                            </button>
                                        </div>


                                        @if ($preferredVendor)

                                            {{-- Price / Amount / Total --}}
                                            <div class="rounded-lg border border-gray-100 bg-gray-50/70 p-3">

                                                <div class="grid grid-cols-2 gap-3">

                                                    {{-- Unit Price --}}
                                                    <div>
                                                        <div class="text-[11px] text-gray-400">
                                                            Unit Price
                                                        </div>

                                                        <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                                            {{ number_format($unitPrice, 2) }}
                                                        </div>
                                                    </div>

                                                    {{-- Amount --}}
                                                    <div>
                                                        <div class="text-[11px] text-gray-400">
                                                            Amount
                                                        </div>

                                                        <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                                            {{ number_format($itemAmount, 2) }}
                                                        </div>
                                                    </div>

                                                    {{-- Item Total --}}
                                                    <div class="col-span-2">
                                                        <div class="text-[11px] text-gray-400">
                                                            Item Total
                                                        </div>

                                                        <div
                                                            x-text="money(total)"
                                                            class="mt-0.5 text-sm font-semibold text-gray-900"
                                                        ></div>
                                                    </div>

                                                </div>

                                                {{-- Adjustments --}}
                                                @if ($unitPrice > 0)
                                                    <div class="mt-4 grid grid-cols-1 gap-3">

                                                        {{-- Shipping --}}
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-500">
                                                                Shipping Fee
                                                            </label>

                                                            <input
                                                                type="tel"
                                                                x-mask:dynamic="$money($input, '.', ',', 2)"
                                                                x-model="itemData.shippingFee"
                                                                @input="validateDiscount()"
                                                                class="mt-1 block w-full rounded-md border-gray-300
                                                                    text-right text-sm shadow-sm
                                                                    focus:border-indigo-500 focus:ring-indigo-500"
                                                                placeholder="0.00"
                                                            >
                                                        </div>

                                                        {{-- Discount --}}
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-500">
                                                                Item Discount
                                                            </label>

                                                            <input
                                                                type="tel"
                                                                x-mask:dynamic="$money($input, '.', ',', 2)"
                                                                x-model="itemData.discount"
                                                                @input="validateDiscount()"
                                                                class="mt-1 block w-full rounded-md border-gray-300
                                                                    text-right text-sm shadow-sm
                                                                    focus:border-indigo-500 focus:ring-indigo-500"
                                                                placeholder="0.00"
                                                            >
                                                        </div>

                                                    </div>
                                                @endif

                                            </div>

                                        @else

                                            {{-- No Vendor --}}
                                            <div
                                                class="rounded-lg border border-dashed border-gray-200
                                                    bg-gray-50 px-4 py-3"
                                            >
                                                <div class="text-sm text-gray-500">
                                                    No vendor selected.
                                                </div>

                                                <div class="mt-0.5 text-xs text-gray-400">
                                                    Select a preferred vendor before approving this request.
                                                </div>
                                            </div>

                                        @endif

                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- Request Discount --}}
                    <div class="border-t border-gray-100 bg-white px-4 py-4 sm:px-5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <div class="text-sm font-medium text-gray-700">
                                    Request Discount
                                </div>
                                <div class="text-xs text-gray-400">
                                    Additional discount applied to the total purchase amount
                                </div>
                            </div>

                            <div class="w-40">
                                <input
                                    type="tel"
                                    x-mask:dynamic="$money($input, '.', ',', 2)"
                                    x-model="requestDiscount"
                                    @input="validateRequestDiscount()"
                                    class="block w-full rounded-md border-gray-300
                                        text-right text-sm shadow-sm
                                        focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="0.00"
                                >
                            </div>
                        </div>
                    </div>
                    {{-- Footer --}}
                    <div class="border-t border-gray-100 bg-gray-50 px-4 py-4 sm:px-5">

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                            {{-- Remark --}}
                            <div class="min-w-0">
                                @if ($request->remark)
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium">Remark:</span>
                                        {{ $request->remark }}
                                    </div>
                                @else
                                    <div class="hidden sm:block"></div>
                                @endif
                            </div>

                            {{-- Total + Approve --}}
                            <div class="flex items-center justify-between gap-3 sm:justify-end">

                                <div class="shrink-0 text-right">
                                    <div class="text-xs text-gray-500">
                                        Total
                                    </div>

                                    <div
                                        x-text="money(total)"
                                        class="text-base font-bold text-gray-900 sm:text-sm"
                                    ></div>
                                </div>

                                <x-button
                                    type="button"
                                    @click="
                                        $wire.approve(
                                            {{ $workflow->id }},
                                            requestDiscount,
                                            items
                                        )
                                    "
                                    :disabled="!$workflow->can_approve"
                                    class="shrink-0 {{ !$workflow->can_approve ? 'opacity-50 cursor-not-allowed' : '' }}"
                                >
                                    Approved
                                </x-button>

                            </div>
                        </div>
                    </div>
                </div>
                
            @endforeach
            <div class="mt-6">
                {{ $requests->links() }}
            </div>
        @else
            <div class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-12 text-center">
                <div class="text-sm font-medium text-gray-900">
                    No pending procurement requests.
                </div>
                <div class="mt-1 text-sm text-gray-500">
                    There are currently no purchase requests waiting for procurement.
                </div>
            </div>
        @endif
    </div>
    <x-dialog-modal wire:model="showVendorModal">
        <x-slot name="title">
            <div>
                <div class="text-lg font-semibold text-gray-900">
                    Vendors
                </div>
                <div class="mt-1 text-sm font-normal text-gray-500">
                    {{ $selectedItemName }}
                </div>
            </div>
        </x-slot>
        <x-slot name="content">
            <div class="space-y-3" wire:key="vendor-modal-{{ $selectedItemId }}">
                @forelse ($vendorForms as $itemVendorId => $vendor)
                    <div
                        class="rounded-lg border p-4
                            {{ !empty($vendor['is_preferred'])
                                ? 'border-indigo-300 bg-indigo-50/30'
                                : 'border-gray-200 bg-white' }}"
                    >
                        {{-- Vendor header --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="font-medium text-gray-900">
                                    {{ $vendor['vendor_name'] }}
                                </div>
                                @if (!empty($vendor['is_preferred']))
                                    <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-700">
                                        Primary
                                    </span>
                                @endif
                            </div>
                            <button
                                type="button"
                                wire:click="setPrimaryVendor({{ $itemVendorId }})"
                                class="text-xs font-medium
                                    {{ !empty($vendor['is_preferred'])
                                        ? 'text-indigo-700'
                                        : 'text-gray-500 hover:text-gray-900' }}"
                            >
                                {{ !empty($vendor['is_preferred'])
                                    ? 'Primary Vendor'
                                    : 'Set as Primary' }}
                            </button>
                            @if (!$vendor['is_preferred'])
                                <button
                                    type="button"
                                    wire:click="removeVendor({{ $itemVendorId }})"
                                    wire:confirm="Are you sure you want to remove this vendor?"
                                    class="text-xs font-medium text-red-600 hover:text-red-700"
                                >
                                    Remove
                                </button>
                            @endif
                        </div>
                        {{-- Vendor fields --}}
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            {{-- Vendor SKU --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-500">
                                    Vendor SKU
                                </label>
                                <input
                                    type="text"
                                    wire:model.defer="vendorForms.{{ $itemVendorId }}.vendor_sku"
                                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>
                            {{-- Unit Price --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-500">
                                    Unit Price
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    wire:model.defer="vendorForms.{{ $itemVendorId }}.unit_price"
                                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>
                            {{-- MOQ --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-500">
                                    MOQ
                                </label>
                                <input
                                    type="number"
                                    min="1"
                                    wire:model.defer="vendorForms.{{ $itemVendorId }}.minimum_order_qty"
                                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>
                            {{-- Lead Time --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-500">
                                    Lead Time
                                </label>
                                <div class="relative">
                                    <input
                                        type="number"
                                        min="0"
                                        wire:model.defer="vendorForms.{{ $itemVendorId }}.lead_time"
                                        class="mt-1 block w-full rounded-md border-gray-300 pr-12 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                    <span class="pointer-events-none absolute right-3 top-1/2 mt-0.5 -translate-y-1/2 text-xs text-gray-400">
                                        days
                                    </span>
                                </div>
                            </div>
                            {{-- Disbursement Type --}}
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-500">
                                    Disbursement Type
                                    <span class="text-red-500">*</span>
                                </label>

                                <select
                                    wire:model.defer="vendorForms.{{ $itemVendorId }}.disbursement_type_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm
                                        focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Select type</option>

                                    @foreach ($disbursementTypes as $type)
                                        <option value="{{ $type->id }}">
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error("vendorForms.$itemVendorId.disbursement_type_id")
                                    <p class="mt-1 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            {{-- Payment Details --}}
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-500">
                                    Payment Details
                                </label>

                                <textarea
                                    wire:model.defer="vendorForms.{{ $itemVendorId }}.payment_details"
                                    rows="2"
                                    placeholder="e.g. BDO Bank / Account Name / Account Number"
                                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm
                                        focus:border-indigo-500 focus:ring-indigo-500"
                                ></textarea>

                                @error("vendorForms.$itemVendorId.payment_details")
                                    <p class="mt-1 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border border-dashed border-gray-300 px-6 py-8 text-center">
                        <div class="text-sm font-medium text-gray-900">
                            No vendors
                        </div>
                        <div class="mt-1 text-sm text-gray-500">
                            No vendors are assigned to this item.
                        </div>
                    </div>
                @endforelse
            </div>
            <div class="mt-2">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="vendorSearch"
                    placeholder="Search vendor by name or code..."
                    autocomplete="off"
                    class="w-full rounded-lg border-gray-300 pr-10
                        shadow-sm focus:border-indigo-500
                        focus:ring-indigo-500"
                >
                @if (count($vendorSearchResults) > 0)
                    <div class="mt-2 overflow-hidden rounded-lg
                        border border-gray-200 bg-white shadow-sm">
                        @foreach ($vendorSearchResults as $vendor)
                            <button
                                type="button"
                                wire:key="vendor-search-{{ $vendor['id'] }}"
                                wire:click="addVendor({{ $vendor['id'] }})"
                                wire:loading.attr="disabled"
                                class="flex w-full items-center
                                    justify-between px-4 py-3
                                    text-left hover:bg-gray-50"
                            >
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $vendor['name'] }}
                                    </div>
                                    @if ($vendor['code'])
                                        <div class="mt-0.5 text-xs text-gray-500">
                                            {{ $vendor['code'] }}
                                        </div>
                                    @endif
                                </div>
                                <span class="text-sm font-medium text-indigo-600">
                                    Add
                                </span>
                            </button>
                        @endforeach
                    </div>
                @elseif (
                    strlen(trim($vendorSearch)) >= 2 &&
                    ! $vendorSearchResults
                )
                    <div class="mt-2 rounded-lg border border-gray-200
                        px-4 py-6 text-center">
                        <p class="text-sm text-gray-500">
                            No available vendors found.
                        </p>
                    </div>
                @endif
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button
                type="button"
                wire:click="closeVendorModal"
            >
                Close
            </x-secondary-button>
            <x-button
                type="button"
                class="ml-3"
                wire:click="saveVendors"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="saveVendors">
                    Save
                </span>
                <span wire:loading wire:target="saveVendors">
                    Saving...
                </span>
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>