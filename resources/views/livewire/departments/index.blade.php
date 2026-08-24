<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Departments') }}
    </h2>
</x-slot>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Header --}}
    <div class="mb-4 space-y-3">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            {{-- Search --}}
            <div class="w-full sm:max-w-md">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search department..."
                    class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-sm shadow-sm
                        focus:border-indigo-500 focus:ring-indigo-500"
                    autocomplete="off"
                >
            </div>

            {{-- Add --}}
            <button
                type="button"
                wire:click="$set('showCreateModal', true)"
                class="inline-flex w-full items-center justify-center rounded-lg
                    bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white
                    shadow-sm hover:bg-indigo-700
                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                    sm:w-auto"
            >
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>

                Add Department
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

    {{-- Department List --}}
    <div class="overflow-hidden rounded-xl bg-white shadow-sm">

        {{-- =========================================================
            Mobile: Card View
            ========================================================= --}}
        <div class="divide-y divide-gray-100 md:hidden">

            @forelse ($departments as $department)

                <div class="p-4">

                    {{-- Top --}}
                    <div class="flex items-start justify-between gap-3">

                        <div class="min-w-0">
                            <h3 class="truncate text-sm font-semibold text-gray-900">
                                {{ $department->name }}
                            </h3>

                            <p class="mt-1 font-mono text-xs text-gray-500">
                                {{ $department->code }}
                            </p>
                        </div>

                        {{-- Status --}}
                        @if ($department->is_active)
                            <span class="shrink-0 inline-flex items-center rounded-full
                                        bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                Active
                            </span>
                        @else
                            <span class="shrink-0 inline-flex items-center rounded-full
                                        bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                Inactive
                            </span>
                        @endif

                    </div>

                    {{-- Info --}}
                    <div class="mt-4 flex items-center justify-between">

                        <div>
                            <p class="text-xs text-gray-500">
                                Employees
                            </p>

                            <p class="mt-0.5 text-sm font-medium text-gray-900">
                                {{ $department->users_count }}
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-3">

                            <button
                                type="button"
                                wire:click="editDepartment({{ $department->id }})"
                                class="rounded-md px-3 py-2 text-sm font-medium text-indigo-600
                                    hover:bg-indigo-50"
                            >
                                Edit
                            </button>

                            @if ($department->users_count === 0)

                                <button
                                    type="button"
                                    wire:click="deleteDepartment({{ $department->id }})"
                                    wire:confirm="Are you sure you want to delete this department?"
                                    class="rounded-md px-3 py-2 text-sm font-medium text-red-600
                                        hover:bg-red-50"
                                >
                                    Delete
                                </button>

                            @endif

                        </div>

                    </div>

                </div>

            @empty

                <div class="px-4 py-12 text-center text-sm text-gray-500">
                    No departments found.
                </div>

            @endforelse

        </div>


        {{-- =========================================================
            Desktop: Table View
            ========================================================= --}}
        <div class="hidden overflow-x-auto md:block">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>

                        <th class="px-6 py-3 text-left text-xs font-medium
                                uppercase tracking-wider text-gray-500">
                            Name
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium
                                uppercase tracking-wider text-gray-500">
                            Code
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium
                                uppercase tracking-wider text-gray-500">
                            Employees
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium
                                uppercase tracking-wider text-gray-500">
                            Status
                        </th>

                        <th class="px-6 py-3 text-right text-xs font-medium
                                uppercase tracking-wider text-gray-500">
                            Action
                        </th>

                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">

                    @forelse ($departments as $department)

                        <tr class="hover:bg-gray-50">

                            {{-- Name --}}
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $department->name }}
                                </div>
                            </td>

                            {{-- Code --}}
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="font-mono text-sm text-gray-600">
                                    {{ $department->code }}
                                </span>
                            </td>

                            {{-- Employees --}}
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="text-sm text-gray-700">
                                    {{ $department->users_count }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="whitespace-nowrap px-6 py-4">

                                @if ($department->is_active)

                                    <span class="inline-flex items-center rounded-full
                                                bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                        Active
                                    </span>

                                @else

                                    <span class="inline-flex items-center rounded-full
                                                bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                        Inactive
                                    </span>

                                @endif

                            </td>

                            {{-- Action --}}
                            <td class="whitespace-nowrap px-6 py-4 text-right">

                                <button
                                    type="button"
                                    wire:click="editDepartment({{ $department->id }})"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-900"
                                >
                                    Edit
                                </button>

                                @if ($department->users_count === 0)

                                    <button
                                        type="button"
                                        wire:click="deleteDepartment({{ $department->id }})"
                                        wire:confirm="Are you sure you want to delete this department?"
                                        class="ml-4 text-sm font-medium text-red-600 hover:text-red-900"
                                    >
                                        Delete
                                    </button>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="5"
                                class="px-6 py-12 text-center text-sm text-gray-500"
                            >
                                No departments found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $departments->links() }}
    </div>


    {{-- Create Modal --}}
    <x-dialog-modal
        maxWidth="md"
        wire:model.live="showCreateModal"
    >

        <x-slot name="title">
            Add Department
        </x-slot>

        <x-slot name="content">

            <div class="space-y-4">

                {{-- Name --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Name
                    </label>

                    <input
                        type="text"
                        wire:model="name"
                        placeholder="Department name"
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
                        placeholder="Department code"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >

                    @error('code')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Active --}}
                <div>
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
                wire:click="$set('showCreateModal', false)"
                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700"
            >
                Cancel
            </button>

            <button
                type="button"
                wire:click="createDepartment"
                wire:loading.attr="disabled"
                class="ml-3 px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium"
            >
                <span wire:loading.remove wire:target="createDepartment">
                    Create
                </span>

                <span wire:loading wire:target="createDepartment">
                    Creating...
                </span>
            </button>

        </x-slot>

    </x-dialog-modal>


    {{-- Edit Modal --}}
    <x-dialog-modal
        maxWidth="md"
        wire:model.live="showEditModal"
    >

        <x-slot name="title">
            Edit Department
        </x-slot>

        <x-slot name="content">

            <div class="space-y-4">

                {{-- Name --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Name
                    </label>

                    <input
                        type="text"
                        wire:model="editName"
                        placeholder="Department name"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >

                    @error('editName')
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
                        wire:model="editCode"
                        placeholder="Department code"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >

                    @error('editCode')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Active --}}
                <div>
                    <label class="inline-flex items-center gap-2">

                        <input
                            type="checkbox"
                            wire:model="editIsActive"
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
                wire:click="$set('showEditModal', false)"
                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700"
            >
                Cancel
            </button>

            <button
                type="button"
                wire:click="updateDepartment"
                wire:loading.attr="disabled"
                class="ml-3 px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium"
            >
                <span wire:loading.remove wire:target="updateDepartment">
                    Update
                </span>

                <span wire:loading wire:target="updateDepartment">
                    Updating...
                </span>
            </button>

        </x-slot>

    </x-dialog-modal>

</div>