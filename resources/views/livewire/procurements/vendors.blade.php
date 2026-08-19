<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Vendors') }}
    </h2>
</x-slot>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Header --}}
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by vendor name, code, phone..."
                class="w-full sm:w-96 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                autocomplete="off"
            >
            <select
                wire:model.live="typeFilter"
                class="w-full sm:w-40 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">All Types</option>
                <option value="supplier">Supplier</option>
                <option value="customer">Customer</option>
            </select>
            <select
                wire:model.live="statusFilter"
                class="w-full sm:w-40 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <button
            type="button"
            wire:click="create"
            class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
        >
            Add Vendor
        </button>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Code
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Contact
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Phone
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($vendors as $vendor)
                        <tr>
                            {{-- Name --}}
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $vendor->name }}
                                </div>
                                @if ($vendor->legal_name)
                                    <div class="text-xs text-gray-500">
                                        {{ $vendor->legal_name }}
                                    </div>
                                @endif
                            </td>
                            {{-- Code --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono text-gray-600">
                                    {{ $vendor->code }}
                                </span>
                            </td>
                            {{-- Type --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($vendor->type === 'supplier')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Supplier
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Customer
                                    </span>
                                @endif
                            </td>
                            {{-- Contact --}}
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700">
                                    {{ $vendor->contact_person ?: '-' }}
                                </div>
                                @if ($vendor->email)
                                    <div class="text-xs text-gray-500">
                                        {{ $vendor->email }}
                                    </div>
                                @endif
                            </td>
                            {{-- Phone --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-700">
                                    {{ $vendor->phone ?: '-' }}
                                </span>
                            </td>
                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($vendor->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            {{-- Action --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <button
                                    type="button"
                                    wire:click="edit({{ $vendor->id }})"
                                    class="text-indigo-600 hover:text-indigo-900"
                                >
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    wire:click="deleteVendor({{ $vendor->id }})"
                                    wire:confirm="Are you sure you want to delete this vendor?"
                                    class="ml-3 text-red-600 hover:text-red-900"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="7"
                                class="px-6 py-10 text-center text-gray-500"
                            >
                                No vendors found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{-- Pagination --}}
    <div class="mt-4">
        {{ $vendors->links() }}
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
            Edit Vendor
        </x-slot>


        <x-slot name="content">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">


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
                wire:click="closeEditModal"
                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700"
            >
                Cancel
            </button>

            <button
                type="button"
                wire:click="updateVendor"
                wire:loading.attr="disabled"
                class="ml-3 px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium"
            >

                <span wire:loading.remove wire:target="updateVendor">
                    Update
                </span>

                <span wire:loading wire:target="updateVendor">
                    Updating...
                </span>

            </button>

        </x-slot>

    </x-dialog-modal>

</div>