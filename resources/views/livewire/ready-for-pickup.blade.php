<div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
    @foreach ($groupedPurchaseActions as $actedBy => $actions)
        @php
            $holder = $actions->first()->actedBy;
        @endphp

        <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

            {{-- Employee header --}}
            <div class="flex items-center gap-3 border-b border-gray-200 bg-gray-50 px-4 py-3">

                <img
                    src="{{ $holder->profile_photo_url }}"
                    alt="{{ $holder->name }}"
                    class="h-12 w-12 rounded-full object-cover"
                >

                <div>
                    <div class="font-semibold text-gray-900">
                        {{ $holder->name }}
                    </div>

                    <div class="text-sm text-gray-500">
                        {{ $holder->department?->name }}
                    </div>
                </div>

                <div class="ml-auto text-sm text-gray-500">
                    {{ $actions->count() }} item{{ $actions->count() > 1 ? 's' : '' }}
                </div>

            </div>

            {{-- Items --}}
            <div class="divide-y divide-gray-100">

                @foreach ($actions as $purchaseAction)

                    @php
                        $purchaseItem = $purchaseAction
                            ->purchaseWorkflowItem
                            ->purchaseItem;

                        $item = $purchaseItem->item;

                        $imageUrl = $item->primaryImage
                            ? asset('storage/' . $item->primaryImage->path)
                            : asset('images/default-item.png');
                    @endphp

                    <div class="flex items-center gap-4 px-4 py-3">

                        <img
                            src="{{ $imageUrl }}"
                            alt="{{ $item->name }}"
                            class="h-16 w-16 shrink-0 rounded-lg border border-gray-200 bg-gray-50 object-cover"
                        >

                        <div class="min-w-0 flex-1">
                            <div class="font-medium text-gray-900">
                                {{ $item->name }}
                            </div>

                            @if ($purchaseItem->vendor_name)
                                <div class="mt-1 text-sm text-gray-500">
                                    {{ $purchaseItem->vendor_name }}
                                </div>
                            @endif
                        </div>

                        <div class="shrink-0 text-right">
                            <div class="text-xs text-gray-500">
                                Quantity
                            </div>

                            <div class="font-semibold text-gray-900">
                                × {{ $purchaseAction->quantity }}
                            </div>
                        </div>

                    </div>

                @endforeach

            </div>
        </div>

    @endforeach
    {{ $purchase_actions->links() }}
</div>
