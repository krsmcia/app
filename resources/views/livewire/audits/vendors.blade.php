<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Vendors') }}
    </h2>
</x-slot>
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6">
    {{-- Header --}}
    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-row gap-2 sm:gap-3">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search vendor..."
                class="w-full lg:w-96 rounded-lg border-gray-300 shadow-sm
                    focus:border-indigo-500 focus:ring-indigo-500"
                autocomplete="off"
            >
            <select
                wire:model.live="typeFilter"
                class="w-full lg:w-40 rounded-lg border-gray-300 shadow-sm
                    focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">All Types</option>
                <option value="supplier">Supplier</option>
                <option value="customer">Customer</option>
            </select>
            <select
                wire:model.live="statusFilter"
                class="w-full lg:w-40 rounded-lg border-gray-300 shadow-sm
                    focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>
    {{-- Success --}}
    @if (session()->has('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-3 sm:p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif
    {{-- Error --}}
    @if (session()->has('error'))
        <div class="mb-4 rounded-lg bg-red-50 p-3 sm:p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif
    {{-- ========================================================= --}}
    {{-- Mobile --}}
    {{-- ========================================================= --}}
    <div class="space-y-2 sm:hidden">
        @forelse ($vendors as $vendor)
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                {{-- Top --}}
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="font-semibold text-gray-900 truncate">
                            <button
                                x-on:click="$dispatch('open-vendor', {
                                    vendorId: {{ $vendor->id }}
                                })"
                                wire:loading.attr="disabled"
                            >
                                {{ $vendor->name }}
                            </button>
                        </div>
                        @if ($vendor->legal_name)
                            <div class="mt-0.5 text-xs text-gray-500 truncate">
                                {{ $vendor->legal_name }}
                            </div>
                        @endif
                        <div class="mt-1 font-mono text-xs text-gray-400">
                            {{ $vendor->code }}
                        </div>
                    </div>
                    {{-- Status --}}
                    @if ($vendor->is_active)
                        <span class="shrink-0 inline-flex items-center rounded-full
                            bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">
                            Active
                        </span>
                    @else
                        <span class="shrink-0 inline-flex items-center rounded-full
                            bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500">
                            Inactive
                        </span>
                    @endif
                </div>
                {{-- Details --}}
                <div class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2 border-t border-gray-100 pt-3">
                    {{-- Type --}}
                    <div>
                        <div class="text-[10px] font-medium uppercase tracking-wide text-gray-400">
                            Type
                        </div>
                        @if ($vendor->type === 'supplier')
                            <span class="mt-0.5 inline-flex items-center rounded-full
                                bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">
                                Supplier
                            </span>
                        @else
                            <span class="mt-0.5 inline-flex items-center rounded-full
                                bg-purple-100 px-2 py-0.5 text-xs font-medium text-purple-700">
                                Customer
                            </span>
                        @endif
                    </div>
                    {{-- Phone --}}
                    <div>
                        <div class="text-[10px] font-medium uppercase tracking-wide text-gray-400">
                            Phone
                        </div>

                        <div class="mt-0.5 truncate text-sm text-gray-700">
                            {{ $vendor->phone ?: '-' }}
                        </div>
                    </div>
                    {{-- Contact --}}
                    <div class="min-w-0">
                        <div class="text-[10px] font-medium uppercase tracking-wide text-gray-400">
                            Contact
                        </div>
                        <div class="mt-0.5 truncate text-sm text-gray-700">
                            {{ $vendor->contact_person ?: '-' }}
                        </div>
                    </div>
                    {{-- Email --}}
                    <div class="min-w-0">
                        <div class="text-[10px] font-medium uppercase tracking-wide text-gray-400">
                            Email
                        </div>
                        <div class="mt-0.5 truncate text-sm text-gray-700">
                            {{ $vendor->email ?: '-' }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-gray-200 bg-white px-4 py-10 text-center text-sm text-gray-500">
                No vendors found.
            </div>
        @endforelse
    </div>
    {{-- ========================================================= --}}
    {{-- Desktop --}}
    {{-- ========================================================= --}}
    <div class="hidden sm:block overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
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
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($vendors as $vendor)
                        <tr class="hover:bg-gray-50">
                            {{-- Name --}}
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <button
                                        x-on:click="$dispatch('open-vendor', {
                                            vendorId: {{ $vendor->id }}
                                        })"
                                        wire:loading.attr="disabled"
                                    >
                                        {{ $vendor->name }}
                                    </button>
                                </div>
                                @if ($vendor->legal_name)
                                    <div class="text-xs text-gray-500">
                                        {{ $vendor->legal_name }}
                                    </div>
                                @endif
                            </td>
                            {{-- Code --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-sm text-gray-600">
                                    {{ $vendor->code }}
                                </span>
                            </td>
                            {{-- Type --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($vendor->type === 'supplier')
                                    <span class="inline-flex items-center rounded-full
                                        bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                        Supplier
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full
                                        bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800">
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
                                    <span class="inline-flex items-center rounded-full
                                        bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full
                                        bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="6"
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
    <livewire:audits.modals.vendors />
</div>