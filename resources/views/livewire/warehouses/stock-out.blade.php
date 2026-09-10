<div
    class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8"
    x-data="{
        barcode: '',
        scannedItems: [],
        scanning: false,
        toast: {
            show: false,
            type: 'success',
            message: '',
        },
        toastTimer: null,
        init() {
            this.focusScanner();
        },
        scan() {
            const barcode = this.barcode.trim();
            if (!barcode || this.scanning) {
                return;
            }
            console.log('Scanned barcode:', barcode);
            this.scanning = true;
            // 검색하기 전에 입력창 비우기
            this.barcode = '';
            // Livewire 검색
            this.$wire.findItemByBarcode(barcode);
        },
        focusScanner() {
            this.$nextTick(() => {
                const input = this.$refs.scanner;
                if (input) {
                    input.focus();
                }
            });
        },
        addItem(item) {
            const existing = this.scannedItems.find(
                scanned => scanned.id === item.id
            );
            if (existing) {
                existing.quantity++;
                this.showToast(
                    'success',
                    `${item.name} quantity: ${existing.quantity}`
                );
            } else {
                this.scannedItems.push({
                    id: item.id,
                    barcode: item.barcode ?? '',
                    name: item.name ?? '',
                    sku: item.sku ?? '',
                    image_url: item.image_url ?? '',
                    quantity: 1,
                });
                this.showToast(
                    'success',
                    `${item.name} added`
                );
            }
            this.scanning = false;
            this.focusScanner();
        },
        itemNotFound(message) {
            this.scanning = false;
            this.barcode = '';
            this.showToast(
                'error',
                message || 'Item not found.'
            );
            this.focusScanner();
        },
        removeItem(id) {
            this.scannedItems = this.scannedItems.filter(
                item => item.id !== id
            );
            this.focusScanner();
        },
        showToast(type, message) {
            clearTimeout(this.toastTimer);
            this.toast = {
                show: true,
                type: type,
                message: message,
            };
            this.toastTimer = setTimeout(() => {
                this.toast.show = false;
            }, 2500);
        },
        saving: false,
        async save() {
            if (this.scannedItems.length === 0) {
                this.showToast('error', 'No items to save.');
                return;
            }

            if (this.saving) {
                return;
            }

            this.saving = true;

            try {
                await this.$wire.save(this.scannedItems);
            } catch (error) {
                console.error(error);
                this.saving = false;
            }
        },
    }"
    x-on:item-found.window="addItem($event.detail.item)"
    x-on:item-not-found.window="itemNotFound($event.detail.message)"
    x-on:stock-out-saved.window="
        scannedItems = [];
        saving = false;
        showToast('success', $event.detail.message);
        focusScanner();
    "
    x-on:stock-out-error.window="
        saving = false;
        showToast('error', $event.detail.message);
    "
