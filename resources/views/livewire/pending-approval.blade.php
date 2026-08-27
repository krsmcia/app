<div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">
                Pending Approval
            </h1>
            @if ($approvalStep)
                <p class="mt-0.5 text-xs text-gray-500">
                    Approval level:
                    <span class="font-medium">
                        {{ ucfirst($approvalStep) }}
                    </span>
                </p>
            @endif
        </div>
        <div class="text-sm text-gray-500">
            {{ $groupedItems->count() }} requesters
        </div>
    </div>
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">

        @forelse ($groupedItems as $userId => $userItems)

            @php
                $firstItem = $userItems->first();

                $request = $firstItem
                    ->purchaseWorkflow
                    ->purchaseRequest;

                $requester = $request->user;
            @endphp

            <div class="flex h-[430px] flex-col overflow-hidden rounded-lg border border-gray-200 bg-white">

                {{-- Header --}}
                <div class="flex shrink-0 items-center justify-between border-b bg-gray-50 px-4 py-3">

                    <div class="min-w-0">

                        <div class="truncate text-sm font-semibold text-gray-900">
                            {{ $requester->name }}
                        </div>

                        <div class="mt-0.5 text-xs text-gray-500">
                            {{ $request->department?->name ?? '-' }}
                            · {{ $userItems->count() }} items
                        </div>

                    </div>

                </div>


                {{-- Item List --}}
                <div class="min-h-0 flex-1 divide-y divide-gray-100 overflow-y-auto">

                    @foreach ($userItems as $item)

                        @php
                            $purchaseRequest = $item
                                ->purchaseWorkflow
                                ->purchaseRequest;

                            $purchaseItem = $item->purchaseItem;
                        @endphp

                        <div class="flex items-center gap-3 px-4 py-2.5">

                            {{-- Image --}}
                            <div class="h-10 w-10 shrink-0 overflow-hidden rounded-md border bg-gray-50">

                                <img
                                    src="{{ $purchaseItem->item?->primaryImage
                                        ? Storage::url($purchaseItem->item->primaryImage->path)
                                        : asset('images/default-item.png') }}"
                                    alt="{{ $purchaseItem->item_name }}"
                                    class="h-full w-full object-cover"
                                >

                            </div>


                            {{-- Item --}}
                            <div class="min-w-0 flex-1">

                                <div class="truncate text-sm font-medium text-gray-900">
                                    {{ $purchaseItem->item_name }}
                                </div>

                                <div class="mt-0.5 flex items-center gap-1.5 text-xs text-gray-500">

                                    <span>
                                        {{ $purchaseRequest->request_no }}
                                    </span>

                                    <span class="text-gray-300">
                                        ·
                                    </span>

                                    <span>
                                        Qty {{ $purchaseItem->quantity }}
                                    </span>

                                </div>

                            </div>


                            {{-- Individual Action --}}
                            <div class="flex shrink-0 gap-1">

                                <button
                                    type="button"
                                    wire:click="reject({{ $item->id }})"
                                    wire:confirm="Reject this item?"
                                    class="rounded px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50"
                                >
                                    Reject
                                </button>

                                <button
                                    type="button"
                                    wire:click="approve({{ $item->id }})"
                                    wire:confirm="Approve this item?"
                                    class="rounded bg-green-600 px-2 py-1 text-xs font-medium text-white hover:bg-green-700"
                                >
                                    Approve
                                </button>

                            </div>

                        </div>

                    @endforeach

                </div>


                {{-- Bulk Actions --}}
                <div class="flex shrink-0 items-center justify-between border-t bg-gray-50 px-4 py-2.5">

                    <span class="text-xs text-gray-500">
                        {{ $userItems->count() }} pending items
                    </span>

                    <div class="flex gap-2">
                        {{--
                            <button
                                type="button"
                                wire:click="rejectUserItems({{ $userId }})"
                                wire:confirm="Reject all pending items from {{ $requester->name }}?"
                                class="rounded-md border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50"
                            >
                                Reject All
                            </button>
                        --}}
                        <button
                            type="button"
                            wire:click="approveUserItems({{ $userId }})"
                            wire:confirm="Approve all pending items from {{ $requester->name }}?"
                            class="rounded-md bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700"
                        >
                            Approve All
                        </button>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-span-full rounded-lg border border-dashed bg-white py-12 text-center text-sm text-gray-500">
                No pending approvals.
            </div>

        @endforelse

    </div>
</div>