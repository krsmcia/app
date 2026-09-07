<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    {{-- =========================================================
        Header
    ========================================================== --}}
    <div class="mb-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl">
                    Inventory Management
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Monitor inventory levels across warehouses.
                </p>
            </div>
            <div>
                <x-button
                    type="button"
                    wire:click="openAddItemModal"
                >
                    + Add Item
                </x-button>
            </div>
        </div>
    </div>
    {{-- =========================================================
        Filters
    ========================================================== --}}
    <div class="mb-5 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
            {{-- Search --}}
            <div class="md:col-span-2">
                <label class="mb-1.5 block text-xs font-medium text-gray-500">
                    Search
                </label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-4 text-gray-400"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 11-13.5 0 6.75 6.75 0 0113.5 0z"
                            />
                        </svg>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search item name or SKU..."
                        class="block w-full rounded-lg border-gray-300 pl-9 text-sm shadow-sm
                            focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>
            </div>
            {{-- Warehouse --}}
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-500">
                    Warehouse
                </label>
                <select
                    wire:model.live="warehouseId"
                    class="block w-full rounded-lg border-gray-300 text-sm shadow-sm
                        focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">
                        All Warehouses
                    </option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">
                            {{ $warehouse->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            {{-- Status --}}
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-500">
                    Stock Status
                </label>
                <select
                    wire:model.live="stockStatus"
                    class="block w-full rounded-lg border-gray-300 text-sm shadow-sm
                        focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">
                        All Status
                    </option>
                    <option value="available">
                        Available
                    </option>
                    <option value="low">
                        Low Stock
                    </option>
                    <option value="out">
                        Out of Stock
                    </option>
                </select>
            </div>
        </div>
        {{-- Clear --}}
        @if ($search !== '' || $warehouseId !== '' || $stockStatus !== '')
            <div class="mt-3 flex justify-end">
                <button
                    type="button"
                    wire:click="clearFilters"
                    class="text-xs font-medium text-gray-500 transition hover:text-gray-900"
                >
                    Clear filters
                </button>
            </div>
        @endif
    </div>
    {{-- =========================================================
        Inventory
    ========================================================== --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        {{-- Desktop Table --}}
        <div class="hidden md:block">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                            Item
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                            Warehouse
                        </th>
                        <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                            Quantity
                        </th>
                        <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                            Reserved
                        </th>
                        <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                            Available
                        </th>
                        <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                            Reorder Point
                        </th>
                        <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($stocks as $stock)
                        @php
                            $item = $stock->item;
                            $status = $stock->stock_status;
                        @endphp
                        <tr
                            wire:key="stock-{{ $stock->id }}"
                            class="transition hover:bg-gray-50"
                        >
                            {{-- Item --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-11 w-11 shrink-0 overflow-hidden rounded-lg bg-gray-100">
                                        <img
                                            src="{{ $item?->primaryImage
                                                ? Storage::url($item->primaryImage->path)
                                                : asset('images/default-item.png') }}"
                                            alt="{{ $item?->name }}"
                                            class="h-full w-full object-cover"
                                        >
                                    </div>
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-medium text-gray-900">
                                            {{ $item?->name ?? '-' }}
                                        </div>
                                        <div class="mt-0.5 text-xs text-gray-500">
                                            SKU: {{ $item?->sku ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            {{-- Warehouse --}}
                            <td class="px-5 py-4">
                                <div class="text-sm font-medium text-gray-700">
                                    {{ $stock->warehouse?->name ?? '-' }}
                                </div>
                                <div class="mt-0.5 text-xs text-gray-400">
                                    {{ $stock->warehouse?->code ?? '-' }}
                                </div>
                            </td>
                            {{-- Quantity --}}
                            <td class="px-5 py-4 text-right">
                                <button
                                    type="button"
                                    x-on:click="$dispatch('stock-movement', {
                                        stockId: {{ $stock->id }}
                                    })"
                                    wire:loading.attr="disabled"
                                    class="text-sm font-semibold text-gray-900 hover:text-indigo-600 hover:underline"
                                >
                                    {{ number_format((float) $stock->quantity, 2) }}
                                </button>
                            </td>
                            {{-- Reserved --}}
                            <td class="px-5 py-4 text-right">
                                <span class="text-sm text-gray-600">
                                    {{ number_format((float) $stock->reserved_quantity, 2) }}
                                </span>
                            </td>
                            {{-- Available --}}
                            <td class="px-5 py-4 text-right">
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ number_format($stock->quantity - $stock->reserved_quantity, 2) }}
                                </span>
                            </td>
                            {{-- Reorder --}}
                            <td class="px-5 py-4 text-right">
                                <span class="text-sm text-gray-600">
                                    {{ number_format((float) $stock->reorder_point, 2) }}
                                </span>
                            </td>
                            {{-- Status --}}
                            <td class="px-5 py-4 text-right">
                                @if ($status === 'out')
                                    <span class="inline-flex rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-medium text-red-700">
                                        Out of Stock
                                    </span>
                                @elseif ($status === 'low')
                                    <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-medium text-amber-700">
                                        Low Stock
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-medium text-emerald-700">
                                        Available
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="7"
                                class="px-6 py-12 text-center"
                            >
                                <div class="text-sm font-medium text-gray-900">
                                    No inventory found.
                                </div>
                                <div class="mt-1 text-sm text-gray-500">
                                    Try adjusting your search or filters.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- =====================================================
            Mobile Cards
        ====================================================== --}}
        <div class="divide-y divide-gray-100 md:hidden">
            @forelse ($stocks as $stock)
                @php
                    $item = $stock->item;
                    $status = $stock->stock_status;
                @endphp
                <div
                    wire:key="mobile-stock-{{ $stock->id }}"
                    class="p-4"
                >
                    {{-- Item Header --}}
                    <div class="flex items-start gap-3">
                        <div class="h-12 w-12 shrink-0 overflow-hidden rounded-lg bg-gray-100">
                            <img
                                src="{{ $item?->primaryImage
                                    ? Storage::url($item->primaryImage->path)
                                    : asset('images/default-item.png') }}"
                                alt="{{ $item?->name }}"
                                class="h-full w-full object-cover"
                            >
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold text-gray-900">
                                        {{ $item?->name ?? '-' }}
                                    </div>
                                    <div class="mt-0.5 text-xs text-gray-500">
                                        SKU: {{ $item?->sku ?? '-' }}
                                    </div>
                                </div>
                                @if ($status === 'out')
                                    <span class="shrink-0 rounded-full bg-red-50 px-2 py-1 text-[10px] font-medium text-red-700">
                                        Out
                                    </span>
                                @elseif ($status === 'low')
                                    <span class="shrink-0 rounded-full bg-amber-50 px-2 py-1 text-[10px] font-medium text-amber-700">
                                        Low
                                    </span>
                                @else
                                    <span class="shrink-0 rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-medium text-emerald-700">
                                        Available
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- Warehouse --}}
                    <div class="mt-3 rounded-lg bg-gray-50 px-3 py-2.5">
                        <div class="text-[10px] uppercase tracking-wide text-gray-400">
                            Warehouse
                        </div>
                        <div class="mt-0.5 text-sm font-medium text-gray-700">
                            {{ $stock->warehouse?->name ?? '-' }}
                        </div>
                    </div>
                    {{-- Inventory Details --}}
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <div class="rounded-lg border border-gray-100 bg-white p-3">
                            <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                Quantity
                            </div>
                            <div class="mt-1 text-sm font-semibold text-gray-900">
                                {{ number_format((float) $stock->quantity, 2) }}
                            </div>
                        </div>
                        <div class="rounded-lg border border-gray-100 bg-white p-3">
                            <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                Available
                            </div>
                            <div class="mt-1 text-sm font-semibold text-gray-900">
                                {{ number_format($stock->available_quantity, 2) }}
                            </div>
                        </div>
                        <div class="rounded-lg border border-gray-100 bg-white p-3">
                            <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                Reserved
                            </div>
                            <div class="mt-1 text-sm font-semibold text-gray-700">
                                {{ number_format((float) $stock->reserved_quantity, 2) }}
                            </div>
                        </div>
                        <div class="rounded-lg border border-gray-100 bg-white p-3">
                            <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                Reorder Point
                            </div>
                            <div class="mt-1 text-sm font-semibold text-gray-700">
                                {{ number_format((float) $stock->reorder_point, 2) }}
                            </div>
                        </div>
                        
                    </div>
                    <div class="mt-3">
                        <button
                            type="button"
                            wire:click="openMovementModal({{ $stock->id }})"
                            wire:loading.attr="disabled"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm font-medium text-gray-700 transition hover:border-indigo-200 hover:text-indigo-600"
                        >
                            View Movement History
                        </button>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <div class="text-sm font-medium text-gray-900">
                        No inventory found.
                    </div>
                    <div class="mt-1 text-sm text-gray-500">
                        Try adjusting your search or filters.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    {{-- =========================================================
        Pagination
    ========================================================== --}}
    @if ($stocks->hasPages())
        <div class="mt-5">
            {{ $stocks->links() }}
        </div>
    @endif
    <x-dialog-modal wire:model.live="addItemModal">
        <x-slot name="title">
            <div>
                <div class="text-lg font-semibold text-gray-900">
                    Add Item to Inventory
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Add an item to a warehouse inventory.
                </p>
            </div>
        </x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label for="addItemSearch" value="Item" />
                    <div class="relative">
                        <x-input
                            id="addItemSearch"
                            type="text"
                            class="mt-1 block w-full"
                            wire:model.live.debounce.300ms="addItemSearch"
                            placeholder="Search item name or SKU..."
                            autocomplete="off"
                        />
                        @if (!empty($addItemResults))
                            <div class="absolute left-0 right-0 z-[100] mt-1 max-h-60 overflow-y-auto rounded-md border border-gray-200 bg-white shadow-lg">
                                @foreach ($addItemResults as $item)
                                    <button
                                        type="button"
                                        wire:click="selectAddItem({{ $item['id'] }})"
                                        class="block w-full px-4 py-2 text-left hover:bg-gray-50"
                                    >
                                        <div class="font-medium text-gray-900">
                                            {{ $item['name'] }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $item['sku'] }}
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @error('addItemId')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Warehouse --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Warehouse
                        <span class="text-red-500">*</span>
                    </label>
                    <select
                        wire:model.defer="addWarehouseId"
                        class="block w-full rounded-lg border-gray-300 text-sm shadow-sm
                            focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">
                            Select Warehouse
                        </option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('addWarehouseId')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                {{-- Initial Quantity --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Initial Quantity
                    </label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        wire:model.defer="addQuantity"
                        class="block w-full rounded-lg border-gray-300 text-sm shadow-sm
                            focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('addQuantity')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                {{-- Reorder Point --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Reorder Point
                    </label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        wire:model.defer="addReorderPoint"
                        class="block w-full rounded-lg border-gray-300 text-sm shadow-sm
                            focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('addReorderPoint')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex w-full justify-end gap-2">
                <x-secondary-button
                    type="button"
                    wire:click="$set('addItemModal', false)"
                    wire:loading.attr="disabled"
                >
                    Cancel
                </x-secondary-button>
                <x-button
                    type="button"
                    wire:click="addItem"
                    wire:loading.attr="disabled"
                    wire:target="addItem"
                >
                    <span wire:loading.remove wire:target="addItem">
                        Add Item
                    </span>
                    <span wire:loading wire:target="addItem">
                        Adding...
                    </span>
                </x-button>
            </div>
        </x-slot>
    </x-dialog-modal>
    <livewire:warehouses.modals.stock-movements />
</div>