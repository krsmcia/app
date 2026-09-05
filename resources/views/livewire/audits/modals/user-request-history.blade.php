<x-dialog-modal wire:model.live="userRequestHistoryModal" maxWidth="7xl">

    <x-slot name="title">

        <div class="flex items-center gap-3">

            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                <svg
                    class="h-5 w-5 text-indigo-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                </svg>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    User Request History
                </h2>

                @if ($user)
                    <p class="text-sm text-gray-500">
                        {{ $user->name }}
                    </p>
                @endif
            </div>

        </div>

    </x-slot>


    <x-slot name="content">

        @if ($user)

            <div class="space-y-6">

                {{-- =====================================================
                    USER INFORMATION
                ====================================================== --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">

                    <div class="mb-4 flex items-center justify-between">

                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">
                                Requester Information
                            </h3>

                            <p class="mt-1 text-xs text-gray-500">
                                User's purchase request history
                            </p>
                        </div>

                        <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-700">
                            {{ $requestsCount }} Requests
                        </span>

                    </div>


                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                        <div>
                            <div class="text-xs text-gray-400">
                                Name
                            </div>

                            <div class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $user->name }}
                            </div>
                        </div>


                        <div>
                            <div class="text-xs text-gray-400">
                                Email
                            </div>

                            <div class="mt-1 text-sm text-gray-700">
                                {{ $user->email }}
                            </div>
                        </div>


                        <div>
                            <div class="text-xs text-gray-400">
                                Department
                            </div>

                            <div class="mt-1 text-sm font-medium text-gray-900">
                                {{ $user->department?->name ?? '-' }}
                            </div>
                        </div>


                        <div>
                            <div class="text-xs text-gray-400">
                                Total Requests
                            </div>

                            <div class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $requestsCount }}
                            </div>
                        </div>

                    </div>

                </div>
                {{-- =====================================================
                    SEARCH FILTERS
                ====================================================== --}}
                <div class="rounded-xl border border-gray-200 bg-white p-4">

                    <div class="mb-4 flex items-center justify-between">

                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">
                                Search Request History
                            </h3>

                            <p class="mt-1 text-xs text-gray-500">
                                Filter this user's requested products by date or title.
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
                                wire:model.live.debounce.300ms="from"
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
                                wire:model.live.debounce.300ms="to"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>


                        {{-- Title --}}
                        <div class="md:col-span-2">

                            <label class="block text-xs font-medium text-gray-600">
                                Title
                            </label>

                            <input
                                type="text"
                                wire:model.live.debounce.400ms="title"
                                placeholder="Search product title, SKU, or vendor..."
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                        </div>

                    </div>


                    {{-- Clear --}}
                    @if ($from || $to || $title)
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

                {{-- =====================================================
                    REQUESTED PRODUCTS
                ====================================================== --}}
                <div>

                    <div class="mb-3 flex items-center justify-between">

                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">
                                Requested Products
                            </h3>

                            <p class="mt-1 text-xs text-gray-500">
                                Products requested by this user.
                            </p>
                        </div>

                        <span class="text-xs text-gray-500">
                            {{ $groupedItems?->count() ?? 0 }} Products
                        </span>

                    </div>


                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">

                        <div class="overflow-x-auto">

                            <table class="min-w-full divide-y divide-gray-200">

                                <thead class="bg-gray-50">

                                    <tr>

                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Product
                                        </th>

                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            SKU
                                        </th>

                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Vendor
                                        </th>

                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Requests
                                        </th>

                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Total Qty
                                        </th>

                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Last Requested
                                        </th>

                                    </tr>

                                </thead>


                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($groupedItems as $product)
                                        @php
                                            $purchaseItem = $product['purchase_item'];
                                            $item = $product['item'];
                                        @endphp
                                        <tr class="hover:bg-gray-50">

                                            {{-- Product --}}
                                            <td class="px-4 py-3">

                                                <div class="flex items-center gap-3">

                                                    <div class="h-11 w-11 shrink-0 overflow-hidden rounded-lg border border-gray-200 bg-gray-50">

                                                        @if ($item?->primaryImage)

                                                            <img
                                                                src="{{ Storage::url($item->primaryImage->path) }}"
                                                                alt="{{ $purchaseItem->item_name }}"
                                                                class="h-full w-full object-cover"
                                                            >

                                                        @else

                                                            <img
                                                                src="{{ asset('images/default-item.png') }}"
                                                                alt="No image"
                                                                class="h-full w-full object-cover"
                                                            >

                                                        @endif

                                                    </div>

                                                    <div class="min-w-0">

                                                        <div class="max-w-[280px] truncate text-sm font-semibold text-gray-900">
                                                            {{ $purchaseItem->item_name }}
                                                        </div>

                                                        @if ($item)

                                                            <div class="text-xs text-gray-400">
                                                                Item ID: {{ $item->id }}
                                                            </div>

                                                        @endif

                                                    </div>

                                                </div>

                                            </td>


                                            {{-- SKU --}}
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                {{ $purchaseItem->sku ?? '-' }}
                                            </td>


                                            {{-- Vendor --}}
                                            <td class="px-4 py-3">

                                                <div class="text-sm text-gray-700">
                                                    {{ $purchaseItem->vendor_name ?? '-' }}
                                                </div>

                                                @if ($purchaseItem->vendor_sku)

                                                    <div class="text-xs text-gray-400">
                                                        SKU: {{ $purchaseItem->vendor_sku }}
                                                    </div>

                                                @endif

                                            </td>


                                            {{-- Request Count --}}
                                            <td class="px-4 py-3 text-center">

                                                <span class="inline-flex rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                                    {{ $product['request_count'] }}
                                                </span>

                                            </td>


                                            {{-- Total Quantity --}}
                                            <td class="whitespace-nowrap px-4 py-3 text-right">

                                                <span class="text-sm font-semibold text-gray-900">
                                                    {{ number_format($product['total_quantity']) }}
                                                </span>

                                            </td>


                                            {{-- Last Requested --}}
                                            <td class="whitespace-nowrap px-4 py-3 text-right">

                                                <div class="text-sm text-gray-700">
                                                    {{ $product['last_requested_at']?->format('M d, Y') ?? '-' }}
                                                </div>

                                                <div class="text-xs text-gray-400">
                                                    {{ $product['last_request_no'] ?? '-' }}
                                                </div>

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td
                                                colspan="6"
                                                class="px-5 py-12 text-center"
                                            >

                                                <div class="text-sm font-medium text-gray-900">
                                                    No requested products
                                                </div>

                                                <div class="mt-1 text-sm text-gray-500">
                                                    This user has not requested any products yet.
                                                </div>

                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>
                            @if ($groupedItems->hasPages())
                                <div class="border-t border-gray-200 px-4 py-3">
                                    {{ $groupedItems->links() }}
                                </div>
                            @endif
                        </div>

                    </div>

                </div>

            </div>

        @else

            <div class="py-12 text-center text-sm text-gray-500">
                Loading user information...
            </div>

        @endif

    </x-slot>


    <x-slot name="footer">

        <x-secondary-button
            wire:click="closeModal"
            wire:loading.attr="disabled"
        >
            {{ __('Close') }}
        </x-secondary-button>

    </x-slot>

</x-dialog-modal>