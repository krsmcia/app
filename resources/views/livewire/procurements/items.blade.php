<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Items') }}
    </h2>
</x-slot>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Header --}}
    <div class="mb-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div class="flex flex-col md:flex-row gap-3 w-full lg:w-auto">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by SKU, barcode, product name..."
                class="w-full md:w-96 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                autocomplete="off"
            >
            <select
                wire:model.live="statusFilter"
                class="w-full md:w-40 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
            <button
                type="button"
                wire:click="openImportModal"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
            >
                Import Excel
            </button>
            <button
                type="button"
                wire:click="create"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
            >
                Add Item
            </button>
        </div>
    </div>
    {{-- Success --}}
    @if (session()->has('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif
    {{-- Error --}}
    @if (session()->has('error'))
        <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif
    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white shadow-sm rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Image
                        </th>
                        <th
                            wire:click="sortBy('sku')"
                            class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap"
                        >
                            SKU
                            @if ($sortField === 'sku')
                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                            @endif
                        </th>
                        <th
                            wire:click="sortBy('barcode')"
                            class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap"
                        >
                            Barcode
                            @if ($sortField === 'barcode')
                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                            @endif
                        </th>
                        <th
                            wire:click="sortBy('name')"
                            class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                        >
                            Name
                            @if ($sortField === 'name')
                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Categories
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Unit
                            <br>Brand
                            <br>Color
                            <br>Size
                        </th>
                        <th
                            wire:click="sortBy('is_active')"
                            class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                        >
                            Status
                            @if ($sortField === 'is_active')
                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                            @endif
                        </th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img
                                    src="{{ $item->image_url }}"
                                    alt="{{ $item->name }}"
                                    class="h-14 w-14 rounded-lg object-cover border border-gray-200"
                                >
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono text-gray-700">
                                    {{ $item->sku }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-600">
                                    {{ $item->barcode ?: '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $item->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($item->categories->isNotEmpty())
                                    <div class="flex flex-wrap gap-1.5 max-w-xs">
                                        @foreach ($item->categories as $category)
                                            <span
                                                class="inline-flex items-center rounded-full
                                                    bg-indigo-50 px-2 py-0.5
                                                    text-xs font-medium text-indigo-700"
                                            >
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">
                                        -
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $item->unit }}<br>
                                {{ $item->brand ?: '-' }}<br>
                                {{ $item->color ?: '-' }}<br>
                                {{ $item->size ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($item->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <button
                                    type="button"
                                    wire:click="manageCategories({{ $item->id }})"
                                    class="text-green-600 hover:text-green-900"
                                >
                                    Categories
                                </button>
                                <button
                                    type="button"
                                    wire:click="manageVendors({{ $item->id }})"
                                    class="ml-3 text-blue-600 hover:text-blue-900"
                                >
                                    Vendors
                                </button>
                                <button
                                    type="button"
                                    wire:click="edit({{ $item->id }})"
                                    class="ml-3 text-indigo-600 hover:text-indigo-900"
                                >
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    wire:click="deleteItem({{ $item->id }})"
                                    wire:confirm="Are you sure you want to delete this item?"
                                    class="ml-3 text-red-600 hover:text-red-900"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center text-gray-500">
                                No items found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse ($items as $item)
            <div
                wire:key="mobile-item-{{ $item->id }}"
                class="rounded-xl bg-white border border-gray-200 shadow-sm p-4"
            >
                {{-- Header --}}
                <div class="flex items-start gap-3">
                    <img
                        src="{{ $item->image_url }}"
                        alt="{{ $item->name }}"
                        class="h-20 w-20 shrink-0 rounded-lg object-cover border border-gray-200"
                    >
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="text-sm font-semibold text-gray-900 leading-5">
                                {{ $item->name }}
                            </h3>
                            @if ($item->is_active)
                                <span class="shrink-0 inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-medium text-green-700">
                                    Active
                                </span>
                            @else
                                <span class="shrink-0 inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600">
                                    Inactive
                                </span>
                            @endif
                        </div>
                        <div class="mt-1 text-xs font-mono text-gray-500">
                            SKU: {{ $item->sku }}
                        </div>
                        @if ($item->barcode)
                            <div class="mt-0.5 text-xs text-gray-500">
                                Barcode: {{ $item->barcode }}
                            </div>
                        @endif
                    </div>
                </div>
                {{-- Product Info --}}
                <div class="mt-4 grid grid-cols-2 gap-x-4 gap-y-3 border-t border-gray-100 pt-3">
                    <div>
                        <div class="text-[11px] text-gray-400 uppercase">
                            Unit
                        </div>
                        <div class="mt-0.5 text-sm text-gray-700">
                            {{ $item->unit ?: '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[11px] text-gray-400 uppercase">
                            Brand
                        </div>
                        <div class="mt-0.5 text-sm text-gray-700">
                            {{ $item->brand ?: '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[11px] text-gray-400 uppercase">
                            Color
                        </div>
                        <div class="mt-0.5 text-sm text-gray-700">
                            {{ $item->color ?: '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[11px] text-gray-400 uppercase">
                            Size
                        </div>
                        <div class="mt-0.5 text-sm text-gray-700">
                            {{ $item->size ?: '-' }}
                        </div>
                    </div>
                    <div class="col-span-2">
                        <div class="text-[11px] text-gray-400 uppercase">
                            Categories
                        </div>
                        <div class="mt-1 flex flex-wrap gap-1.5">
                            @forelse ($item->categories as $category)
                                <span
                                    class="inline-flex items-center rounded-full
                                        bg-indigo-50 px-2 py-0.5
                                        text-[11px] font-medium text-indigo-700"
                                >
                                    {{ $category->name }}
                                </span>
                            @empty
                                <span class="text-sm text-gray-400">
                                    -
                                </span>
                            @endforelse
                        </div>
                    </div>
                </div>
                {{-- Actions --}}
                <div class="mt-4 grid grid-cols-2 gap-2 border-t border-gray-100 pt-3">
                    <button
                        type="button"
                        wire:click="manageCategories({{ $item->id }})"
                        class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-green-600 hover:bg-green-50"
                    >
                        Categories
                    </button>

                    <button
                        type="button"
                        wire:click="manageVendors({{ $item->id }})"
                        class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-blue-600 hover:bg-blue-50"
                    >
                        Vendors
                    </button>

                    <button
                        type="button"
                        wire:click="edit({{ $item->id }})"
                        class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-indigo-600 hover:bg-indigo-50"
                    >
                        Edit
                    </button>

                    <button
                        type="button"
                        wire:click="deleteItem({{ $item->id }})"
                        wire:confirm="Are you sure you want to delete this item?"
                        class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50"
                    >
                        Delete
                    </button>

                </div>
            </div>
        @empty
            <div class="rounded-xl border border-gray-200 bg-white px-6 py-10 text-center">
                <p class="text-sm text-gray-500">
                    No items found.
                </p>
            </div>
        @endforelse
    </div>
    <div class="mt-4">
        {{ $items->links() }}
    </div>
    {{-- Create Item Modal --}}
    <x-dialog-modal
        maxWidth="2xl"
        wire:model.live="showCreateModal"
    >
        <x-slot name="title">
            Add Item
        </x-slot>
        <x-slot name="content">
            <div class="space-y-6">
                {{-- Images --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Product Images
                    </label>
                    <input
                        type="file"
                        wire:model="images"
                        multiple
                        accept="image/jpeg,image/png,image/webp"
                        class="block w-full text-sm text-gray-700
                            file:mr-4
                            file:py-2
                            file:px-4
                            file:rounded-md
                            file:border-0
                            file:text-sm
                            file:font-semibold
                            file:bg-indigo-50
                            file:text-indigo-700
                            hover:file:bg-indigo-100"
                    >
                    @error('images')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                    @error('images.*')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                    <div
                        wire:loading
                        wire:target="images"
                        class="mt-2 text-sm text-gray-500"
                    >
                        Uploading images...
                    </div>
                    @if (count($images) > 0)
                        <div class="mt-4">
                            <p class="mb-2 text-xs font-medium text-gray-500">
                                Preview
                            </p>
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                                @foreach ($images as $index => $image)
                                    <div
                                        wire:key="create-image-{{ $index }}"
                                        class="relative"
                                    >
                                        <img
                                            src="{{ $image->temporaryUrl() }}"
                                            alt="Preview"
                                            class="h-28 w-full rounded-lg object-cover border border-gray-200"
                                        >
                                        @if ($index === 0)
                                            <span
                                                class="absolute top-1 left-1 rounded bg-indigo-600 px-2 py-0.5 text-[10px] font-medium text-white"
                                            >
                                                Primary
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                {{-- Basic Information --}}
                <div>
                    <h3 class="mb-3 text-sm font-semibold text-gray-900">
                        Basic Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- SKU --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                SKU
                            </label>
                            <input
                                type="text"
                                wire:model="sku"
                                placeholder="SKU"
                                autocomplete="off"
                                class="block w-full rounded-md border-gray-300 shadow-sm
                                    focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            @error('sku')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Barcode --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Barcode
                            </label>
                            <input
                                type="text"
                                wire:model="barcode"
                                placeholder="Barcode"
                                autocomplete="off"
                                class="block w-full rounded-md border-gray-300 shadow-sm
                                    focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            @error('barcode')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Name --}}
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Product Name
                            </label>
                            <input
                                type="text"
                                wire:model="name"
                                placeholder="Product name"
                                autocomplete="off"
                                class="block w-full rounded-md border-gray-300 shadow-sm
                                    focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Unit --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Unit
                            </label>
                            <input
                                type="text"
                                wire:model="unit"
                                placeholder="e.g. pcs"
                                class="block w-full rounded-md border-gray-300 shadow-sm
                                    focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            @error('unit')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Brand --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Brand
                            </label>
                            <input
                                type="text"
                                wire:model="brand"
                                placeholder="Brand"
                                class="block w-full rounded-md border-gray-300 shadow-sm
                                    focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            @error('brand')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Color --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Color
                            </label>
                            <input
                                type="text"
                                wire:model="color"
                                placeholder="Color"
                                class="block w-full rounded-md border-gray-300 shadow-sm
                                    focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            @error('color')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Size --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Size
                            </label>
                            <input
                                type="text"
                                wire:model="size"
                                placeholder="Size"
                                class="block w-full rounded-md border-gray-300 shadow-sm
                                    focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            @error('size')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Description --}}
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <textarea
                                wire:model="description"
                                rows="3"
                                placeholder="Description"
                                class="block w-full rounded-md border-gray-300 shadow-sm
                                    focus:border-indigo-500 focus:ring-indigo-500"
                            ></textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Active --}}
                        <div class="md:col-span-2">
                            <label class="inline-flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    wire:model="isActive"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >
                                <span class="text-sm text-gray-700">
                                    Active
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <button
                type="button"
                wire:click="closeCreateModal"
                wire:loading.attr="disabled"
                class="px-4 py-2 bg-white border border-gray-300 rounded-md
                    text-sm font-medium text-gray-700
                    hover:bg-gray-50"
            >
                Cancel
            </button>
            <button
                type="button"
                wire:click="createItem"
                wire:loading.attr="disabled"
                wire:target="createItem"
                class="ml-3 px-4 py-2 bg-indigo-600 text-white rounded-md
                    text-sm font-medium hover:bg-indigo-700"
            >
                <span
                    wire:loading.remove
                    wire:target="createItem"
                >
                    Create
                </span>
                <span
                    wire:loading
                    wire:target="createItem"
                >
                    Creating...
                </span>
            </button>
        </x-slot>
    </x-dialog-modal>
    {{-- Edit Item Modal --}}
    <x-dialog-modal
        maxWidth="2xl"
        wire:model.live="showEditModal"
    >
        <x-slot name="title">
            Edit Item
        </x-slot>
        <x-slot name="content">
            <div class="space-y-6">
                {{-- Images --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Product Images
                    </label>
                    {{-- Existing Images --}}
                    @if (count($existingImages) > 0)
                        <div class="mb-5">
                            <p class="mb-2 text-xs font-medium text-gray-500">
                                Current Images
                            </p>
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                                @foreach ($existingImages as $image)
                                    <div
                                        wire:key="existing-image-{{ $image['id'] }}"
                                        class="relative group"
                                    >
                                        <img
                                            src="{{ asset('storage/' . $image['path']) }}"
                                            alt="Product image"
                                            class="h-28 w-full rounded-lg object-cover border-2
                                                {{ $image['is_primary']
                                                    ? 'border-indigo-500'
                                                    : 'border-gray-200' }}"
                                        >
                                        {{-- Primary --}}
                                        @if ($image['is_primary'])
                                            <span
                                                class="absolute top-1 left-1 rounded bg-indigo-600
                                                    px-2 py-0.5 text-[10px] font-medium text-white"
                                            >
                                                Primary
                                            </span>
                                        @else
                                            <button
                                                type="button"
                                                wire:click="setPrimaryImage({{ $image['id'] }})"
                                                wire:loading.attr="disabled"
                                                class="absolute top-1 left-1 rounded
                                                    bg-white/95 px-2 py-0.5
                                                    text-[10px] font-medium text-gray-700
                                                    opacity-0 group-hover:opacity-100
                                                    transition hover:bg-white"
                                            >
                                                Set Primary
                                            </button>
                                        @endif
                                        {{-- Delete --}}
                                        <button
                                            type="button"
                                            wire:click="deleteImage({{ $image['id'] }})"
                                            wire:confirm="Are you sure you want to delete this image?"
                                            wire:loading.attr="disabled"
                                            class="absolute top-1 right-1 h-6 w-6
                                                rounded-full bg-red-600 text-white
                                                text-xs flex items-center justify-center
                                                opacity-0 group-hover:opacity-100
                                                transition hover:bg-red-700"
                                        >
                                            ×
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div
                            class="mb-5 rounded-lg border border-dashed
                                border-gray-300 px-4 py-6 text-center"
                        >
                            <p class="text-sm text-gray-500">
                                No images uploaded.
                            </p>
                        </div>
                    @endif
                    {{-- Add New Images --}}
                    <div>
                        <p class="mb-2 text-xs font-medium text-gray-500">
                            Add New Images
                        </p>
                        <input
                            type="file"
                            wire:model="images"
                            multiple
                            accept="image/jpeg,image/png,image/webp"
                            class="block w-full text-sm text-gray-700
                                file:mr-4
                                file:py-2
                                file:px-4
                                file:rounded-md
                                file:border-0
                                file:text-sm
                                file:font-semibold
                                file:bg-indigo-50
                                file:text-indigo-700
                                hover:file:bg-indigo-100"
                        >
                        @error('images')
                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                        @error('images.*')
                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                        <div
                            wire:loading
                            wire:target="images"
                            class="mt-2 text-sm text-gray-500"
                        >
                            Uploading images...
                        </div>
                        {{-- New Image Preview --}}
                        @if (count($images) > 0)
                            <div class="mt-4">
                                <p class="mb-2 text-xs font-medium text-gray-500">
                                    New Images
                                </p>
                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                                    @foreach ($images as $index => $image)
                                        <div
                                            wire:key="new-edit-image-{{ $index }}"
                                            class="relative"
                                        >
                                            <img
                                                src="{{ $image->temporaryUrl() }}"
                                                alt="New image"
                                                class="h-28 w-full rounded-lg object-cover border border-gray-200"
                                            >
                                            {{-- New image becomes primary only
                                            when there are no existing images --}}
                                            @if (
                                                $index === 0 &&
                                                count($existingImages) === 0
                                            )
                                                <span
                                                    class="absolute top-1 left-1
                                                        rounded bg-indigo-600
                                                        px-2 py-0.5
                                                        text-[10px] font-medium
                                                        text-white"
                                                >
                                                    Primary
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                {{-- Basic Information --}}
                <div>
                    <h3 class="mb-3 text-sm font-semibold text-gray-900">
                        Basic Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- SKU --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                SKU
                            </label>
                            <input
                                type="text"
                                wire:model="sku"
                                placeholder="SKU"
                                autocomplete="off"
                                class="block w-full rounded-md border-gray-300 shadow-sm
                                    focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            @error('sku')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Barcode --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Barcode
                            </label>
                            <input
                                type="text"
                                wire:model="barcode"
                                placeholder="Barcode"
                                autocomplete="off"
                                class="block w-full rounded-md border-gray-300 shadow-sm
                                    focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            @error('barcode')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Name --}}
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Product Name
                            </label>
                            <input
                                type="text"
                                wire:model="name"
                                placeholder="Product name"
                                autocomplete="off"
                                class="block w-full rounded-md border-gray-300 shadow-sm
                                    focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Unit --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Unit
                            </label>
                            <input
                                type="text"
                                wire:model="unit"
                                placeholder="e.g. pcs"
                                class="block w-full rounded-md border-gray-300 shadow-sm
                                    focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            @error('unit')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Brand --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Brand
                            </label>
                            <input
                                type="text"
                                wire:model="brand"
                                placeholder="Brand"
                                class="block w-full rounded-md border-gray-300 shadow-sm
                                    focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            @error('brand')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Color --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Color
                            </label>
                            <input
                                type="text"
                                wire:model="color"
                                placeholder="Color"
                                class="block w-full rounded-md border-gray-300 shadow-sm
                                    focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            @error('color')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Size --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Size
                            </label>
                           <input
                                type="text"
                                wire:model="size"
                                placeholder="Size"
                                class="block w-full rounded-md border-gray-300 shadow-sm
                                    focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            @error('size')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Description --}}
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <textarea
                                wire:model="description"
                                rows="3"
                                placeholder="Description"
                                class="block w-full rounded-md border-gray-300 shadow-sm
                                    focus:border-indigo-500 focus:ring-indigo-500"
                            ></textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Active --}}
                        <div class="md:col-span-2">
                            <label class="inline-flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    wire:model="isActive"
                                    class="rounded border-gray-300
                                        text-indigo-600 focus:ring-indigo-500"
                                >
                                <span class="text-sm text-gray-700">
                                    Active
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <button
                type="button"
                wire:click="closeEditModal"
                wire:loading.attr="disabled"
                class="px-4 py-2 bg-white border border-gray-300
                    rounded-md text-sm font-medium text-gray-700
                    hover:bg-gray-50"
            >
                Cancel
            </button>
            <button
                type="button"
                wire:click="updateItem"
                wire:loading.attr="disabled"
                wire:target="updateItem"
                class="ml-3 px-4 py-2 bg-indigo-600 text-white
                    rounded-md text-sm font-medium hover:bg-indigo-700"
            >
                <span
                    wire:loading.remove
                    wire:target="updateItem"
                >
                    Update
                </span>
                <span
                    wire:loading
                    wire:target="updateItem"
                >
                    Updating...
                </span>
            </button>
        </x-slot>
    </x-dialog-modal>
    <x-dialog-modal
        maxWidth="lg"
        wire:model.live="showImportModal"
    >
        <x-slot name="title">
            Import Items from Excel
        </x-slot>
        <x-slot name="content">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Excel File
                </label>
                <input
                    type="file"
                    wire:model="excelFile"
                    accept=".xlsx,.xls,.csv"
                    class="block w-full text-sm text-gray-700
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100"
                >
                @error('excelFile')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
            <div class="mt-4 rounded-md bg-gray-50 p-4">
                <p class="text-sm font-medium text-gray-700">
                    Required Excel columns
                </p>
                <p class="mt-1 text-xs text-gray-500">
                    SKU, 바코드, 제품명, UNIT, BRAND, COLOR, SIZE
                </p>
                <p class="mt-2 text-xs text-gray-500">
                    Existing items with the same SKU will be updated.
                </p>
            </div>
            <div
                wire:loading
                wire:target="excelFile"
                class="mt-3 text-sm text-gray-500"
            >
                Uploading...
            </div>
        </x-slot>
        <x-slot name="footer">
            <button
                type="button"
                wire:click="closeImportModal"
                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700"
            >
                Cancel
            </button>
            <button
                type="button"
                wire:click="importExcel"
                wire:loading.attr="disabled"
                wire:target="importExcel"
                class="ml-3 px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium"
            >
                <span wire:loading.remove wire:target="importExcel">
                    Import
                </span>
                <span wire:loading wire:target="importExcel">
                    Importing...
                </span>
            </button>
        </x-slot>
    </x-dialog-modal>
    {{-- Vendor Modal --}}
    <x-dialog-modal
        maxWidth="3xl"
        wire:model.live="showVendorModal"
    >
        <x-slot name="title">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Vendors
                </h2>
                @if ($vendorItem)
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $vendorItem->name }}
                        <span class="text-gray-400">
                            · {{ $vendorItem->sku }}
                        </span>
                    </p>
                @endif
            </div>
        </x-slot>
        <x-slot name="content">
            <div class="space-y-6">
                {{-- Add Vendor --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Add Vendor
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="vendorSearch"
                            placeholder="Search vendor by name or code..."
                            autocomplete="off"
                            class="w-full rounded-lg border-gray-300 pr-10
                                shadow-sm focus:border-indigo-500
                                focus:ring-indigo-500"
                        >
                        @if ($vendorSearch)
                            <button
                                type="button"
                                wire:click="$set('vendorSearch', '')"
                                class="absolute right-3 top-1/2
                                    -translate-y-1/2
                                    text-gray-400
                                    hover:text-gray-600"
                            >
                                ×
                            </button>
                        @endif
                    </div>
                    {{-- Search loading --}}
                    <div
                        wire:loading
                        wire:target="vendorSearch"
                        class="mt-2 text-xs text-gray-500"
                    >
                        Searching vendors...
                    </div>
                    {{-- Search Results --}}
                    @if (count($vendorSearchResults) > 0)
                        <div class="mt-2 overflow-hidden rounded-lg
                            border border-gray-200 bg-white shadow-sm">
                            @foreach ($vendorSearchResults as $vendor)
                                <button
                                    type="button"
                                    wire:key="vendor-search-{{ $vendor['id'] }}"
                                    wire:click="addVendor({{ $vendor['id'] }})"
                                    wire:loading.attr="disabled"
                                    class="flex w-full items-center
                                        justify-between px-4 py-3
                                        text-left hover:bg-gray-50
                                        transition"
                                >
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $vendor['name'] }}
                                        </div>
                                        @if (! empty($vendor['code']))
                                            <div class="mt-0.5 text-xs text-gray-500">
                                                {{ $vendor['code'] }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="text-sm font-medium text-indigo-600">
                                        Add
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    @elseif (
                        strlen(trim($vendorSearch)) >= 2 &&
                        ! $vendorSearchResults
                    )
                        <div
                            class="mt-2 rounded-lg border border-gray-200
                                px-4 py-6 text-center"
                        >
                            <p class="text-sm text-gray-500">
                                No available vendors found.
                            </p>
                        </div>
                    @endif
                </div>
                {{-- Current Vendors --}}
                <div>
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">
                            Connected Vendors
                        </h3>
                        <span class="text-xs text-gray-500">
                            {{ $vendorItem?->vendors->count() ?? 0 }}
                            {{ ($vendorItem?->vendors->count() ?? 0) === 1 ? 'vendor' : 'vendors' }}
                        </span>
                    </div>
                    @if (
                        $vendorItem &&
                        $vendorItem->vendors->count() > 0
                    )
                        <div class="space-y-4">
                            @foreach ($vendorItem->vendors as $vendor)
                                <div
                                    wire:key="item-{{ $vendorItem->id }}-vendor-{{ $vendor->id }}"
                                    class="rounded-xl border border-gray-200
                                        bg-white p-4"
                                >
                                    {{-- Header --}}
                                    <div class="flex items-start
                                        justify-between gap-4">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap
                                                items-center gap-2">
                                                <span class="text-sm font-semibold
                                                    text-gray-900">
                                                    {{ $vendor->name }}
                                                </span>
                                                @if ($vendor->pivot->is_preferred)
                                                    <span
                                                        class="rounded-full bg-green-100
                                                            px-2 py-0.5 text-xs
                                                            font-medium text-green-700"
                                                    >
                                                        Preferred
                                                    </span>
                                                @endif
                                            </div>
                                            @if ($vendor->code)
                                                <div class="mt-1 text-xs text-gray-500">
                                                    {{ $vendor->code }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex shrink-0 items-center gap-3">
                                            @if (! $vendor->pivot->is_preferred)
                                                <button
                                                    type="button"
                                                    wire:click="setPreferredVendor({{ $vendor->id }})"
                                                    wire:loading.attr="disabled"
                                                    class="text-xs font-medium
                                                        text-indigo-600
                                                        hover:text-indigo-800"
                                                >
                                                    Set Preferred
                                                </button>
                                            @endif
                                            <button
                                                type="button"
                                                wire:click="removeVendor({{ $vendor->id }})"
                                                wire:confirm="Remove this vendor from the item?"
                                                wire:loading.attr="disabled"
                                                class="text-xs font-medium
                                                    text-red-600
                                                    hover:text-red-800"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                    {{-- Vendor Fields --}}
                                    <div class="mt-4 grid grid-cols-1
                                        gap-4 sm:grid-cols-2">
                                        {{-- Vendor SKU --}}
                                        <div>
                                            <label
                                                class="mb-1 block text-xs
                                                    font-medium text-gray-500"
                                            >
                                                Vendor SKU
                                            </label>
                                            <input
                                                type="text"
                                                wire:model.defer="vendorForms.{{ $vendor->id }}.vendor_sku"
                                                placeholder="Vendor SKU"
                                                class="block w-full rounded-md
                                                    border-gray-300 text-sm
                                                    shadow-sm focus:border-indigo-500
                                                    focus:ring-indigo-500"
                                            >
                                            @error("vendorForms.{$vendor->id}.vendor_sku")
                                                <p class="mt-1 text-xs text-red-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                        {{-- Unit Price --}}
                                        <div>
                                            <label
                                                class="mb-1 block text-xs
                                                    font-medium text-gray-500"
                                            >
                                                Unit Price
                                            </label>
                                            <input
                                                type="number"
                                                @wheel="$event.target.blur()"
                                                step="0.01"
                                                min="0"
                                                wire:model.defer="vendorForms.{{ $vendor->id }}.unit_price"
                                                placeholder="0.00"
                                                class="block w-full rounded-md
                                                    border-gray-300 text-sm
                                                    shadow-sm focus:border-indigo-500
                                                    focus:ring-indigo-500"
                                            >
                                            @error("vendorForms.{$vendor->id}.unit_price")
                                                <p class="mt-1 text-xs text-red-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                        {{-- Minimum Order Qty --}}
                                        <div>
                                            <label
                                                class="mb-1 block text-xs
                                                    font-medium text-gray-500"
                                            >
                                                Minimum Order Qty
                                            </label>
                                            <input
                                                type="number"
                                                @wheel="$event.target.blur()"
                                                min="1"
                                                step="1"
                                                wire:model.defer="vendorForms.{{ $vendor->id }}.minimum_order_qty"
                                                class="block w-full rounded-md
                                                    border-gray-300 text-sm
                                                    shadow-sm focus:border-indigo-500
                                                    focus:ring-indigo-500"
                                            >
                                            @error("vendorForms.{$vendor->id}.minimum_order_qty")
                                                <p class="mt-1 text-xs text-red-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                        {{-- Lead Time --}}
                                        <div>
                                            <label
                                                class="mb-1 block text-xs
                                                    font-medium text-gray-500"
                                            >
                                                Lead Time
                                            </label>
                                            <div class="relative">
                                                <input
                                                    type="number"
                                                    @wheel="$event.target.blur()"
                                                    min="0"
                                                    step="1"
                                                    wire:model.defer="vendorForms.{{ $vendor->id }}.lead_time"
                                                    placeholder="0"
                                                    class="block w-full rounded-md
                                                        border-gray-300 pr-14
                                                        text-sm shadow-sm
                                                        focus:border-indigo-500
                                                        focus:ring-indigo-500"
                                                >
                                                <span
                                                    class="pointer-events-none
                                                        absolute inset-y-0 right-3
                                                        flex items-center
                                                        text-xs text-gray-400"
                                                >
                                                    days
                                                </span>
                                            </div>
                                            @error("vendorForms.{$vendor->id}.lead_time")
                                                <p class="mt-1 text-xs text-red-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- Save --}}
                                    <div class="mt-4 flex justify-end">
                                        <button
                                            type="button"
                                            wire:click="updateVendor({{ $vendor->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="updateVendor({{ $vendor->id }})"
                                            class="inline-flex items-center
                                                rounded-md bg-indigo-600
                                                px-3 py-2 text-xs
                                                font-semibold text-white
                                                hover:bg-indigo-700
                                                disabled:opacity-50"
                                        >
                                            <span
                                                wire:loading.remove
                                                wire:target="updateVendor({{ $vendor->id }})"
                                            >
                                                Save Changes
                                            </span>
                                            <span
                                                wire:loading
                                                wire:target="updateVendor({{ $vendor->id }})"
                                            >
                                                Saving...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div
                            class="rounded-xl border border-dashed
                                border-gray-300 px-6 py-10 text-center"
                        >
                            <div class="text-sm font-medium text-gray-900">
                                No vendors connected
                            </div>
                            <div class="mt-1 text-sm text-gray-500">
                                Search for an existing vendor above to add one.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <button
                type="button"
                wire:click="closeVendorModal"
                class="inline-flex items-center rounded-md
                    border border-gray-300 bg-white
                    px-4 py-2 text-sm font-medium
                    text-gray-700 hover:bg-gray-50"
            >
                Close
            </button>
        </x-slot>
    </x-dialog-modal>
    {{-- Category Modal --}}
    <x-dialog-modal
        maxWidth="lg"
        wire:model.live="showCategoryModal"
    >
        <x-slot name="title">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Categories
                </h2>

                @if ($categoryItem)
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $categoryItem->name }}
                        <span class="text-gray-400">
                            · {{ $categoryItem->sku }}
                        </span>
                    </p>
                @endif
            </div>
        </x-slot>

        <x-slot name="content">

            <div class="space-y-5">

                {{-- Selected Count --}}
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">
                        Select Categories
                    </h3>

                    <span class="text-xs text-gray-500">
                        {{ count($selectedCategories) }} selected
                    </span>
                </div>

                {{-- Category Tree --}}
                <div
                    class="max-h-[450px] overflow-y-auto
                        rounded-lg border border-gray-200
                        bg-white p-3"
                >
                    @forelse ($categories as $category)

                        @include(
                            'livewire.procurements.partials.category-checkbox',
                            [
                                'category' => $category,
                                'level' => 0,
                            ]
                        )

                    @empty

                        <div class="py-10 text-center">
                            <p class="text-sm text-gray-500">
                                No categories available.
                            </p>
                        </div>

                    @endforelse
                </div>

            </div>

        </x-slot>

        <x-slot name="footer">

            <button
                type="button"
                wire:click="closeCategoryModal"
                wire:loading.attr="disabled"
                class="px-4 py-2 bg-white border border-gray-300
                    rounded-md text-sm font-medium text-gray-700
                    hover:bg-gray-50"
            >
                Cancel
            </button>

            <button
                type="button"
                wire:click="saveCategories"
                wire:loading.attr="disabled"
                wire:target="saveCategories"
                class="ml-3 px-4 py-2 bg-indigo-600
                    text-white rounded-md text-sm font-medium
                    hover:bg-indigo-700"
            >
                <span
                    wire:loading.remove
                    wire:target="saveCategories"
                >
                    Save Categories
                </span>

                <span
                    wire:loading
                    wire:target="saveCategories"
                >
                    Saving...
                </span>
            </button>

        </x-slot>
    </x-dialog-modal>
</div>