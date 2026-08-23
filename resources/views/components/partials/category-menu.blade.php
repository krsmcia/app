<div
    @class([
        'space-y-1',
        'ml-2 border-l border-gray-200 pl-2' => $level > 1,
    ])
>
    @foreach ($categories as $category)

        <div>

            <a
                href="{{ route('items.category', ['category' => $category->code]) }}"
                @class([
                    'group flex items-center justify-between gap-2 rounded-lg px-3 py-2 text-sm transition',
                    'text-gray-700 hover:bg-white hover:text-blue-600' => !isset($mobile) || !$mobile,
                    'text-gray-600 hover:bg-white hover:text-blue-600' => isset($mobile) && $mobile,
                ])
            >

                <span class="min-w-0 truncate">
                    {{ $category->name }}
                </span>

                @if ($category->childrenRecursive->isNotEmpty())

                    <svg
                        class="h-3.5 w-3.5 shrink-0 text-gray-400 transition group-hover:text-blue-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m9 5 7 7-7 7"
                        />
                    </svg>

                @endif

            </a>


            {{-- Recursive Children --}}
            @if ($category->childrenRecursive->isNotEmpty())

                @include(
                    'components.partials.category-menu',
                    [
                        'categories' => $category->childrenRecursive,
                        'level' => $level + 1,
                        'mobile' => $mobile ?? false,
                    ]
                )

            @endif

        </div>

    @endforeach
</div>