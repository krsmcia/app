
<x-dialog-modal
    wire:model.live="purchaseRequestHistoryModal"
    maxWidth="7xl"
>
    <x-slot name="title">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                <svg class="h-5 w-5 text-indigo-600"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Purchase Request History
                </h2>

                <p class="text-sm text-gray-500">
                    {{ $productName ?? 'Product requests' }}
                </p>
            </div>
        </div>
    </x-slot>

    <x-slot name="content">
        <div class="space-y-4">

            {{-- Applied filters --}}
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="rounded-full bg-indigo-50 px-3 py-1 text-indigo-700">
                    Department ID: {{ $departmentId ?? '-' }}
                </span>

                @if ($from || $to)
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-gray-700">
                        {{ $from ?: 'Beginning' }}
                        –
                        {{ $to ?: 'Today' }}
                    </span>
                @else
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-gray-600">
                        All dates
                    </span>
                @endif

                <span class="ml-auto text-gray-500">
                    {{ number_format($requests->total()) }} requests
                </span>
            </div>

            {{-- Request table --}}
            <div class="overflow-hidden rounded-xl border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Request No.
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Requested By
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Date
                                </th>

                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-500">
                                    Status
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($requests as $request)
                                @php
                                    $status = strtolower(
                                        $request->status ?? 'pending'
                                    );

                                    $statusClasses = match ($status) {
                                        'approved', 'completed' =>
                                            'bg-green-100 text-green-700',

                                        'rejected', 'denied' =>
                                            'bg-red-100 text-red-700',

                                        'pending', 'submitted' =>
                                            'bg-amber-100 text-amber-700',

                                        default =>
                                            'bg-gray-100 text-gray-700',
                                    };
                                @endphp

                                <tr wire:key="purchase-request-{{ $request->id }}"
                                    class="hover:bg-gray-50">

                                    <td class="whitespace-nowrap px-4 py-3">
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $request->request_no ?? '#' . $request->id }}
                                        </div>

                                        <div class="text-xs text-gray-400">
                                            ID: {{ $request->id }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $request->user?->name ?? 'Unknown' }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $request->department?->name ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-3">
                                        <div class="text-sm text-gray-700">
                                            {{ $request->created_at?->format('M d, Y') ?? '-' }}
                                        </div>

                                        <div class="text-xs text-gray-400">
                                            {{ $request->created_at?->format('h:i A') ?? '' }}
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-3 text-center">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                            {{ str($status)->replace('_', ' ')->title() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="px-5 py-12 text-center">
                                        <div class="text-sm font-medium text-gray-900">
                                            No purchase requests found
                                        </div>

                                        <p class="mt-1 text-sm text-gray-500">
                                            No requests match this product and date range.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($requests->hasPages())
                    <div class="border-t border-gray-200 px-4 py-3">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button
            wire:click="closeModal"
            wire:loading.attr="disabled"
        >
            Close
        </x-secondary-button>
    </x-slot>
</x-dialog-modal>