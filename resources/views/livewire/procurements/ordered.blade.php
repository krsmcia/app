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
            @php
                $workflowItem = $purchase_action->purchaseWorkflowItem;
                $purchaseItem = $workflowItem?->purchaseItem;
                $item = $purchaseItem?->item;
                $request = $purchaseItem?->purchaseRequest;
                $quantity = (float) ($purchaseItem?->quantity ?? 0);
                $unitPrice = (float) ($purchaseItem?->unit_price ?? 0);
                $amount = (float) ($quantity * $unitPrice);
                $shippingFee = (float) ($purchaseItem?->shipping_fee ?? 0);
                $discount = (float) ($purchaseItem?->discount ?? 0);

                $vendorName = $purchaseItem?->vendor_name;

                $itemTotal = max(
                    0,
                    $amount + $shippingFee - $discount
                );
            @endphp
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

                            @if ($request)
                                <h2 class="font-semibold text-gray-900">
                                    {{ $request->request_no }}
                                </h2>
                            @else
                                <h2 class="font-semibold text-gray-900">
                                    Purchase #{{ $purchase_action->id }}
                                </h2>
                            @endif


                            <span
                                class="rounded-full bg-blue-50 px-2.5 py-1
                                    text-xs font-medium text-blue-700"
                            >
                                Ordered
                            </span>

                        </div>


                        @if ($request)

                            <div class="mt-1 text-sm text-gray-500">

                                Requested by

                                <span class="font-medium text-gray-700">
                                    {{ $request->user?->name }}
                                </span>

                                @if ($request->department)
                                    · {{ $request->department->name }}
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
                                src="{{ $item?->primaryImage
                                    ? Storage::url($item->primaryImage->path)
                                    : asset('images/default-item.png') }}"
                                alt="{{ $item?->item_name }}"
                                class="h-full w-full object-cover"
                            >
                        </div>


                        {{-- Item Info --}}
                        <div class="min-w-0">

                            <div
                                class="truncate text-sm font-semibold text-gray-900"
                            >
                                {{ $item?->name ?? 'Unknown Item' }}
                            </div>
                            <div
                                class="mt-1 flex items-center gap-2
                                    text-xs text-gray-500"
                            >
                                @if ($item?->sku)
                                    <span>
                                        SKU:
                                        <span class="font-medium text-gray-600">
                                            {{ $item->sku }}
                                        </span>
                                    </span>
                                    <span class="text-gray-300">
                                        •
                                    </span>
                                @endif
                                <span>
                                    Qty:
                                    <span class="font-semibold text-gray-700">
                                        {{ $quantity }}
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
                            @if ($vendorName)
                                <div
                                    class="mt-0.5 truncate text-sm
                                        font-semibold text-gray-800"
                                    title="{{ $vendorName }}"
                                >
                                    {{ $vendorName }}
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
                                {{ number_format($unitPrice, 2) }}
                            </div>
                        </div>
                        {{-- Quantity --}}
                        <div>
                            <div class="text-[10px] text-gray-400">
                                Quantity
                            </div>
                            <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                {{ number_format($quantity, 0) }}
                            </div>
                        </div>
                        {{-- Amount --}}
                        <div>
                            <div class="text-[10px] text-gray-400">
                                Amount
                            </div>
                            <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                {{ number_format($amount, 2) }}
                            </div>
                            @if ($shippingFee > 0 || $discount > 0)
                                <div class="mt-1 space-y-0.5 text-[10px] leading-tight">
                                    @if ($shippingFee > 0)
                                        <div class="text-gray-400">
                                            Shipping:
                                            <span class="font-medium text-gray-600">
                                                +{{ number_format($shippingFee, 2) }}
                                            </span>
                                        </div>
                                    @endif
                                    @if ($discount > 0)
                                        <div class="text-gray-400">
                                            Discount:
                                            <span class="font-medium text-gray-600">
                                                -{{ number_format($discount, 2) }}
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
                                {{ number_format($itemTotal, 2) }}
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
                                    src="{{ $item?->primaryImage
                                        ? Storage::url($item->primaryImage->path)
                                        : asset('images/default-item.png') }}"
                                    alt="{{ $item?->name }}"
                                    class="h-full w-full object-cover"
                                >
                            </div>
                            {{-- Info --}}
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $item?->name ?? 'Unknown Item' }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    @if ($item?->sku)
                                        SKU:
                                        <span class="font-medium">
                                            {{ $item->sku }}
                                        </span>
                                        <span class="mx-1 text-gray-300">
                                            •
                                        </span>
                                    @endif
                                    Qty:
                                    <span class="font-semibold text-gray-700">
                                        {{ $quantity }}
                                    </span>
                                </div>
                                {{-- Vendor --}}
                                @if ($vendorName)
                                    <div class="mt-2 flex items-center gap-1.5">
                                        <span class="text-[11px] text-gray-400">
                                            Vendor
                                        </span>
                                        <span
                                            class="truncate text-xs
                                                font-medium text-gray-700"
                                        >
                                            {{ $vendorName }}
                                        </span>
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
                                        {{ number_format($unitPrice, 2) }}
                                    </div>
                                </div>
                                {{-- Quantity --}}
                                <div>
                                    <div class="text-[11px] text-gray-400">
                                        Quantity
                                    </div>
                                    <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                        {{ number_format($quantity, 0) }}
                                    </div>
                                </div>
                                {{-- Amount --}}
                                <div>
                                    <div class="text-[11px] text-gray-400">
                                        Amount
                                    </div>
                                    <div class="mt-0.5 text-sm font-semibold text-gray-900">
                                        {{ number_format($unitPrice, 2) }}
                                    </div>
                                </div>
                                {{-- Total --}}
                                <div>
                                    <div class="text-[11px] text-gray-400">
                                        Total
                                    </div>
                                    <div class="mt-0.5 text-sm font-bold text-gray-900">
                                        {{ number_format($itemTotal, 2) }}
                                    </div>
                                </div>
                            </div>
                            {{-- Shipping / Discount --}}
                            @if ($shippingFee > 0 || $discount > 0)
                                <div
                                    class="mt-4 border-t border-gray-200
                                        pt-3 grid grid-cols-2 gap-3"
                                >
                                    @if ($shippingFee > 0)
                                        <div>
                                            <div class="text-[11px] text-gray-400">
                                                Shipping
                                            </div>
                                            <div class="mt-0.5 text-sm font-medium text-gray-700">
                                                {{ number_format($shippingFee, 2) }}
                                            </div>
                                        </div>
                                    @endif
                                    @if ($discount > 0)
                                        <div>
                                            <div class="text-[11px] text-gray-400">
                                                Discount
                                            </div>
                                            <div class="mt-0.5 text-sm font-medium text-gray-700">
                                                -{{ number_format($discount, 2) }}
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
                            @if ($request?->remark)
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">
                                        Remark:
                                    </span>
                                    {{ $request->remark }}
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
                                    {{ number_format($itemTotal, 2) }}
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
            <div
                x-data="{
                    photoName: null,
                    photoPreview: null,
                    resetPhoto() {
                        this.photoName = null;
                        this.photoPreview = null;

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
                                                    new Error('Image resize failed.')
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
                x-init="
                    $refs.photo.addEventListener('change', async (event) => {
                        const originalFile = event.target.files[0];
                        if (!originalFile) {
                            return;
                        }
                        const resizedFile = await resizeImage(originalFile);
                        photoName = resizedFile.name;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            photoPreview = e.target.result;
                        };
                        reader.readAsDataURL(resizedFile);

                        $wire.upload(
                            'itemPhoto',
                            resizedFile
                        );
                    });

                    window.addEventListener('reset-recipient-photo', () => {
                        resetPhoto();
                    });
                "
            >

                {{-- Hidden file input --}}
                <input
                    type="file"
                    id="photo"
                    class="hidden"
                    x-ref="photo"
                    accept="image/*"
                    capture="environment"
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
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button
                type="button"
                wire:click="$toggle('itemPhotoModal')" wire:loading.attr="disabled"
            >
                Close
            </x-secondary-button>
            <x-button
                type="button"
                class="ml-3"
                wire:click="saveItemPhoto"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="saveItemPhoto">
                    Save
                </span>
                <span wire:loading wire:target="saveItemPhoto">
                    Saving...
                </span>
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>