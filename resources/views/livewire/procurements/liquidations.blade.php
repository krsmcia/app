<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl">
            Liquidations
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Review purchases by date and liquidate the outstanding cash for that date.
        </p>
    </div>
    {{-- Filters / Date Summary --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            {{-- Purchase Date --}}
            <div>
                <label
                    for="purchaseDate"
                    class="block text-sm font-medium text-gray-700"
                >
                    Purchase Date
                </label>
                <input
                    id="purchaseDate"
                    type="date"
                    wire:model.live="purchaseDate"
                    max="{{ now()->toDateString() }}"
                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm
                        focus:border-indigo-500 focus:ring-indigo-500"
                >
                @error('purchaseDate')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            {{-- Search --}}
            <div>
                <label
                    for="search"
                    class="block text-sm font-medium text-gray-700"
                >
                    Search
                </label>
                <input
                    id="search"
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Request no., item, SKU..."
                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm
                        focus:border-indigo-500 focus:ring-indigo-500"
                >
            </div>
            {{-- Settlement --}}
            <div
                class="rounded-lg border p-4
                    {{ $totalLiquidationAmount > 0
                        ? 'border-red-200 bg-red-50'
                        : 'border-gray-200 bg-gray-50' }}"
            >
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Additional Funds
                        </div>
                        <div
                            class="mt-1 text-xl font-bold
                                {{ $totalLiquidationAmount > 0
                                    ? 'text-red-700'
                                    : 'text-gray-600' }}"
                        >
                            ₱{{ number_format($totalLiquidationAmount, 2) }}
                        </div>
                        <div class="mt-1 text-xs">
                            @if ($totalLiquidationAmount > 0)
                                <span class="font-medium text-red-700">
                                    Additional funds required
                                </span>
                            @else
                                <span class="font-medium text-gray-600">
                                    Fully balanced
                                </span>
                            @endif
                        </div>
                    </div>
                    @if (
                        $purchaseDate &&
                        //$purchaseDate < now()->toDateString() &&
                        $totalLiquidationAmount > 0
                    )
                        <button
                            type="button"
                            wire:click="openLiquidationModal"
                            wire:loading.attr="disabled"
                            wire:target="openLiquidationModal"
                            class="inline-flex items-center justify-center rounded-lg bg-violet-600
                                px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                                transition hover:bg-violet-700 disabled:opacity-50"
                        >
                            <svg
                                class="mr-2 h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h10v-2m4-4H9m0 0l3-3m-3 3l3 3"
                                />
                            </svg>
                            Receive Funds
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if ($purchaseDate)
        {{-- Selected Date --}}
        <div class="mt-6">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($purchaseDate)->format('F j, Y') }}
                    </h2>
                    @if ($purchaseDate >= now()->toDateString())
                        <p class="mt-1 text-sm text-amber-600">
                            Today's purchases are still active and cannot be liquidated yet.
                        </p>
                    @else
                        <p class="mt-1 text-sm text-gray-500">
                            All purchase items purchased on this date are included.
                        </p>
                    @endif
                </div>
                @if (session()->has('success'))
                    <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
            {{-- Purchase Items --}}
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
                                    Cash In
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">
                                    Purchased
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">
                                    Return
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">
                                    Balance
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($items as $item)
                                @php
                                    $transactions = $item->purchaseItemTransactions ?? collect();
                                    $cashInTransactions = $transactions
                                        ->filter(function ($pivot) {
                                            $transaction = $pivot->transaction;
                                            return $transaction
                                                && $transaction->to_user_id === auth()->id()
                                                && in_array(
                                                    $transaction->type,
                                                    ['released', 'transfer'],
                                                    true
                                                );
                                        })
                                        ->sortBy(function ($pivot) {
                                            return $pivot->transaction->created_at;
                                        });
                                    $purchasedTransactions = $transactions
                                        ->filter(function ($pivot) {
                                            $transaction = $pivot->transaction;
                                            return $transaction
                                                && $transaction->type === 'purchased'
                                                && $transaction->from_user_id === auth()->id()
                                                && is_null($transaction->to_user_id)
                                                && ! is_null($transaction->vendor_id);
                                        });
                                    $returnedTransactions = $transactions
                                        ->filter(function ($pivot) {
                                            $transaction = $pivot->transaction;

                                            return $transaction
                                                && $transaction->type === 'returned'
                                                && $transaction->from_user_id === auth()->id()
                                                && ! is_null($transaction->to_user_id);
                                        })
                                        ->sortBy(function ($pivot) {
                                            return $pivot->transaction->created_at;
                                        });
                                    $cashIn = (float) $cashInTransactions->sum('amount');
                                    $purchased = (float) $purchasedTransactions->sum('amount');
                                    $returned = (float) $returnedTransactions->sum('amount');

                                    $balance = round($cashIn - $purchased - $returned, 2);
                                    $releasedTransactions = $cashInTransactions
                                        ->filter(
                                            fn ($pivot) =>
                                                $pivot->transaction?->type === 'released'
                                        )
                                        ->sortBy(
                                            fn ($pivot) =>
                                                $pivot->transaction?->created_at
                                        );
                                    $initialReleased = $releasedTransactions->first();
                                @endphp
                                <tr wire:key="liquidation-{{ $item->id }}">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                        {{ $item->purchaseRequest?->request_no }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $item->item_name }}
                                        </div>
                                        @if ($item->sku)
                                            <div class="text-xs text-gray-500">
                                                {{ $item->sku }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="space-y-2">
                                            @forelse ($cashInTransactions as $cashInTransaction)
                                                @php
                                                    $transaction = $cashInTransaction->transaction;
                                                    $isInitial =
                                                        $transaction->type === 'released'
                                                        && $initialReleased
                                                        && $initialReleased->id === $cashInTransaction->id;
                                                @endphp
                                                <div class="flex items-center justify-end gap-2">
                                                    <span class="font-medium text-gray-900">
                                                        ₱{{ number_format($cashInTransaction->amount, 2) }}
                                                    </span>
                                                    @if ($transaction->type === 'transfer')
                                                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                                                            Transfer
                                                        </span>
                                                    @elseif ($isInitial)
                                                        <span class="rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">
                                                            Initial
                                                        </span>
                                                    @else
                                                        <span class="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">
                                                            Liquidation
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-right text-xs text-gray-400">
                                                    {{ $transaction->created_at->format('M d, Y') }}
                                                </div>
                                            @empty
                                                <div class="text-right text-sm text-gray-400">
                                                    ₱0.00
                                                </div>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">
                                        ₱{{ number_format($purchased, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @foreach ($returnedTransactions as $returnedTransaction)
                                            <div class="mt-1 text-xs text-blue-600">
                                                ₱{{ number_format($returnedTransaction->amount, 2) }}
                                            </div>

                                            <div class="text-xs text-gray-400">
                                                {{ $returnedTransaction->transaction->created_at->format('M d, Y') }}
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="text-sm font-semibold
                                            {{ $balance > 0
                                                ? 'text-green-700'
                                                : ($balance < 0
                                                    ? 'text-red-600'
                                                    : 'text-gray-500') }}"
                                        >
                                            @if ($balance > 0)
                                                +₱{{ number_format($balance, 2) }}
                                            @elseif ($balance < 0)
                                                -₱{{ number_format(abs($balance), 2) }}
                                            @else
                                                ₱0.00
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="5"
                                        class="px-4 py-10 text-center text-sm text-gray-500"
                                    >
                                        No purchases found for this date.
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
    @else
        {{-- No Date --}}
        <div class="mt-6 rounded-xl border border-dashed border-gray-300 bg-gray-50 p-10 text-center">
            <svg
                class="mx-auto h-10 w-10 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
            </svg>
            <p class="mt-3 text-sm font-medium text-gray-700">
                Select a purchase date
            </p>
            <p class="mt-1 text-sm text-gray-500">
                Choose a date to view purchases and the outstanding cash balance.
            </p>
        </div>
    @endif
    <div class=""
        x-data="{ selectedAccountingUser: null }"
    >
        <x-dialog-modal wire:model.live="showLiquidationModal">
            <x-slot name="title">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Receive Additional Funds
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Receive the outstanding cash from Accounting for this purchase date.
                    </p>
                </div>
            </x-slot>
            <x-slot name="content">
                <div class="mb-5 rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        Purchase Date
                    </div>
                    <div class="mt-1 text-xl font-bold text-gray-900">
                        {{ \Carbon\Carbon::parse($purchaseDate)->format('F j, Y') }}
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        Amount to receive:
                        <span class="font-bold text-red-600">
                            ₱{{ number_format(abs($totalLiquidationAmount), 2) }}
                        </span>
                    </div>
                </div>
                <div
                    class="mb-5"
                >
                    <h2>Released By</h2>
                    <div class="mt-2 space-y-3">
                        @foreach ($accountingUsers as $accountingUser)
                            <label
                                class="group relative flex cursor-pointer items-center gap-4 rounded-xl border p-4 transition"
                                :class="selectedAccountingUser == {{ $accountingUser->id }}
                                    ? 'border-indigo-500 bg-indigo-50 ring-1 ring-indigo-500'
                                    : 'border-gray-200 bg-white hover:border-indigo-300 hover:bg-gray-50'"
                                @click="selectedAccountingUser = {{ $accountingUser->id }}"
                            >
                                <input
                                    type="radio"
                                    name="accountingUserId"
                                    value="{{ $accountingUser->id }}"
                                    class="sr-only"
                                    :checked="selectedAccountingUser == {{ $accountingUser->id }}"
                                >
                                <div class="shrink-0">
                                    <img
                                        src="{{ $accountingUser->profile_photo_url }}"
                                        alt="{{ $accountingUser->name }}"
                                        class="h-8 w-8 rounded-full object-cover"
                                    >
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-gray-900">
                                        {{ $accountingUser->name }}
                                    </p>
                                    @if ($accountingUser->email)
                                        <p class="mt-0.5 truncate text-sm text-gray-500">
                                            {{ $accountingUser->email }}
                                        </p>
                                    @endif
                                </div>
                                <div
                                    class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full"
                                    :class="selectedAccountingUser == {{ $accountingUser->id }}
                                        ? 'bg-indigo-600'
                                        : 'border-2 border-gray-300'"
                                >
                                    <svg
                                        x-show="selectedAccountingUser == {{ $accountingUser->id }}"
                                        class="h-4 w-4 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('accountingUserId')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                    <div class="mt-3 text-xs text-gray-500">
                        Select the Accounting user who released the funds.
                    </div>
                </div>
                <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3">
                    <p class="text-xs leading-5 text-blue-800">
                        <strong>Important:</strong>
                        The selected date is processed as a whole.
                        Cash In includes released funds and transfers.
                        Additional funds are calculated from the total cash received
                        and total purchased amount for this date.
                    </p>
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button
                    wire:click="closeLiquidationModal"
                    wire:loading.attr="disabled"
                >
                    {{ __('Close') }}
                </x-secondary-button>
                <x-button
                    type="button"
                    wire:click="liquidateDate(selectedAccountingUser)"
                    wire:loading.attr="disabled"
                    wire:target="liquidateDate"
                    class="ml-2"
                >
                    <span wire:loading.remove wire:target="liquidateDate">
                        Receive Funds
                    </span>
                    <span wire:loading wire:target="liquidateDate">
                        Processing...
                    </span>
                </x-button>
            </x-slot>
        </x-dialog-modal>
    </div>
</div>