<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">
            Budget Requests
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Purchase requests waiting for budget processing.
        </p>
    </div>
    <div class="space-y-4">
        @if (count($requests) > 0)
            @foreach ($requests as $request)
                @php
                    $allCash = $request->audit_workflow->purchaseWorkflowItems
                        ->every(fn ($workflowItem) =>
                            $workflowItem->purchaseItem->disbursement_type_name === 'Cash'
                        );
                @endphp
                <div
                    class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm"
                    wire:key="request-{{ $request->id }}"
                >
                    {{-- =====================================================
                        Header
                    ====================================================== --}}
                    <div class="border-b border-gray-100 px-4 py-4 sm:px-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-sm font-semibold text-gray-900 sm:text-base">
                                        {{ $request->request_no }}
                                    </h2>
                                    <span class="rounded-full bg-yellow-50 px-2.5 py-1 text-[10px] font-medium text-yellow-700 sm:text-xs">
                                        Pending
                                    </span>
                                </div>
                                <div class="mt-1.5 text-xs text-gray-500 sm:text-sm sm:flex gap-2">
                                    <span>
                                        Requested by
                                    </span>
                                    <p
                                        class="font-medium text-gray-700"
                                        wire:loading.attr="disabled"
                                    >
                                        {{ $request->user->name }}
                                        @if ($request->department)
                                            <span class="mx-1 text-gray-300">
                                                ·
                                            </span>
                                            <span
                                                class="font-medium text-gray-700"
                                            >
                                                {{ $request->department->name }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="shrink-0 text-right text-[11px] text-gray-400 sm:text-sm sm:text-gray-500">
                                {{ $request->created_at->format('Y-m-d') }}
                                <div class="sm:hidden">
                                    {{ $request->created_at->format('H:i') }}
                                </div>
                                <span class="hidden sm:inline">
                                    {{ $request->created_at->format('H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    {{-- =====================================================
                        Items
                    ====================================================== --}}
                    <div class="divide-y divide-gray-100">
                        @foreach ($request->audit_workflow->purchaseWorkflowItems as $workflowItem)
                            @php
                                $purchaseItem = $workflowItem->purchaseItem;
                                $item = $purchaseItem->item;
                                $itemVendor = $purchaseItem->itemVendor;
                            @endphp
                            <div
                                class="px-4 py-4 sm:px-5"
                                wire:key="workflow-item-{{ $workflowItem->id }}"
                            >
                                {{-- =================================================
                                    DESKTOP
                                ================================================== --}}
                                <div class="hidden items-center gap-4 md:flex justify-between">
                                    <div class="flex items-center gap-4">
                                        {{-- Image --}}
                                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-md bg-gray-100">
                                            <img
                                                src="{{ $item?->primaryImage
                                                    ? Storage::url($item->primaryImage->path)
                                                    : asset('images/default-item.png') }}"
                                                alt="{{ $purchaseItem->item_name }}"
                                                class="h-full w-full object-cover"
                                            >
                                        </div>
                                        {{-- Item Info --}}
                                        <div class="">
                                            <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                                Name
                                            </div>
                                            <span
                                                x-on:click="$dispatch('open-item', {
                                                    itemId: {{ $purchaseItem->item_id }}
                                                })"
                                                class="block max-w-full truncate text-sm font-medium text-gray-900 hover:text-gray-700"
                                                wire:loading.attr="disabled"
                                            >
                                                {{ $purchaseItem->item_name }}
                                            </span>
                                            {{-- Vendor --}}
                                            <div class="w-52 min-w-0 shrink-0">
                                                <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                                    Vendor
                                                </div>
                                                @if ($itemVendor)
                                                    <span
                                                        x-on:click="$dispatch('open-vendor', {
                                                            vendorId: {{ $itemVendor->vendor_id }}
                                                        })"
                                                        class="mt-0.5 block w-full truncate text-left text-sm font-medium text-gray-700 hover:text-gray-900"
                                                        wire:loading.attr="disabled"
                                                    >
                                                        {{ $purchaseItem->vendor_name }}
                                                    </span>
                                                @else
                                                    <div class="mt-0.5 text-sm text-gray-500">
                                                        {{ $purchaseItem->vendor_name ?: '-' }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="mt-1 flex items-center gap-3 text-xs text-gray-500">
                                                <span>
                                                    {{ $purchaseItem->sku }}
                                                </span>
                                                <span class="text-gray-300">
                                                    |
                                                </span>
                                                <span>
                                                    Qty:
                                                    <span class="font-semibold text-gray-700">
                                                        {{ $purchaseItem->quantity }}
                                                    </span>
                                                </span>
                                                <span class="text-gray-300">
                                                    |
                                                </span>
                                                <span>
                                                    Unit Price:
                                                    <span class="font-semibold text-gray-900">
                                                        {{ number_format($purchaseItem->unit_price, 2) }}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="w-[240px] shrink-0 rounded-xl border border-emerald-100 bg-emerald-50/60 px-4 py-3 text-right">
                                            {{-- Payment Type --}}
                                            <div class="flex items-center justify-end gap-2">
                                                <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">
                                                    Payment
                                                </span>
                                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold uppercase text-emerald-700">
                                                    Cash
                                                </span>
                                            </div>
                                            {{-- Amount --}}
                                            <div class="mt-1 whitespace-nowrap text-2xl font-extrabold leading-tight tracking-tight text-gray-900">
                                                ₱{{ number_format($purchaseItem->amount, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Actions --}}
                                    <div class="flex shrink-0 items-center gap-2">
                                        <x-approve-button
                                            type="button"
                                            wire:click="openAttachReceiptModal({{ $workflowItem->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="openAttachReceiptModal({{ $workflowItem->id }})"
                                        >
                                            {{__('Released Cash')}}
                                        </x-approve-button>
                                        <x-approve-button
                                            type="button"
                                            wire:click="openPlaceOrderModal({{ $workflowItem->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="openPlaceOrderModal({{ $workflowItem->id }})"
                                        >
                                            {{ __('Place Order') }}
                                        </x-approve-button>
                                    </div>
                                </div>
                                {{-- =================================================
                                    MOBILE
                                ================================================== --}}
                                <div class="md:hidden">
                                    {{-- Item Header --}}
                                    <div class="flex items-start gap-3">
                                        {{-- Image --}}
                                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-md bg-gray-100">
                                            <img
                                                src="{{ $item?->primaryImage
                                                    ? Storage::url($item->primaryImage->path)
                                                    : asset('images/default-item.png') }}"
                                                alt="{{ $purchaseItem->item_name }}"
                                                class="h-full w-full object-cover"
                                            >
                                        </div>
                                        {{-- Item Name / SKU --}}
                                        <div class="min-w-0 flex-1">
                                            <button
                                                x-on:click="$dispatch('open-item', {
                                                    itemId: {{ $purchaseItem->item_id }}
                                                })"
                                                class="block w-full truncate text-left text-sm font-semibold text-gray-900"
                                                wire:loading.attr="disabled"
                                            >
                                                {{ $purchaseItem->item_name }}
                                            </button>
                                            <div class="mt-1 truncate text-xs text-gray-500">
                                                {{ $purchaseItem->sku }}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Item Details --}}
                                    <div class="mt-3 rounded-lg bg-gray-50 p-3">
                                        {{-- Quantity / Unit Price --}}
                                        <div class="grid grid-cols-2 gap-4">
                                            {{-- Quantity --}}
                                            <div class="min-w-0">
                                                <div class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                                    Qty
                                                </div>
                                                <div class="mt-1 text-sm font-semibold text-gray-900">
                                                    {{ number_format($purchaseItem->quantity) }}
                                                </div>
                                            </div>
                                            {{-- Unit Price --}}
                                            <div class="min-w-0">
                                                <div class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                                    Unit Price
                                                </div>

                                                <div class="mt-1 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                    ₱{{ number_format($purchaseItem->unit_price, 2) }}
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Amount --}}
                                        <div class="mt-3 border-t border-gray-200 pt-3">
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="text-[10px] font-semibold uppercase tracking-wide text-emerald-600">
                                                    Amount to Release
                                                </div>

                                                <div class="whitespace-nowrap text-xl font-extrabold leading-tight tracking-tight text-emerald-800">
                                                    ₱{{ number_format($purchaseItem->amount, 2) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Vendor --}}
                                    <div class="mt-3">
                                        <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                            Vendor
                                        </div>
                                        @if ($itemVendor)
                                            <button
                                                x-on:click="$dispatch('open-vendor', {
                                                    vendorId: {{ $itemVendor->vendor_id }}
                                                })"
                                                class="mt-0.5 block max-w-full truncate text-left text-sm font-medium text-gray-700"
                                                wire:loading.attr="disabled"
                                            >
                                                {{ $purchaseItem->vendor_name }}
                                            </button>
                                        @else
                                            <div class="mt-0.5 text-sm text-gray-500">
                                                {{ $purchaseItem->vendor_name ?: '-' }}
                                            </div>
                                        @endif
                                    </div>
                                    {{-- Mobile Actions --}}
                                    <div class="mt-4">
                                        <x-approve-button
                                            type="button"
                                            wire:click="releaseCash({{ $workflowItem->id }})"
                                            wire:confirm="Are you sure you want to approve this item?"
                                            wire:loading.attr="disabled"
                                            wire:target="releaseCash({{ $workflowItem->id }})"
                                            class="w-full"
                                        >
                                            {{__('Released Cash')}}
                                        </x-approve-button>
                                    </div>
                                </div>
                                <div class="mt-3 border-t border-gray-100 pt-3">
                                    <div class="flex items-center justify-between">
                                        <div class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                            Status
                                        </div>
                                        @if ($workflowItem->status === 'pending')
                                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-bold uppercase text-amber-700">
                                                Pending
                                            </span>
                                        @elseif ($workflowItem->status === 'ordered')
                                            <span class="rounded-full bg-blue-100 px-2.5 py-1 text-[10px] font-bold uppercase text-blue-700">
                                                Ordered
                                            </span>
                                        @endif
                                    </div>
                                    @if ($workflowItem->status === 'pending')
                                        <div class="mt-2 text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                            Payment Details
                                        </div>
                                        <div class="mt-1 rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-600">
                                            <div class="">
                                                {{ $purchaseItem->payment_details ?: '-' }}
                                            </div>
                                        </div>
                                    @elseif ($workflowItem->status === 'ordered')
                                        <div class="mt-2 text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                            Order History
                                        </div>

                                        <div class="mt-1 rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-600">
                                            <div class="break-words whitespace-pre-wrap leading-relaxed">
                                                {{ $purchaseItem->remark ?: '-' }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- =====================================================
                        Footer
                    ====================================================== --}}
                    <div class="border-t border-gray-100 bg-gray-50 px-4 py-4 sm:px-5">
                        {{-- Desktop Footer --}}
                        <div class="hidden sm:flex items-center justify-end gap-4">
                            <div class="text-right">
                                <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">
                                    Total Amount to Release
                                </div>
                                <div class="mt-0.5 whitespace-nowrap text-3xl font-extrabold leading-none tracking-tight text-gray-900">
                                    ₱{{ number_format($request->audit_total, 2) }}
                                </div>
                            </div>
                        </div>
                        {{-- Mobile Footer --}}
                        <div class="sm:hidden rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                            <div class="min-w-0 rounded-lg bg-emerald-50 p-3">
                                <div class="text-[10px] font-semibold uppercase tracking-wide text-emerald-600">
                                    Amount
                                </div>
                                <div class="mt-1 whitespace-nowrap text-xl font-extrabold leading-tight tracking-tight text-emerald-800">
                                    ₱{{ number_format($request->audit_total, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            {{-- Pagination --}}
            <div class="mt-6">
                {{ $requests->links() }}
            </div>
        @else
            <div class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-12 text-center">
                <div class="text-sm font-medium text-gray-900">
                    No pending budget requests.
                </div>
                <div class="mt-1 text-sm text-gray-500">
                    There are currently no purchase requests waiting for budget.
                </div>
            </div>
        @endif
    </div>
    <x-dialog-modal wire:model.live="remarkModal">
        <x-slot name="title">
            <div>
                <div class="text-lg font-semibold text-gray-900">
                    Complete Order Item
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Please provide the payment or accounting information for this item.
                </p>
            </div>
        </x-slot>
        <x-slot name="content">
            <div class="space-y-5">
                {{-- Remark --}}
                <div
                    x-data="{
                        count: {{ strlen($remark ?? '') }}
                    }"
                >
                    <label
                        for="remark"
                        class="block text-sm font-medium text-gray-700"
                    >
                        {{ __('Remark') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="remark"
                        wire:model.defer="remark"
                        x-on:input="count = $event.target.value.length"
                        rows="4"
                        maxlength="500"
                        class="mt-2 block w-full rounded-lg border-gray-300 text-sm shadow-sm
                            focus:border-emerald-500 focus:ring-emerald-500"
                        placeholder="e.g. Transaction number, voucher number, approval number..."
                    ></textarea>
                    @error('remark')
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
            <div class="flex w-full justify-end gap-2">
                <x-secondary-button
                    type="button"
                    wire:click="$set('remarkModal', false)"
                    wire:loading.attr="disabled"
                >
                    Cancel
                </x-secondary-button>
                <x-approve-button
                    type="button"
                    wire:click="complete"
                    wire:loading.attr="disabled"
                    wire:target="complete"
                >
                    <span wire:loading.remove wire:target="complete">
                        {{ __('Complete Purchase') }}
                    </span>
                    <span wire:loading wire:target="complete">
                        {{ __('Processing...') }}
                    </span>
                </x-approve-button>
            </div>
        </x-slot>
    </x-dialog-modal>
    <x-dialog-modal wire:model.live="showAttachReceiptModal">
        <x-slot name="title">
            <div>
                <div class="text-lg font-semibold text-gray-900">
                    Complete Order Item
                </div>

                <p class="mt-1 text-sm text-gray-500">
                    Please provide the payment or accounting information for this item.
                </p>
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
                            'recipientPhoto',
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
                    {{ __('Receipt Photo') }}
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
                        {{ __('Add Receipt Photo') }}
                    </span>

                    <span x-show="photoPreview">
                        {{ __('Replace Receipt Photo') }}
                    </span>
                </x-secondary-button>

                <x-input-error
                    for="recipientPhoto"
                    class="mt-2"
                />
            </div>


            {{-- Remark --}}
            <div class="mt-5 space-y-5">
                <div
                    x-data="{
                        count: {{ strlen($remark ?? '') }}
                    }"
                >
                    <label
                        for="remark"
                        class="block text-sm font-medium text-gray-700"
                    >
                        {{ __('Remark') }}
                        <span class="text-red-500">*</span>
                    </label>

                    <textarea
                        id="remark"
                        wire:model.defer="remark"
                        x-on:input="count = $event.target.value.length"
                        rows="4"
                        maxlength="500"
                        class="mt-2 block w-full rounded-lg border-gray-300 text-sm shadow-sm
                            focus:border-emerald-500 focus:ring-emerald-500"
                        placeholder="e.g. Transaction number, voucher number, approval number..."
                    ></textarea>

                    @error('remark')
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
            <div class="flex w-full justify-end gap-2">

                <x-secondary-button
                    type="button"
                    wire:click="$set('showAttachReceiptModal', false)"
                    wire:loading.attr="disabled"
                >
                    Cancel
                </x-secondary-button>

                <x-approve-button
                    type="button"
                    wire:click="releaseCash"
                    wire:loading.attr="disabled"
                    wire:target="releaseCash"
                >
                    <span wire:loading.remove wire:target="releaseCash">
                        {{ __('Complete Purchase') }}
                    </span>

                    <span wire:loading wire:target="releaseCash">
                        {{ __('Processing...') }}
                    </span>
                </x-approve-button>

            </div>
        </x-slot>
    </x-dialog-modal>
</div>