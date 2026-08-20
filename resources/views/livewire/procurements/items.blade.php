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


        <div class="flex gap-2">

            <button
                type="button"
                wire:click="openImportModal"
                class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
            >
                Import Excel
            </button>

            <button
                type="button"
                wire:click="create"
                class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
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
    {{-- Table --}}
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>

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


                        <th
                            wire:click="sortBy('unit')"
                            class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                        >
                            Unit
                        </th>


                        <th
                            wire:click="sortBy('brand')"
                            class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                        >
                            Brand
                        </th>


                        <th
                            wire:click="sortBy('color')"
                            class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                        >
                            Color
                        </th>


                        <th
                            wire:click="sortBy('size')"
                            class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                        >
                            Size
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


                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody class="bg-white divide-y divide-gray-200">

                    @forelse ($items as $item)

                        <tr>

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


                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-700">
                                    {{ $item->unit }}
                                </span>
                            </td>


                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-700">
                                    {{ $item->brand ?: '-' }}
                                </span>
                            </td>


                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-700">
                                    {{ $item->color ?: '-' }}
                                </span>
                            </td>


                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-700">
                                    {{ $item->size ?: '-' }}
                                </span>
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
                                    wire:click="edit({{ $item->id }})"
                                    class="text-indigo-600 hover:text-indigo-900"
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

                            <td
                                colspan="9"
                                class="px-6 py-10 text-center text-gray-500"
                            >
                                No items found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    <div class="mt-4">
        {{ $items->links() }}
    </div>
    {{-- Create Modal --}}
    <x-dialog-modal
        maxWidth="2xl"
        wire:model.live="showCreateModal"
    >
        <x-slot name="title">
            Add Vendor
        </x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">

                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Product Images
                    </label>

                    <input
                        type="file"
                        wire:model="images"
                        multiple
                        accept="image/jpeg,image/png,image/webp"
                        class="block w-full text-sm text-gray-700
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-semibold
                            file:bg-indigo-50 file:text-indigo-700
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

                    @if ($images)
                        <div class="mt-4 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">

                            @foreach ($images as $index => $image)
                                <div class="relative">

                                    <img
                                        src="{{ $image->temporaryUrl() }}"
                                        class="h-24 w-24 rounded-lg object-cover border"
                                    >

                                    @if ($index === 0)
                                        <span class="absolute bottom-1 left-1 rounded bg-indigo-600 px-1.5 py-0.5 text-[10px] text-white">
                                            Primary
                                        </span>
                                    @endif

                                </div>
                            @endforeach

                        </div>
                    @endif

                </div>
                {{-- Name --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Name
                    </label>
                    <input
                        type="text"
                        wire:model="name"
                        placeholder="Vendor name"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                {{-- Code --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Code
                    </label>
                    <input
                        type="text"
                        wire:model="code"
                        placeholder="Vendor code"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('code')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                {{-- Legal Name --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Legal Name
                    </label>
                    <input
                        type="text"
                        wire:model="legalName"
                        placeholder="Legal name"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('legalName')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                {{-- Type --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Type
                    </label>
                    <select
                        wire:model="type"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="supplier">Supplier</option>
                        <option value="customer">Customer</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                {{-- Contact Person --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Contact Person
                    </label>
                    <input
                        type="text"
                        wire:model="contactPerson"
                        placeholder="Contact person"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('contactPerson')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                {{-- Email --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Email
                    </label>
                    <input
                        type="email"
                        wire:model="email"
                        placeholder="Email address"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                {{-- Phone --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Phone
                    </label>
                    <input
                        type="text"
                        wire:model="phone"
                        placeholder="Phone number"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                {{-- Website --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Website
                    </label>
                    <input
                        type="text"
                        wire:model="website"
                        placeholder="Website"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('website')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                {{-- Tax Number --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Tax Number
                    </label>
                    <input
                        type="text"
                        wire:model="taxNumber"
                        placeholder="Tax number"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('taxNumber')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                {{-- Payment Terms --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Payment Terms
                    </label>
                    <input
                        type="text"
                        wire:model="paymentTerms"
                        placeholder="e.g. 30 Days"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('paymentTerms')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                {{-- Address --}}
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Address
                    </label>
                    <textarea
                        wire:model="address"
                        rows="2"
                        placeholder="Vendor address"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    ></textarea>
                    @error('address')
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
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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

        </x-slot>


        <x-slot name="footer">

            <button
                type="button"
                wire:click="closeCreateModal"
                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700"
            >
                Cancel
            </button>

            <button
                type="button"
                wire:click="createVendor"
                wire:loading.attr="disabled"
                class="ml-3 px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium"
            >

                <span wire:loading.remove wire:target="createVendor">
                    Create
                </span>

                <span wire:loading wire:target="createVendor">
                    Creating...
                </span>

            </button>

        </x-slot>

    </x-dialog-modal>


    {{-- Edit Modal --}}
    <x-dialog-modal
        maxWidth="2xl"
        wire:model.live="showEditModal"
    >

        <x-slot name="title">
            Edit Item
        </x-slot>

        <x-slot name="content">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Images --}}
                <div class="md:col-span-2">

                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Product Images
                    </label>

                    {{-- Existing Images --}}
                    @if (count($existingImages) > 0)

                        <div class="mb-4">

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
                                            class="h-28 w-full rounded-lg object-cover border
                                                {{ $image['is_primary']
                                                    ? 'border-2 border-indigo-500'
                                                    : 'border-gray-200' }}"
                                        >

                                        {{-- Primary --}}
                                        @if ($image['is_primary'])

                                            <span
                                                class="absolute top-1 left-1
                                                    rounded bg-indigo-600
                                                    px-2 py-0.5
                                                    text-[10px] font-medium
                                                    text-white"
                                            >
                                                Primary
                                            </span>

                                        @else

                                            <button
                                                type="button"
                                                wire:click="setPrimaryImage({{ $image['id'] }})"
                                                class="absolute top-1 left-1
                                                    rounded bg-white/90
                                                    px-2 py-0.5
                                                    text-[10px] font-medium
                                                    text-gray-700
                                                    opacity-0 group-hover:opacity-100
                                                    transition"
                                            >
                                                Set Primary
                                            </button>

                                        @endif

                                        {{-- Delete --}}
                                        <button
                                            type="button"
                                            wire:click="deleteImage({{ $image['id'] }})"
                                            wire:confirm="Are you sure you want to delete this image?"
                                            class="absolute top-1 right-1
                                                h-6 w-6
                                                rounded-full
                                                bg-red-600
                                                text-white
                                                text-xs
                                                flex items-center justify-center
                                                opacity-0
                                                group-hover:opacity-100
                                                transition"
                                        >
                                            ×
                                        </button>

                                    </div>

                                @endforeach

                            </div>

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
                                            wire:key="new-image-{{ $index }}"
                                            class="relative"
                                        >

                                            <img
                                                src="{{ $image->temporaryUrl() }}"
                                                class="h-28 w-full rounded-lg object-cover border border-gray-200"
                                            >

                                            @if ($index === 0 && count($existingImages) === 0)

                                                <span
                                                    class="absolute top-1 left-1
                                                        rounded bg-indigo-600
                                                        px-2 py-0.5
                                                        text-[10px]
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


                {{-- SKU --}}
                <div>

                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        SKU
                    </label>

                    <input
                        type="text"
                        wire:model="sku"
                        placeholder="SKU"
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
                        Name
                    </label>

                    <input
                        type="text"
                        wire:model="name"
                        placeholder="Product name"
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
                            class="rounded border-gray-300 text-indigo-600
                                focus:ring-indigo-500"
                        >

                        <span class="text-sm text-gray-700">
                            Active
                        </span>

                    </label>

                </div>

            </div>

        </x-slot>


        <x-slot name="footer">

            <button
                type="button"
                wire:click="closeEditModal"
                class="px-4 py-2 bg-white border border-gray-300 rounded-md
                    text-sm font-medium text-gray-700"
            >
                Cancel
            </button>

            <button
                type="button"
                wire:click="updateItem"
                wire:loading.attr="disabled"
                wire:target="updateItem"
                class="ml-3 px-4 py-2 bg-indigo-600 text-white rounded-md
                    text-sm font-medium"
            >

                <span wire:loading.remove wire:target="updateItem">
                    Update
                </span>

                <span wire:loading wire:target="updateItem">
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
</div>