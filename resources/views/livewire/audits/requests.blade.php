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
        @if (count($requests) > 0)
            @foreach ($requests as $request)
                <div
                    class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm"
                    wire:key="request-{{ $request->id }}"
                >
                    {{-- =====================================================
                        Header
                    ====================================================== --}}
                    <div class="border-b border-gray-100 px-4 py-4 sm:px-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-sm font-semibold text-gray-900 sm:text-base">
                                        {{ $request->request_no }}
                                    </h2>
                                    <span class="rounded-full bg-yellow-50 px-2.5 py-1 text-[10px] font-medium text-yellow-700 sm:text-xs">
                                        Pending
                                    </span>
                                </div>
                                <div class="mt-1.5 text-xs text-gray-500 sm:text-sm">
                                    <span>
                                        Requested by
                                    </span>
                                    <button
                                        x-on:click="$dispatch('open-user-request-history', {
                                            userId: {{ $request->user->id }}
                                        })"
                                        class="font-medium text-gray-700"
                                        wire:loading.attr="disabled"
                                    >
                                        {{ $request->user->name }}
                                    </button>
                                    @if ($request->department)
                                        <span class="mx-1 text-gray-300">
                                            ·
                                        </span>
                                        <button
                                            x-on:click="$dispatch('open-department-request-history', {
                                                departmentId: {{ $request->department->id }}
                                            })"
                                            class="font-medium text-gray-700"
                                            wire:loading.attr="disabled"
                                        >
                                            {{ $request->department->name }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="shrink-0 text-right text-[11px] text-gray-400 sm:text-sm sm:text-gray-500">
                                {{ $request->created_at->format('Y-m-d') }}
                                <div class="sm:hidden">
                                    {{ $request->created_at->format('H:i') }}
                                </div>
                                <span class="hidden sm:inline">
                                    {{ $request->created_at->format('H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    {{-- =====================================================
                        Items
                    ====================================================== --}}
                    <div class="divide-y divide-gray-100">
                        @foreach ($request->audit_workflow->purchaseWorkflowItems as $workflowItem)
                            @php
                                $purchaseItem = $workflowItem->purchaseItem;
                                $item = $purchaseItem->item;
                                $itemVendor = $purchaseItem->itemVendor;
                            @endphp
                            <div
                                class="px-4 py-4 sm:px-5"
                                wire:key="workflow-item-{{ $workflowItem->id }}"
                            >
                                {{-- =================================================
                                    DESKTOP
                                ================================================== --}}
                                <div class="hidden items-center gap-4 md:flex">
                                    {{-- Image --}}
                                    <div class="h-14 w-14 shrink-0 overflow-hidden rounded-md bg-gray-100">

                                        <img
                                            src="{{ $item?->primaryImage
                                                ? Storage::url($item->primaryImage->path)
                                                : asset('images/default-item.png') }}"
                                            alt="{{ $purchaseItem->item_name }}"
                                            class="h-full w-full object-cover"
                                        >
                                    </div>
                                    {{-- Item Info --}}
                                    <div class="min-w-0 flex-1">
                                        <button
                                            x-on:click="$dispatch('open-item', {
                                                itemId: {{ $purchaseItem->item_id }}
                                            })"
                                            class="block max-w-full truncate text-sm font-medium text-gray-900 hover:text-gray-700"
                                            wire:loading.attr="disabled"
                                        >
                                            {{ $purchaseItem->item_name }}
                                        </button>
                                        <div class="mt-1 flex items-center gap-3 text-xs text-gray-500">
                                            <span>
                                                SKU:
                                                {{ $purchaseItem->sku }}
                                            </span>
                                            <span class="text-gray-300">
                                                |
                                            </span>
                                            <span>
                                                Qty:
                                                <span class="font-semibold text-gray-700">
                                                    {{ $purchaseItem->quantity }}
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    {{-- Vendor --}}
                                    <div class="w-52 min-w-0 shrink-0">
                                        <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                            Vendor
                                        </div>
                                        @if ($itemVendor)
                                            <button
                                                x-on:click="$dispatch('open-vendor', {
                                                    vendorId: {{ $itemVendor->vendor_id }}
                                                })"
                                                class="mt-0.5 block w-full truncate text-left text-sm font-medium text-gray-700 hover:text-gray-900"
                                                wire:loading.attr="disabled"
                                            >
                                                {{ $purchaseItem->vendor_name }}
                                            </button>
                                        @else
                                            <div class="mt-0.5 text-sm text-gray-500">
                                                {{ $purchaseItem->vendor_name ?: '-' }}
                                            </div>
                                        @endif
                                        <div class="mt-1 text-xs text-gray-500">
                                            Unit Price:
                                            <span class="font-semibold text-gray-900">
                                                {{ number_format($purchaseItem->unit_price, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    {{-- Actions --}}
                                    <div class="flex shrink-0 items-center gap-2">
                                        <x-deny-button
                                            type="button"
                                            wire:click="openDenyModal({{ $workflowItem->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="deny({{ $workflowItem->id }})"
                                        >
                                            Deny
                                        </x-deny-button>
                                        <x-approve-button
                                            type="button"
                                            wire:click="approveItem({{ $workflowItem->id }})"
                                            wire:confirm="Are you sure you want to approve this item?"
                                            wire:loading.attr="disabled"
                                            wire:target="approveItem({{ $workflowItem->id }})"
                                        >
                                            Approve
                                        </x-approve-button>
                                    </div>
                                </div>
                                {{-- =================================================
                                    MOBILE
                                ================================================== --}}
                                <div class="md:hidden">
                                    {{-- Item Header --}}
                                    <div class="flex items-start gap-3">
                                        {{-- Image --}}
                                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-md bg-gray-100">
                                            <img
                                                src="{{ $item?->primaryImage
                                                    ? Storage::url($item->primaryImage->path)
                                                    : asset('images/default-item.png') }}"
                                                alt="{{ $purchaseItem->item_name }}"
                                                class="h-full w-full object-cover"
                                            >
                                        </div>
                                        {{-- Item Name / SKU --}}
                                        <div class="min-w-0 flex-1">
                                            <button
                                                x-on:click="$dispatch('open-item', {
                                                    itemId: {{ $purchaseItem->item_id }}
                                                })"
                                                class="block w-full truncate text-left text-sm font-semibold text-gray-900"
                                                wire:loading.attr="disabled"
                                            >
                                                {{ $purchaseItem->item_name }}
                                            </button>
                                            <div class="mt-1 truncate text-xs text-gray-500">
                                                SKU:
                                                {{ $purchaseItem->sku }}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Item Details --}}
                                    <div class="mt-3 grid grid-cols-3 gap-2 rounded-lg bg-gray-50 p-3">
                                        {{-- Quantity --}}
                                        <div class="min-w-0">
                                            <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                                Qty
                                            </div>
                                            <div class="mt-1 text-sm font-semibold text-gray-900">
                                                {{ $purchaseItem->quantity }}
                                            </div>
                                        </div>
                                        {{-- Unit Price --}}
                                        <div class="min-w-0">
                                            <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                                Unit Price
                                            </div>
                                            <div class="mt-1 truncate text-sm font-semibold text-gray-900">
                                                {{ number_format($purchaseItem->unit_price, 2) }}
                                            </div>
                                        </div>
                                        {{-- Amount --}}
                                        <div class="min-w-0">
                                            <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                                Amount
                                            </div>
                                            <div class="mt-1 truncate text-sm font-semibold text-gray-900">
                                                {{ number_format($purchaseItem->amount, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Vendor --}}
                                    <div class="mt-3">
                                        <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                            Vendor
                                        </div>
                                        @if ($itemVendor)
                                            <button
                                                x-on:click="$dispatch('open-vendor', {
                                                    vendorId: {{ $itemVendor->vendor_id }}
                                                })"
                                                class="mt-0.5 block max-w-full truncate text-left text-sm font-medium text-gray-700"
                                                wire:loading.attr="disabled"
                                            >
                                                {{ $purchaseItem->vendor_name }}
                                            </button>
                                        @else
                                            <div class="mt-0.5 text-sm text-gray-500">
                                                {{ $purchaseItem->vendor_name ?: '-' }}
                                            </div>
                                        @endif
                                    </div>
                                    {{-- Mobile Actions --}}
                                    <div class="mt-4 grid grid-cols-2 gap-2">
                                        <x-deny-button
                                            type="button"
                                            wire:click="openDenyModal({{ $workflowItem->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="deny({{ $workflowItem->id }})"
                                            class="w-full justify-center"
                                        >
                                            Deny
                                        </x-deny-button>
                                        <x-approve-button
                                            type="button"
                                            wire:click="approveItem({{ $workflowItem->id }})"
                                            wire:confirm="Are you sure you want to approve this item?"
                                            wire:loading.attr="disabled"
                                            wire:target="approveItem({{ $workflowItem->id }})"
                                            class="w-full justify-center"
                                        >
                                            Approve
                                        </x-approve-button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- =====================================================
                        Footer
                    ====================================================== --}}
                    <div class="border-t border-gray-100 bg-gray-50 px-4 py-4 sm:px-5">
                        {{-- Remark --}}
                        @if ($request->remark)
                            <div class="mb-3 rounded-md bg-white px-3 py-2 text-xs text-gray-600 sm:mb-0 sm:max-w-xl sm:bg-transparent sm:px-0 sm:py-0 sm:text-sm">
                                <span class="font-medium text-gray-700">
                                    Remark:
                                </span>
                                {{ $request->remark }}
                            </div>
                        @endif
                        {{-- Desktop Footer --}}
                        <div class="hidden items-center justify-end gap-2 sm:flex">
                            <span class="mr-1 text-sm text-gray-500">
                                Total:
                            </span>
                            <span class="mr-2 text-sm font-semibold text-gray-900">
                                {{ number_format($request->audit_total, 2) }}
                            </span>
                            <x-deny-button
                                type="button"
                                wire:click="denyAll({{ $request->audit_workflow->id }})"
                                wire:confirm="Are you sure you want to deny all items?"
                                wire:loading.attr="disabled"
                                wire:target="denyAll({{ $request->audit_workflow->id }})"
                            >
                                Deny All
                            </x-deny-button>
                            <x-approve-button
                                type="button"
                                wire:click="approve({{ $request->audit_workflow->id }})"
                                wire:confirm="Are you sure you want to approve all items?"
                                wire:loading.attr="disabled"
                                wire:target="approve({{ $request->audit_workflow->id }})"
                            >
                                Approve All
                            </x-approve-button>
                        </div>
                        {{-- Mobile Footer --}}
                        <div class="sm:hidden">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">
                                    Total
                                </span>
                                <span class="text-base font-semibold text-gray-900">
                                    {{ number_format($request->audit_total, 2) }}
                                </span>
                            </div>
                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <x-deny-button
                                    type="button"
                                    wire:click="denyAll({{ $request->audit_workflow->id }})"
                                    wire:confirm="Are you sure you want to deny all items?"
                                    wire:loading.attr="disabled"
                                    wire:target="denyAll({{ $request->audit_workflow->id }})"
                                    class="w-full justify-center"
                                >
                                    Deny All
                                </x-deny-button>
                                <x-approve-button
                                    type="button"
                                    wire:click="approve({{ $request->audit_workflow->id }})"
                                    wire:confirm="Are you sure you want to approve all items?"
                                    wire:loading.attr="disabled"
                                    wire:target="approve({{ $request->audit_workflow->id }})"
                                    class="w-full justify-center"
                                >
                                    Approve All
                                </x-approve-button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            {{-- Pagination --}}
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
    <x-dialog-modal wire:model.live="denyModal">
        <x-slot name="title">
            <div>
                <div class="text-lg font-semibold text-gray-900">
                    Deny Item
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Please provide a reason for denying this item.
                </p>
            </div>
        </x-slot>
        <x-slot name="content">
            <div>
                <label
                    for="denyComment"
                    class="block text-sm font-medium text-gray-700"
                >
                    Reason
                    <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="denyComment"
                    wire:model.defer="denyComment"
                    rows="5"
                    maxlength="2000"
                    class="mt-2 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"
                    placeholder="Please explain why this item is being denied..."
                ></textarea>
                @error('denyComment')
                    <p class="mt-1.5 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
                <div class="mt-1 text-right text-xs text-gray-400">
                    {{ strlen($denyComment) }}/2000
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex w-full justify-end gap-2">
                <x-secondary-button
                    type="button"
                    wire:click="$set('denyModal', false)"
                    wire:loading.attr="disabled"
                >
                    Cancel
                </x-secondary-button>
                <x-deny-button
                    type="button"
                    wire:click="deny"
                    wire:loading.attr="disabled"
                    wire:target="deny"
                >
                    <span wire:loading.remove wire:target="deny">
                        Deny Item
                    </span>

                    <span wire:loading wire:target="deny">
                        Denying...
                    </span>
                </x-deny-button>
            </div>
        </x-slot>
    </x-dialog-modal>
    <livewire:audits.modals.user-request-history />
    <livewire:audits.modals.department-request-history />
    <livewire:audits.modals.item />
    <livewire:audits.modals.vendors />
</div>