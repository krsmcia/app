<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Employees') }}
    </h2>
</x-slot>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search by name, email or role..."
            class="w-full sm:w-96 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            autocomplete="off"
        >

        <button
            type="button"
            wire:click="$set('showCreateModal', true)"
            class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
        >
            Add Employee
        </button>

    </div>
    @if (session()->has('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Department
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Role
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $user->name }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    {{ $user->email }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($user->department)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                        {{ $user->department->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">
                                        No Department
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($user->roles as $role)
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-sm text-gray-400">
                                            No Role
                                        </span>
                                    @endforelse
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <button
                                    type="button"
                                    wire:click="editEmployee({{ $user->id }})"
                                    class="font-medium text-indigo-600 hover:text-indigo-900"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    wire:click="deleteEmployee({{ $user->id }})"
                                    wire:confirm="Are you sure you want to delete this employee?"
                                    class="ml-3 font-medium text-red-600 hover:text-red-900"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                No employees found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        {{-- Mobile Cards --}}
        <div class="space-y-3 md:hidden">
            @forelse ($users as $user)
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">

                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-gray-900">
                                {{ $user->name }}
                            </div>

                            <div class="mt-1 truncate text-xs text-gray-500">
                                {{ $user->email }}
                            </div>
                        </div>
                        <div class="">
                            <button
                                type="button"
                                wire:click="editEmployee({{ $user->id }})"
                                class="shrink-0 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-600 hover:bg-indigo-100"
                            >
                                Edit
                            </button>
                            <button
                                type="button"
                                wire:click="deleteEmployee({{ $user->id }})"
                                wire:confirm="Are you sure you want to delete this employee?"
                                class="ml-2 shrink-0 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100"
                            >
                                Delete
                            </button>
                        </div>
                        
                    </div>


                    {{-- Information --}}
                    <div class="mt-4 grid grid-cols-2 gap-4 border-t border-gray-100 pt-3">

                        {{-- Department --}}
                        <div>
                            <div class="mb-1 text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                Department
                            </div>

                            @if ($user->department)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">
                                    {{ $user->department->name }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">
                                    No Department
                                </span>
                            @endif
                        </div>


                        {{-- Roles --}}
                        <div>
                            <div class="mb-1 text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                Role
                            </div>

                            <div class="flex flex-wrap gap-1">
                                @forelse ($user->roles as $role)
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800">
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400">
                                        No Role
                                    </span>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>

            @empty

                <div class="rounded-xl border border-dashed border-gray-300 bg-white px-4 py-10 text-center text-sm text-gray-500">
                    No employees found.
                </div>

            @endforelse
        </div>

    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
    <x-dialog-modal maxWidth="7xl" wire:model.live="showCreateModal">
        <x-slot name="title">
            Add Employees
        </x-slot>

        <x-slot name="content">

            <div class="space-y-6">

                <div class="space-y-3">

                    @foreach ($employees as $index => $employee)

                        <div
                            wire:key="employee-row-{{ $index }}"
                            class="rounded-lg border border-gray-200 bg-gray-50 p-3"
                        >

                            {{-- Mobile title --}}
                            <div class="flex items-center justify-between mb-3 lg:hidden">
                                <span class="text-sm font-semibold text-gray-700">
                                    Employee #{{ $index + 1 }}
                                </span>

                                @if (count($employees) > 1)
                                    <button
                                        type="button"
                                        wire:click="removeEmployeeRow({{ $index }})"
                                        class="text-sm text-red-600 hover:text-red-800"
                                    >
                                        Remove
                                    </button>
                                @endif
                            </div>

                            {{-- Name / Email / Password / Role / Remove --}}
                            <div class="
                                grid
                                grid-cols-1
                                gap-3
                                lg:grid-cols-[1.1fr_1.4fr_1.1fr_0.8fr_auto]
                                lg:items-start
                            ">

                                {{-- Name --}}
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-500 lg:hidden">
                                        Name
                                    </label>

                                    <input
                                        type="text"
                                        wire:model="employees.{{ $index }}.name"
                                        placeholder="Name"
                                        class="block w-full rounded-md border-gray-300 text-sm shadow-sm"
                                    >

                                    @error("employees.$index.name")
                                        <p class="mt-1 text-xs text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-500 lg:hidden">
                                        Email
                                    </label>

                                    <input
                                        type="email"
                                        wire:model="employees.{{ $index }}.email"
                                        placeholder="Email"
                                        class="block w-full rounded-md border-gray-300 text-sm shadow-sm"
                                    >

                                    @error("employees.$index.email")
                                        <p class="mt-1 text-xs text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Password --}}
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-500 lg:hidden">
                                        Password
                                    </label>

                                    <input
                                        type="password"
                                        wire:model="employees.{{ $index }}.password"
                                        placeholder="Password"
                                        class="block w-full rounded-md border-gray-300 text-sm shadow-sm"
                                    >

                                    @error("employees.$index.password")
                                        <p class="mt-1 text-xs text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Role --}}
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-500 lg:hidden">
                                        Role
                                    </label>

                                    <select
                                        wire:model="employees.{{ $index }}.role"
                                        class="block w-full rounded-md border-gray-300 text-sm shadow-sm"
                                    >
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error("employees.$index.role")
                                        <p class="mt-1 text-xs text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Remove --}}
                                <div class="hidden lg:flex items-center justify-center pt-1">

                                    @if (count($employees) > 1)
                                        <button
                                            type="button"
                                            wire:click="removeEmployeeRow({{ $index }})"
                                            class="p-2 text-gray-400 hover:text-red-600"
                                            title="Remove employee"
                                        >
                                            ✕
                                        </button>
                                    @endif

                                </div>

                            </div>

                            {{-- Departments --}}
                            <div class="mt-4 border-t border-gray-200 pt-3">

                                <label class="mb-2 block text-xs font-medium text-gray-500">
                                    Departments
                                </label>

                                <div class="flex flex-wrap gap-x-4 gap-y-2">

                                    @foreach ($departments as $department)

                                        <label class="inline-flex items-center gap-1.5 text-sm text-gray-700">
                                            <input
                                                type="checkbox"
                                                value="{{ $department->id }}"
                                                wire:model="employees.{{ $index }}.department_ids"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            >

                                            <span>
                                                {{ $department->name }}
                                            </span>
                                        </label>

                                    @endforeach

                                </div>

                                @error("employees.$index.department_ids")
                                    <p class="mt-1 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                        </div>

                    @endforeach

                </div>

                <button
                    type="button"
                    wire:click="addEmployeeRow"
                    class="w-full rounded-md border border-dashed border-gray-300 px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50"
                >
                    + Add Another Employee
                </button>

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
                wire:click="createEmployees"
                wire:loading.attr="disabled"
                class="ml-3 px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium"
            >
                <span wire:loading.remove wire:target="createEmployees">
                    Create Employees
                </span>

                <span wire:loading wire:target="createEmployees">
                    Creating...
                </span>
            </button>

        </x-slot>

    </x-dialog-modal>
    <x-dialog-modal maxWidth="2xl" wire:model.live="showEditModal">
        <x-slot name="title">
            Edit Employee
        </x-slot>
        <x-slot name="content">
            <div class="space-y-5">
                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Name
                    </label>
                    <input
                        type="text"
                        wire:model="editName"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('editName')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>
                    <input
                        type="email"
                        wire:model="editEmail"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('editEmail')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                {{-- Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <input
                        type="password"
                        wire:model="editPassword"
                        placeholder="Leave blank to keep current password"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('editPassword')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        Leave blank if you don't want to change the password.
                    </p>
                </div>
                {{-- Role --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Role
                    </label>
                    <select
                        wire:model="editRole"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('editRole')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                {{-- Departments --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Departments
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($departments as $department)
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input
                                    type="checkbox"
                                    value="{{ $department->id }}"
                                    wire:model="editDepartmentIds"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >

                                <span>
                                    {{ $department->name }}
                                </span>

                            </label>

                        @endforeach

                    </div>

                    @error('editDepartmentIds')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

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
                wire:click="updateEmployee"
                wire:loading.attr="disabled"
                class="ml-3 px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium"
            >

                <span wire:loading.remove wire:target="updateEmployee">
                    Update Employee
                </span>

                <span wire:loading wire:target="updateEmployee">
                    Updating...
                </span>

            </button>

        </x-slot>

    </x-dialog-modal>
</div>