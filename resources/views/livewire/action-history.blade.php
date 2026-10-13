<div 
    class="pb-20 md:pb-0"
    x-data="{
        count: CartStore.count(),
        wishlistCount: WishlistStore.count(),

        init() {
            window.addEventListener('cart-updated', (event) => {
                this.count = event.detail.count;
            });

            window.addEventListener('wishlist-updated', (event) => {
                this.wishlistCount = event.detail.count;
            });
        }
    }"
>
    <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span class="font-medium text-gray-900">
                    Approvals
                </span>
            </div>
            <div class="mt-3 flex items-end justify-between gap-4">
                <div class="min-w-0">
                    @if (filled($search))
                        <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">
                            Search results
                        </h1>

                        <p class="mt-1 truncate text-sm text-gray-500">
                            "{{ $search }}"
                            ·
                            {{ $total }} items
                        </p>
                    @else
                        <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">
                            All Approval
                        </h1>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ $total }} items
                        </p>
                    @endif
                </div>
            </div>
        </div>
        {{-- Items --}}
        @if (count($purchaseActions))
            <div class="grid gap-2">
                @foreach ($purchaseActions as $purchaseAction)
                    <div
                        wire:key="item-{{ $purchaseAction['id'] }}"
                        x-data="itemCard({
                            id: @js($purchaseAction['id']),
                        })"
                        class="
                            p-2
                            overflow-hidden
                            rounded-2xl
                            border
                            border-gray-200
                            bg-white
                            shadow-sm
                            transition
                            duration-200
                            ease-out
                            sm:hover:border-gray-300
                            sm:hover:shadow-lg
                        "
                    >
                        <img class="w-20" src="{{$purchaseAction['image']}}" alt="">
                    </div>
                @endforeach
            </div>
            {{-- Infinite Scroll --}}
            @if ($hasMore)
                <div
                    x-data
                    x-intersect:enter.margin.500px="$wire.loadMore()"
                    class="flex min-h-24 items-center justify-center"
                >
                    <div
                        wire:loading
                        wire:target="loadMore"
                        class="flex items-center gap-2 text-sm text-gray-500"
                    >
                        <svg
                            class="h-5 w-5 animate-spin"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            />
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                            />
                        </svg>
                        Loading...
                    </div>
                </div>
            @endif
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
                        d="m3 16 5-5a2 2 0 0 1 3 0l2 2 2-2a2 2 0 0 1 3 0l3 3M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0 2 2v10a2 2 0 0 0-2 2Z"
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