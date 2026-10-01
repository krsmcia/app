<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">
            Ordered
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Ordered items waiting to be received.
        </p>
    </div>
    <div class="space-y-4">
        @forelse ($purchase_actions as $purchase_action)
            {{-- =========================================================
                Purchase Card
            ========================================================== --}}
            <div
                class="rounded-lg border border-gray-200 bg-white shadow-sm"
                wire:key="purchase-action-{{ $purchase_action->id }}"
            >

                {{-- =====================================================
                    Header
                ====================================================== --}}
                <div
                    class="flex items-center justify-between
                        border-b border-gray-100 px-5 py-4"
                >

                    <div>

                        <div class="flex items-center gap-3">
                            <h2 class="font-semibold text-gray-900">
                                @if ($purchase_action->purchaseWorkflowItem?->purchaseItem?->purchaseRequest)
                                    {{ $purchase_action->purchaseWorkflowItem->purchaseItem->purchaseRequest->request_no }}
                                @else
                                    Purchase #{{ $purchase_action->id }}
                                @endif
                            </h2>
                            <span
                                class="rounded-full bg-blue-50 px-2.5 py-1
                                    text-xs font-medium text-blue-700"
                            >
                                Ordered
                            </span>

                        </div>


                        @if ($purchase_action->purchaseRequest)
                            <div class="mt-1 text-sm text-gray-500">
                                Requested by

                                <span class="font-medium text-gray-700">
                                    {{ $purchase_action->purchaseRequest->user?->name }}
                                </span>

                                @if ($purchase_action->purchaseRequest->department)
                                    · {{ $purchase_action->purchaseRequest->department->name }}
                                @endif
                            </div>
                        @endif

                    </div>


                    <div class="text-right text-sm text-gray-500">

                        {{ $purchase_action->created_at?->format('Y-m-d H:i') }}

                    </div>

                </div>


                {{-- =====================================================
                    Item
                ====================================================== --}}
                <div class="px-4 py-4 sm:px-5">

                    {{-- Desktop --}}
                    <div
                        class="hidden xl:grid
                            xl:grid-cols-[56px_minmax(180px,1.8fr)_minmax(120px,1.2fr)_90px_110px_110px_120px]
                            xl:items-center
                            xl:gap-4"
                    >

                        {{-- Image --}}
                        <div
                            class="h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-gray-100"
                        >
                            <img
                                src="{{ $purchase_action->item?->primaryImage
                                    ? Storage::url($purchase_action->item->primaryImage->path)
                                    : asset('images/default-item.png') }}"
                                alt="{{ $purchase_action->item?->name }}"

                                class="h-full w-full object-cover"
                            >
                        </div>


                        {{-- Item Info --}}
                        <div class="min-w-0">

                            <div
                                class="truncate text-sm font-semibold text-gray-900"
                            >
                                {{ $purchase_action->item?->name ?? 'Unknown Item' }}
                            </div>
                            <div
                                class="mt-1 flex items-center gap-2
                                    text-xs text-gray-500"
                            >
                                @if ($purchase_action->item?->sku)
                                    <span>
                                        SKU:
                                        <span class="font-medium text-gray-600">
                                            {{ $purchase_action->item->sku }}
                                        </span>
                                    </span>
                                    <span class="text-gray-300">
                                        •
                                    </span>
                                @endif
                                <span>
                                    Qty:
                                    <span class="font-semibold text-gray-700">
                                        {{ $purchase_action->quantity }}
                                    </span>
                                </span>
                            </div>
                        </div>
                        {{-- Vendor --}}
                        <div class="min-w-0">
                            <div
                                class="text-[10px] font-medium uppercase
                                    tracking-wider text-gray-400"
                            >
                                Vendor
                            </div>
                            @if ($purchase_action->vendorName)
                                <div
                                    class="mt-0.5 truncate text-sm font-semibold text-gray-800"
                                    title="{{ $purchase_action->vendorName }}"
                                >
                                    {{ $purchase_action->vendorName }}
                                </div>
                            @else
                                <div class="mt-0.5 text-sm text-gray-400">
                                    No vendor
                                </div>
                            @endif
                        </div>
                        {{-- Unit Price --}}
                        <div>
                            <div class="text-[10px] text-gray-400">
                                Unit Price
                            </div>
                            <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                {{ number_format($purchase_action->unitPrice, 2) }}
                            </div>
                        </div>
                        {{-- Quantity --}}
                        <div>
                            <div class="text-[10px] text-gray-400">
                                Quantity
                            </div>
                            <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                {{ number_format($purchase_action->quantity, 0) }}
                            </div>
                        </div>
                        {{-- Amount --}}
                        <div>
                            <div class="text-[10px] text-gray-400">
                                Amount
                            </div>
                            <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                {{ number_format($purchase_action->amount, 2) }}
                            </div>
                            @if ($purchase_action->shippingFee > 0 || $purchase_action->discount > 0)
                                <div class="mt-1 space-y-0.5 text-[10px] leading-tight">
                                    @if ($purchase_action->shippingFee > 0)
                                        <div class="text-gray-400">
                                            Shipping:
                                            <span class="font-medium text-gray-600">
                                                +{{ number_format($purchase_action->shippingFee, 2) }}
                                            </span>
                                        </div>
                                    @endif
                                    @if ($purchase_action->discount > 0)
                                        <div class="text-gray-400">
                                            Discount:
                                            <span class="font-medium text-gray-600">
                                                -{{ number_format($purchase_action->discount, 2) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        {{-- Total --}}
                        <div>
                            <div class="text-[10px] text-gray-400">
                                Total
                            </div>
                            <div class="mt-0.5 text-sm font-bold text-gray-900">
                                {{ number_format($purchase_action->itemTotal, 2) }}
                            </div>
                        </div>
                    </div>
                    {{-- =================================================
                        Mobile / Tablet
                    ================================================== --}}
                    <div class="xl:hidden space-y-4">
                        {{-- Item Header --}}
                        <div class="flex items-start gap-3">
                            {{-- Image --}}
                            <div
                                class="h-14 w-14 shrink-0 overflow-hidden
                                    rounded-lg bg-gray-100"
                            >
                                <img
                                    src="{{ $purchase_action->item?->primaryImage
                                        ? Storage::url($purchase_action->item->primaryImage->path)
                                        : asset('images/default-item.png') }}"
                                    alt="{{ $purchase_action->item?->name }}"

                                    class="h-full w-full object-cover"
                                >
                            </div>
                            {{-- Info --}}
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $purchase_action->item?->name ?? 'Unknown Item' }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    @if ($purchase_action->item?->sku)
                                        <span>
                                            SKU:
                                            <span class="font-medium text-gray-600">
                                                {{ $purchase_action->item->sku }}
                                            </span>
                                        </span>
                                        <span class="text-gray-300">
                                            •
                                        </span>
                                    @endif
                                    Qty:
                                    <span class="font-semibold text-gray-700">
                                        {{ $purchase_action->quantity }}
                                    </span>
                                </div>
                                {{-- Vendor --}}
                                @if ($purchase_action->vendorName)
                                    <div class="mt-2 flex items-center gap-1.5">
                                        <span class="text-[11px] text-gray-400">
                                            Vendor
                                        </span>
                                        <span
                                            class="truncate text-xs
                                                font-medium text-gray-700"
                                        >
                                            {{ $purchase_action->vendorName }}
                                        </span>
                                    </div>
                                @else
                                    <div class="mt-0.5 text-sm text-gray-400">
                                        No vendor
                                    </div>
                                @endif
                            </div>
                        </div>
                        {{-- Price / Amount / Total --}}
                        <div
                            class="rounded-lg border border-gray-100
                                bg-gray-50/70 p-3"
                        >
                            <div class="grid grid-cols-2 gap-3">
                                {{-- Unit Price --}}
                                <div>
                                    <div class="text-[11px] text-gray-400">
                                        Unit Price
                                    </div>
                                    <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                        {{ number_format($purchase_action->unitPrice, 2) }}
                                    </div>
                                </div>
                                {{-- Quantity --}}
                                <div>
                                    <div class="text-[11px] text-gray-400">
                                        Quantity
                                    </div>
                                    <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                        {{ number_format($purchase_action->quantity, 0) }}
                                    </div>
                                </div>
                                {{-- Amount --}}
                                <div>
                                    <div class="text-[11px] text-gray-400">
                                        Amount
                                    </div>
                                    <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                        {{ number_format($purchase_action->amount, 2) }}
                                    </div>
                                </div>
                                {{-- Total --}}
                                <div>
                                    <div class="text-[11px] text-gray-400">
                                        Total
                                    </div>
                                    <div class="mt-0.5 text-sm font-bold text-gray-900">
                                        {{ number_format($purchase_action->itemTotal, 2) }}
                                    </div>
                                </div>
                            </div>
                            {{-- Shipping / Discount --}}
                            @if ($purchase_action->shippingFee > 0 || $purchase_action->discount > 0)
                                <div
                                    class="mt-4 border-t border-gray-200
                                        pt-3 grid grid-cols-2 gap-3"
                                >
                                    @if ($purchase_action->shippingFee > 0)
                                        <div>
                                            <div class="text-[11px] text-gray-400">
                                                Shipping
                                            </div>
                                            <div class="mt-0.5 text-sm font-medium text-gray-700">
                                                {{ number_format($purchase_action->shippingFee, 2) }}
                                            </div>
                                        </div>
                                    @endif
                                    @if ($purchase_action->discount > 0)
                                        <div>
                                            <div class="text-[11px] text-gray-400">
                                                Discount
                                            </div>
                                            <div class="mt-0.5 text-sm font-medium text-gray-700">
                                                -{{ number_format($purchase_action->discount, 2) }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                {{-- =====================================================
                    Footer
                ====================================================== --}}
                <div
                    class="border-t border-gray-100 bg-gray-50
                        px-4 py-4 sm:px-5"
                >
                    <div
                        class="flex flex-col gap-3
                            sm:flex-row sm:items-center
                            sm:justify-between"
                    >
                        {{-- Remark --}}
                        <div class="min-w-0">
                            @if ($purchase_action->request?->remark)
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">
                                        Remark:
                                    </span>
                                    {{ $purchase_action->request->remark }}
                                </div>
                            @else
                                <div class="text-xs text-gray-400">
                                    Ordered and waiting for receiving.
                                </div>
                            @endif
                        </div>
                        {{-- Total + Receive --}}
                        <div
                            class="flex items-center justify-between gap-4
                                sm:justify-end"
                        >
                            <div class="shrink-0 text-right">
                                <div class="text-xs text-gray-500">
                                    Total
                                </div>

                                <div class="text-base font-bold text-gray-900">
                                    {{ number_format($purchase_action->itemTotal, 2) }}
                                </div>
                            </div>

                            <div class="flex items-center gap-2">

                                {{-- Refund --}}
                                <button
                                    class="flex items-center gap-1"
                                    type="button"
                                    variant="secondary"
                                    x-data
                                    x-on:click="alert('Unused funds must be returned to the Accounting Department. Please hand over the remaining cash to an Accounting Department employee.')"
                                >
                                    <div class="text-xs text-gray-500">
                                        Refund
                                    </div>
                                    <span
                                        class="flex h-4 w-4 items-center justify-center
                                            rounded-full border border-gray-300
                                            text-[10px] font-semibold text-gray-500
                                            hover:border-gray-400 hover:text-gray-700"
                                    >
                                        ?
                                    </span>
                                </button>

                                {{-- Receive --}}
                                <x-button
                                    type="button"
                                    wire:click="openReceiveModal({{ $purchase_action->id }})"
                                    wire:loading.attr="disabled"
                                >
                                    <span wire:loading.remove wire:target="openReceiveModal({{ $purchase_action->id }})">
                                        Receive
                                    </span>

                                    <span wire:loading wire:target="openReceiveModal({{ $purchase_action->id }})">
                                        Receiving...
                                    </span>
                                </x-button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty State --}}
            <div
                class="rounded-lg border border-dashed border-gray-300
                    bg-white px-6 py-12 text-center"
            >
                <div class="text-sm font-medium text-gray-900">
                    No ordered items.
                </div>
                <div class="mt-1 text-sm text-gray-500">
                    There are currently no ordered items waiting to be received.
                </div>
            </div>
        @endforelse
        {{-- Pagination --}}
        @if ($purchase_actions->hasPages())
            <div class="mt-6">
                {{ $purchase_actions->links() }}
            </div>
        @endif
    </div>
    <div
        x-data="{
            photoName: null,
            photoPreview: null,
            status: 'idle',

            resetPhoto() {
                this.photoName = null;
                this.photoPreview = null;
                this.status = 'idle';

                if (this.$refs.photo) {
                    this.$refs.photo.value = '';
                }
            },

            resizeImage(file) {
                return new Promise((resolve, reject) => {
                    const maxSize = 1024;
                    const reader = new FileReader();

                    reader.onload = (event) => {
                        const img = new Image();

                        img.onload = () => {
                            let width = img.width;
                            let height = img.height;

                            if (width > maxSize || height > maxSize) {
                                if (width > height) {
                                    height = Math.round(
                                        height * (maxSize / width)
                                    );
                                    width = maxSize;
                                } else {
                                    width = Math.round(
                                        width * (maxSize / height)
                                    );
                                    height = maxSize;
                                }
                            }

                            const canvas = document.createElement('canvas');
                            canvas.width = width;
                            canvas.height = height;

                            const ctx = canvas.getContext('2d');

                            ctx.drawImage(
                                img,
                                0,
                                0,
                                width,
                                height
                            );

                            canvas.toBlob(
                                (blob) => {
                                    if (!blob) {
                                        reject(
                                            new Error(
                                                'Image resize failed.'
                                            )
                                        );
                                        return;
                                    }

                                    resolve(
                                        new File(
                                            [blob],
                                            file.name.replace(
                                                /\.[^/.]+$/,
                                                '.jpg'
                                            ),
                                            {
                                                type: 'image/jpeg',
                                            }
                                        )
                                    );
                                },
                                'image/jpeg',
                                0.85
                            );
                        };

                        img.onerror = reject;
                        img.src = event.target.result;
                    };

                    reader.onerror = reject;
                    reader.readAsDataURL(file);
                });
            }
        }"
        x-on:reset-item-photo.window="resetPhoto()"
    >
        <x-dialog-modal wire:model.live="itemPhotoModal">
            <x-slot name="title">
                <div>
                    <div class="text-lg font-semibold text-gray-900">
                        Upload Received Item
                    </div>
                    <p class="mt-1 text-sm text-gray-500"></p>
                </div>
            </x-slot>
            <x-slot name="content">
                {{-- Receipt Photo --}}
                <div class="">

                    {{-- Hidden file input --}}
                    <input
                        type="file"
                        class="hidden"
                        x-ref="photo"
                        accept="image/*"
                        capture="environment"
                        x-on:change="
                            const originalFile = $event.target.files[0];

                            if (!originalFile) {
                                return;
                            }

                            status = 'resizing';
                            photoPreview = null;
                            photoName = null;

                            try {
                                const resizedFile = await resizeImage(originalFile);

                                photoName = resizedFile.name;
                                status = 'uploading';

                                const reader = new FileReader();

                                reader.onload = (e) => {
                                    photoPreview = e.target.result;
                                };

                                reader.readAsDataURL(resizedFile);

                                $wire.upload(
                                    'itemPhoto',
                                    resizedFile,
                                    () => {
                                        status = 'ready';
                                    },
                                    () => {
                                        status = 'idle';
                                    }
                                );

                            } catch (error) {
                                console.error(error);
                                status = 'idle';
                            }
                        "
                    />
                    <label
                            for="photo"
                            class="block text-sm font-medium text-gray-700"
                        >
                        {{ __('Item Photo') }}
                        <span class="text-red-500">*</span>
                    </label>

                    {{-- Preview --}}
                    <div
                        class="mt-2"
                        x-show="photoPreview"
                        x-cloak
                    >
                        <img
                            :src="photoPreview"
                            alt="Receipt preview"
                            class="w-full max-h-96 object-contain rounded-lg border border-gray-200 bg-gray-50"
                        >
                    </div>

                    {{-- Select / Take Photo --}}
                    <x-secondary-button
                        class="mt-2 me-2"
                        type="button"
                        x-on:click.prevent="$refs.photo.click()"
                    >
                        <span x-show="!photoPreview">
                            {{ __('Add Item Photo') }}
                        </span>

                        <span x-show="photoPreview">
                            {{ __('Replace Item Photo') }}
                        </span>
                    </x-secondary-button>

                    <x-input-error
                        for="itemPhoto"
                        class="mt-2"
                    />
                </div>
                {{-- Actual Purchase Amount --}}
                <div class="mt-5">
                    {{-- Original Amount --}}
                    <div class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-2.5">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs font-medium text-gray-500">
                                Original Amount
                            </span>
                            <span class="text-sm font-semibold text-gray-800">
                                ₱{{ number_format($originalAmount ?? 0, 2) }}
                            </span>
                        </div>
                        <div class="mt-1 text-[10px] text-gray-400">
                            Amount released for this purchase
                        </div>
                    </div>
                    {{-- Actual Purchase Amount --}}
                    <div class="mt-2">
                        <label
                            for="purchaseAmount"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Actual Purchase Amount
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-400">
                                ₱
                            </span>
                            <input
                                type="tel"
                                x-mask:dynamic="$money($input, '.', ',', 2)"
                                id="purchaseAmount"
                                wire:model.defer="amount"
                                class="block w-full rounded-lg border-gray-300 pl-8 text-sm shadow-sm
                                    focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="0.00"
                                autocomplete="off"
                            >
                        </div>
                        <x-input-error
                            for="amount"
                            class="mt-1.5"
                        />
                    </div>
                </div>
                {{-- comment --}}
                <div class="mt-5 space-y-5">
                    <div
                        x-data="{
                            count: {{ strlen($comment ?? '') }}
                        }"
                    >
                        <label
                            for="receiptComment"
                            class="block text-sm font-medium text-gray-700"
                        >
                            {{ __('Comment') }}
                        </label>
                        <textarea
                            id="receiptComment"
                            wire:model.defer="comment"
                            x-on:input="count = $event.target.value.length"
                            rows="4"
                            maxlength="500"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 resize-none"
                            placeholder="e.g. Transaction number, voucher number, approval number..."
                        ></textarea>
                        @error('comment')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                        <div class="mt-1 text-right text-xs text-gray-400">
                            <span x-text="count"></span>/500
                        </div>
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button
                    type="button"
                    wire:click="$toggle('itemPhotoModal')"
                    wire:loading.attr="disabled"
                    x-bind:disabled="status === 'resizing' || status === 'uploading'"
                >
                    Close
                </x-secondary-button>
                <x-button
                    x-show="status !== 'idle'"
                    type="button"
                    class="ml-3"
                    wire:click="saveItemPhoto"
                    wire:loading.attr="disabled"
                    x-bind:disabled="status !== 'ready'"
                >
                    <span x-show="status === 'resizing'">
                        Resizing...
                    </span>

                    <span x-show="status === 'uploading'">
                        Uploading...
                    </span>

                    <span
                        x-show="status === 'ready'"
                        wire:loading.remove
                        wire:target="saveItemPhoto"
                    >
                        Save
                    </span>

                    <span
                        x-show="status === 'ready'"
                        wire:loading
                        wire:target="saveItemPhoto"
                    >
                        Saving...
                    </span>
                </x-button>
            </x-slot>
        </x-dialog-modal>
    </div>
    
</div>