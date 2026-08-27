<div
    x-data="{
        items: [],
        totalQuantity: 0,

        init() {
            this.refresh();

            window.addEventListener('cart-updated', () => {
                this.refresh();
            });

            window.addEventListener('cart-request-created', () => {
                CartStore.clear();
            });
        },

        refresh() {
            this.items = CartStore.items();
            this.totalQuantity = CartStore.count();
        },

        increase(id) {
            CartStore.increase(id);
        },

        decrease(id) {
            CartStore.decrease(id);
        },

        updateQuantity(id, quantity) {
            CartStore.updateQuantity(id, quantity);
        },

        remove(id) {
            CartStore.remove(id);
        },

        clear() {
            if (!confirm('Are you sure you want to remove all items from your cart?')) {
                return;
            }

            CartStore.clear();
        }
    }"
    class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8"
>
    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Shopping Cart
            </h1>
            <p
                x-show="items.length > 0"
                class="mt-1 text-sm text-gray-500"
            >
                <span x-text="totalQuantity"></span>
                item(s) in your cart
            </p>
        </div>
        <button
            type="button"
            x-show="items.length > 0"
            @click="clear()"
            class="text-sm font-medium text-red-600 hover:text-red-700"
        >
            Clear Cart
        </button>
    </div>
    {{-- Empty Cart --}}
    <div
        x-show="items.length === 0"
        x-cloak
        class="rounded-xl border border-gray-200 bg-white px-6 py-16 text-center"
    >
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
            <svg
                class="h-8 w-8 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 2m2-2 2 2m8-2 2 2m-2-2 2-2M9 19.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm10 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"
                />
            </svg>
        </div>
        <h2 class="mt-5 text-lg font-semibold text-gray-900">
            Your cart is empty
        </h2>
        <p class="mt-2 text-sm text-gray-500">
            Add some products to your cart and they will appear here.
        </p>
        <a
            href="{{ route('items') }}"
            class="mt-6 inline-flex items-center rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-800"
        >
            Continue Shopping
        </a>
    </div>
    {{-- Cart --}}
    <div
        x-show="items.length > 0"
        x-cloak
        class="grid grid-cols-1 gap-8 lg:grid-cols-3 mb-12"
    >
        {{-- Items --}}
        <div class="lg:col-span-2">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                <template x-for="item in items" :key="item.id">
                    <div class="flex gap-4 border-b border-gray-200 p-5 last:border-b-0">

                        {{-- Image --}}
                        <div class="h-24 w-24 shrink-0 overflow-hidden rounded-lg bg-gray-100">
                            <template x-if="item.image">
                                <img
                                    :src="item.image"
                                    :alt="item.name"
                                    class="h-full w-full object-cover"
                                >
                            </template>

                            <template x-if="!item.image">
                                <div class="flex h-full w-full items-center justify-center">
                                    <svg
                                        class="h-8 w-8 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="m3 16 5-5 4 4 3-3 6 6M5 20h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"
                                        />
                                    </svg>
                                </div>
                            </template>
                        </div>

                        {{-- Info --}}
                        <div class="min-w-0 flex-1">

                            {{-- Header --}}
                            <div class="flex items-start justify-between gap-4">

                                <div class="min-w-0">
                                    <h3
                                        class="font-semibold leading-5 text-gray-900"
                                        x-text="item.name"
                                    ></h3>

                                    <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-gray-500">

                                        <span>
                                            SKU:
                                            <span
                                                class="font-medium text-gray-700"
                                                x-text="item.sku"
                                            ></span>
                                        </span>

                                        <span class="text-gray-300">|</span>

                                        <span>
                                            Unit:
                                            <span
                                                class="font-medium text-gray-700"
                                                x-text="item.unit"
                                            ></span>
                                        </span>

                                        <template x-if="item.brand">
                                            <span>
                                                Brand:
                                                <span
                                                    class="font-medium text-gray-700"
                                                    x-text="item.brand"
                                                ></span>
                                            </span>
                                        </template>

                                        <template x-if="item.color">
                                            <span>
                                                Color:
                                                <span
                                                    class="font-medium text-gray-700"
                                                    x-text="item.color"
                                                ></span>
                                            </span>
                                        </template>

                                        <template x-if="item.size">
                                            <span>
                                                Size:
                                                <span
                                                    class="font-medium text-gray-700"
                                                    x-text="item.size"
                                                ></span>
                                            </span>
                                        </template>

                                    </div>
                                </div>

                                {{-- Remove --}}
                                <button
                                    type="button"
                                    @click="remove(item.id)"
                                    class="shrink-0 text-gray-400 hover:text-red-600"
                                    title="Remove"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M6 6l12 12M6 18 18 6"
                                        />
                                    </svg>
                                </button>

                            </div>

                            {{-- Quantity --}}
                            <div class="mt-5 flex items-center">
                                <div class="inline-flex h-10 items-center overflow-hidden rounded-lg border border-gray-300 bg-white shadow-sm">

                                    <button
                                        type="button"
                                        @click="decrease(item.id)"
                                        class="flex h-full w-10 items-center justify-center text-lg text-gray-500 transition hover:bg-gray-50 hover:text-gray-900 active:bg-gray-100"
                                    >
                                        −
                                    </button>

                                    <input
                                        type="number"
                                        inputmode="numeric"
                                        min="1"
                                        max="999"
                                        :value="item.quantity"
                                        @change="updateQuantity(item.id, $event.target.value)"
                                        class="h-full w-20 border-0 border-x border-gray-200 bg-transparent p-0 text-center text-sm font-medium text-gray-900 focus:border-gray-200 focus:outline-none focus:ring-0"
                                    >

                                    <button
                                        type="button"
                                        @click="increase(item.id)"
                                        class="flex h-full w-10 items-center justify-center text-lg text-gray-500 transition hover:bg-gray-50 hover:text-gray-900 active:bg-gray-100"
                                    >
                                        +
                                    </button>

                                </div>

                                <span class="ml-3 text-sm text-gray-500">
                                    Qty:
                                    <span
                                        class="font-medium text-gray-700"
                                        x-text="item.quantity"
                                    ></span>
                                </span>
                            </div>

                            {{-- Remark --}}
                            <div class="mt-4">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                    Remark
                                    <span class="text-red-500">*</span>
                                </label>

                                <textarea
                                    rows="2"
                                    :value="item.remark"
                                    @input="CartStore.updateRemark(item.id, $event.target.value)"
                                    placeholder="Please enter a remark for this item."
                                    class="block w-full resize-y rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                                ></textarea>

                                <p
                                    x-show="!item.remark || !item.remark.trim()"
                                    class="mt-1 text-xs text-red-500"
                                >
                                    Remark is required.
                                </p>
                            </div>

                        </div>
                    </div>
                </template>
            </div>
        </div>
        {{-- Summary --}}
        <div class="lg:col-span-1">
            <div class="sticky top-6 rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="text-lg font-semibold text-gray-900">
                    Order Summary
                </h2>
                <div class="mt-6 space-y-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">
                            Products
                        </span>
                        <span
                            class="font-medium text-gray-900"
                            x-text="items.length"
                        ></span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">
                            Total Quantity
                        </span>
                        <span
                            class="font-medium text-gray-900"
                            x-text="totalQuantity"
                        ></span>
                    </div>
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-900">
                                Total Items
                            </span>
                            <span
                                class="text-xl font-bold text-gray-900"
                                x-text="totalQuantity"
                            ></span>
                        </div>
                    </div>
                </div>
                <button
                    type="button"
                    :disabled="items.some(item => !item.remark || !item.remark.trim())"
                    wire:click="createRequest(CartStore.items())"
                    wire:loading.attr="disabled"
                    wire:target="createRequest"
                    class="mt-6 w-full rounded-lg bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="createRequest">
                        Proceed to Request
                    </span>

                    <span wire:loading wire:target="createRequest">
                        Creating Request...
                    </span>
                </button>
                <a
                    href="{{ route('items') }}"
                    class="mt-3 block text-center text-sm font-medium text-gray-600 hover:text-gray-900"
                >
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
    <x-mobile-purchase-bottom-nav />
</div>