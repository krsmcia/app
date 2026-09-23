<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">
            Purchased
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Purchased items waiting to be received.
        </p>
    </div>


    <div class="space-y-4">

        @forelse ($purchase_actions as $purchase_action)

            @php
                $workflowItem = $purchase_action->purchaseWorkflowItem;
                $purchaseItem = $workflowItem?->purchaseItem;
                $item = $purchaseItem?->item;
                $request = $purchaseItem?->purchaseRequest;

                /*
                 * PurchaseAction 자체에 vendor 관계가 없으므로
                 * purchaseItem의 itemVendors에서 preferred vendor를 가져온다.
                 */
                $preferredVendor = $item?->itemVendors
                    ?->firstWhere('is_preferred', true);

                $unitPrice = $preferredVendor?->unit_price !== null
                    ? (float) $preferredVendor->unit_price
                    : 0;

                $quantity = (float) ($purchaseItem?->quantity ?? 0);

                $itemAmount = $quantity * $unitPrice;

                /*
                 * 이미 ordered 상태이므로
                 * 구매 당시 금액을 단순 표시한다.
                 */
                $shippingFee = (float) ($purchaseItem?->shipping_fee ?? 0);
                $discount = (float) ($purchaseItem?->discount ?? 0);

                $itemTotal = max(
                    0,
                    $itemAmount + $shippingFee - $discount
                );
            @endphp


            {{-- =========================================================
                Purchase Card
            ========================================================== --}}
            <div
                class="rounded-lg border border-gray-200 bg-white shadow-sm"
                wire:key="purchase-action-{{ $purchase_action->id }}"
            >

                {{-- =====================================================
                    Header
                ====================================================== --}}
                <div
                    class="flex items-center justify-between
                        border-b border-gray-100 px-5 py-4"
                >

                    <div>

                        <div class="flex items-center gap-3">

                            @if ($request)
                                <h2 class="font-semibold text-gray-900">
                                    {{ $request->request_no }}
                                </h2>
                            @else
                                <h2 class="font-semibold text-gray-900">
                                    Purchase #{{ $purchase_action->id }}
                                </h2>
                            @endif


                            <span
                                class="rounded-full bg-blue-50 px-2.5 py-1
                                    text-xs font-medium text-blue-700"
                            >
                                Ordered
                            </span>

                        </div>


                        @if ($request)

                            <div class="mt-1 text-sm text-gray-500">

                                Requested by

                                <span class="font-medium text-gray-700">
                                    {{ $request->user?->name }}
                                </span>

                                @if ($request->department)
                                    · {{ $request->department->name }}
                                @endif

                            </div>

                        @endif

                    </div>


                    <div class="text-right text-sm text-gray-500">

                        {{ $purchase_action->created_at?->format('Y-m-d H:i') }}

                    </div>

                </div>


                {{-- =====================================================
                    Item
                ====================================================== --}}
                <div class="px-4 py-4 sm:px-5">

                    {{-- Desktop --}}
                    <div
                        class="hidden xl:grid
                            xl:grid-cols-[56px_minmax(180px,1.8fr)_minmax(120px,1.2fr)_90px_110px_110px_120px]
                            xl:items-center
                            xl:gap-4"
                    >

                        {{-- Image --}}
                        <div
                            class="h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-gray-100"
                        >
                            <img
                                src="{{ $item?->primaryImage
                                    ? Storage::url($item->primaryImage->path)
                                    : asset('images/default-item.png') }}"
                                alt="{{ $item?->item_name }}"
                                class="h-full w-full object-cover"
                            >
                        </div>


                        {{-- Item Info --}}
                        <div class="min-w-0">

                            <div
                                class="truncate text-sm font-semibold text-gray-900"
                            >
                                {{ $item?->item_name ?? 'Unknown Item' }}
                            </div>

                            <div
                                class="mt-1 flex items-center gap-2
                                    text-xs text-gray-500"
                            >

                                @if ($item?->sku)

                                    <span>
                                        SKU:
                                        <span class="font-medium text-gray-600">
                                            {{ $item->sku }}
                                        </span>
                                    </span>

                                    <span class="text-gray-300">
                                        •
                                    </span>

                                @endif

                                <span>
                                    Qty:
                                    <span class="font-semibold text-gray-700">
                                        {{ $quantity }}
                                    </span>
                                </span>

                            </div>

                        </div>


                        {{-- Vendor --}}
                        <div class="min-w-0">

                            <div
                                class="text-[10px] font-medium uppercase
                                    tracking-wider text-gray-400"
                            >
                                Vendor
                            </div>

                            @if ($preferredVendor)

                                <div
                                    class="mt-0.5 truncate text-sm
                                        font-semibold text-gray-800"
                                    title="{{ $preferredVendor->vendor?->name }}"
                                >
                                    {{ $preferredVendor->vendor?->name }}
                                </div>

                            @else

                                <div class="mt-0.5 text-sm text-gray-400">
                                    No vendor
                                </div>

                            @endif

                        </div>


                        {{-- Unit Price --}}
                        <div>

                            <div class="text-[10px] text-gray-400">
                                Unit Price
                            </div>

                            <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                {{ number_format($unitPrice, 2) }}
                            </div>

                        </div>


                        {{-- Quantity --}}
                        <div>

                            <div class="text-[10px] text-gray-400">
                                Quantity
                            </div>

                            <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                {{ number_format($quantity, 0) }}
                            </div>

                        </div>


                        {{-- Amount --}}
                        <div>

                            <div class="text-[10px] text-gray-400">
                                Amount
                            </div>

                            <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                {{ number_format($itemAmount, 2) }}
                            </div>

                        </div>


                        {{-- Total --}}
                        <div>

                            <div class="text-[10px] text-gray-400">
                                Total
                            </div>

                            <div class="mt-0.5 text-sm font-bold text-gray-900">
                                {{ number_format($itemTotal, 2) }}
                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                        Mobile / Tablet
                    ================================================== --}}
                    <div class="xl:hidden space-y-4">

                        {{-- Item Header --}}
                        <div class="flex items-start gap-3">

                            {{-- Image --}}
                            <div
                                class="h-14 w-14 shrink-0 overflow-hidden
                                    rounded-lg bg-gray-100"
                            >
                                <img
                                    src="{{ $item?->primaryImage
                                        ? Storage::url($item->primaryImage->path)
                                        : asset('images/default-item.png') }}"
                                    alt="{{ $item?->item_name }}"
                                    class="h-full w-full object-cover"
                                >
                            </div>


                            {{-- Info --}}
                            <div class="min-w-0 flex-1">

                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $item?->item_name ?? 'Unknown Item' }}
                                </div>


                                <div class="mt-1 text-xs text-gray-500">

                                    @if ($item?->sku)

                                        SKU:
                                        <span class="font-medium">
                                            {{ $item->sku }}
                                        </span>

                                        <span class="mx-1 text-gray-300">
                                            •
                                        </span>

                                    @endif

                                    Qty:
                                    <span class="font-semibold text-gray-700">
                                        {{ $quantity }}
                                    </span>

                                </div>


                                {{-- Vendor --}}
                                @if ($preferredVendor)

                                    <div class="mt-2 flex items-center gap-1.5">

                                        <span class="text-[11px] text-gray-400">
                                            Vendor
                                        </span>

                                        <span
                                            class="truncate text-xs
                                                font-medium text-gray-700"
                                        >
                                            {{ $preferredVendor->vendor?->name }}
                                        </span>

                                    </div>

                                @endif

                            </div>

                        </div>


                        {{-- Price / Amount / Total --}}
                        <div
                            class="rounded-lg border border-gray-100
                                bg-gray-50/70 p-3"
                        >

                            <div class="grid grid-cols-2 gap-3">

                                {{-- Unit Price --}}
                                <div>

                                    <div class="text-[11px] text-gray-400">
                                        Unit Price
                                    </div>

                                    <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                        {{ number_format($unitPrice, 2) }}
                                    </div>

                                </div>


                                {{-- Quantity --}}
                                <div>

                                    <div class="text-[11px] text-gray-400">
                                        Quantity
                                    </div>

                                    <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                        {{ number_format($quantity, 0) }}
                                    </div>

                                </div>


                                {{-- Amount --}}
                                <div>

                                    <div class="text-[11px] text-gray-400">
                                        Amount
                                    </div>

                                    <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                        {{ number_format($itemAmount, 2) }}
                                    </div>

                                </div>


                                {{-- Total --}}
                                <div>

                                    <div class="text-[11px] text-gray-400">
                                        Total
                                    </div>

                                    <div class="mt-0.5 text-sm font-bold text-gray-900">
                                        {{ number_format($itemTotal, 2) }}
                                    </div>

                                </div>

                            </div>


                            {{-- Shipping / Discount --}}
                            @if ($shippingFee > 0 || $discount > 0)

                                <div
                                    class="mt-4 border-t border-gray-200
                                        pt-3 grid grid-cols-2 gap-3"
                                >

                                    @if ($shippingFee > 0)

                                        <div>

                                            <div class="text-[11px] text-gray-400">
                                                Shipping
                                            </div>

                                            <div class="mt-0.5 text-sm font-medium text-gray-700">
                                                {{ number_format($shippingFee, 2) }}
                                            </div>

                                        </div>

                                    @endif


                                    @if ($discount > 0)

                                        <div>

                                            <div class="text-[11px] text-gray-400">
                                                Discount
                                            </div>

                                            <div class="mt-0.5 text-sm font-medium text-gray-700">
                                                -{{ number_format($discount, 2) }}
                                            </div>

                                        </div>

                                    @endif

                                </div>

                            @endif

                        </div>

                    </div>

                </div>


                {{-- =====================================================
                    Footer
                ====================================================== --}}
                <div
                    class="border-t border-gray-100 bg-gray-50
                        px-4 py-4 sm:px-5"
                >

                    <div
                        class="flex flex-col gap-3
                            sm:flex-row sm:items-center
                            sm:justify-between"
                    >

                        {{-- Remark --}}
                        <div class="min-w-0">

                            @if ($request?->remark)

                                <div class="text-sm text-gray-600">

                                    <span class="font-medium">
                                        Remark:
                                    </span>

                                    {{ $request->remark }}

                                </div>

                            @else

                                <div class="text-xs text-gray-400">
                                    Ordered and waiting for receiving.
                                </div>

                            @endif

                        </div>


                        {{-- Total + Receive --}}
                        <div
                            class="flex items-center justify-between gap-4
                                sm:justify-end"
                        >

                            <div class="shrink-0 text-right">

                                <div class="text-xs text-gray-500">
                                    Total
                                </div>

                                <div class="text-base font-bold text-gray-900">
                                    {{ number_format($itemTotal, 2) }}
                                </div>

                            </div>


                            <x-button
                                type="button"
                                wire:click="receive({{ $purchase_action->id }})"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove wire:target="receive({{ $purchase_action->id }})">
                                    Receive
                                </span>

                                <span wire:loading wire:target="receive({{ $purchase_action->id }})">
                                    Receiving...
                                </span>
                            </x-button>

                        </div>

                    </div>

                </div>

            </div>

        @empty

            {{-- Empty State --}}
            <div
                class="rounded-lg border border-dashed border-gray-300
                    bg-white px-6 py-12 text-center"
            >

                <div class="text-sm font-medium text-gray-900">
                    No purchased items.
                </div>

                <div class="mt-1 text-sm text-gray-500">
                    There are currently no purchased items waiting to be received.
                </div>

            </div>

        @endforelse


        {{-- Pagination --}}
        @if ($purchase_actions->hasPages())

            <div class="mt-6">
                {{ $purchase_actions->links() }}
            </div>

        @endif

    </div>

</div>