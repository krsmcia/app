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
            @foreach ($requests as $request)
                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
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
                                <button x-on:click="$dispatch('open-user-request-history', { userId: {{ $request->user->id }} })" class="font-medium text-gray-700" wire:loading.attr="disabled">
                                    {{ $request->user->name }}
                                </button>
                                @if ($request->department)
                                    <button x-on:click="$dispatch('open-department-request-history', { departmentId: {{ $request->department->id }} })" wire:loading.attr="disabled">· {{ $request->department->name }}</button>
                                @endif
                            </div>
                        </div>
                        <div class="text-right text-sm text-gray-500">
                            {{ $request->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>
                    {{-- Items --}}
                    <div class="divide-y divide-gray-100">
                        @foreach ($request->audit_workflow->purchaseWorkflowItems as $workflowItem)
                            <div
                                class="px-4 py-4 sm:px-5"
                                wire:key="workflow-item-{{ $workflowItem->id }}"
                            >
                                <div class="flex items-center gap-3 sm:gap-4">
                                    {{-- Image --}}
                                    <div class="h-12 w-12 shrink-0 overflow-hidden rounded-md bg-gray-100 sm:h-14 sm:w-14">
                                        <img
                                            src="{{ $workflowItem->purchaseItem->item->primaryImage
                                                ? Storage::url($workflowItem->purchaseItem->item->primaryImage->path)
                                                : asset('images/default-item.png') }}"
                                            alt="{{ $workflowItem->purchaseItem->item_name }}"
                                            class="h-full w-full object-cover"
                                        >
                                    </div>
                                    {{-- Item Info --}}
                                    <div class="min-w-0 flex-1">
                                        <button 
                                            x-on:click="$dispatch('open-item', { itemId: {{$workflowItem->purchaseItem->item_id}} })"
                                            class="truncate text-sm font-medium text-gray-900 sm:text-base"
                                            wire:loading.attr="disabled"
                                        >
                                            {{ $workflowItem->purchaseItem->item_name }}
                                        </button>
                                        <div class="mt-0.5 flex items-center gap-3 text-xs text-gray-500">
                                            <span>
                                                SKU:
                                                {{ $workflowItem->purchaseItem->item->sku }}
                                            </span>
                                            <span class="text-gray-300">|</span>
                                            <span>
                                                Qty:
                                                <span class="font-semibold text-gray-700">
                                                    {{ $workflowItem->purchaseItem->quantity }}
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    {{-- Vendor --}}
                                    <div class="hidden w-60 min-w-0 shrink-0 sm:block">
                                        <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                            Vendor
                                        </div>
                                        <button
                                            x-on:click="$dispatch('open-vendor', { vendorId: {{$workflowItem->purchaseItem->itemVendor->vendor_id}} })"
                                            class="block w-full truncate text-left text-sm font-medium text-gray-700"
                                            wire:loading.attr="disabled"
                                        >
                                            {{ $workflowItem->purchaseItem->vendor_name }}
                                        </button>

                                        <div class="mt-1 text-sm">
                                            <span class="text-gray-400">
                                                Unit Price:
                                            </span>
                                            <span class="font-semibold text-gray-900">
                                                {{ number_format($workflowItem->purchaseItem->unit_price, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                {{-- Mobile Vendor --}}
                                <div class="mt-2 pl-[60px] sm:hidden">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[11px] text-gray-400">
                                            Vendor:
                                        </span>
                                        <button 
                                            x-on:click="$dispatch('open-vendor', { vendorId: {{$workflowItem->purchaseItem->itemVendor->vendor_id}} })" 
                                            class="truncate text-xs font-medium text-gray-700"
                                            wire:loading.attr="disabled"
                                        >
                                            {{ $workflowItem->purchaseItem->vendor_name }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- Footer --}}
                    <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 px-4 py-3">
                        @if ($request->remark)
                            <div class="text-sm text-gray-600">
                                <span class="font-medium">
                                    Remark:
                                </span>
                                {{ $request->remark }}
                            </div>
                        @else
                            <div></div>
                        @endif
                        <div class="flex flex-none items-center gap-2 text-sm">
                            <span class="text-gray-500">
                                Total:
                            </span>
                            <span class="font-semibold text-gray-900">
                                {{ number_format($request->audit_workflow->procurement_total, 2) }}
                            </span>
                            <x-button
                                type="button"
                                wire:click="approve({{ $request->audit_workflow->id }})"
                                wire:confirm="Are you sure you want to approve all items?"
                                wire:loading.attr="disabled"
                                class=""
                            >
                                Approve All
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
    <livewire:audits.modals.user-request-history />
    <livewire:audits.modals.department-request-history />
    <livewire:audits.modals.item />
    <livewire:audits.modals.vendors />
</div>