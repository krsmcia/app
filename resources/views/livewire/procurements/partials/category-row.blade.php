<div
    x-sort:item
    class="category-item"
    data-id="{{ $category->id }}"
    wire:key="category-{{ $category->id }}"
>

    {{-- Category Row --}}
    <div
        class="group flex items-center gap-3 px-4 py-3
               hover:bg-gray-50 transition"
        style="padding-left: {{ 1 + ($level * 2) }}rem;"
    >

        {{-- Drag Handle --}}
        <button
            type="button"
            x-sort:handle
            class="shrink-0 cursor-grab active:cursor-grabbing
                   text-gray-400 hover:text-gray-600"
            title="Drag to reorder"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 6h.01M8 12h.01M8 18h.01
                       M16 6h.01M16 12h.01M16 18h.01"
                />
            </svg>
        </button>

        {{-- Category Number --}}
        <div
            class="shrink-0 w-14 text-xs font-mono
                   text-gray-400"
        >
            {{ $number }}
        </div>

        {{-- Folder Icon --}}
        <div
            class="flex h-9 w-9 shrink-0 items-center
                   justify-center rounded-lg bg-indigo-50"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-5 h-5 text-indigo-600"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3.75 6.75h5.25l2.25 2.25h9v8.25
                       a1.5 1.5 0 01-1.5 1.5H5.25
                       a1.5 1.5 0 01-1.5-1.5V6.75z"
                />
            </svg>
        </div>

        {{-- Content --}}
        <div class="min-w-0 flex-1">

            <div class="flex items-center gap-2">

                <span class="text-sm font-medium text-gray-900">
                    {{ $category->name }}
                </span>

                @if ($level === 0)

                    <span
                        class="text-[10px] px-1.5 py-0.5
                               rounded bg-indigo-50 text-indigo-600
                               font-medium"
                    >
                        ROOT
                    </span>

                @endif

            </div>

            <div class="flex items-center gap-3 mt-0.5">

                <span class="font-mono text-xs text-gray-500">
                    {{ $category->code }}
                </span>

                @if ($category->children_count > 0)

                    <span class="text-xs text-gray-400">
                        {{ $category->children_count }}
                        {{ Str::plural('child', $category->children_count) }}
                    </span>

                @endif

            </div>

        </div>

        {{-- Status --}}
        <div class="hidden sm:block shrink-0">

            @if ($category->is_active)

                <span
                    class="inline-flex items-center px-2.5 py-0.5
                           rounded-full text-xs font-medium
                           bg-green-100 text-green-800"
                >
                    Active
                </span>

            @else

                <span
                    class="inline-flex items-center px-2.5 py-0.5
                           rounded-full text-xs font-medium
                           bg-gray-100 text-gray-600"
                >
                    Inactive
                </span>

            @endif

        </div>

        {{-- Actions --}}
        <div
            class="flex items-center gap-3 shrink-0
                   opacity-0 group-hover:opacity-100
                   transition"
        >

            <button
                type="button"
                wire:click="openCreateModal({{ $category->id }})"
                class="text-green-600 hover:text-green-900 text-sm"
            >
                + Child
            </button>

            <button
                type="button"
                wire:click="editCategory({{ $category->id }})"
                class="text-indigo-600 hover:text-indigo-900 text-sm"
            >
                Edit
            </button>

            @if ($category->children_count === 0)

                <button
                    type="button"
                    wire:click="deleteCategory({{ $category->id }})"
                    wire:confirm="Are you sure you want to delete this category?"
                    class="text-red-600 hover:text-red-900 text-sm"
                >
                    Delete
                </button>

            @endif

        </div>

    </div>

    {{-- Children --}}
    @if ($category->childrenRecursive->isNotEmpty())

        <div
            class="category-children divide-y divide-gray-100"
            x-data
            x-sort="$wire.reorderCategories(
                Array.from($el.children)
                    .map(el => Number(el.dataset.id)),
                {{ $category->id }}
            )"
        >

            @foreach ($category->childrenRecursive as $child)

                @include(
                    'livewire.procurements.partials.category-row',
                    [
                        'category' => $child,
                        'level' => $level + 1,
                        'number' => $number . '.' . $loop->iteration,
                    ]
                )

            @endforeach

        </div>

    @endif

</div>