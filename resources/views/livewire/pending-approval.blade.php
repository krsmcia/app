<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <div class="space-y-6">

        <div>
            <h1 class="text-xl font-semibold text-gray-900">
                Pending Approval
            </h1>

            @if ($approvalRole)
                <p class="mt-1 text-sm text-gray-500">
                    Approval level:
                    <span class="font-medium">
                        {{ ucfirst($approvalRole) }}
                    </span>
                </p>
            @endif
        </div>

        @forelse ($items as $item)

            @php
                $request = $item->purchaseWorkflow->purchaseRequest;
                $purchaseItem = $item->purchaseItem;
            @endphp
            <div class="rounded-lg border bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 shrink-0 overflow-hidden rounded-lg border bg-gray-50">
                            <img
                                src="{{ $purchaseItem->item?->primaryImage ? Storage::url($purchaseItem->item->primaryImage->path) : asset('images/default-item.png') }}"
                                alt="{{ $purchaseItem->item_name }}"
                                class="h-full w-full object-cover"
                            >
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">
                                {{ $purchaseItem->item_name }}
                            </div>

                            <div class="mt-1 text-sm text-gray-500">
                                SKU: {{ $purchaseItem->sku }}
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium">
                            {{ $request->request_no }}
                        </div>

                        <div class="text-xs text-gray-500">
                            Requested by:
                            {{ $request->user->name }}
                        </div>
                    </div>

                </div>

                <div class="mt-4 grid grid-cols-3 gap-4 text-sm">

                    <div>
                        <div class="text-gray-500">Quantity</div>
                        <div class="font-medium">
                            {{ $purchaseItem->quantity }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">Unit Price</div>
                        <div class="font-medium">
                            {{ $purchaseItem->unit_price ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">Amount</div>
                        <div class="font-medium">
                            {{ $purchaseItem->amount ?? '-' }}
                        </div>
                    </div>

                </div>

                <div class="mt-4 flex justify-end gap-2">

                    <button
                        type="button"
                        wire:click="reject({{ $item->id }})"
                        wire:confirm="Reject this item?"
                        class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                    >
                        Reject
                    </button>

                    <button
                        type="button"
                        wire:click="approve({{ $item->id }})"
                        wire:confirm="Approve this item?"
                        class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                    >
                        Approve
                    </button>

                </div>

            </div>

        @empty

            <div class="rounded-lg border bg-white p-8 text-center text-sm text-gray-500">
                No pending approvals.
            </div>

        @endforelse

    </div>
</div>
