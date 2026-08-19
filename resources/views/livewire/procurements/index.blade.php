<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Procurement Categories') }}
    </h2>
</x-slot>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Header --}}
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search categories..."
            class="w-full sm:w-96 rounded-md border-gray-300 shadow-sm
                focus:border-indigo-500 focus:ring-indigo-500"
            autocomplete="off"
        >

        <button
            type="button"
            wire:click="openCreateModal"
            class="inline-flex items-center justify-center px-4 py-2
                bg-indigo-600 border border-transparent rounded-md
                font-semibold text-xs text-white uppercase tracking-widest
                hover:bg-indigo-700"
        >
            Add Category
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

    {{-- Category Tree --}}
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">

        <div
            id="category-tree"
            class="divide-y divide-gray-100"
            x-data
            x-sort="$wire.reorderCategories(
                Array.from($el.children)
                    .map(el => Number(el.dataset.id)),
                null
            )"
        >

            @forelse ($categories as $category)

                @include(
                    'livewire.procurements.partials.category-row',
                    [
                        'category' => $category,
                        'level' => 0,
                        'number' => (string) ($loop->iteration),
                    ]
                )

            @empty

                <div class="px-6 py-10 text-center text-gray-500">
                    No categories found.
                </div>

            @endforelse

        </div>

    </div>

    {{-- Create Modal --}}
    <x-dialog-modal
        maxWidth="2xl"
        wire:model.live="showCreateModal"
    >

        <x-slot name="title">
            Add Category
        </x-slot>

        <x-slot name="content">

            <div class="space-y-5">

                <div>
                    <x-label value="Parent Category" />

                    <select
                        wire:model="parentId"
                        class="mt-1 block w-full rounded-md border-gray-300
                            shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">
                            Root Category
                        </option>

                        @foreach ($parentCategories as $parent)

                            <option value="{{ $parent->id }}">
                                {{ $parent->name }}
                            </option>

                        @endforeach

                    </select>

                    @error('parentId')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <x-label value="Name" />

                    <x-input
                        wire:model="name"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="e.g. IT Equipment"
                    />

                    @error('name')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <x-label value="Code" />

                    <x-input
                        wire:model="code"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="e.g. it-equipment"
                    />

                    @error('code')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <x-label value="Description" />

                    <textarea
                        wire:model="description"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300
                            shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Optional description..."
                    ></textarea>

                    @error('description')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="flex items-center gap-2">

                    <input
                        type="checkbox"
                        wire:model="isActive"
                        id="create-category-active"
                        class="rounded border-gray-300 text-indigo-600
                            focus:ring-indigo-500"
                    >

                    <label
                        for="create-category-active"
                        class="text-sm text-gray-700"
                    >
                        Active
                    </label>

                </div>

            </div>

        </x-slot>

        <x-slot name="footer">

            <button
                type="button"
                wire:click="$set('showCreateModal', false)"
                class="px-4 py-2 bg-white border border-gray-300
                    rounded-md text-sm font-medium text-gray-700"
            >
                Cancel
            </button>

            <button
                type="button"
                wire:click="createCategory"
                wire:loading.attr="disabled"
                class="ml-3 px-4 py-2 bg-indigo-600 text-white
                    rounded-md text-sm font-medium"
            >
                <span
                    wire:loading.remove
                    wire:target="createCategory"
                >
                    Create Category
                </span>

                <span
                    wire:loading
                    wire:target="createCategory"
                >
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
            Edit Category
        </x-slot>

        <x-slot name="content">

            <div class="space-y-5">

                <div>
                    <x-label value="Parent Category" />

                    <select
                        wire:model="parentId"
                        class="mt-1 block w-full rounded-md border-gray-300
                            shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">
                            Root Category
                        </option>

                        @foreach ($parentCategories as $parent)

                            <option value="{{ $parent->id }}">
                                {{ $parent->name }}
                            </option>

                        @endforeach

                    </select>

                    @error('parentId')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <x-label value="Name" />

                    <x-input
                        wire:model="name"
                        type="text"
                        class="mt-1 block w-full"
                    />

                    @error('name')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <x-label value="Code" />

                    <x-input
                        wire:model="code"
                        type="text"
                        class="mt-1 block w-full"
                    />

                    @error('code')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <x-label value="Description" />

                    <textarea
                        wire:model="description"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300
                            shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    ></textarea>

                    @error('description')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="flex items-center gap-2">

                    <input
                        type="checkbox"
                        wire:model="isActive"
                        id="edit-category-active"
                        class="rounded border-gray-300 text-indigo-600
                            focus:ring-indigo-500"
                    >

                    <label
                        for="edit-category-active"
                        class="text-sm text-gray-700"
                    >
                        Active
                    </label>

                </div>

            </div>

        </x-slot>

        <x-slot name="footer">

            <button
                type="button"
                wire:click="$set('showEditModal', false)"
                class="px-4 py-2 bg-white border border-gray-300
                    rounded-md text-sm font-medium text-gray-700"
            >
                Cancel
            </button>

            <button
                type="button"
                wire:click="updateCategory"
                wire:loading.attr="disabled"
                class="ml-3 px-4 py-2 bg-indigo-600 text-white
                    rounded-md text-sm font-medium"
            >
                <span
                    wire:loading.remove
                    wire:target="updateCategory"
                >
                    Save Changes
                </span>

                <span
                    wire:loading
                    wire:target="updateCategory"
                >
                    Saving...
                </span>
            </button>

        </x-slot>

    </x-dialog-modal>

</div>