<x-dialog-modal wire:model.live="showModal" maxWidth="5xl">
    <x-slot name="title">
        <div>
            <div class="text-lg font-semibold text-gray-900">
                Stock Movement
            </div>
            @if ($stock)
                <div class="mt-1 text-sm text-gray-500">
                    {{ $stock->item->name }}
                    <span class="mx-1">·</span>
                    {{ $stock->item->sku }}
                    <span class="mx-1">·</span>
                    {{ $stock->warehouse->name }}
                </div>
            @endif
        </div>
    </x-slot>
    <x-slot name="content">
        @if ($stock)
            {{-- Current Stock --}}
            <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Current Stock
                        </div>
                        <div class="mt-1 text-xl font-semibold text-gray-900">
                            {{ number_format($stock->quantity, 2) }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Reserved
                        </div>
                        <div class="mt-1 text-xl font-semibold text-gray-900">
                            {{ number_format($stock->reserved_quantity, 2) }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Available
                        </div>
                        <div class="mt-1 text-xl font-semibold text-gray-900">
                            {{ number_format(
                                max(0, $stock->quantity - $stock->reserved_quantity),
                                2
                            ) }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Reorder Point
                        </div>
                        <div class="mt-1 text-xl font-semibold text-gray-900">
                            {{ number_format($stock->reorder_point, 2) }}
                        </div>
                    </div>
                </div>
            </div>
            {{-- Movement History --}}
            <div>
                <div class="mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">
                        Movement History
                    </h3>
                </div>
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Date
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Type
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Quantity
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Balance
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        User
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Checker
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Remark
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($movements as $movement)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                            {{ $movement->created_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3">
                                            @php
                                                $typeClasses = [
                                                    'in' => 'bg-green-100 text-green-700',
                                                    'out' => 'bg-red-100 text-red-700',
                                                    'return' => 'bg-blue-100 text-blue-700',
                                                    'adjustment' => 'bg-yellow-100 text-yellow-700',
                                                    'transfer_in' => 'bg-indigo-100 text-indigo-700',
                                                    'transfer_out' => 'bg-purple-100 text-purple-700',
                                                ];
                                            @endphp
                                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium {{ $typeClasses[$movement->type] ?? 'bg-gray-100 text-gray-700' }}">
                                                {{ str_replace('_', ' ', ucfirst($movement->type)) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium">
                                            @if (in_array($movement->type, ['out', 'transfer_out']))
                                                -
                                            @endif
                                            {{ number_format($movement->quantity, 2) }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                            {{ number_format($movement->balance_after, 2) }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3">
                                            @if ($movement->user)
                                                <div class="flex items-center gap-2.5">
                                                    <img
                                                        src="{{ $movement->user->profile_photo_url }}"
                                                        alt="{{ $movement->user->name }}"
                                                        class="h-8 w-8 rounded-full object-cover"
                                                    >
                                                    <div class="min-w-0">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $movement->user->name }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $movement->user->email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-500">
                                                    System
                                                </span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                            @if($movement->warehouseUser)
                                                <div class="flex items-center gap-2">
                                                    @if ($movement->warehouseUser)
                                                        <img
                                                            src="{{ $movement->warehouseUser->profile_photo_url }}"
                                                            alt="{{ $movement->warehouseUser->name }}"
                                                            class="h-7 w-7 rounded-full object-cover"
                                                        >
                                                        <div class="min-w-0">
                                                            <span class="text-sm text-gray-700">
                                                                {{ $movement->warehouseUser->name }}
                                                            </span>
                                                            <div class="text-xs text-gray-500">
                                                                {{ $movement->user->email }}
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-sm text-gray-400">System</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-500">
                                                    System
                                                </span>
                                            @endif
                                        </td>
                                        <td class="max-w-xs px-4 py-3 text-sm text-gray-600">
                                            <div class="truncate">
                                                {{ $movement->remark ?: '-' }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            colspan="6"
                                            class="px-4 py-8 text-center text-sm text-gray-500"
                                        >
                                            No stock movements found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $movements->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </x-slot>
    <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('showModal')" wire:loading.attr="disabled">
            Close
        </x-secondary-button>
    </x-slot>
</x-dialog-modal>