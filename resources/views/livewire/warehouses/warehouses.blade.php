<div class="max-w-3xl mx-auto px-4 pt-6 pb-28">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    Warehouses
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Manage your warehouse locations and availability.
                </p>
            </div>
            <button
                wire:click="create"
                type="button"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5
                    text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700
                    focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>

                Add Warehouse
            </button>
        </div>


        {{-- Filters --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row">

                {{-- Search --}}
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg
                            class="h-5 w-5 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"
                            />
                        </svg>
                    </div>

                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Search warehouses..."
                        class="w-full rounded-xl border-gray-300 py-2.5 pl-10 pr-4 text-sm
                            focus:border-emerald-500 focus:ring-emerald-500"
                    >
                </div>

                {{-- Status --}}
                <select
                    wire:model.live="statusFilter"
                    class="rounded-xl border-gray-300 py-2.5 text-sm
                        focus:border-emerald-500 focus:ring-emerald-500 sm:w-40"
                >
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>

            </div>
        </div>


        {{-- Desktop Table --}}
        <div class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm md:block">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Code
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Warehouse
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Description
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Status
                        </th>

                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse ($warehouses as $warehouse)

                        <tr wire:key="warehouse-{{ $warehouse->id }}" class="hover:bg-gray-50">

                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="font-mono text-sm font-medium text-gray-900">
                                    {{ $warehouse->code }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">
                                    {{ $warehouse->name }}
                                </div>
                            </td>

                            <td class="max-w-md px-6 py-4">
                                <p class="truncate text-sm text-gray-500">
                                    {{ $warehouse->description ?: '—' }}
                                </p>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">

                                <button
                                    wire:click="toggleStatus({{ $warehouse->id }})"
                                    type="button"
                                >
                                    @if ($warehouse->is_active)
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                            Inactive
                                        </span>
                                    @endif
                                </button>

                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-right">

                                <div class="flex justify-end gap-2">

                                    <button
                                        wire:click="edit({{ $warehouse->id }})"
                                        type="button"
                                        class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-900"
                                        title="Edit"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m16.862 4.487 1.65-1.65a2.121 2.121 0 0 1 3 3l-1.65 1.65m-3-3L7 13.7 6 18l4.3-1 9.562-9.513m-3-3L21 7"
                                            />
                                        </svg>
                                    </button>

                                    <button
                                        wire:click="delete({{ $warehouse->id }})"
                                        wire:confirm="Are you sure you want to delete this warehouse?"
                                        type="button"
                                        class="rounded-lg p-2 text-gray-500 transition hover:bg-red-50 hover:text-red-600"
                                        title="Delete"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m19 7-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 0 1 2-2h2a1 1 0 0 1 2 2v3m-9 0h12"
                                            />
                                        </svg>
                                    </button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">

                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100">
                                    <svg
                                        class="h-6 w-6 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 7h18M5 7v13h14V7M8 7V4h8v3"
                                        />
                                    </svg>
                                </div>

                                <h3 class="mt-3 text-sm font-medium text-gray-900">
                                    No warehouses found
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Create your first warehouse to get started.
                                </p>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Mobile Cards --}}
        <div class="space-y-3 md:hidden">

            @forelse ($warehouses as $warehouse)

                <div
                    wire:key="mobile-warehouse-{{ $warehouse->id }}"
                    class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm"
                >

                    <div class="flex items-start justify-between gap-4">

                        <div class="min-w-0">

                            <div class="flex items-center gap-2">

                                <span class="font-mono text-xs font-medium text-gray-500">
                                    {{ $warehouse->code }}
                                </span>

                                @if ($warehouse->is_active)
                                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-medium text-emerald-700">
                                        Active
                                    </span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-600">
                                        Inactive
                                    </span>
                                @endif

                            </div>

                            <h3 class="mt-1 truncate font-semibold text-gray-900">
                                {{ $warehouse->name }}
                            </h3>

                        </div>

                        <div class="flex shrink-0 gap-1">

                            <button
                                wire:click="edit({{ $warehouse->id }})"
                                type="button"
                                class="rounded-lg p-2 text-gray-500 hover:bg-gray-100"
                            >
                                Edit
                            </button>

                            <button
                                wire:click="delete({{ $warehouse->id }})"
                                wire:confirm="Are you sure you want to delete this warehouse?"
                                type="button"
                                class="rounded-lg p-2 text-gray-500 hover:bg-red-50 hover:text-red-600"
                            >
                                Delete
                            </button>

                        </div>

                    </div>

                    @if ($warehouse->description)

                        <p class="mt-3 text-sm text-gray-500">
                            {{ $warehouse->description }}
                        </p>

                    @endif

                </div>

            @empty

                <div class="rounded-2xl border border-gray-200 bg-white px-6 py-12 text-center">
                    <p class="text-sm text-gray-500">
                        No warehouses found.
                    </p>
                </div>

            @endforelse

        </div>


        {{-- Pagination --}}
        @if ($warehouses->hasPages())
            <div>
                {{ $warehouses->links() }}
            </div>
        @endif


        {{-- Modal --}}
        <x-dialog-modal wire:model.live="showModal">
            <x-slot name="title">
                {{ $editing ? 'Edit Warehouse' : 'Add Warehouse' }}
            </x-slot>

            <x-slot name="content">

                <div class="space-y-5">

                    {{-- Code --}}
                    <div>
                        <x-label for="code" value="Code" />

                        <x-input
                            id="code"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="e.g. wh-main"
                            wire:model="code"
                        />

                        <x-input-error for="code" class="mt-2" />
                    </div>


                    {{-- Name --}}
                    <div>
                        <x-label for="name" value="Name" />

                        <x-input
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="Main Warehouse"
                            wire:model="name"
                        />

                        <x-input-error for="name" class="mt-2" />
                    </div>


                    {{-- Description --}}
                    <div>
                        <x-label for="description" value="Description" />

                        <textarea
                            id="description"
                            rows="3"
                            wire:model="description"
                            placeholder="Optional description..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                focus:border-indigo-500 focus:ring-indigo-500"
                        ></textarea>

                        <x-input-error for="description" class="mt-2" />
                    </div>


                    {{-- Active --}}
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 p-4">

                        <div>
                            <div class="text-sm font-medium text-gray-900">
                                Active
                            </div>

                            <div class="mt-1 text-sm text-gray-500">
                                Allow this warehouse to be used for inventory operations.
                            </div>
                        </div>

                        <label class="relative inline-flex cursor-pointer items-center">
                            <input
                                type="checkbox"
                                wire:model="is_active"
                                class="peer sr-only"
                            >

                            <div
                                class="h-6 w-11 rounded-full bg-gray-200
                                    after:absolute after:start-[2px] after:top-[2px]
                                    after:h-5 after:w-5 after:rounded-full
                                    after:border after:border-gray-300 after:bg-white
                                    after:transition-all after:content-['']
                                    peer-checked:bg-emerald-600
                                    peer-checked:after:translate-x-full
                                    peer-checked:after:border-white
                                    peer-focus:outline-none peer-focus:ring-4
                                    peer-focus:ring-emerald-300"
                            ></div>
                        </label>

                    </div>

                </div>

            </x-slot>


            <x-slot name="footer">

                <x-secondary-button
                    wire:click="closeModal"
                    wire:loading.attr="disabled"
                >
                    Cancel
                </x-secondary-button>

                <x-button
                    class="ms-3"
                    wire:click="save"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>
                        {{ $editing ? 'Update Warehouse' : 'Create Warehouse' }}
                    </span>

                    <span wire:loading>
                        Saving...
                    </span>
                </x-button>

            </x-slot>

        </x-dialog-modal>

    </div>
</div>