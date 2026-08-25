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
    <x-categories />

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span class="font-medium text-gray-900">
                    Items
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
                            All Items
                        </h1>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ $total }} items
                        </p>
                    @endif
                </div>
            </div>
        </div>
        {{-- Items --}}
        @if (count($items))
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

                    <div
                        wire:key="item-{{ $item['id'] }}"
                        x-data="itemCard({
                            id: @js($item['id']),
                            name: @js($item['name']),
                            sku: @js($item['sku']),
                            image: @js($item['image']),
                            unit: @js($item['unit']),
                            brand: @js($item['brand']),
                            color: @js($item['color']),
                            size: @js($item['size']),
                        })"
                        class="
                            group
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

                        {{-- Image --}}
                        <div class="relative aspect-square overflow-hidden bg-gray-100">

                            <img
                                src="{{ $item['image'] }}"
                                alt="{{ $item['name'] }}"
                                loading="lazy"
                                decoding="async"
                                class="
                                    h-full
                                    w-full
                                    object-cover
                                    transition-transform
                                    duration-500
                                    ease-out
                                    sm:group-hover:scale-[1.03]
                                "
                            >

                            {{-- Image Overlay --}}
                            <div
                                class="
                                    pointer-events-none
                                    absolute
                                    inset-0
                                    bg-gradient-to-t
                                    from-black/5
                                    via-transparent
                                    to-transparent
                                    opacity-0
                                    transition-opacity
                                    duration-300
                                    sm:group-hover:opacity-100
                                "
                            ></div>
                            {{-- Wishlist --}}
                            <button
                                type="button"
                                @click="toggleWishlist()"
                                class="
                                    absolute
                                    right-3
                                    top-3
                                    flex
                                    h-9
                                    w-9
                                    items-center
                                    justify-center
                                    rounded-full
                                    bg-white/90
                                    text-gray-500
                                    shadow-sm
                                    ring-1
                                    ring-black/5
                                    backdrop-blur-sm
                                    transition-all
                                    duration-200
                                    hover:bg-white
                                    hover:text-red-500
                                    hover:shadow-md
                                    active:scale-90
                                "
                                :aria-label="wishlisted ? 'Remove from wishlist' : 'Add to wishlist'"
                            >
                                <template x-if="wishlisted">
                                    <svg
                                        class="h-5 w-5 fill-red-500 text-red-500"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M12 21s-7-4.6-9.4-9.2C.7 7.8 2.8 4 6.5 4c2.1 0 4.1 1.2 5.5 3 1.4-1.8 3.4-3 5.5-3 3.7 0 5.8 3.8 3.9 7.8C19 16.4 12 21 12 21z"
                                        />
                                    </svg>
                                </template>
                                <template x-if="!wishlisted">
                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M20.8 8.7c0 5.5-8.8 10.3-8.8 10.3S3.2 14.2 3.2 8.7A4.7 4.7 0 0 1 12 6.1a4.7 4.7 0 0 1 8.8 2.6Z"
                                        />
                                    </svg>
                                </template>
                            </button>
                        </div>
                        {{-- Info --}}
                        <div class="p-3.5">

                            <h2
                                class="
                                    min-h-[2.5rem]
                                    line-clamp-2
                                    text-sm
                                    font-medium
                                    leading-5
                                    text-gray-900
                                "
                            >
                                {{ $item['name'] }}
                            </h2>

                            @if ($item['sku'])
                                <p class="mt-1 truncate text-xs text-gray-400">
                                    {{ $item['sku'] }}
                                </p>
                            @endif

                            {{-- Quantity --}}
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-xs font-medium text-gray-400">
                                    Quantity
                                </span>
                                <div
                                    class="
                                        flex
                                        items-center
                                        overflow-hidden
                                        rounded-lg
                                        border
                                        border-gray-200
                                        bg-gray-50
                                    "
                                >
                                    {{-- Decrease --}}
                                    <button
                                        type="button"
                                        @click="decrease()"
                                        :disabled="quantity <= 1"
                                        class="
                                            flex
                                            h-8
                                            w-8
                                            items-center
                                            justify-center
                                            text-gray-500
                                            transition
                                            hover:bg-white
                                            hover:text-gray-900
                                            active:bg-gray-100
                                            disabled:cursor-not-allowed
                                            disabled:opacity-40
                                        "
                                    >
                                        <span class="text-lg leading-none">
                                            −
                                        </span>
                                    </button>
                                    {{-- Quantity --}}
                                    <span
                                        x-text="quantity"
                                        class="
                                            flex
                                            h-8
                                            min-w-8
                                            items-center
                                            justify-center
                                            border-x
                                            border-gray-200
                                            bg-white
                                            text-sm
                                            font-semibold
                                            text-gray-900
                                        "
                                    ></span>
                                    {{-- Increase --}}
                                    <button
                                        type="button"
                                        @click="increase()"
                                        :disabled="quantity >= 999"
                                        class="
                                            flex
                                            h-8
                                            w-8
                                            items-center
                                            justify-center
                                            text-gray-500
                                            transition
                                            hover:bg-white
                                            hover:text-gray-900
                                            active:bg-gray-100
                                            disabled:cursor-not-allowed
                                            disabled:opacity-40
                                        "
                                    >
                                        <span class="text-lg leading-none">
                                            +
                                        </span>
                                    </button>
                                </div>
                            </div>
                            {{-- Add to Cart --}}
                            <button
                                type="button"
                                @click="addToCart()"
                                class="
                                    mt-3
                                    flex
                                    w-full
                                    items-center
                                    justify-center
                                    gap-2
                                    rounded-lg
                                    bg-gray-900
                                    px-3
                                    py-2.5
                                    text-sm
                                    font-semibold
                                    text-white
                                    shadow-sm
                                    transition-all
                                    duration-200
                                    hover:bg-gray-800
                                    hover:shadow-md
                                    active:scale-[0.98]
                                "
                            >

                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 7h13L17 13M9 21a1 1 0 1 1-2 0m10 0a1 1 0 1 1-2 0"
                                    />
                                </svg>

                                <span>
                                    Add to Cart
                                </span>

                            </button>

                        </div>
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
    <x-mobile-purchase-bottom-nav />
</div>