<x-dialog-modal wire:model.live="itemModal" maxWidth="7xl">

    <x-slot name="title">
        <div class="min-w-0">
            <div class="truncate text-lg font-semibold text-gray-900">
                {{ $selectedItem?->name ?? 'Item' }}
            </div>

            @if ($selectedItem)
                <div class="mt-1 text-xs text-gray-500">
                    {{ $selectedItem->sku }}
                </div>
            @endif
        </div>
    </x-slot>


    <x-slot name="content">

        @if ($selectedItem)

            {{-- Item Information --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4">

                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-900">
                        Item Information
                    </h3>

                    <p class="mt-1 text-xs text-gray-500">
                        Basic information about this item.
                    </p>
                </div>


                <div class="grid grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-3 lg:grid-cols-4">

                    {{-- SKU --}}
                    <div class="min-w-0">
                        <div class="text-xs font-medium text-gray-500">
                            SKU
                        </div>

                        <div class="mt-1 break-all text-sm text-gray-900">
                            {{ $selectedItem->sku }}
                        </div>
                    </div>


                    {{-- Barcode --}}
                    <div class="min-w-0">
                        <div class="text-xs font-medium text-gray-500">
                            Barcode
                        </div>

                        <div class="mt-1 break-all text-sm text-gray-900">
                            {{ $selectedItem->barcode ?: '-' }}
                        </div>
                    </div>


                    {{-- Unit --}}
                    <div class="min-w-0">
                        <div class="text-xs font-medium text-gray-500">
                            Unit
                        </div>

                        <div class="mt-1 text-sm text-gray-900">
                            {{ $selectedItem->unit }}
                        </div>
                    </div>


                    {{-- Brand --}}
                    <div class="min-w-0">
                        <div class="text-xs font-medium text-gray-500">
                            Brand
                        </div>

                        <div class="mt-1 break-words text-sm text-gray-900">
                            {{ $selectedItem->brand ?: '-' }}
                        </div>
                    </div>


                    {{-- Color --}}
                    <div class="min-w-0">
                        <div class="text-xs font-medium text-gray-500">
                            Color
                        </div>

                        <div class="mt-1 break-words text-sm text-gray-900">
                            {{ $selectedItem->color ?: '-' }}
                        </div>
                    </div>


                    {{-- Size --}}
                    <div class="min-w-0">
                        <div class="text-xs font-medium text-gray-500">
                            Size
                        </div>

                        <div class="mt-1 break-words text-sm text-gray-900">
                            {{ $selectedItem->size ?: '-' }}
                        </div>
                    </div>


                    {{-- Status --}}
                    <div class="min-w-0">
                        <div class="text-xs font-medium text-gray-500">
                            Status
                        </div>

                        <div class="mt-1">
                            @if ($selectedItem->is_active)
                                <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>

                </div>


                {{-- Description --}}
                @if ($selectedItem->description)
                    <div class="mt-4 border-t border-gray-100 pt-4">

                        <div class="text-xs font-medium text-gray-500">
                            Description
                        </div>

                        <div class="mt-1 whitespace-pre-line break-words text-sm text-gray-700">
                            {{ $selectedItem->description }}
                        </div>

                    </div>
                @endif

            </div>


            {{-- Vendors --}}
            <div class="mt-4 rounded-xl border border-gray-200 bg-white p-4">

                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-900">
                        Vendors
                    </h3>

                    <p class="mt-1 text-xs text-gray-500">
                        Vendors associated with this item.
                    </p>
                </div>


                {{-- Desktop --}}
                <div class="hidden overflow-x-auto md:block">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead>
                            <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-500">

                                <th class="px-4 py-3">
                                    Vendor
                                </th>

                                <th class="px-4 py-3">
                                    Vendor SKU
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Unit Price
                                </th>

                                <th class="px-4 py-3 text-center">
                                    MOQ
                                </th>

                                <th class="px-4 py-3 text-center">
                                    Lead Time
                                </th>

                                <th class="px-4 py-3 text-center">
                                    Preferred
                                </th>

                            </tr>
                        </thead>


                        <tbody class="divide-y divide-gray-200">

                            @forelse ($selectedItem->itemVendors as $itemVendor)

                                <tr>

                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                        {{ $itemVendor->vendor?->name ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $itemVendor->vendor_sku ?: '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-right text-sm text-gray-900">
                                        {{ $itemVendor->unit_price !== null
                                            ? number_format($itemVendor->unit_price, 2)
                                            : '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-center text-sm text-gray-600">
                                        {{ $itemVendor->minimum_order_qty }}
                                    </td>

                                    <td class="px-4 py-3 text-center text-sm text-gray-600">
                                        {{ $itemVendor->lead_time !== null
                                            ? $itemVendor->lead_time . ' days'
                                            : '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-center">

                                        @if ($itemVendor->is_preferred)
                                            <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                                Preferred
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">
                                                -
                                            </span>
                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="6"
                                        class="px-4 py-8 text-center text-sm text-gray-500"
                                    >
                                        No vendors found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- Mobile --}}
                <div class="space-y-3 md:hidden">

                    @forelse ($selectedItem->itemVendors as $itemVendor)

                        <div class="rounded-lg border border-gray-200 p-3">

                            {{-- Vendor Header --}}
                            <div class="flex items-start justify-between gap-3">

                                <div class="min-w-0">

                                    <div class="break-words text-sm font-semibold text-gray-900">
                                        {{ $itemVendor->vendor?->name ?? '-' }}
                                    </div>

                                    @if ($itemVendor->vendor_sku)
                                        <div class="mt-0.5 break-all text-xs text-gray-500">
                                            SKU: {{ $itemVendor->vendor_sku }}
                                        </div>
                                    @endif

                                </div>


                                @if ($itemVendor->is_preferred)
                                    <span class="shrink-0 inline-flex rounded-full bg-green-100 px-2 py-1 text-[10px] font-medium text-green-700">
                                        Preferred
                                    </span>
                                @endif

                            </div>


                            {{-- Vendor Details --}}
                            <div class="mt-3 grid grid-cols-3 gap-3 border-t border-gray-100 pt-3">

                                {{-- Price --}}
                                <div class="min-w-0">

                                    <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                        Unit Price
                                    </div>

                                    <div class="mt-1 break-all text-sm font-medium text-gray-900">
                                        {{ $itemVendor->unit_price !== null
                                            ? number_format($itemVendor->unit_price, 2)
                                            : '-' }}
                                    </div>

                                </div>


                                {{-- MOQ --}}
                                <div class="min-w-0">

                                    <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                        MOQ
                                    </div>

                                    <div class="mt-1 text-sm font-medium text-gray-900">
                                        {{ $itemVendor->minimum_order_qty }}
                                    </div>

                                </div>


                                {{-- Lead Time --}}
                                <div class="min-w-0">

                                    <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                        Lead Time
                                    </div>

                                    <div class="mt-1 text-sm font-medium text-gray-900">
                                        {{ $itemVendor->lead_time !== null
                                            ? $itemVendor->lead_time . ' days'
                                            : '-' }}
                                    </div>

                                </div>

                            </div>

                        </div>

                    @empty
                        <div class="py-8 text-center text-sm text-gray-500">
                            No vendors found.
                        </div>
                    @endforelse
                </div>
            </div>
            {{-- Purchase History --}}
            <div class="mt-4 rounded-xl border border-gray-200 bg-white p-4">

                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-900">
                        Purchase History
                    </h3>

                    <p class="mt-1 text-xs text-gray-500">
                        Completed purchase history for this item.
                    </p>
                </div>


                {{-- Desktop --}}
                <div class="hidden overflow-x-auto md:block">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead>
                            <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-500">

                                <th class="px-4 py-3">
                                    Request No.
                                </th>

                                <th class="px-4 py-3">
                                    Requested By
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Quantity
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Unit Price
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Amount
                                </th>

                                <th class="px-4 py-3">
                                    Vendor
                                </th>

                                <th class="px-4 py-3">
                                    Completed At
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">

                            @forelse ($this->completedPurchaseItems as $workflowItem)

                                @php
                                    $purchaseItem = $workflowItem->purchaseItem;
                                    $purchaseRequest = $workflowItem->purchaseWorkflow?->purchaseRequest;
                                @endphp

                                <tr>

                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                        {{ $purchaseRequest?->request_no ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $purchaseRequest?->user?->name ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-right text-sm text-gray-600">
                                        {{ $purchaseItem?->quantity ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-right text-sm text-gray-600">
                                        {{ $purchaseItem?->unit_price !== null
                                            ? number_format($purchaseItem->unit_price, 2)
                                            : '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-right text-sm text-gray-900">
                                        {{ $purchaseItem?->amount !== null
                                            ? number_format($purchaseItem->amount, 2)
                                            : '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $purchaseItem?->vendor_name ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $workflowItem->acted_at?->format('M d, Y H:i') ?? '-' }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="7"
                                        class="px-4 py-8 text-center text-sm text-gray-500"
                                    >
                                        No completed purchase history found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- Mobile --}}
                <div class="space-y-3 md:hidden">

                    @forelse ($this->completedPurchaseItems as $workflowItem)

                        @php
                            $purchaseItem = $workflowItem->purchaseItem;
                            $purchaseRequest = $workflowItem->purchaseWorkflow?->purchaseRequest;
                        @endphp

                        <div class="rounded-lg border border-gray-200 p-3">

                            <div class="flex items-start justify-between gap-3">

                                <div class="min-w-0">

                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $purchaseRequest?->request_no ?? '-' }}
                                    </div>

                                    <div class="mt-0.5 text-xs text-gray-500">
                                        {{ $purchaseRequest?->user?->name ?? '-' }}
                                    </div>

                                </div>

                                <span class="shrink-0 inline-flex rounded-full bg-green-100 px-2 py-1 text-[10px] font-medium text-green-700">
                                    Completed
                                </span>

                            </div>


                            <div class="mt-3 grid grid-cols-2 gap-3 border-t border-gray-100 pt-3">

                                <div>
                                    <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                        Quantity
                                    </div>

                                    <div class="mt-1 text-sm font-medium text-gray-900">
                                        {{ $purchaseItem?->quantity ?? '-' }}
                                    </div>
                                </div>


                                <div>
                                    <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                        Unit Price
                                    </div>

                                    <div class="mt-1 text-sm font-medium text-gray-900">
                                        {{ $purchaseItem?->unit_price !== null
                                            ? number_format($purchaseItem->unit_price, 2)
                                            : '-' }}
                                    </div>
                                </div>


                                <div>
                                    <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                        Amount
                                    </div>

                                    <div class="mt-1 text-sm font-medium text-gray-900">
                                        {{ $purchaseItem?->amount !== null
                                            ? number_format($purchaseItem->amount, 2)
                                            : '-' }}
                                    </div>
                                </div>


                                <div>
                                    <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                        Vendor
                                    </div>

                                    <div class="mt-1 break-words text-sm font-medium text-gray-900">
                                        {{ $purchaseItem?->vendor_name ?? '-' }}
                                    </div>
                                </div>

                            </div>


                            <div class="mt-3 border-t border-gray-100 pt-3">

                                <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                    Completed At
                                </div>

                                <div class="mt-1 text-xs text-gray-600">
                                    {{ $workflowItem->acted_at?->format('M d, Y H:i') ?? '-' }}
                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="py-8 text-center text-sm text-gray-500">
                            No completed purchase history found.
                        </div>

                    @endforelse

                </div>


                {{-- Pagination --}}
                @if ($this->completedPurchaseItems->hasPages())

                    <div class="mt-4 border-t border-gray-100 pt-4">
                        {{ $this->completedPurchaseItems->links() }}
                    </div>

                @endif

            </div>
        @endif
    </x-slot>
    <x-slot name="footer">
        <x-secondary-button
            wire:click="$set('itemModal', false)"
            wire:loading.attr="disabled"
        >
            {{ __('Close') }}
        </x-secondary-button>

    </x-slot>

</x-dialog-modal>