<div>
    {{-- Categories --}}
    <x-categories />
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span class="font-medium text-gray-900">
                    Items
                </span>
            </div>
            {{-- Title --}}
            <div class="mt-3 flex items-end justify-between gap-4">
                <div class="min-w-0">
                    @if (filled($search))
                        <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">
                            Search results
                        </h1>
                        <p class="mt-1 truncate text-sm text-gray-500">
                            "{{ $search }}"
                            ·
                            {{ $items->total() }} items
                        </p>
                    @else
                        <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">
                            All Items
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $items->total() }} items
                        </p>
                    @endif
                </div>
            </div>
        </div>
        {{-- Items --}}
        @if ($items->isNotEmpty())
            <div
                class="
                    grid
                    grid-cols-2
                    gap-3
                    sm:grid-cols-3
                    md:grid-cols-4
                    lg:grid-cols-5
                    xl:grid-cols-5
                "
            >
                @foreach ($items as $item)
                    <a
                        href="#"
                        wire:key="item-{{ $item->id }}"
                        class="
                            group
                            overflow-hidden
                            rounded-xl
                            border
                            border-gray-200
                            bg-white
                            transition
                            hover:-translate-y-0.5
                            hover:border-gray-300
                            hover:shadow-md
                        "
                    >
                        {{-- Image --}}
                        <div class="aspect-square overflow-hidden bg-gray-100">
                            <div class="aspect-square overflow-hidden bg-gray-100">
                                @if ($item->primaryImage)
                                    <img
                                        src="{{ Storage::url($item->primaryImage->path) }}"
                                        alt="{{ $item->name }}"
                                        loading="lazy"
                                        decoding="async"
                                        class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                    >
                                @else
                                    <img
                                        src="{{ asset('images/default-item.png') }}"
                                        alt="{{ $item->name }}"
                                        loading="lazy"
                                        decoding="async"
                                        class="h-full w-full object-cover"
                                    >
                                @endif
                            </div>
                        </div>
                        {{-- Info --}}
                        <div class="p-3">
                            <h2
                                class="
                                    min-h-[2.5rem]
                                    line-clamp-2
                                    text-sm
                                    font-medium
                                    text-gray-900
                                "
                            >
                                {{ $item->name }}
                            </h2>
                            @if ($item->sku)
                                <p class="mt-1 truncate text-xs text-gray-500">
                                    {{ $item->sku }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
            {{-- Pagination --}}
            <div class="mt-8">
                {{ $items->links() }}
            </div>
        @else
            {{-- Empty --}}
            <div
                class="
                    rounded-xl
                    border
                    border-dashed
                    border-gray-300
                    bg-white
                    px-6
                    py-16
                    text-center
                "
            >
                <svg
                    class="mx-auto h-12 w-12 text-gray-300"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="m3 16 5-5a2 2 0 0 1 3 0l2 2 2-2a2 2 0 0 1 3 0l3 3M5 19h14a2 2 0 0 1 2-2V7a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2Z"
                    />
                </svg>
                @if (filled($search))
                    <h2 class="mt-4 text-sm font-semibold text-gray-900">
                        No search results
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        No items found for "{{ $search }}".
                    </p>

                    <a
                        href="{{ route('items') }}"
                        class="
                            mt-5
                            inline-flex
                            items-center
                            rounded-lg
                            bg-gray-900
                            px-4
                            py-2
                            text-sm
                            font-medium
                            text-white
                            hover:bg-gray-800
                        "
                    >
                        View all items
                    </a>

                @else

                    <h2 class="mt-4 text-sm font-semibold text-gray-900">
                        No items found
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        There are no items available.
                    </p>

                @endif

            </div>

        @endif

    </div>
</div>