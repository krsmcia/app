<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">
            Procurement Requests
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Purchase requests waiting for procurement processing.
        </p>
    </div>

    <div class="space-y-4">

        @forelse ($workflows as $workflow)

            @php
                $request = $workflow->purchaseRequest;
            @endphp

            <div class="rounded-lg border border-gray-200 bg-white shadow-sm">

                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">

                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="font-semibold text-gray-900">
                                {{ $request->request_no }}
                            </h2>

                            <span class="rounded-full bg-yellow-50 px-2.5 py-1 text-xs font-medium text-yellow-700">
                                Pending
                            </span>
                        </div>

                        <div class="mt-1 text-sm text-gray-500">
                            Requested by
                            <span class="font-medium text-gray-700">
                                {{ $request->user->name }}
                            </span>

                            @if ($request->department)
                                · {{ $request->department->name }}
                            @endif
                        </div>
                    </div>

                    <div class="text-right text-sm text-gray-500">
                        {{ $request->created_at->format('Y-m-d H:i') }}
                    </div>

                </div>

                {{-- Items --}}
                <div class="divide-y divide-gray-100">

                    @foreach ($workflow->purchaseWorkflowItems as $workflowItem)

                        @php
                            $item = $workflowItem->purchaseItem;
                        @endphp

                        <div class="flex items-center gap-4 px-5 py-4">

                            {{-- Image --}}
                            <div class="h-14 w-14 shrink-0 overflow-hidden rounded-md bg-gray-100">
                                @if ($item->item?->primaryImage)
                                    <img
                                        src="{{ $item->item->primaryImage->url }}"
                                        alt="{{ $item->item_name }}"
                                        class="h-full w-full object-cover"
                                    >
                                @endif
                            </div>

                            {{-- Item info --}}
                            <div class="min-w-0 flex-1">

                                <div class="font-medium text-gray-900">
                                    {{ $item->item_name }}
                                </div>

                                <div class="mt-1 text-sm text-gray-500">
                                    SKU: {{ $item->sku }}
                                </div>

                            </div>

                            {{-- Quantity --}}
                            <div class="text-right">
                                <div class="text-xs text-gray-500">
                                    Quantity
                                </div>

                                <div class="font-semibold text-gray-900">
                                    {{ $item->quantity }}
                                </div>
                            </div>

                        </div>

                    @endforeach

                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 px-5 py-3">

                    @if ($request->remark)
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">Remark:</span>
                            {{ $request->remark }}
                        </div>
                    @else
                        <div></div>
                    @endif

                    <div class="text-sm">
                        <span class="text-gray-500">
                            Total:
                        </span>

                        <span class="font-semibold text-gray-900">
                            {{ number_format($request->total_amount, 2) }}
                        </span>
                    </div>

                </div>

            </div>

        @empty

            <div class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-12 text-center">

                <div class="text-sm font-medium text-gray-900">
                    No pending procurement requests.
                </div>

                <div class="mt-1 text-sm text-gray-500">
                    There are currently no purchase requests waiting for procurement.
                </div>

            </div>

        @endforelse

    </div>

</div>