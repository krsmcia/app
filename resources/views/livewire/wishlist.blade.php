<div
    x-data="{
        initialized: false,

        init() {
            this.syncWishlist();

            window.addEventListener('wishlist-updated', () => {
                this.syncWishlist();
            });
        },

        syncWishlist() {
            this.initialized = false;

            $wire.setWishlist(WishlistStore.get()).then(() => {
                this.initialized = true;
            });
        }
    }"
    class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8"
>
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">
                Wishlist
            </h1>

            <p
                x-show="initialized"
                x-cloak
                class="mt-1 text-sm text-gray-500"
            >
                {{ $this->wishlistItems->count() }} items
            </p>

            {{-- Header skeleton --}}
            <div
                x-show="!initialized"
                class="mt-2 h-4 w-20 animate-pulse rounded bg-gray-200"
            ></div>
        </div>

        <button
            x-show="initialized && {{ $this->wishlistItems->count() > 0 ? 'true' : 'false' }}"
            x-cloak
            type="button"
            @click="
                if (confirm('Remove all items from your wishlist?')) {
                    WishlistStore.save([]);
                }
            "
            class="text-sm font-medium text-red-600 hover:text-red-700"
        >
            Clear all
        </button>
    </div>


    {{-- ============================================================
        Loading Skeleton
    ============================================================= --}}
    <div
        x-show="!initialized"
        x-cloak
        class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-5 lg:grid-cols-4 xl:grid-cols-5"
    >
        @for ($i = 0; $i < 10; $i++)
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                {{-- Image skeleton --}}
                <div class="aspect-square animate-pulse bg-gray-200"></div>

                <div class="space-y-3 p-3 sm:p-4">
                    {{-- Name --}}
                    <div class="h-4 w-4/5 animate-pulse rounded bg-gray-200"></div>

                    {{-- SKU --}}
                    <div class="h-3 w-2/5 animate-pulse rounded bg-gray-200"></div>

                    {{-- Button --}}
                    <div class="h-10 w-full animate-pulse rounded-lg bg-gray-200"></div>
                </div>
            </div>
        @endfor
    </div>


    {{-- ============================================================
        Actual Content
    ============================================================= --}}
    <div
        x-show="initialized"
        x-cloak
        x-transition.opacity.duration.150ms
    >
        @if ($this->wishlistItems->isEmpty())

            {{-- Empty --}}
            <div class="flex min-h-[400px] flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50 px-6 text-center">
                <svg
                    class="mb-4 h-12 w-12 text-gray-300"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                    />
                </svg>

                <h2 class="text-lg font-semibold text-gray-900">
                    Your wishlist is empty
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Add items you like to your wishlist.
                </p>

                <a
                    href="{{ route('items') }}"
                    class="mt-5 inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                >
                    Browse items
                </a>
            </div>

        @else

            {{-- Items --}}
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-5 lg:grid-cols-4 xl:grid-cols-5">
                @foreach ($this->wishlistItems as $item)
                    <div
                        wire:key="wishlist-item-{{ $item->id }}"
                        class="group overflow-hidden rounded-xl border border-gray-200 bg-white"
                    >
                        {{-- Image --}}
                        <div class="relative aspect-square overflow-hidden bg-gray-100">
                            @if ($item->image)
                                <img
                                    src="{{ $item->image }}"
                                    alt="{{ $item->name }}"
                                    loading="lazy"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                >
                            @else
                                <div class="flex h-full w-full items-center justify-center text-gray-400">
                                    <svg
                                        class="h-10 w-10"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />
                                    </svg>
                                </div>
                            @endif

                            {{-- Wishlist --}}
                            <button
                                type="button"
                                @click="WishlistStore.toggle({{ $item->id }})"
                                class="absolute right-2 top-2 flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-red-500 shadow-sm backdrop-blur transition hover:bg-white"
                                aria-label="Remove from wishlist"
                            >
                                <svg
                                    class="h-5 w-5 fill-current"
                                    viewBox="0 0 24 24"
                                >
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Info --}}
                        <div class="p-3 sm:p-4">
                            <h3 class="line-clamp-2 text-sm font-medium text-gray-900">
                                {{ $item->name }}
                            </h3>

                            @if ($item->sku)
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $item->sku }}
                                </p>
                            @endif

                            <div class="mt-3">
                                <button
                                    type="button"
                                    @click="
                                        CartStore.add({
                                            id: {{ $item->id }},
                                            name: @js($item->name),
                                            sku: @js($item->sku),
                                            image: @js($item->image),
                                            quantity: 1,
                                            unit: @js($item->unit),
                                            brand: @js($item->brand),
                                            color: @js($item->color),
                                            size: @js($item->size),
                                        })
                                    "
                                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-gray-900 px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-gray-800 active:scale-[0.98] sm:text-sm"
                                >
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
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2-2m2 2l2 2m8-2l2 2m-2-2l-2 2M9 21h.01M17 21h.01"
                                        />
                                    </svg>

                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @endif
    </div>
</div>