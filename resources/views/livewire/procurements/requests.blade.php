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
        @forelse ($workflows as $workflow)
            @php
                $request = $workflow->purchaseRequest;
            @endphp
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
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
                        @endphp
                        <div class="px-4 py-4 sm:px-5">
                            <div class="flex items-center gap-3 sm:gap-4">
                                {{-- Image --}}
                                <div class="h-12 w-12 shrink-0 overflow-hidden rounded-md bg-gray-100 sm:h-14 sm:w-14">
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
                                    <div class="truncate text-sm font-medium text-gray-900 sm:text-base">
                                        {{ $item?->item?->item_name }}
                                    </div>
                                    {{-- SKU + Quantity --}}
                                    <div class="mt-0.5 flex items-center gap-3 text-xs text-gray-500">
                                        <span>
                                            SKU: {{ $item?->item?->sku }}
                                        </span>
                                        <span class="text-gray-300">|</span>
                                        <span>
                                            Qty:
                                            <span class="font-semibold text-gray-700">
                                                {{ $item->quantity }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                {{-- Preferred Vendor --}}
                                <div class="hidden w-40 shrink-0 sm:block">
                                    @if ($preferredVendor)
                                        <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                            Vendor
                                        </div>
                                        <div class="truncate text-sm font-medium text-gray-700">
                                            {{ $preferredVendor->vendor->name }}
                                        </div>
                                        <div class="mt-1 text-sm">
                                            @if ($preferredVendor->unit_price !== null && (float) $preferredVendor->unit_price > 0)
                                                <div class="mt-1 text-sm">
                                                    <span class="text-gray-400">Unit Price:</span>
                                                    <span class="font-semibold text-gray-900">
                                                        {{ number_format($preferredVendor->unit_price, 2) }}
                                                    </span>
                                                </div>
                                            @else
                                                <div class="mt-1 text-sm font-medium text-red-600">
                                                    No price set
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-400">
                                            No vendor
                                        </div>
                                    @endif
                                </div>
                                {{-- Manage Vendors --}}
                                <div class="shrink-0">
                                    <button
                                        type="button"
                                        wire:click="openVendorModal({{ $item->item_id }})"
                                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                    >
                                        <span class="sm:hidden">
                                            Vendors
                                        </span>
                                        <span class="hidden sm:inline">
                                            Manage Vendors
                                        </span>
                                    </button>
                                </div>
                            </div>
                            {{-- Mobile Vendor --}}
                            <div class="mt-2 pl-[60px] sm:hidden">
                                @if ($preferredVendor)
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[11px] text-gray-400">
                                            Vendor:
                                        </span>
                                        <span class="truncate text-xs font-medium text-gray-700">
                                            {{ $preferredVendor->vendor->name }}
                                        </span>
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400">
                                        No vendor selected
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                {{-- Footer --}}
                <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 px-4 py-3">
                    @if ($request->remark)
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">Remark:</span>
                            {{ $request->remark }}
                        </div>
                    @else
                        <div></div>
                    @endif
                    <div class="text-sm flex-none flex items-center gap-2">
                        <span class="text-gray-500">
                            Total:
                        </span>
                        <span class="font-semibold text-gray-900">
                            {{ number_format($workflow->procurement_total, 2) }}
                        </span>
                        <x-button
                            type="button"
                            wire:click="approve({{ $workflow->id }})"
                            :disabled="!$workflow->can_approve"
                            class="{{ !$workflow->can_approve ? 'opacity-50 cursor-not-allowed' : '' }}"
                        >
                            Approved
                        </x-button>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-12 text-center">
                <div class="text-sm font-medium text-gray-900">
                    No pending procurement requests.
                </div>
                <div class="mt-1 text-sm text-gray-500">
                    There are currently no purchase requests waiting for procurement.
                </div>
            </div>
        @endforelse
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
                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
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
                Cancel
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