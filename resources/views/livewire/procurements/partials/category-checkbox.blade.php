<div
    wire:key="category-tree-{{ $category->id }}"
    class="{{ $level > 0 ? 'ml-5 border-l border-gray-200 pl-4' : '' }}"
>
    <label
        class="flex cursor-pointer items-center gap-3 rounded-md px-2 py-2 hover:bg-gray-50"
    >
        <input
            type="checkbox"
            value="{{ $category->id }}"
            wire:model.live="selectedCategories"
            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
        >

        <div class="min-w-0 flex-1">
            <div class="text-sm font-medium text-gray-800">
                {{ $category->name }}
            </div>

            <div class="text-xs text-gray-400">
                {{ $category->code }}
            </div>
        </div>
    </label>

    @foreach ($category->childrenRecursive as $child)
        @include(
            'livewire.procurements.partials.category-checkbox',
            [
                'category' => $child,
                'level' => $level + 1,
            ]
        )
    @endforeach
</div>