<div
    x-data="{
        count: CartStore.count(),
        wishlistCount: WishlistStore.count(),
    }"
    x-init="
        window.addEventListener('cart-updated', (event) => {
            count = event.detail.count;
        });

        window.addEventListener('wishlist-updated', (event) => {
            wishlistCount = event.detail.count;
        });
    "
    class="
        fixed
        inset-x-0
        bottom-0
        z-50
        border-t
        border-gray-200
        bg-white/95
        shadow-[0_-4px_20px_rgba(0,0,0,0.08)]
        backdrop-blur
        md:hidden
    "
>
    <div
        class="
            mx-auto
            grid
            h-16
            max-w-lg
            grid-cols-4
        "
    >

        {{-- Home --}}
        <a
            href="{{ route('items') }}"
            @class([
                'relative flex flex-col items-center justify-center gap-0.5 active:bg-gray-50',
                'text-gray-900' => request()->routeIs('items'),
                'text-gray-400' => ! request()->routeIs('items'),
            ])
        >
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
                    d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V10Z"
                />
            </svg>

            <span class="text-[11px] font-medium">
                Home
            </span>
        </a>


        {{-- Wishlist --}}
        <a
            wire:ignore
            href="{{ route('wishlist') }}"
            @class([
                'group relative flex flex-col items-center justify-center gap-0.5 transition active:bg-gray-50',
                'text-red-400' => request()->routeIs('wishlist'),
                'text-gray-400' => ! request()->routeIs('wishlist'),
            ])
        >
            <div class="relative">

                <svg
                    class="h-5 w-5 fill-current transition"
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

                <span
                    x-cloak
                    x-show="wishlistCount > 0"
                    x-text="wishlistCount"
                    class="
                        absolute
                        -right-2
                        -top-2
                        flex
                        h-4
                        min-w-4
                        items-center
                        justify-center
                        rounded-full
                        bg-red-500
                        px-1
                        text-[9px]
                        font-bold
                        text-white
                    "
                ></span>

            </div>

            <span class="text-[11px] font-medium">
                Wishlist
            </span>
        </a>


        {{-- History --}}
        <a
            href="{{ route('purchase-requests') }}"
            @class([
                'relative flex flex-col items-center justify-center gap-0.5 active:bg-gray-50',
                'text-gray-900' => request()->routeIs('purchase-requests*'),
                'text-gray-400' => ! request()->routeIs('purchase-requests*'),
            ])
        >
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
                    d="M12 8v4l2.5 2.5"
                />

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                />

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3.5 6.5 5 4"
                />
            </svg>

            <span class="text-[11px] font-medium">
                History
            </span>
        </a>


        {{-- Cart --}}
        <a
            href="{{ route('cart') }}"
            @class([
                'relative flex flex-col items-center justify-center gap-0.5 active:bg-gray-50',
                'text-gray-900' => request()->routeIs('cart'),
                'text-gray-400' => ! request()->routeIs('cart'),
            ])
        >
            <div class="relative">

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
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 7h13L17 13M9 21a1 1 0 1 1-2 0m10 0a1 1 0 0 1-2 0"
                    />
                </svg>

                <span
                    x-cloak
                    x-show="count > 0"
                    x-text="count"
                    class="
                        absolute
                        -right-2
                        -top-2
                        flex
                        h-4
                        min-w-4
                        items-center
                        justify-center
                        rounded-full
                        bg-gray-900
                        px-1
                        text-[9px]
                        font-bold
                        text-white
                    "
                ></span>

            </div>

            <span class="text-[11px] font-medium">
                Cart
            </span>
        </a>

    </div>
</div>