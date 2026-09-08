<div class="mx-auto max-w-7xl px-3 py-4 sm:px-6 sm:py-6 lg:px-8">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">
            Approved Purchase Items
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Purchase items approved by the procurement team.
        </p>
    </div>
    {{-- Search --}}
    <div class="mb-5">
        <div class="relative w-full sm:max-w-md">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search item or SKU..."
                class="w-full rounded-lg border-gray-300 py-2.5 pl-10 pr-4 text-sm shadow-sm
                       focus:border-indigo-500 focus:ring-indigo-500"
            >
            <svg
                class="absolute left-3 top-3 h-5 w-5 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.04 6.04a7.5 7.5 0 0 0 10.61 10.61Z"
                />
            </svg>
        </div>
    </div>
    {{-- DESKTOP --}}
    <div class="hidden overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm lg:block">
        {{-- Table Header --}}
        <div class="grid grid-cols-[minmax(280px,2.5fr)_1.2fr_1.2fr_1.4fr_110px]
                    items-center
                    border-b border-gray-200
                    bg-gray-50
                    px-5 py-3">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                Item
            </div>
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                Requester
            </div>
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                Department
            </div>
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                Purchase
            </div>
            <div class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500">
                Status
            </div>
        </div>
        {{-- Rows --}}
        <div class="divide-y divide-gray-100">
            @forelse($items as $workflowItem)
                @php
                    $purchaseItem = $workflowItem->purchaseItem;
                    $item = $purchaseItem?->item;
                    $vendor = $purchaseItem?->itemVendor?->vendor;
                    $request = $workflowItem->purchaseWorkflow?->purchaseRequest;

                    $quantity = (int) ($purchaseItem?->quantity ?? 0);
                    $unitPrice = (float) ($purchaseItem?->unit_price ?? 0);
                    $amount = (float) ($purchaseItem?->amount ?? 0);
                @endphp
                <div
                    class="grid grid-cols-[minmax(280px,2.5fr)_1.2fr_1.2fr_1.4fr_110px]
                        items-center
                        px-5 py-4
                        transition-colors
                        hover:bg-gray-50"
                >
                    {{-- ================================================= --}}
                    {{-- ITEM --}}
                    {{-- ================================================= --}}
                    <div class="flex min-w-0 items-center gap-3">
                        {{-- Image --}}
                        <div
                            class="h-12 w-12 shrink-0 overflow-hidden rounded-lg
                                border border-gray-200 bg-gray-50"
                        >
                            <img
                                src="{{ $item?->image_url ?? asset('images/default-item.png') }}"
                                alt="{{ $item?->name ?? 'Item' }}"
                                class="h-full w-full object-cover"
                                loading="lazy"
                            >
                        </div>
                        {{-- Information --}}
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-gray-900">
                                {{ $item?->name ?? 'Unknown Item' }}
                            </div>
                            <div class="mt-0.5 truncate text-xs text-gray-500">
                                <span class="text-gray-400">SKU:</span>
                                {{ $item?->sku ?? '-' }}
                            </div>
                            <div class="mt-0.5 truncate text-xs text-gray-500">
                                <span class="text-gray-400">Vendor:</span>
                                {{ $vendor?->name ?? '-' }}
                            </div>
                        </div>
                    </div>
                    {{-- ================================================= --}}
                    {{-- REQUESTER --}}
                    {{-- ================================================= --}}
                    <div class="min-w-0 pr-4">
                        <div class="text-[10px] font-medium uppercase tracking-wide text-gray-400">
                            Requested by
                        </div>
                        <div class="mt-1 truncate text-sm font-medium text-gray-700">
                            {{ $request?->user?->name ?? '-' }}
                        </div>
                    </div>
                    {{-- ================================================= --}}
                    {{-- DEPARTMENT --}}
                    {{-- ================================================= --}}
                    <div class="min-w-0 pr-4">
                        <div class="text-[10px] font-medium uppercase tracking-wide text-gray-400">
                            Department
                        </div>
                        <div class="mt-1 truncate text-sm font-medium text-gray-700">
                            {{ $request?->department?->name ?? '-' }}
                        </div>
                    </div>
                    {{-- ================================================= --}}
                    {{-- PURCHASE --}}
                    {{-- ================================================= --}}
                    <div class="flex items-center gap-5">
                        {{-- Quantity --}}
                        <div class="min-w-[45px]">
                            <div class="text-[10px] font-medium uppercase tracking-wide text-gray-400">
                                Qty
                            </div>
                            <div class="mt-1 text-sm font-semibold text-gray-800">
                                {{ number_format($quantity) }}
                            </div>
                        </div>
                        {{-- Unit Price --}}
                        <div class="min-w-[85px]">
                            <div class="text-[10px] font-medium uppercase tracking-wide text-gray-400">
                                Unit Price
                            </div>
                            <div class="mt-1 text-sm text-gray-700">
                                {{ number_format($unitPrice, 2) }}
                            </div>
                        </div>
                        {{-- Total --}}
                        <div class="min-w-[90px]">
                            <div class="text-[10px] font-medium uppercase tracking-wide text-gray-400">
                                Total
                            </div>
                            <div class="mt-1 text-sm font-semibold text-gray-900">
                                {{ number_format($amount, 2) }}
                            </div>
                        </div>
                    </div>
                    {{-- ================================================= --}}
                    {{-- STATUS --}}
                    {{-- ================================================= --}}
                    <div class="flex justify-center">
                        <span
                            class="inline-flex items-center rounded-full
                                bg-green-100 px-2.5 py-1
                                text-xs font-medium text-green-700"
                        >
                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-green-500"></span>
                            Approved
                        </span>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100">
                        <svg
                            class="h-6 w-6 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                            />
                        </svg>
                    </div>
                    <div class="mt-3 text-sm font-medium text-gray-900">
                        No approved purchase items
                    </div>
                    <div class="mt-1 text-sm text-gray-500">
                        There are currently no purchase items approved by procurement.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    {{-- MOBILE / TABLET --}}
    <div class="space-y-3 lg:hidden">
        @forelse($items as $workflowItem)
            @php
                $purchaseItem = $workflowItem->purchaseItem;
                $item = $purchaseItem?->item;
                $vendor = $purchaseItem?->itemVendor?->vendor;
                $request = $workflowItem->purchaseWorkflow?->purchaseRequest;
            @endphp
            <div
                class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm
                       transition hover:border-gray-300"
            >
                {{-- Item Header --}}
                <div class="flex items-start gap-3">
                    <div
                        class="h-14 w-14 shrink-0 overflow-hidden rounded-lg
                               border border-gray-200 bg-gray-50"
                    >
                        <img
                            src="{{ $item?->image_url ?? asset('images/default-item.png') }}"
                            alt="{{ $item?->name ?? 'Item' }}"
                            class="h-full w-full object-cover"
                            loading="lazy"
                        >
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-semibold text-gray-900">
                                    {{ $item?->name ?? 'Unknown Item' }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    SKU: {{ $item?->sku ?? '-' }}
                                </div>
                            </div>
                            <span
                                class="inline-flex shrink-0 items-center rounded-full
                                       bg-green-100 px-2.5 py-1
                                       text-[11px] font-medium text-green-700"
                            >
                                Approved
                            </span>
                        </div>
                        <div class="mt-2 truncate text-xs text-gray-500">
                            Vendor: {{ $vendor?->name ?? '-' }}
                        </div>
                    </div>
                </div>
                {{-- Information --}}
                <div class="mt-4 grid grid-cols-2 gap-2">
                    <div class="rounded-lg bg-gray-50 px-3 py-2.5">
                        <div class="text-[10px] font-medium uppercase tracking-wide text-gray-400">
                            Requester
                        </div>
                        <div class="mt-0.5 truncate text-xs font-medium text-gray-700">
                            {{ $request?->user?->name ?? '-' }}
                        </div>
                    </div>
                    <div class="rounded-lg bg-gray-50 px-3 py-2.5">
                        <div class="text-[10px] font-medium uppercase tracking-wide text-gray-400">
                            Department
                        </div>
                        <div class="mt-0.5 truncate text-xs font-medium text-gray-700">
                            {{ $request?->department?->name ?? '-' }}
                        </div>
                    </div>
                    <div class="rounded-lg bg-gray-50 px-3 py-2.5">
                        <div class="text-[10px] font-medium uppercase tracking-wide text-gray-400">
                            Quantity
                        </div>
                        <div class="mt-0.5 text-xs font-semibold text-gray-900">
                            {{ $purchaseItem?->quantity ?? 0 }}
                        </div>
                    </div>
                    <div class="rounded-lg bg-gray-50 px-3 py-2.5">
                        <div class="text-[10px] font-medium uppercase tracking-wide text-gray-400">
                            Amount
                        </div>
                        <div class="mt-0.5 text-xs font-semibold text-gray-900">
                            {{ number_format((float) ($purchaseItem?->amount ?? 0), 2) }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-gray-200 bg-white px-5 py-12 text-center shadow-sm">
                <div class="text-sm font-medium text-gray-900">
                    No approved purchase items
                </div>
                <div class="mt-1 text-sm text-gray-500">
                    There are currently no purchase items approved by procurement.
                </div>
            </div>
        @endforelse
    </div>
    {{-- Pagination --}}
    @if($items->hasPages())
        <div class="mt-4">
            {{ $items->links() }}
        </div>
    @endif
</div>
