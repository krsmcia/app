<div
    x-data="{
        recipientUserId: null
    }"
    x-init="recipientUserId = null"
>
    <x-dialog-modal wire:model.live="cashHandoverModal">

        {{-- TITLE --}}
        <x-slot name="title">
            <div class="space-y-2.5">

                {{-- Total Cash --}}
                <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-2.5 sm:px-4 sm:py-3">
                    <div class="flex items-center justify-between gap-2">

                        <div class="min-w-0">
                            <div class="text-[10px] font-semibold uppercase tracking-wide text-emerald-600 sm:text-[11px]">
                                Cash to Release
                            </div>

                            <div class="mt-0.5 truncate text-2xl font-extrabold tracking-tight text-emerald-900 sm:text-3xl">
                                ₱{{ number_format($cashReleaseAmount) }}
                            </div>
                        </div>

                        <div class="shrink-0 rounded-full bg-emerald-100 px-2 py-1 text-[10px] font-semibold text-emerald-700 sm:px-2.5 sm:py-1 sm:text-[11px]">
                            {{ count($cashItems) }}
                            {{ count($cashItems) === 1 ? 'item' : 'items' }}
                        </div>

                    </div>
                </div>


                {{-- Cash Items --}}
                @if (count($cashItems) > 0)

                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">

                        {{-- Header --}}
                        <div class="flex items-center justify-between gap-2 border-b border-gray-100 bg-gray-50 px-3 py-2">

                            <span class="text-[11px] font-semibold uppercase tracking-wide text-gray-600 sm:text-xs">
                                Cash Items
                            </span>

                            <span class="shrink-0 text-[9px] text-gray-400 sm:text-[10px]">
                                0.45+ rounded
                            </span>

                        </div>
                        {{-- Items --}}
                        <div class="divide-y divide-gray-100">
                            @foreach ($cashItems as $workflowItem)
                                @php
                                    $purchaseItem = $workflowItem->purchaseItem;
                                    $unitPrice = (float) ($purchaseItem->unit_price ?? 0);
                                    $quantity = (int) ($purchaseItem->quantity ?? 1);
                                    $shippingFee = (float) ($purchaseItem->shipping_fee ?? 0);
                                    $discount = (float) ($purchaseItem->discount ?? 0);
                                    $originalTotal = $unitPrice * $quantity;
                                    $calculatedTotal =
                                        $originalTotal
                                        + $shippingFee
                                        - $discount;
                                    $whole = floor($calculatedTotal);
                                    $decimal = $calculatedTotal - $whole;

                                    $releaseAmount = $decimal >= 0.45
                                        ? (int) $whole + 1
                                        : (int) $whole;
                                @endphp
                                <div
                                    wire:key="cash-item-{{ $workflowItem->id }}"
                                    class="px-3 py-2.5 sm:px-4 sm:py-3"
                                >
                                    {{-- Item Header --}}
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <div class="truncate text-xs font-semibold text-gray-900 sm:text-sm">
                                                {{ $purchaseItem->item_name }}
                                            </div>
                                            <div class="mt-0.5 flex min-w-0 items-center gap-1 text-[10px] text-gray-500 sm:text-[11px]">
                                                <span class="shrink-0">
                                                    Qty {{ $quantity }}
                                                </span>
                                                @if ($purchaseItem->sku)
                                                    <span class="shrink-0">·</span>
                                                    <span class="truncate">
                                                        {{ $purchaseItem->sku }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        {{-- Release --}}
                                        <div class="shrink-0 text-right">

                                            <div class="text-[9px] font-medium uppercase tracking-wide text-gray-400">
                                                Release
                                            </div>
                                            <div class="flex items-center gap-2">
                                                {{-- Rounding Notice --}}
                                                @if ($calculatedTotal != $releaseAmount)
                                                    <div class="mt-1.5 flex items-center justify-end text-[9px] text-gray-400 sm:text-[10px]">
                                                        Rounded from
                                                        <span class="mx-1 font-medium">
                                                            ₱{{ number_format($calculatedTotal, 2) }}
                                                        </span>
                                                    </div>
                                                @endif
                                                <p class="text-base font-extrabold text-emerald-700 sm:text-lg">₱{{ number_format($releaseAmount) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Price Breakdown --}}
                                    <div class="overflow-hidden rounded-lg border border-gray-100 bg-gray-50">

                                        {{-- Original --}}
                                        <div class="flex items-center justify-between gap-2 border-b border-gray-100 px-2.5 py-1.5">

                                            <span class="text-[10px] text-gray-500 sm:text-[11px]">
                                                Original
                                            </span>

                                            <span class="text-[10px] font-medium text-gray-700 sm:text-[11px]">
                                                ₱{{ number_format($originalTotal, 2) }}
                                            </span>

                                        </div>


                                        {{-- Shipping --}}
                                        @if ($shippingFee != 0)

                                            <div class="flex items-center justify-between gap-2 border-b border-gray-100 px-2.5 py-1.5">

                                                <span class="text-[10px] text-gray-500 sm:text-[11px]">
                                                    Shipping
                                                </span>

                                                <span class="text-[10px] font-medium text-gray-700 sm:text-[11px]">
                                                    +₱{{ number_format($shippingFee, 2) }}
                                                </span>

                                            </div>

                                        @endif


                                        {{-- Discount --}}
                                        @if ($discount != 0)

                                            <div class="flex items-center justify-between gap-2 border-b border-gray-100 px-2.5 py-1.5">

                                                <span class="text-[10px] text-gray-500 sm:text-[11px]">
                                                    Discount
                                                </span>

                                                <span class="text-[10px] font-medium text-gray-700 sm:text-[11px]">
                                                    -₱{{ number_format($discount, 2) }}
                                                </span>

                                            </div>

                                        @endif


                                        {{-- Calculated --}}
                                        <div class="flex items-center justify-between gap-2 px-2.5 py-1.5">

                                            <span class="text-[10px] font-medium text-gray-500 sm:text-[11px]">
                                                Calculated
                                            </span>

                                            <span class="text-[10px] font-semibold text-gray-800 sm:text-[11px]">
                                                ₱{{ number_format($calculatedTotal, 2) }}
                                            </span>

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endif

            </div>
        </x-slot>


        {{-- CONTENT --}}
        <x-slot name="content">

            {{-- Recipient --}}
            <div>

                <div class="flex items-center justify-between gap-2">

                    <label class="text-xs font-semibold text-gray-800 sm:text-sm">
                        Cash Recipient
                    </label>

                    <span class="shrink-0 text-[9px] font-medium uppercase tracking-wide text-gray-400 sm:text-[10px]">
                        Procurement
                    </span>

                </div>


                {{-- Search --}}
                <div class="mt-1.5 sm:mt-2">

                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        x-on:input="recipientUserId = null"
                        placeholder="Search name or email..."
                        class="block w-full rounded-lg border-gray-300 px-2.5 py-2 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:px-3 sm:py-2.5 sm:text-sm"
                    >

                </div>

            </div>


            {{-- User List --}}
            <div class="mt-2.5 overflow-hidden rounded-xl border border-gray-200">

                @forelse ($users as $user)

                    <label
                        wire:key="procurement-user-{{ $user->id }}"
                        class="flex cursor-pointer items-center gap-2.5 border-b border-gray-100 px-2.5 py-2.5 last:border-b-0 hover:bg-gray-50 sm:gap-3 sm:px-3 sm:py-2.5"
                        :class="recipientUserId == {{ $user->id }} ? 'bg-indigo-50' : ''"
                    >

                        {{-- Radio --}}
                        <input
                            type="radio"
                            name="recipient_user"
                            value="{{ $user->id }}"
                            x-model="recipientUserId"
                            class="h-4 w-4 shrink-0 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >


                        {{-- User --}}
                        <div class="min-w-0 flex-1">

                            <div class="truncate text-xs font-medium text-gray-900 sm:text-sm">
                                {{ $user->name }}
                            </div>

                            <div class="truncate text-[10px] text-gray-500 sm:text-xs">
                                {{ $user->email }}
                            </div>

                        </div>

                    </label>

                @empty

                    <div class="p-4 text-center text-xs text-gray-500 sm:p-5 sm:text-sm">
                        No Procurement team members found.
                    </div>

                @endforelse

            </div>


            {{-- Validation Error --}}
            @error('recipientUserId')

                <p class="mt-1.5 text-[10px] text-red-600 sm:text-xs">
                    Please select the person who will receive the cash.
                </p>

            @enderror


            {{-- Pagination --}}
            @if ($users->hasPages())

                <div class="mt-2.5">
                    {{ $users->links() }}
                </div>

            @endif

        </x-slot>


        {{-- FOOTER --}}
        <x-slot name="footer">

            <div class="flex w-full items-center justify-between gap-2">

                <x-secondary-button
                    type="button"
                    wire:click="$set('cashHandoverModal', false)"
                    class="!px-2.5 !py-2 text-xs sm:!px-3 sm:text-sm"
                >
                    Cancel
                </x-secondary-button>


                <x-button
                    type="button"
                    class="ms-auto !px-3 !py-2 text-xs sm:!px-4 sm:text-sm"
                    x-bind:disabled="!recipientUserId"
                    x-on:click="
                        $wire.recipientUserId = recipientUserId;
                        $wire.releaseCash();
                    "
                    wire:loading.attr="disabled"
                >

                    <span wire:loading.remove wire:target="releaseCash">
                        Confirm Handover
                    </span>

                    <span wire:loading wire:target="releaseCash">
                        Processing...
                    </span>

                </x-button>

            </div>

        </x-slot>

    </x-dialog-modal>
</div>