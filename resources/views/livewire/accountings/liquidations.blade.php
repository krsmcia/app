<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">
            Liquidations
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Purchase items with remaining cash to be liquidated.
        </p>
    </div>
    <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div>
            <x-label for="fromDate" value="From" />
            <x-input
                id="fromDate"
                type="date"
                wire:model.live="fromDate"
                class="mt-1 block w-full"
            />
        </div>
        <div>
            <x-label for="toDate" value="To" />
            <x-input
                id="toDate"
                type="date"
                wire:model.live="toDate"
                class="mt-1 block w-full"
            />
        </div>
        <div>
            <x-label for="search" value="Search" />
            <x-input
                id="search"
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Request No, item, SKU..."
                class="mt-1 block w-full"
            />
        </div>
    </div>
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                            Request
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                            Item
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">
                            Released
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">
                            Purchased
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">
                            Returned
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">
                            Remaining
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($items as $item)
                        @php
                            $released = (float) ($item->released_amount ?? 0);
                            $purchased = (float) ($item->purchased_amount ?? 0);
                            $returned = (float) ($item->returned_amount ?? 0);

                            $remaining = $released + $returned - $purchased;
                        @endphp
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                {{ $item->purchaseRequest?->request_no }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $item->item?->name }}
                                </div>
                                @if ($item->item?->sku)
                                    <div class="text-xs text-gray-500">
                                        {{ $item->item->sku }}
                                    </div>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">
                                ₱{{ number_format($released, 2) }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">
                                ₱{{ number_format($purchased, 2) }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">
                                ₱{{ number_format($returned, 2) }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                ₱{{ number_format($remaining, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                                No items requiring liquidation.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($items->hasPages())
            <div class="border-t border-gray-200 px-4 py-3">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>