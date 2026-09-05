<x-dialog-modal wire:model.live="historyModal" maxWidth="7xl">

<x-slot name="title">
    <div class="flex items-start gap-3">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-100">
            <svg class="h-5 w-5 text-indigo-600"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>

        <div class="min-w-0">
            <h3 class="text-sm font-semibold text-gray-900">
                Purchase Request History
            </h3>

            <p class="mt-0.5 text-xs text-gray-500">
                Review previous purchase requests and approval history.
            </p>
        </div>
    </div>
</x-slot>


<x-slot name="content">

    @if ($request)

        @php
            $latestWorkflow = $request->purchaseWorkflows
                ->sortByDesc('id')
                ->first();

            $statusClasses = match ($latestWorkflow?->status) {
                'approved' => 'bg-green-100 text-green-700',
                'rejected' => 'bg-red-100 text-red-700',
                'pending' => 'bg-yellow-100 text-yellow-700',
                default => 'bg-gray-100 text-gray-600',
            };
        @endphp


        <div class="space-y-4 sm:space-y-6">

            {{-- =====================================================
                REQUEST SUMMARY
            ====================================================== --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">

                <div class="border-b border-gray-200 bg-gray-50 px-4 py-4 sm:px-5">

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        {{-- Request Number --}}
                        <div class="min-w-0">

                            <div class="flex flex-wrap items-center gap-2">

                                <span class="break-all text-base font-semibold text-gray-900">
                                    {{ $request->request_no }}
                                </span>

                                @if ($latestWorkflow)
                                    <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses }}">
                                        {{ ucfirst($latestWorkflow->status) }}
                                    </span>
                                @endif

                            </div>

                            <div class="mt-2 grid grid-cols-1 gap-1 text-xs text-gray-500 sm:flex sm:flex-wrap sm:gap-x-5">

                                <span>
                                    Requested:
                                    <strong class="font-medium text-gray-700">
                                        {{ $request->created_at?->format('M d, Y h:i A') }}
                                    </strong>
                                </span>

                                <span>
                                    Department:
                                    <strong class="font-medium text-gray-700">
                                        {{ $request->department?->name ?? '-' }}
                                    </strong>
                                </span>

                            </div>

                        </div>


                        {{-- Total --}}
                        <div class="border-t border-gray-200 pt-3 sm:border-0 sm:pt-0 sm:text-right">

                            <div class="text-xs text-gray-500">
                                Total Amount
                            </div>

                            <div class="text-xl font-bold text-gray-900">
                                ₱{{ number_format($request->total_amount ?? 0, 2) }}
                            </div>

                        </div>

                    </div>

                </div>


                {{-- Request Remark --}}
                @if ($request->remark)

                    <div class="border-b border-gray-100 px-4 py-4 sm:px-5">

                        <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">
                            Request Remark
                        </div>

                        <div class="mt-1 break-words text-sm leading-6 text-gray-700">
                            {{ $request->remark }}
                        </div>

                    </div>

                @endif


                {{-- =================================================
                    REQUESTED ITEMS
                ================================================== --}}
                <div class="px-4 py-4 sm:px-5 sm:py-5">

                    <div class="mb-3 flex items-center justify-between">

                        <h4 class="text-sm font-semibold text-gray-900">
                            Requested Items
                        </h4>

                        <span class="text-xs text-gray-500">
                            {{ $request->purchaseItems->count() }} items
                        </span>

                    </div>


                    {{-- =================================================
                        MOBILE ITEM CARDS
                    ================================================== --}}
                    <div class="space-y-3 md:hidden">

                        @forelse ($request->purchaseItems as $purchaseItem)

                            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">

                                {{-- Item Header --}}
                                <div class="flex gap-3 border-b border-gray-100 bg-gray-50 p-3">

                                    <div class="h-12 w-12 shrink-0 overflow-hidden rounded-lg border border-gray-200 bg-white">

                                        @if ($purchaseItem->item?->primaryImage)

                                            <img
                                                src="{{ Storage::url($purchaseItem->item->primaryImage->path) }}"
                                                alt="{{ $purchaseItem->item_name }}"
                                                class="h-full w-full object-cover"
                                            >

                                        @else

                                            <img
                                                src="{{ asset('images/default-item.png') }}"
                                                alt="No image"
                                                class="h-full w-full object-cover"
                                            >

                                        @endif

                                    </div>


                                    <div class="min-w-0 flex-1">

                                        <div class="break-words text-sm font-semibold text-gray-900">
                                            {{ $purchaseItem->item_name }}
                                        </div>

                                        @if ($purchaseItem->item)
                                            <div class="mt-0.5 text-xs text-gray-400">
                                                Item ID: {{ $purchaseItem->item->id }}
                                            </div>
                                        @endif

                                    </div>

                                </div>


                                {{-- Item Details --}}
                                <div class="grid grid-cols-2 gap-x-4 gap-y-3 p-3">

                                    <div>
                                        <div class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                            SKU
                                        </div>
                                        <div class="mt-0.5 break-all text-sm text-gray-700">
                                            {{ $purchaseItem->sku ?: '-' }}
                                        </div>
                                    </div>


                                    <div>
                                        <div class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                            Vendor
                                        </div>
                                        <div class="mt-0.5 break-words text-sm text-gray-700">
                                            {{ $purchaseItem->vendor_name ?? '-' }}
                                        </div>

                                        @if ($purchaseItem->vendor_sku)
                                            <div class="mt-0.5 break-all text-[11px] text-gray-400">
                                                SKU: {{ $purchaseItem->vendor_sku }}
                                            </div>
                                        @endif
                                    </div>


                                    <div>
                                        <div class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                            Quantity
                                        </div>
                                        <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                            {{ number_format($purchaseItem->quantity) }}
                                        </div>
                                    </div>


                                    <div>
                                        <div class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                            Unit Price
                                        </div>
                                        <div class="mt-0.5 text-sm text-gray-700">
                                            ₱{{ number_format($purchaseItem->unit_price ?? 0, 2) }}
                                        </div>
                                    </div>

                                </div>


                                {{-- Amount --}}
                                <div class="flex items-center justify-between border-t border-gray-100 px-3 py-3">

                                    <span class="text-xs font-medium text-gray-500">
                                        Amount
                                    </span>

                                    <span class="text-sm font-bold text-gray-900">
                                        ₱{{ number_format($purchaseItem->amount ?? 0, 2) }}
                                    </span>

                                </div>


                                {{-- Remark --}}
                                @if ($purchaseItem->remark)

                                    <div class="border-t border-gray-100 px-3 py-3">

                                        <div class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                            Remark
                                        </div>

                                        <div class="mt-1 break-words text-sm leading-5 text-gray-600">
                                            {{ $purchaseItem->remark }}
                                        </div>

                                    </div>

                                @endif

                            </div>

                        @empty

                            <div class="rounded-lg border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500">
                                No items found.
                            </div>

                        @endforelse

                    </div>


                    {{-- =================================================
                        DESKTOP ITEM TABLE
                    ================================================== --}}
                    <div class="hidden overflow-x-auto rounded-lg border border-gray-200 md:block">

                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">

                                <tr>

                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Item
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        SKU
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Vendor
                                    </th>

                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Qty
                                    </th>

                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Unit Price
                                    </th>

                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Amount
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Remark
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="divide-y divide-gray-100 bg-white">

                                @forelse ($request->purchaseItems as $purchaseItem)

                                    <tr class="hover:bg-gray-50">

                                        <td class="px-4 py-3">

                                            <div class="flex items-center gap-3">

                                                <div class="h-11 w-11 shrink-0 overflow-hidden rounded-lg border border-gray-200 bg-gray-50">

                                                    @if ($purchaseItem->item?->primaryImage)

                                                        <img
                                                            src="{{ Storage::url($purchaseItem->item->primaryImage->path) }}"
                                                            alt="{{ $purchaseItem->item_name }}"
                                                            class="h-full w-full object-cover"
                                                        >

                                                    @else

                                                        <img
                                                            src="{{ asset('images/default-item.png') }}"
                                                            alt="No image"
                                                            class="h-full w-full object-cover"
                                                        >

                                                    @endif

                                                </div>

                                                <div class="min-w-0">

                                                    <div class="max-w-[220px] truncate text-sm font-medium text-gray-900">
                                                        {{ $purchaseItem->item_name }}
                                                    </div>

                                                    @if ($purchaseItem->item)
                                                        <div class="text-xs text-gray-400">
                                                            Item ID: {{ $purchaseItem->item->id }}
                                                        </div>
                                                    @endif

                                                </div>

                                            </div>

                                        </td>


                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                            {{ $purchaseItem->sku }}
                                        </td>


                                        <td class="px-4 py-3">

                                            <div class="text-sm text-gray-700">
                                                {{ $purchaseItem->vendor_name ?? '-' }}
                                            </div>

                                            @if ($purchaseItem->vendor_sku)
                                                <div class="text-xs text-gray-400">
                                                    SKU: {{ $purchaseItem->vendor_sku }}
                                                </div>
                                            @endif

                                        </td>


                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium text-gray-900">
                                            {{ number_format($purchaseItem->quantity) }}
                                        </td>


                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">
                                            ₱{{ number_format($purchaseItem->unit_price ?? 0, 2) }}
                                        </td>


                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                            ₱{{ number_format($purchaseItem->amount ?? 0, 2) }}
                                        </td>


                                        <td class="max-w-[180px] px-4 py-3 text-sm text-gray-500">
                                            {{ $purchaseItem->remark ?? '-' }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="7" class="px-5 py-8 text-center text-sm text-gray-500">
                                            No items found.
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>


                {{-- =================================================
                    APPROVAL HISTORY
                ================================================== --}}
                <div class="border-t border-gray-200 bg-gray-50 px-4 py-4 sm:px-5 sm:py-5">

                    <div class="mb-4">

                        <h4 class="text-sm font-semibold text-gray-900">
                            Approval History
                        </h4>

                        <p class="mt-1 text-xs text-gray-500">
                            Workflow status and item-level approval history.
                        </p>

                    </div>


                    {{-- =================================================
                        MOBILE TIMELINE
                    ================================================== --}}
                    <div class="space-y-4 md:hidden">

                        @forelse ($request->purchaseWorkflows->sortBy('id') as $workflow)

                            @php
                                $workflowClasses = match ($workflow->status) {
                                    'approved' => [
                                        'box' => 'border-green-200 bg-green-50',
                                        'dot' => 'bg-green-500',
                                        'text' => 'text-green-700',
                                    ],
                                    'rejected' => [
                                        'box' => 'border-red-200 bg-red-50',
                                        'dot' => 'bg-red-500',
                                        'text' => 'text-red-700',
                                    ],
                                    'pending' => [
                                        'box' => 'border-yellow-200 bg-yellow-50',
                                        'dot' => 'bg-yellow-500',
                                        'text' => 'text-yellow-700',
                                    ],
                                    default => [
                                        'box' => 'border-gray-200 bg-white',
                                        'dot' => 'bg-gray-400',
                                        'text' => 'text-gray-600',
                                    ],
                                };
                            @endphp


                            <div class="relative pl-6">

                                {{-- Timeline Line --}}
                                @if (!$loop->last)
                                    <div class="absolute left-[5px] top-3 h-full w-px bg-gray-300"></div>
                                @endif


                                {{-- Timeline Dot --}}
                                <span class="absolute left-0 top-1.5 h-3 w-3 rounded-full {{ $workflowClasses['dot'] }} ring-4 ring-gray-50"></span>


                                <div class="rounded-xl border {{ $workflowClasses['box'] }} p-3">

                                    <div class="flex items-start justify-between gap-3">

                                        <div class="min-w-0">

                                            <div class="text-sm font-semibold capitalize text-gray-900">
                                                {{ str_replace('_', ' ', $workflow->step) }}
                                            </div>

                                            @if ($workflow->acted_at)
                                                <div class="mt-0.5 text-[11px] text-gray-500">
                                                    {{ $workflow->acted_at->format('M d, Y h:i A') }}
                                                </div>
                                            @endif

                                        </div>


                                        <span class="shrink-0 rounded-full px-2 py-1 text-[11px] font-medium {{ $workflowClasses['text'] }}">
                                            {{ ucfirst($workflow->status) }}
                                        </span>

                                    </div>


                                    {{-- Workflow Items --}}
                                    @if ($workflow->purchaseWorkflowItems->isNotEmpty())

                                        <div class="mt-3 space-y-2">

                                            @foreach ($workflow->purchaseWorkflowItems as $workflowItem)

                                                <div class="rounded-lg border border-gray-200 bg-white p-3">

                                                    <div class="flex items-start justify-between gap-3">

                                                        <div class="min-w-0">

                                                            <div class="break-words text-sm font-medium text-gray-800">
                                                                {{ $workflowItem->purchaseItem?->item_name ?? '-' }}
                                                            </div>

                                                            @if ($workflowItem->purchaseItem?->sku)
                                                                <div class="mt-0.5 text-[11px] text-gray-400">
                                                                    SKU: {{ $workflowItem->purchaseItem->sku }}
                                                                </div>
                                                            @endif

                                                        </div>


                                                        @php
                                                            $itemStatusClasses = match ($workflowItem->status) {
                                                                'approved' => 'bg-green-100 text-green-700',
                                                                'rejected' => 'bg-red-100 text-red-700',
                                                                'pending' => 'bg-yellow-100 text-yellow-700',
                                                                default => 'bg-gray-100 text-gray-600',
                                                            };
                                                        @endphp

                                                        <span class="shrink-0 rounded-full px-2 py-1 text-[10px] font-medium {{ $itemStatusClasses }}">
                                                            {{ ucfirst($workflowItem->status) }}
                                                        </span>

                                                    </div>


                                                    <div class="mt-3 grid grid-cols-2 gap-3 border-t border-gray-100 pt-3">

                                                        <div>
                                                            <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                                                Quantity
                                                            </div>

                                                            <div class="mt-0.5 text-sm font-medium text-gray-800">
                                                                {{ number_format($workflowItem->purchaseItem?->quantity ?? 0) }}
                                                            </div>
                                                        </div>


                                                        <div>
                                                            <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                                                Acted At
                                                            </div>

                                                            <div class="mt-0.5 text-xs text-gray-600">
                                                                {{ $workflowItem->acted_at?->format('M d, Y h:i A') ?? '-' }}
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>

                                            @endforeach

                                        </div>

                                    @endif

                                </div>

                            </div>

                        @empty

                            <div class="rounded-lg border border-dashed border-gray-300 bg-white px-5 py-8 text-center text-sm text-gray-500">
                                No approval history found.
                            </div>

                        @endforelse

                    </div>


                    {{-- =================================================
                        DESKTOP APPROVAL HISTORY
                    ================================================== --}}
                    <div class="hidden space-y-3 md:block">

                        @forelse ($request->purchaseWorkflows->sortBy('id') as $workflow)

                            @php
                                $workflowClasses = match ($workflow->status) {
                                    'approved' => [
                                        'box' => 'border-green-200 bg-green-50',
                                        'dot' => 'bg-green-500',
                                        'text' => 'text-green-700',
                                    ],
                                    'rejected' => [
                                        'box' => 'border-red-200 bg-red-50',
                                        'dot' => 'bg-red-500',
                                        'text' => 'text-red-700',
                                    ],
                                    'pending' => [
                                        'box' => 'border-yellow-200 bg-yellow-50',
                                        'dot' => 'bg-yellow-500',
                                        'text' => 'text-yellow-700',
                                    ],
                                    default => [
                                        'box' => 'border-gray-200 bg-white',
                                        'dot' => 'bg-gray-400',
                                        'text' => 'text-gray-600',
                                    ],
                                };
                            @endphp


                            <div class="rounded-lg border {{ $workflowClasses['box'] }} p-4">

                                <div class="flex items-center justify-between gap-4">

                                    <div class="flex items-center gap-3">

                                        <span class="h-3 w-3 shrink-0 rounded-full {{ $workflowClasses['dot'] }}"></span>

                                        <div>

                                            <div class="text-sm font-semibold capitalize text-gray-900">
                                                {{ str_replace('_', ' ', $workflow->step) }}
                                            </div>

                                            @if ($workflow->acted_at)
                                                <div class="mt-0.5 text-xs text-gray-500">
                                                    {{ $workflow->acted_at->format('M d, Y h:i A') }}
                                                </div>
                                            @endif

                                        </div>

                                    </div>


                                    <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $workflowClasses['text'] }}">
                                        {{ ucfirst($workflow->status) }}
                                    </span>

                                </div>


                                @if ($workflow->purchaseWorkflowItems->isNotEmpty())

                                    <div class="mt-4 overflow-x-auto rounded-lg border border-gray-200 bg-white">

                                        <table class="min-w-full">

                                            <thead class="bg-gray-50">

                                                <tr>

                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">
                                                        Item
                                                    </th>

                                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">
                                                        Qty
                                                    </th>

                                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">
                                                        Status
                                                    </th>

                                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">
                                                        Acted At
                                                    </th>

                                                </tr>

                                            </thead>


                                            <tbody class="divide-y divide-gray-100">

                                                @foreach ($workflow->purchaseWorkflowItems as $workflowItem)

                                                    @php
                                                        $itemStatusClasses = match ($workflowItem->status) {
                                                            'approved' => 'bg-green-100 text-green-700',
                                                            'rejected' => 'bg-red-100 text-red-700',
                                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                                            default => 'bg-gray-100 text-gray-600',
                                                        };
                                                    @endphp

                                                    <tr>

                                                        <td class="px-3 py-2 text-sm text-gray-700">
                                                            {{ $workflowItem->purchaseItem?->item_name ?? '-' }}
                                                        </td>

                                                        <td class="px-3 py-2 text-right text-sm text-gray-700">
                                                            {{ number_format($workflowItem->purchaseItem?->quantity ?? 0) }}
                                                        </td>

                                                        <td class="px-3 py-2 text-center">

                                                            <span class="rounded-full px-2 py-1 text-xs font-medium {{ $itemStatusClasses }}">
                                                                {{ ucfirst($workflowItem->status) }}
                                                            </span>

                                                        </td>

                                                        <td class="whitespace-nowrap px-3 py-2 text-right text-xs text-gray-500">
                                                            {{ $workflowItem->acted_at?->format('M d, Y h:i A') ?? '-' }}
                                                        </td>

                                                    </tr>

                                                @endforeach

                                            </tbody>

                                        </table>

                                    </div>

                                @endif

                            </div>

                        @empty

                            <div class="rounded-lg border border-dashed border-gray-300 bg-white px-5 py-8 text-center text-sm text-gray-500">
                                No approval history found.
                            </div>

                        @endforelse

                    </div>

                </div>

            </div>

        </div>

    @else

        <div class="py-12 text-center text-sm text-gray-500">
            Loading user information...
        </div>

    @endif

</x-slot>


<x-slot name="footer">

    <x-secondary-button
        wire:click="closeModal"
        wire:loading.attr="disabled"
        class="w-full justify-center sm:w-auto"
    >
        {{ __('Close') }}
    </x-secondary-button>

</x-slot>

</x-dialog-modal>
