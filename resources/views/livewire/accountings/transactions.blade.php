<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">
            Transactions
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Purchase transactions with remaining cash to be liquidated.
        </p>
    </div>
    {{-- Filters --}}
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
    {{-- Table --}}
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <div class="overflow-x-auto">

            <table class="min-w-[1100px] w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Transaction
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Type
                        </th>

                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                            Amount
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Remark
                        </th>
                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="min-w-[300px]">

                                    {{-- From → To --}}
                                    <div class="flex items-center gap-2">

                                        {{-- From --}}
                                        <div class="flex min-w-0 items-center gap-2">
                                            @if ($transaction->fromUser)
                                                <img
                                                    class="size-7 shrink-0 rounded-full object-cover"
                                                    src="{{ $transaction->fromUser->profile_photo_url }}"
                                                    alt="{{ $transaction->fromUser->name }}"
                                                />

                                                <div class="min-w-0">
                                                    <div class="truncate text-sm font-medium text-gray-900">
                                                        {{ $transaction->fromUser->name }}
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-400">—</span>
                                            @endif
                                        </div>

                                        <span class="text-gray-400">→</span>

                                        {{-- To --}}
                                        <div class="flex min-w-0 items-center gap-2">
                                            @if ($transaction->toUser)
                                                <img
                                                    class="size-7 shrink-0 rounded-full object-cover"
                                                    src="{{ $transaction->toUser->profile_photo_url }}"
                                                    alt="{{ $transaction->toUser->name }}"
                                                />

                                                <div class="min-w-0">
                                                    <div class="truncate text-sm font-medium text-gray-900">
                                                        {{ $transaction->toUser->name }}
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-400">—</span>
                                            @endif
                                        </div>

                                    </div>

                                    {{-- Items --}}
                                    @if ($transaction->purchaseItemTransactions?->isNotEmpty())
                                        <div class="mt-3 space-y-2 border-t border-gray-100 pt-2">

                                            @foreach ($transaction->purchaseItemTransactions as $itemTransaction)

                                                @php
                                                    $purchaseItem = $itemTransaction->purchaseItem;
                                                    $item = $purchaseItem?->item;
                                                @endphp

                                                @if ($item)
                                                    <div class="flex items-center gap-3">

                                                        {{-- Image --}}
                                                        <img
                                                            src="{{ $item->image_url }}"
                                                            alt="{{ $item->name }}"
                                                            class="size-10 shrink-0 rounded-lg object-cover"
                                                        >

                                                        {{-- Info --}}
                                                        <div class="min-w-0">
                                                            <div class="truncate text-sm font-medium text-gray-900">
                                                                {{ $item->name }}
                                                            </div>

                                                            <div class="text-xs text-gray-500">
                                                                SKU: {{ $item->sku }}

                                                                @if ($purchaseItem->quantity !== null)
                                                                    · Qty {{ number_format($purchaseItem->quantity) }}
                                                                @endif
                                                            </div>
                                                        </div>

                                                    </div>
                                                @endif

                                            @endforeach

                                        </div>
                                    @endif

                                    {{-- Vendor --}}
                                    @if ($transaction->vendor)
                                        <div class="mt-2 text-xs text-gray-500">
                                            Vendor:
                                            <span class="font-medium text-gray-700">
                                                {{ $transaction->vendor->name }}
                                            </span>
                                        </div>
                                    @endif

                                </div>
                            </td>


                            {{-- Type --}}
                            <td class="whitespace-nowrap px-4 py-3">
                                @php
                                    $typeClass = match ($transaction->type) {
                                        'purchased' => 'bg-green-50 text-green-700',
                                        'released' => 'bg-blue-50 text-blue-700',
                                        'returned' => 'bg-red-50 text-red-700',
                                        'transfer' => 'bg-purple-50 text-purple-700',
                                        'spent' => 'bg-orange-50 text-orange-700',
                                        'adjustment' => 'bg-gray-50 text-gray-700',
                                        default => 'bg-gray-50 text-gray-700',
                                    };
                                    $typeLabel = match ($transaction->type) {
                                        'purchased' => 'Purchased',
                                        'released' => 'Release',
                                        'returned' => 'Returned',
                                        'transfer' => 'Transfer',
                                        'spent' => 'Spent',
                                        'adjustment' => 'Adjustment',
                                        default => ucfirst($transaction->type),
                                    };
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $typeClass }}">
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            {{-- Amount --}}
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <div class="text-base font-semibold text-gray-900">
                                    ₱{{ number_format($transaction->amount, 2) }}
                                </div>
                            </td>


                            {{-- Remark --}}
                            <td class="px-4 py-3 align-top">
                                <div class="min-w-[320px] max-w-[420px]">

                                    @if ($transaction->remark)
                                        <div
                                            class="whitespace-normal break-words text-sm leading-5 text-gray-700"
                                            title="{{ $transaction->remark }}"
                                        >
                                            {{ $transaction->remark }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">
                                            —
                                        </span>
                                    @endif

                                    {{-- Created --}}
                                    <div class="mt-1.5 border-t border-gray-100 pt-1.5 text-xs text-gray-400">
                                        {{ $transaction->created_at?->format('Y-m-d H:i') ?? '—' }}

                                        @if (
                                            $transaction->updated_at &&
                                            $transaction->updated_at->ne($transaction->created_at)
                                        )
                                            <span class="ml-1">
                                                · Updated {{ $transaction->updated_at->format('Y-m-d H:i') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="4"
                                class="px-4 py-10 text-center text-sm text-gray-500"
                            >
                                No transactions requiring liquidation.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if ($transactions->hasPages())

            <div class="border-t border-gray-200 px-4 py-3">
                {{ $transactions->links() }}
            </div>

        @endif

    </div>

</div>