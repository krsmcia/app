<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    {{-- =========================================================
        Header
    ========================================================== --}}
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl">
            Liquidations
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Review purchases by date and liquidate the selected date as a whole.
        </p>
    </div>


    {{-- =========================================================
        Date Filter
    ========================================================== --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

            <div class="w-full sm:max-w-xs">
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
                    max="{{ now('Asia/Manila')->toDateString() }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                        focus:border-indigo-500 focus:ring-indigo-500"
                >

                @error('purchaseDate')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>
    </div>


    {{-- =========================================================
        Selected Date
    ========================================================== --}}
    @if ($purchaseDate)

        <div class="mt-6">

            {{-- Date Header --}}
            <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($purchaseDate)->format('F j, Y') }}
                    </h2>

                    @if ($purchaseDate >= now('Asia/Manila')->toDateString())
                        <p class="mt-1 text-sm text-amber-600">
                            Today's purchases are still active.
                            You can review the current balance, but liquidation is not available yet.
                        </p>
                    @else
                        <p class="mt-1 text-sm text-gray-500">
                            All purchases made on this date can be liquidated together.
                        </p>
                    @endif
                </div>


                {{-- =================================================
                    Date Liquidation Button
                ================================================== --}}
                @if (
                    $purchaseDate < now('Asia/Manila')->toDateString()
                    && $items->total() > 0
                )

                    <button
                        type="button"
                        wire:click="$set('showLiquidationModal', true)"
                        class="inline-flex items-center justify-center rounded-lg
                            bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white
                            shadow-sm transition hover:bg-violet-700"
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
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2m4-4H9m0 0l3-3m-3 3l3 3"
                            />
                        </svg>

                        Liquidate {{ \Carbon\Carbon::parse($purchaseDate)->format('M j') }}
                    </button>

                @endif

            </div>


            {{-- =====================================================
                Success Message
            ====================================================== --}}
            @if (session()->has('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif


            {{-- =====================================================
                Table
            ====================================================== --}}
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
                                    Received
                                </th>

                                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">
                                    Purchased
                                </th>

                                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">
                                    Transfer Out
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
                                    $released = (float) ($item->received_released_amount ?? 0);
                                    $transferIn = (float) ($item->received_transfer_amount ?? 0);
                                    $purchased = (float) ($item->purchased_amount ?? 0);
                                    $transferOut = (float) ($item->transferred_out_amount ?? 0);
                                    $returned = (float) ($item->returned_amount ?? 0);

                                    $received = $released + $transferIn;

                                    $remaining =
                                        $received
                                        - $purchased
                                        - $transferOut
                                        - $returned;
                                @endphp


                                <tr wire:key="liquidation-{{ $item->id }}">

                                    {{-- Request --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                        {{ $item->purchaseRequest?->request_no }}
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


                                    {{-- Received --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">

                                        ₱{{ number_format($received, 2) }}

                                        @if ($transferIn > 0)
                                            <div class="text-xs text-gray-400">
                                                incl. transfer
                                            </div>
                                        @endif

                                    </td>


                                    {{-- Purchased --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">
                                        ₱{{ number_format($purchased, 2) }}
                                    </td>


                                    {{-- Transfer Out --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">
                                        ₱{{ number_format($transferOut, 2) }}
                                    </td>


                                    {{-- Returned --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">
                                        ₱{{ number_format($returned, 2) }}
                                    </td>


                                    {{-- Remaining --}}
                                    <td
                                        class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold
                                        {{ $remaining > 0
                                            ? 'text-green-700'
                                            : ($remaining < 0
                                                ? 'text-red-600'
                                                : 'text-gray-500') }}"
                                    >
                                        @if ($remaining > 0)
                                            +₱{{ number_format($remaining, 2) }}
                                        @elseif ($remaining < 0)
                                            -₱{{ number_format(abs($remaining), 2) }}
                                        @else
                                            ₱0.00
                                        @endif
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="7"
                                        class="px-4 py-10 text-center text-sm text-gray-500"
                                    >
                                        No purchases found for this date.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- Pagination --}}
                @if ($items->hasPages())
                    <div class="border-t border-gray-200 px-4 py-3">
                        {{ $items->links() }}
                    </div>
                @endif

            </div>

        </div>

    @else

        {{-- =========================================================
            No Date Selected
        ========================================================== --}}
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
                Choose a date to view purchases and liquidation balances.
            </p>

        </div>

    @endif


    {{-- =============================================================
        Liquidation Modal
    ============================================================= --}}
    <x-dialog-modal wire:model.live="showLiquidationModal">

        <x-slot name="title">

            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Liquidate Purchase Date
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Complete the liquidation for all purchases on this date.
                </p>
            </div>

        </x-slot>


        <x-slot name="content">

            {{-- =====================================================
                Date
            ====================================================== --}}
            <div class="mb-5 rounded-xl border border-gray-200 bg-gray-50 p-4">

                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                    Purchase Date
                </div>

                <div class="mt-1 text-xl font-bold text-gray-900">
                    {{ \Carbon\Carbon::parse($purchaseDate)->format('F j, Y') }}
                </div>

            </div>


            {{-- =====================================================
                Explanation
            ====================================================== --}}
            <div class="mb-5 rounded-xl border border-indigo-200 bg-indigo-50 p-4">

                <div class="text-sm font-medium text-indigo-900">
                    Date-based liquidation
                </div>

                <p class="mt-1 text-sm leading-5 text-indigo-700">
                    This action will liquidate all eligible purchase items
                    for the selected date, not only the items shown on the
                    current page.
                </p>

            </div>


            {{-- =====================================================
                Accounting Recipient
            ====================================================== --}}
            <div class="mb-5">

                <x-label
                    for="accountingUserId"
                    value="Return To"
                />

                <div class="mt-2 space-y-3">

                    @foreach ($accountingUsers as $accountingUser)

                        <label
                            class="group relative flex cursor-pointer items-center gap-4 rounded-xl border p-4 transition
                                {{ $accountingUserId == $accountingUser->id
                                    ? 'border-indigo-500 bg-indigo-50 ring-1 ring-indigo-500'
                                    : 'border-gray-200 bg-white hover:border-indigo-300 hover:bg-gray-50' }}"
                        >

                            <input
                                type="radio"
                                wire:model="accountingUserId"
                                value="{{ $accountingUser->id }}"
                                class="sr-only"
                            >


                            {{-- Profile Photo --}}
                            <div class="shrink-0">

                                @if ($accountingUser->profile_photo_url)

                                    <img
                                        src="{{ $accountingUser->profile_photo_url }}"
                                        alt="{{ $accountingUser->name }}"
                                        class="h-12 w-12 rounded-full object-cover"
                                    >

                                @else

                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-full
                                            bg-gray-100 text-sm font-semibold text-gray-600"
                                    >
                                        {{ strtoupper(substr($accountingUser->name, 0, 1)) }}
                                    </div>

                                @endif

                            </div>


                            {{-- User Information --}}
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


                            {{-- Selected Icon --}}
                            <div class="shrink-0">

                                @if ($accountingUserId == $accountingUser->id)

                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600">

                                        <svg
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

                                @else

                                    <div class="h-6 w-6 rounded-full border-2 border-gray-300"></div>

                                @endif

                            </div>

                        </label>

                    @endforeach

                </div>


                @error('accountingUserId')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- =====================================================
                Warning
            ====================================================== --}}
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3">

                <p class="text-xs leading-5 text-amber-800">

                    <strong>Important:</strong>
                    The liquidation will process the entire selected
                    purchase date. Items on other pagination pages will
                    also be included.

                    The final remaining amount will be recalculated from
                    the latest transaction records before processing.

                </p>

            </div>

        </x-slot>


        <x-slot name="footer">

            <x-secondary-button
                wire:click="$set('showLiquidationModal', false)"
                wire:loading.attr="disabled"
            >
                {{ __('Close') }}
            </x-secondary-button>


            <x-button
                type="button"
                wire:click="liquidateAll"
                wire:loading.attr="disabled"
                wire:target="liquidateAll"
                class="ml-2"
            >

                <span wire:loading.remove wire:target="liquidateAll">
                    Confirm Liquidation
                </span>

                <span wire:loading wire:target="liquidateAll">
                    Processing...
                </span>

            </x-button>

        </x-slot>

    </x-dialog-modal>
</div>
