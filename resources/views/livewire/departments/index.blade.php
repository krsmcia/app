<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Departments') }}
    </h2>
</x-slot>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Header --}}
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search by department name or code..."
            class="w-full sm:w-96 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            autocomplete="off"
        >

        <button
            type="button"
            wire:click="$set('showCreateModal', true)"
            class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
        >
            Add Department
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
                        Employees
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

                @forelse ($departments as $department)

                    <tr>

                        {{-- Name --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $department->name }}
                            </div>
                        </td>

                        {{-- Code --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-mono text-gray-600">
                                {{ $department->code }}
                            </span>
                        </td>

                        {{-- Employees --}}
                        <td class="px-6 py-4 whitespace-nowrap">

                            <span class="text-sm text-gray-700">
                                {{ $department->users_count }}
                            </span>

                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4 whitespace-nowrap">

                            @if ($department->is_active)

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
                                wire:click="editDepartment({{ $department->id }})"
                                class="text-indigo-600 hover:text-indigo-900"
                            >
                                Edit
                            </button>

                            @if ($department->users_count === 0)

                                <button
                                    type="button"
                                    wire:click="deleteDepartment({{ $department->id }})"
                                    wire:confirm="Are you sure you want to delete this department?"
                                    class="ml-3 text-red-600 hover:text-red-900"
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
                            class="px-6 py-10 text-center text-gray-500"
                        >
                            No departments found.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

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