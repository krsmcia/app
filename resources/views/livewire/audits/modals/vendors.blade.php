<x-dialog-modal wire:model.live="vendorModal" maxWidth="7xl">

    <x-slot name="title">
        <div>
            <div class="text-lg font-semibold">
                {{ $selectedVendorName }}
            </div>

            <div class="text-sm text-gray-500">
                Completed Purchase History
            </div>
        </div>
    </x-slot>

    <x-slot name="content">

        <div
            x-data="{
                dateFrom: @entangle('dateFrom').live,
                dateTo: @entangle('dateTo').live,
            }"
            class="rounded-xl border border-gray-200 bg-gray-50 p-4"
        >

            <div class="mb-4 flex items-center justify-between">

                <div>
                    <h3 class="text-sm font-semibold text-gray-900">
                        Search Purchase History
                    </h3>

                    <p class="mt-1 text-xs text-gray-500">
                        Filter this vendor's completed purchase history by date or item name.
                    </p>
                </div>

            </div>


            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">

                {{-- From --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600">
                        From
                    </label>

                    <input
                        type="date"
                        x-model="dateFrom"
                        :max="dateTo || null"
                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>


                {{-- To --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600">
                        To
                    </label>

                    <input
                        type="date"
                        x-model="dateTo"
                        :min="dateFrom || null"
                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>


                {{-- Item --}}
                <div class="md:col-span-2">

                    <label class="block text-xs font-medium text-gray-600">
                        Item
                    </label>

                    <input
                        type="text"
                        wire:model.live.debounce.400ms="itemSearch"
                        placeholder="Search item name..."
                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >

                </div>

            </div>


            {{-- Clear --}}
            @if ($dateFrom || $dateTo || $itemSearch)
                <div class="mt-3 flex justify-end">

                    <button
                        type="button"
                        wire:click="clearFilters"
                        class="text-xs font-medium text-gray-500 hover:text-gray-700"
                    >
                        Clear filters
                    </button>

                </div>
            @endif

        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">

                <thead>
                    <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        <th class="px-4 py-3">
                            Date
                        </th>

                        <th class="px-4 py-3">
                            Item
                        </th>

                        <th class="px-4 py-3 text-right">
                            Total
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">

                    @forelse ($vendorPurchaseItems as $workflowItem)

                        @php
                            $purchaseItem = $workflowItem->purchaseItem;
                        @endphp

                        <tr>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                {{ $workflowItem->acted_at
                                    ? date('Y-m-d', strtotime($workflowItem->acted_at))
                                    : '-' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-900">
                                {{ $purchaseItem?->item_name ?? '-' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900">
                                {{ $purchaseItem?->amount !== null
                                    ? number_format($purchaseItem->amount, 2)
                                    : '-' }}
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="3"
                                class="px-4 py-8 text-center text-sm text-gray-500"
                            >
                                No completed purchase history found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($vendorPurchaseItems instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">
                {{ $vendorPurchaseItems->links() }}
            </div>
        @endif

    </x-slot>

    <x-slot name="footer">
        <x-secondary-button
            wire:click="$set('vendorModal', false)"
            wire:loading.attr="disabled"
        >
            {{ __('Close') }}
        </x-secondary-button>
    </x-slot>

</x-dialog-modal>