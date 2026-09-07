<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

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
    <div class="mb-4">
        <div class="relative max-w-md">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search item or SKU..."
                class="w-full rounded-lg border-gray-300 pl-10 pr-4 text-sm shadow-sm
                       focus:border-indigo-500 focus:ring-indigo-500"
            >

            <svg
                class="absolute left-3 top-2.5 h-5 w-5 text-gray-400"
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

    {{-- Items --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        <div class="divide-y divide-gray-100">

            @forelse($items as $workflowItem)

                @php
                    $purchaseItem = $workflowItem->purchaseItem;
                    $item = $purchaseItem?->item;
                    $vendor = $purchaseItem?->itemVendor?->vendor;
                    $request = $workflowItem->purchaseWorkflow?->purchaseRequest;
                @endphp

                <div class="p-4 hover:bg-gray-50">

                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                        {{-- Item --}}
                        <div class="flex min-w-0 items-center gap-4">

                            {{-- Image --}}
                            <div class="h-14 w-14 shrink-0 overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                                <img
                                    src="{{ $item?->image_url ?? asset('images/default-item.png') }}"
                                    alt="{{ $item?->name ?? 'Item' }}"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                >
                            </div>

                            {{-- Item information --}}
                            <div class="min-w-0">
                                <div class="font-medium text-gray-900">
                                    {{ $item?->name ?? 'Unknown Item' }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500">
                                    SKU:
                                    {{ $item?->sku ?? '-' }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500">
                                    Vendor:
                                    {{ $vendor?->name ?? '-' }}
                                </div>
                            </div>
                        </div>

                        {{-- Request information --}}
                        <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-sm lg:grid-cols-4">

                            <div>
                                <div class="text-xs text-gray-400">
                                    Requester
                                </div>

                                <div class="font-medium text-gray-700">
                                    {{ $request?->user?->name ?? '-' }}
                                </div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-400">
                                    Department
                                </div>

                                <div class="font-medium text-gray-700">
                                    {{ $request?->department?->name ?? '-' }}
                                </div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-400">
                                    Quantity
                                </div>

                                <div class="font-medium text-gray-700">
                                    {{ $purchaseItem?->quantity ?? 0 }}
                                </div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-400">
                                    Amount
                                </div>

                                <div class="font-semibold text-gray-900">
                                    {{ number_format((float) ($purchaseItem?->amount ?? 0), 2) }}
                                </div>
                            </div>

                        </div>

                        {{-- Status --}}
                        <div class="shrink-0">
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                Approved
                            </span>
                        </div>

                    </div>

                </div>

            @empty

                <div class="px-6 py-12 text-center">
                    <div class="text-sm font-medium text-gray-900">
                        No approved purchase items
                    </div>

                    <div class="mt-1 text-sm text-gray-500">
                        There are currently no purchase items approved by procurement.
                    </div>
                </div>

            @endforelse

        </div>

    </div>

    {{-- Pagination --}}
    @if($items->hasPages())
        <div class="mt-4">
            {{ $items->links() }}
        </div>
    @endif

</div>