>
    <x-slot name="header"><h1 class="font-bold text-2xl">{{$warehouse->name}}</h1> {{__('Stock-Out')}}</x-slot>
    {{-- Barcode Scanner --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="mb-2 flex items-center justify-between">
            <label class="text-sm font-semibold text-gray-700">
                Barcode Scanner
            </label>
            <span
                x-show="!scanning"
                class="inline-flex items-center gap-1.5 text-xs font-medium text-red-600"
            >
                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                Ready
            </span>
            <span
                x-show="scanning"
                class="inline-flex items-center gap-1.5 text-xs font-medium text-amber-600"
            >
                <span class="h-2 w-2 animate-pulse rounded-full bg-amber-500"></span>
                Searching...
            </span>
        </div>
        <input
            x-ref="scanner"
            x-model="barcode"
            @keydown.enter.prevent="scan()"
            type="text"
            autocomplete="off"
            autocapitalize="off"
            spellcheck="false"
            class="w-full rounded-xl border-gray-300 px-4 py-3 text-lg
                   focus:border-red-500 focus:ring-red-500"
            placeholder="Scan barcode..."
        >
        <p class="mt-2 text-xs text-gray-500">
            Scan a barcode to automatically search and add the item.
        </p>
    </div>
    {{-- Toast --}}
    <div
        x-show="toast.show"
        x-transition
        class="fixed right-5 top-5 z-[100] w-80"
        style="display: none;"
    >
        <div
            class="rounded-xl border bg-white p-4 shadow-lg"
            :class="
                toast.type === 'success'
                    ? 'border-red-200'
                    : 'border-red-200'
            "
        >
            <div class="flex items-center gap-3">
                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
                    :class="
                        toast.type === 'success'
                            ? 'bg-red-100'
                            : 'bg-red-100'
                    "
                >
                    <span
                        class="text-lg font-bold"
                        :class="
                            toast.type === 'success'
                                ? 'text-red-600'
                                : 'text-red-600'
                        "
                        x-text="
                            toast.type === 'success'
                                ? '✓'
                                : '!'
                        "
                    ></span>
                </div>
                <div class="min-w-0 flex-1">
                    <p
                        class="text-sm font-semibold"
                        :class="
                            toast.type === 'success'
                                ? 'text-red-700'
                                : 'text-red-700'
                        "
                        x-text="
                            toast.type === 'success'
                                ? 'Success'
                                : 'Not Found'
                        "
                    ></p>
                    <p
                        class="mt-0.5 text-sm text-gray-600"
                        x-text="toast.message"
                    ></p>
                </div>
                <button
                    type="button"
                    @click="toast.show = false"
                    class="text-gray-400 hover:text-gray-600"
                >
                    ×
                </button>
            </div>
        </div>
    </div>
    {{-- Scanned Items --}}
    <div class="mt-4 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b bg-gray-50 px-4 py-3">
            <div>
                <h3 class="font-semibold text-gray-900">
                    Items Total
                </h3>
                <p class="text-xs text-gray-500">
                    <span x-text="scannedItems.length"></span>
                    item(s)
                </p>
            </div>
            <span
                x-show="scannedItems.length > 0"
                class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700"
                x-text="
                    scannedItems.reduce(
                        (total, item) => total + item.quantity,
                        0
                    ) + ' pcs'
                "
            ></span>
        </div>
        <div class="divide-y divide-gray-100">
            <template
                x-for="item in scannedItems"
                :key="item.id"
            >
                <div class="flex items-center gap-4 px-4 py-3">
                    {{-- Image --}}
                    <div
                        class="h-14 w-14 shrink-0 overflow-hidden rounded-xl border bg-gray-50"
                    >
                        <template x-if="item.image_url">
                            <img
                                :src="item.image_url"
                                :alt="item.name"
                                class="h-full w-full object-cover"
                            >
                        </template>
                        <template x-if="!item.image_url">
                            <div class="flex h-full items-center justify-center">
                                <span class="text-xs text-gray-400">
                                    No image
                                </span>
                            </div>
                        </template>
                    </div>
                    {{-- Item Info --}}
                    <div class="min-w-0 flex-1">
                        <div
                            class="truncate font-medium text-gray-900"
                            x-text="item.name"
                        ></div>
                        <div class="mt-1 flex flex-wrap gap-x-3 text-xs text-gray-500">
                            <span
                                x-show="item.sku"
                                x-text="'SKU: ' + item.sku"
                            ></span>
                            <span
                                x-show="item.barcode"
                                x-text="'Barcode: ' + item.barcode"
                            ></span>
                        </div>
                    </div>
                    {{-- Quantity --}}
                    <div class="flex shrink-0 items-center gap-2">
                        <button
                            type="button"
                            @click="
                                item.quantity = Math.max(
                                    1,
                                    item.quantity - 1
                                )
                            "
                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300"
                        >
                            −
                        </button>
                        <input
                            type="number"
                            min="1"
                            x-model.number="item.quantity"
                            class="h-9 w-16 rounded-lg border-gray-300 text-center text-sm"
                        >
                        <button
                            type="button"
                            @click="item.quantity++"
                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300"
                        >
                            +
                        </button>
                    </div>
                    {{-- Remove --}}
                    <button
                        type="button"
                        @click="removeItem(item.id)"
                        class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-500"
                    >
                        ×
                    </button>
                </div>
            </template>
            {{-- Empty --}}
            <div
                x-show="scannedItems.length === 0"
                class="px-4 py-12 text-center"
            >
                <p class="text-sm font-medium text-gray-700">
                    No items scanned
                </p>
                <p class="mt-1 text-xs text-gray-500">
                    Scan a barcode to add an item.
                </p>
            </div>
        </div>
    </div>
    <div class="mt-4 flex justify-end">
        <button
            type="button"
            @click="save()"
            :disabled="saving || scannedItems.length === 0"
            class="inline-flex items-center rounded-xl bg-red-600 px-5 py-3
                text-sm font-semibold text-white shadow-sm
                transition hover:bg-red-700
                disabled:cursor-not-allowed disabled:opacity-50"
        >
            <span x-show="!saving">
                Save Stock Out
            </span>
            <span x-show="saving">
                Saving...
            </span>
        </button>
    </div>
</div>