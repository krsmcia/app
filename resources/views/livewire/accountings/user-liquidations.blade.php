<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl">
            User Liquidations
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Review funds released to {{ $user->name }} and the purchases made for the selected date.
        </p>
    </div>

    {{-- Filters / Summary --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

            {{-- User --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    User
                </label>

                <div
                    class="mt-1 flex min-h-[42px] items-center gap-3 rounded-lg
                        border border-gray-300 bg-gray-50 px-3 py-2"
                >
                    <img
                        src="{{ $user->profile_photo_url }}"
                        alt="{{ $user->name }}"
                        class="h-7 w-7 rounded-full object-cover"
                    >

                    <div class="min-w-0">
                        <div class="truncate text-sm font-medium text-gray-900">
                            {{ $user->name }}
                        </div>

                        @if ($user->email)
                            <div class="truncate text-xs text-gray-500">
                                {{ $user->email }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

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
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Return Balance --}}
            <div
                class="rounded-lg border p-4
                    {{ $totalReturnAmount > 0
                        ? 'border-blue-200 bg-blue-50'
                        : 'border-gray-200 bg-gray-50' }}"
            >
                <div class="flex items-center justify-between gap-3">

                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Return Balance
                        </div>

                        <div
                            class="mt-1 text-xl font-bold
                                {{ $totalReturnAmount > 0
                                    ? 'text-blue-700'
                                    : 'text-gray-600' }}"
                        >
                            ₱{{ number_format($totalReturnAmount, 2) }}
                        </div>

                        <div class="mt-1 text-xs">
                            @if ($totalReturnAmount > 0)
                                <span class="font-medium text-blue-700">
                                    Amount to receive from user
                                </span>
                            @else
                                <span class="font-medium text-gray-600">
                                    Fully liquidated
                                </span>
                            @endif
                        </div>
                    </div>

                    @if ($totalReturnAmount > 0)
                        <button
                            type="button"
                            wire:click="openLiquidationModal"
                            wire:loading.attr="disabled"
                            wire:target="openLiquidationModal"
                            class="inline-flex items-center justify-center rounded-lg
                                bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white
                                shadow-sm transition hover:bg-blue-700 disabled:opacity-50"
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
                                    d="M12 3v12m0 0l-4-4m4 4l4-4M5 21h14"
                                />
                            </svg>

                            Receive Return
                        </button>
                    @endif

                </div>
            </div>

        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-4">

        {{-- Released --}}
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                Released
            </div>

            <div class="mt-1 text-xl font-bold text-gray-900">
                ₱{{ number_format($totalReleased, 2) }}
            </div>

            <div class="mt-1 text-xs text-gray-500">
                Funds released to user
            </div>
        </div>

        {{-- Purchased --}}
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                Purchased
            </div>

            <div class="mt-1 text-xl font-bold text-gray-900">
                ₱{{ number_format($totalPurchased, 2) }}
            </div>

            <div class="mt-1 text-xs text-gray-500">
                Actual purchase amount
            </div>
        </div>

        {{-- Returned --}}
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
            <div class="text-xs font-medium uppercase tracking-wide text-blue-600">
                Returned
            </div>

            <div class="mt-1 text-xl font-bold text-blue-700">
                ₱{{ number_format($totalReturned, 2) }}
            </div>

            <div class="mt-1 text-xs text-blue-600">
                Already received by Accounting
            </div>
        </div>

        {{-- Balance --}}
        <div
            class="rounded-lg border p-4
                {{ $totalReturnAmount > 0
                    ? 'border-blue-200 bg-blue-50'
                    : 'border-green-200 bg-green-50' }}"
        >
            <div
                class="text-xs font-medium uppercase tracking-wide
                    {{ $totalReturnAmount > 0
                        ? 'text-blue-600'
                        : 'text-green-600' }}"
            >
                Balance
            </div>

            <div
                class="mt-1 text-xl font-bold
                    {{ $totalReturnAmount > 0
                        ? 'text-blue-700'
                        : 'text-green-700' }}"
            >
                @if ($totalReturnAmount > 0)
                    +₱{{ number_format($totalReturnAmount, 2) }}
                @else
                    ₱0.00
                @endif
            </div>

            <div
                class="mt-1 text-xs
                    {{ $totalReturnAmount > 0
                        ? 'text-blue-600'
                        : 'text-green-600' }}"
            >
                @if ($totalReturnAmount > 0)
                    User must return
                @else
                    Fully liquidated
                @endif
            </div>
        </div>

    </div>

    {{-- Selected Date --}}
    @if ($purchaseDate)

        <div class="mt-6">

            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($purchaseDate)->format('F j, Y') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        All purchase transactions for
                        <span class="font-medium text-gray-700">
                            {{ $user->name }}
                        </span>
                        on this date are included.
                    </p>
                </div>

                <div class="text-sm text-gray-500">
                    {{ $items->total() }}
                    item{{ $items->total() === 1 ? '' : 's' }}
                </div>

            </div>

            {{-- Search --}}
            <div class="mb-4">
                <div class="w-full sm:max-w-sm">
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
                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm
                            shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>
            </div>

            {{-- Purchase Items --}}
            <div class="overflow-hidden rounded-lg bg-white shadow">

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>

                                <th
                                    class="px-4 py-3 text-left text-xs font-medium
                                        uppercase tracking-wide text-gray-500"
                                >
                                    Request
                                </th>

                                <th
                                    class="px-4 py-3 text-left text-xs font-medium
                                        uppercase tracking-wide text-gray-500"
                                >
                                    Item
                                </th>

                                <th
                                    class="px-4 py-3 text-right text-xs font-medium
                                        uppercase tracking-wide text-gray-500"
                                >
                                    Released
                                </th>

                                <th
                                    class="px-4 py-3 text-right text-xs font-medium
                                        uppercase tracking-wide text-gray-500"
                                >
                                    Purchased
                                </th>

                                <th
                                    class="px-4 py-3 text-right text-xs font-medium
                                        uppercase tracking-wide text-gray-500"
                                >
                                    Returned
                                </th>

                                <th
                                    class="px-4 py-3 text-right text-xs font-medium
                                        uppercase tracking-wide text-gray-500"
                                >
                                    Balance
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">

                            @forelse ($items as $item)

                                @php
                                    $transactions = $item->purchaseItemTransactions ?? collect();

                                    /*
                                     * -------------------------------------------------
                                     * Released
                                     * -------------------------------------------------
                                     */
                                    $releasedTransactions = $transactions
                                        ->filter(function ($pivot) use ($user) {
                                            $transaction = $pivot->transaction;

                                            return $transaction
                                                && $transaction->type === 'released'
                                                && (int) $transaction->to_user_id === (int) $user->id
                                                && is_null($transaction->vendor_id);
                                        })
                                        ->sortBy(function ($pivot) {
                                            return $pivot->transaction->created_at;
                                        });

                                    /*
                                     * -------------------------------------------------
                                     * Purchased
                                     * -------------------------------------------------
                                     */
                                    $purchasedTransactions = $transactions
                                        ->filter(function ($pivot) use ($user) {
                                            $transaction = $pivot->transaction;

                                            return $transaction
                                                && $transaction->type === 'purchased'
                                                && (int) $transaction->from_user_id === (int) $user->id
                                                && is_null($transaction->to_user_id)
                                                && ! is_null($transaction->vendor_id);
                                        })
                                        ->sortBy(function ($pivot) {
                                            return $pivot->transaction->created_at;
                                        });

                                    /*
                                     * -------------------------------------------------
                                     * Returned
                                     * -------------------------------------------------
                                     */
                                    $returnedTransactions = $transactions
                                        ->filter(function ($pivot) use ($user) {
                                            $transaction = $pivot->transaction;

                                            return $transaction
                                                && $transaction->type === 'returned'
                                                && (int) $transaction->from_user_id === (int) $user->id
                                                && ! is_null($transaction->to_user_id);
                                        })
                                        ->sortBy(function ($pivot) {
                                            return $pivot->transaction->created_at;
                                        });

                                    $released = (float) $releasedTransactions->sum('amount');
                                    $purchased = (float) $purchasedTransactions->sum('amount');
                                    $returned = (float) $returnedTransactions->sum('amount');

                                    $balance = round(
                                        $released - $purchased - $returned,
                                        2
                                    );
                                @endphp

                                <tr wire:key="user-liquidation-{{ $item->id }}">

                                    {{-- Request --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                        {{ $item->purchaseRequest?->request_no ?? '-' }}
                                    </td>

                                    {{-- Item --}}
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

                                    {{-- Released --}}
                                    <td class="px-4 py-3 text-right">

                                        <div class="space-y-2">

                                            @forelse ($releasedTransactions as $releasedTransaction)

                                                @php
                                                    $transaction = $releasedTransaction->transaction;

                                                    $isInitial = $releasedTransactions->first()?->id === $releasedTransaction->id;
                                                @endphp

                                                <div>
                                                    <div class="flex items-center justify-end gap-2">

                                                        <span class="font-medium text-gray-900">
                                                            ₱{{ number_format($releasedTransaction->amount, 2) }}
                                                        </span>

                                                        @if ($isInitial)
                                                            <span
                                                                class="rounded-full bg-blue-50 px-2 py-0.5
                                                                    text-xs font-medium text-blue-700"
                                                            >
                                                                Initial
                                                            </span>
                                                        @else
                                                            <span
                                                                class="rounded-full bg-amber-50 px-2 py-0.5
                                                                    text-xs font-medium text-amber-700"
                                                            >
                                                                Liquidation
                                                            </span>
                                                        @endif

                                                    </div>

                                                    <div class="text-right text-xs text-gray-400">
                                                        {{ $transaction->created_at->format('M d, Y') }}
                                                    </div>
                                                </div>

                                            @empty

                                                <span class="text-sm text-gray-400">
                                                    ₱0.00
                                                </span>

                                            @endforelse

                                        </div>

                                    </td>

                                    {{-- Purchased --}}
                                    <td class="px-4 py-3 text-right">

                                        @forelse ($purchasedTransactions as $purchasedTransaction)

                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    ₱{{ number_format($purchasedTransaction->amount, 2) }}
                                                </div>

                                                <div class="text-xs text-gray-400">
                                                    {{ $purchasedTransaction->transaction->created_at->format('M d, Y') }}
                                                </div>
                                            </div>

                                        @empty

                                            <span class="text-sm text-gray-400">
                                                ₱0.00
                                            </span>

                                        @endforelse

                                    </td>

                                    {{-- Returned --}}
                                    <td class="px-4 py-3 text-right">

                                        @forelse ($returnedTransactions as $returnedTransaction)

                                            <div class="mt-1">

                                                <div class="text-sm font-medium text-blue-700">
                                                    ₱{{ number_format($returnedTransaction->amount, 2) }}
                                                </div>

                                                <div class="text-xs text-gray-400">
                                                    {{ $returnedTransaction->transaction->created_at->format('M d, Y') }}
                                                </div>

                                            </div>

                                        @empty

                                            <span class="text-sm text-gray-400">
                                                ₱0.00
                                            </span>

                                        @endforelse

                                    </td>

                                    {{-- Balance --}}
                                    <td class="px-4 py-3 text-right">

                                        <div
                                            class="text-sm font-semibold
                                                {{ $balance > 0
                                                    ? 'text-blue-700'
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
                                        colspan="6"
                                        class="px-4 py-12 text-center"
                                    >

                                        <div class="text-sm font-medium text-gray-700">
                                            No purchases found.
                                        </div>

                                        <p class="mt-1 text-sm text-gray-500">
                                            No purchase transactions were found for
                                            {{ $user->name }}
                                            on this date.
                                        </p>

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
        <div
            class="mt-6 rounded-xl border border-dashed border-gray-300
                bg-gray-50 p-10 text-center"
        >
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
                Choose a date to view this user's purchases and liquidation balance.
            </p>
        </div>

    @endif

    {{-- Receive Return Modal --}}
    <x-dialog-modal wire:model.live="showLiquidationModal">

        <x-slot name="title">

            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Receive Returned Funds
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Receive the outstanding cash returned by this user.
                </p>
            </div>

        </x-slot>

        <x-slot name="content">

            {{-- Summary --}}
            <div class="mb-5 rounded-xl border border-gray-200 bg-gray-50 p-4">

                <div class="grid grid-cols-2 gap-4">

                    {{-- User --}}
                    <div>

                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            User
                        </div>

                        <div class="mt-2 flex items-center gap-2">

                            <img
                                src="{{ $user->profile_photo_url }}"
                                alt="{{ $user->name }}"
                                class="h-8 w-8 rounded-full object-cover"
                            >

                            <div>
                                <div class="font-semibold text-gray-900">
                                    {{ $user->name }}
                                </div>

                                @if ($user->email)
                                    <div class="text-xs text-gray-500">
                                        {{ $user->email }}
                                    </div>
                                @endif
                            </div>

                        </div>

                    </div>

                    {{-- Date --}}
                    <div>

                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Purchase Date
                        </div>

                        <div class="mt-2 text-lg font-bold text-gray-900">
                            {{ \Carbon\Carbon::parse($purchaseDate)->format('F j, Y') }}
                        </div>

                    </div>

                </div>

                {{-- Amount Summary --}}
                <div class="mt-5 grid grid-cols-4 gap-3">

                    <div>
                        <div class="text-xs text-gray-500">
                            Released
                        </div>

                        <div class="mt-1 font-semibold text-gray-900">
                            ₱{{ number_format($totalReleased, 2) }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500">
                            Purchased
                        </div>

                        <div class="mt-1 font-semibold text-gray-900">
                            ₱{{ number_format($totalPurchased, 2) }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500">
                            Returned
                        </div>

                        <div class="mt-1 font-semibold text-blue-700">
                            ₱{{ number_format($totalReturned, 2) }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-blue-600">
                            Receive
                        </div>

                        <div class="mt-1 font-bold text-blue-700">
                            ₱{{ number_format($totalReturnAmount, 2) }}
                        </div>
                    </div>

                </div>

            </div>

            {{-- Explanation --}}
            <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3">

                <p class="text-sm leading-6 text-blue-900">

                    <span class="font-semibold">
                        {{ $user->name }}
                    </span>

                    received more funds than were actually purchased.

                    Accounting should receive the remaining

                    <span class="font-bold">
                        ₱{{ number_format($totalReturnAmount, 2) }}
                    </span>

                    in cash.

                </p>

            </div>

            {{-- Calculation --}}
            <div class="mt-4 rounded-lg border border-gray-200 bg-white">

                <div class="border-b border-gray-200 px-4 py-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Liquidation Calculation
                    </div>
                </div>

                <div class="divide-y divide-gray-100">

                    <div class="flex items-center justify-between px-4 py-3 text-sm">
                        <span class="text-gray-600">
                            Released
                        </span>

                        <span class="font-medium text-gray-900">
                            ₱{{ number_format($totalReleased, 2) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between px-4 py-3 text-sm">
                        <span class="text-gray-600">
                            Less Purchased
                        </span>

                        <span class="font-medium text-gray-900">
                            - ₱{{ number_format($totalPurchased, 2) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between px-4 py-3 text-sm">
                        <span class="text-gray-600">
                            Less Already Returned
                        </span>

                        <span class="font-medium text-blue-700">
                            - ₱{{ number_format($totalReturned, 2) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between bg-blue-50 px-4 py-4">

                        <span class="font-semibold text-blue-900">
                            Amount to Receive
                        </span>

                        <span class="text-lg font-bold text-blue-700">
                            ₱{{ number_format($totalReturnAmount, 2) }}
                        </span>

                    </div>

                </div>

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
                wire:click="confirmLiquidation"
                wire:loading.attr="disabled"
                wire:target="confirmLiquidation"
                class="ml-2"
            >

                <span
                    wire:loading.remove
                    wire:target="confirmLiquidation"
                >
                    Confirm Received
                </span>

                <span
                    wire:loading
                    wire:target="confirmLiquidation"
                >
                    Processing...
                </span>

            </x-button>

        </x-slot>

    </x-dialog-modal>

</div>