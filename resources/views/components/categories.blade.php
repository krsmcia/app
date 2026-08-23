<div
    x-data="{ open: false }"
    @mouseenter="if (window.innerWidth >= 768) open = true"
    @mouseleave="if (window.innerWidth >= 768) open = false"
    @keydown.escape.window="open = false"
    class="relative mx-auto max-w-7xl"
>
    {{-- Categories Button --}}
    <button
        type="button"
        @click="open = !open"
        class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 transition hover:text-gray-900 md:px-4"
        :aria-expanded="open"
        aria-haspopup="true"
    >
        {{-- Menu Icon --}}
        <svg
            class="h-5 w-5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
            />
        </svg>

        <span>Categories</span>

        {{-- Arrow --}}
        <svg
            class="h-4 w-4 transition-transform duration-200"
            :class="{ 'rotate-180': open }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m19 9-7 7-7-7"
            />
        </svg>
    </button>


    {{-- Mobile Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition-opacity duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="fixed inset-0 z-40 bg-black/40 md:hidden"
        style="display: none;"
    ></div>


    {{-- Menu --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"

        class="
            fixed inset-x-0 top-0 z-50
            max-h-[100dvh]
            overflow-hidden
            bg-white

            md:absolute
            md:left-1/2
            md:top-full
            md:w-screen
            md:max-w-7xl
            md:-translate-x-1/2
            md:overflow-visible
            md:rounded-xl
            md:border
            md:border-gray-200
            md:shadow-xl
        "
        style="display: none;"
        @click.outside="if (window.innerWidth >= 768) open = false"
    >

        {{-- Mobile Header --}}
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-4 md:hidden">
            <div class="flex items-center gap-2">
                <svg
                    class="h-5 w-5 text-gray-700"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>

                <span class="text-base font-semibold text-gray-900">
                    Categories
                </span>
            </div>

            <button
                type="button"
                @click="open = false"
                class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700"
                aria-label="Close categories"
            >
                <svg
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18 18 6M6 6l12 12"
                    />
                </svg>
            </button>
        </div>


        {{-- Main Content --}}
        <div
            class="
                max-h-[calc(100dvh-65px)]
                overflow-y-auto
                overscroll-contain
                p-4

                md:max-h-[70vh]
                md:p-6
            "
        >
            @if ($categories->isNotEmpty())

                {{-- Desktop --}}
                <div class="hidden md:grid md:grid-cols-4 md:gap-x-8 md:gap-y-8">
                    @foreach ($categories as $category)
                        <div class="min-w-0">

                            {{-- Root Category --}}
                            <a
                                href="{{ route('items.category', ['category' => $category->code]) }}"
                                class="group mb-3 flex items-center gap-2 text-sm font-semibold text-gray-900 hover:text-blue-600"
                            >
                                <span>
                                    {{ $category->name }}
                                </span>

                                <svg
                                    class="h-3.5 w-3.5 opacity-0 transition-opacity group-hover:opacity-100"
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
                            </a>

                            @if ($category->childrenRecursive->isNotEmpty())
                                @include(
                                    'components.partials.category-menu',
                                    [
                                        'categories' => $category->childrenRecursive,
                                        'level' => 1,
                                    ]
                                )
                            @endif

                        </div>
                    @endforeach
                </div>


                {{-- Mobile --}}
                <div class="space-y-2 md:hidden">

                    @foreach ($categories as $category)

                        <div class="overflow-hidden rounded-xl border border-gray-200">

                            {{-- Root Category --}}
                            <a
                                href="{{ route('items.category', ['category' => $category->code]) }}"
                                class="flex min-h-12 items-center justify-between gap-3 px-4 py-3 text-sm font-semibold text-gray-900 active:bg-gray-50"
                            >
                                <span class="min-w-0 truncate">
                                    {{ $category->name }}
                                </span>

                                <svg
                                    class="h-4 w-4 shrink-0 text-gray-400"
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
                            </a>

                            {{-- Children --}}
                            @if ($category->childrenRecursive->isNotEmpty())
                                <div class="border-t border-gray-100 bg-gray-50 px-2 py-2">
                                    @include(
                                        'components.partials.category-menu',
                                        [
                                            'categories' => $category->childrenRecursive,
                                            'level' => 1,
                                            'mobile' => true,
                                        ]
                                    )
                                </div>
                            @endif

                        </div>

                    @endforeach

                </div>

            @else

                <div class="py-8 text-center text-sm text-gray-500">
                    No categories available.
                </div>

            @endif

        </div>


        {{-- Footer --}}
        <div
            class="
                border-t border-gray-100
                bg-gray-50
                px-4 py-3
                md:px-6
            "
        >
            <a
                href="{{ route('items') }}"
                class="flex min-h-10 items-center justify-center gap-2 rounded-lg text-sm font-medium text-gray-700 transition hover:bg-white hover:text-blue-600 md:inline-flex"
            >
                View all items

                <svg
                    class="h-4 w-4"
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
            </a>
        </div>

    </div>
</div>