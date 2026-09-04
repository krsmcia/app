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
        @if(count($requests)>0)
            @forelse ($requests as $request)
                @php
                    $workflow = $request->purchaseWorkflows
                        ->firstWhere('step', 'procurement');
                @endphp
                <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
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
                                $preferredVendor = $workflowItem->preferred_vendor;
                            @endphp
                            <div class="px-4 py-4 sm:px-5" wire:key="workflow-{{$workflowItem->id}}">
                                <div class="flex items-center gap-3 sm:gap-4">
                                    {{-- Image --}}
                                    <div class="h-12 w-12 shrink-0 overflow-hidden rounded-md bg-gray-100 sm:h-14 sm:w-14">
                                        <img
                                            src="{{ $item?->item?->primaryImage
                                                ? Storage::url($item->item->primaryImage->path)
                                                : asset('images/default-item.png') }}"
                                            alt="{{ $item?->item?->item_name }}"
                                            class="h-full w-full object-cover"
                                        >
                                    </div>
                                    {{-- Item Info --}}
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate text-sm font-medium text-gray-900 sm:text-base">
                                            {{ $item?->item?->item_name }}
                                        </div>
                                        {{-- SKU + Quantity --}}
                                        <div class="mt-0.5 flex items-center gap-3 text-xs text-gray-500">
                                            <span>
                                                SKU: {{ $item?->item?->sku }}
                                            </span>
                                            <span class="text-gray-300">|</span>
                                            <span>
                                                Qty:
                                                <span class="font-semibold text-gray-700">
                                                    {{ $item->quantity }}
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    {{-- Preferred Vendor --}}
                                    <div class="hidden w-40 shrink-0 sm:block">
                                        @if ($preferredVendor)
                                            <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                                Vendor
                                            </div>
                                            <div class="truncate text-sm font-medium text-gray-700">
                                                {{ $preferredVendor->vendor->name }}
                                            </div>
                                            <div class="mt-1 text-sm">
                                                @if ($preferredVendor->unit_price !== null && (float) $preferredVendor->unit_price > 0)
                                                    <div class="mt-1 text-sm">
                                                        <span class="text-gray-400">Unit Price:</span>
                                                        <span class="font-semibold text-gray-900">
                                                            {{ number_format($preferredVendor->unit_price, 2) }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="mt-1 text-sm font-medium text-red-600">
                                                        No price set
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-sm text-gray-400">
                                                No vendor
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                {{-- Mobile Vendor --}}
                                <div class="mt-2 pl-[60px] sm:hidden">
                                    @if ($preferredVendor)
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[11px] text-gray-400">
                                                Vendor:
                                            </span>
                                            <span class="truncate text-xs font-medium text-gray-700">
                                                {{ $preferredVendor->vendor->name }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-400">
                                            No vendor selected
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- Footer --}}
                    <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 px-4 py-3">
                        @if ($request->remark)
                            <div class="text-sm text-gray-600">
                                <span class="font-medium">Remark:</span>
                                {{ $request->remark }}
                            </div>
                        @else
                            <div></div>
                        @endif
                        <div class="text-sm flex-none flex items-center gap-2">
                            <span class="text-gray-500">
                                Total:
                            </span>
                            <span class="font-semibold text-gray-900">
                                {{ number_format($workflow->procurement_total, 2) }}
                            </span>
                            <x-button
                                type="button"
                                wire:click="approve({{ $workflow->id }})"
                                :disabled="!$workflow->can_approve"
                                class="{{ !$workflow->can_approve ? 'opacity-50 cursor-not-allowed' : '' }}"
                            >
                                Approved
                            </x-button>
                        </div>
                    </div>
                </div>
                
            @endforeach
            <div class="mt-6">
                {{ $requests->links() }}
            </div>
        @else
            <div class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-12 text-center">
                <div class="text-sm font-medium text-gray-900">
                    No pending procurement requests.
                </div>
                <div class="mt-1 text-sm text-gray-500">
                    There are currently no purchase requests waiting for procurement.
                </div>
            </div>
        @endif
    </div>
</div>