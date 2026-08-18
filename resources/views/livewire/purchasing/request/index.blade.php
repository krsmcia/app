<div class="max-w-3xl mx-auto px-4 pt-6 pb-28">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            Purchase Request
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Submit a request for purchasing goods or services.
        </p>
    </div>
    {{-- Basic Information --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-5">
        <h2 class="text-lg font-semibold text-gray-900 mb-5">
            Request Information
        </h2>
        <div class="space-y-4">
            {{-- Disbursement Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Disbursement Type
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach ($this->disbursementTypes as $type)
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                name="disbursement_type"
                                value="{{ $type->id }}"
                                class="peer sr-only"
                            >
                            <div
                                class="flex items-center justify-center
                                    min-h-12 px-4 py-3
                                    border border-gray-300 rounded-xl
                                    bg-white
                                    transition
                                    peer-checked:border-indigo-600
                                    peer-checked:bg-indigo-50
                                    peer-checked:text-indigo-700
                                    active:scale-[0.98]"
                            >
                                <span class="text-sm font-medium">
                                    {{ $type->name }}
                                </span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            {{-- Purpose --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Purpose / Reason
                </label>
                <textarea
                    rows="4"
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Why do you need this purchase?"
                ></textarea>
            </div>
        </div>
    </div>
    {{-- Items --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-5">
        {{-- Header --}}
        <div class="sticky top-12 z-20 bg-white py-3 mb-5 border-b border-gray-100">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Purchase Items
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Add the items you want to purchase.
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="addItem"
                    class="shrink-0 inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-gray-900 text-white hover:bg-gray-800"
                >
                    + Add Item
                </button>

            </div>
        </div>


        {{-- Items --}}
        <div class="space-y-4">

            @foreach ($items as $index => $item)

                <div
                    wire:key="purchase-item-{{ $index }}"
                    class="border border-gray-200 rounded-xl p-4"
                >

                    {{-- Item Header --}}
                    <div class="flex items-center justify-between mb-4">

                        <div class="flex items-center gap-2">

                            <span
                                class="flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 text-sm font-semibold"
                            >
                                {{ $index + 1 }}
                            </span>

                            <span class="font-medium text-gray-900">
                                Item
                            </span>

                        </div>

                        @if (count($items) > 1)
                            <button
                                type="button"
                                wire:click="removeItem({{ $index }})"
                                wire:confirm="Are you sure you want to remove this item?"
                                class="text-sm text-red-500 hover:text-red-700"
                            >
                                Remove
                            </button>
                        @endif

                    </div>


                    {{-- Description --}}
                    <div class="mb-4">

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>

                        <input
                            type="text"
                            wire:model.blur="items.{{ $index }}.description"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="e.g. A4 Bond Paper"
                        >

                        @error("items.$index.description")
                            <p class="mt-1 text-sm text-red-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Item Photo --}}
                    <div class="mb-4">

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Item Photo
                        </label>

                        <div class="flex items-start gap-4">

                            {{-- Preview --}}
                            @if (!empty($items[$index]['image']))

                                <div class="relative">

                                    <img
                                        src="{{ $items[$index]['image']->temporaryUrl() }}"
                                        class="w-28 h-28 object-cover rounded-lg border border-gray-200"
                                    >

                                    <button
                                        type="button"
                                        wire:click="$set('items.{{ $index }}.image', null)"
                                        class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-red-500 text-white text-sm flex items-center justify-center hover:bg-red-600"
                                    >
                                        ×
                                    </button>

                                </div>

                            @else

                                {{-- Upload --}}
                                <label
                                    class="w-28 h-28 border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:bg-gray-50 transition"
                                >

                                    <span class="text-2xl">
                                        📷
                                    </span>

                                    <span class="text-xs text-gray-500 mt-1">
                                        Add Photo
                                    </span>

                                    <input
                                        type="file"
                                        accept="image/*"
                                        wire:model="items.{{ $index }}.image"
                                        class="hidden"
                                    >

                                </label>

                            @endif


                            {{-- Uploading --}}
                            <div
                                wire:loading
                                wire:target="items.{{ $index }}.image"
                                class="text-sm text-gray-500 pt-2"
                            >
                                Uploading...
                            </div>

                        </div>

                        @error("items.$index.image")
                            <p class="mt-1 text-sm text-red-500">
                                {{ $message }}
                            </p>
                        @enderror

                        <p class="mt-1 text-xs text-gray-400">
                            JPG, PNG or WEBP. Max 5MB.
                        </p>

                    </div>


                    {{-- Price / Quantity --}}
                    <div class="grid grid-cols-2 gap-3">

                        {{-- Unit Price --}}
                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Unit Price
                            </label>

                            <div class="relative">

                                <span
                                    class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"
                                >
                                    ₱
                                </span>

                                <input
                                    type="number"
                                    @wheel.prevent
                                    wire:model.live="items.{{ $index }}.unit_price"
                                    min="0"
                                    step="0.01"
                                    class="w-full pl-8 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                >

                            </div>

                            @error("items.$index.unit_price")
                                <p class="mt-1 text-sm text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Quantity --}}
                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Quantity
                            </label>

                            <input
                                type="number"
                                @wheel.prevent
                                wire:model.live="items.{{ $index }}.quantity"
                                min="1"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            @error("items.$index.quantity")
                                <p class="mt-1 text-sm text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>


                    {{-- Amount --}}
                    <div
                        class="mt-4 flex justify-between items-center bg-gray-50 rounded-lg px-4 py-3"
                    >

                        <span class="text-sm text-gray-600">
                            Amount
                        </span>

                        <span class="font-semibold text-gray-900">
                            ₱{{ number_format(
                                ((float) ($item['unit_price'] ?? 0))
                                * ((int) ($item['quantity'] ?? 0)),
                                2
                            ) }}
                        </span>

                    </div>


                    {{-- Remarks --}}
                    <div class="mt-4">

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Remarks
                        </label>

                        <input
                            type="text"
                            wire:model.blur="items.{{ $index }}.remarks"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Optional"
                        >

                        @error("items.$index.remarks")
                            <p class="mt-1 text-sm text-red-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>

            @endforeach

        </div>

    </div>
    {{-- Attachment --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-5">
        <h2 class="text-lg font-semibold text-gray-900 mb-1">
            Attachments
        </h2>
        <p class="text-sm text-gray-500 mb-4">
            Attach quotation, receipt, image or other supporting documents.
        </p>
        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center">
            <div class="text-gray-400 text-3xl mb-2">
                📎
            </div>
            <p class="text-sm text-gray-600">
                Upload supporting documents
            </p>
            <button
                type="button"
                class="mt-3 px-4 py-2 text-sm border rounded-lg hover:bg-gray-50"
            >
                Choose Files
            </button>
        </div>
    </div>
    {{-- Receipt Summary --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-5">
        <div class="text-center mb-5">
            <div class="text-xs tracking-widest text-gray-400 uppercase">
                Purchase Request
            </div>
        </div>
        {{-- Receipt Items --}}
        <div class="border-t border-dashed border-gray-300 pt-4">
            @forelse ($items as $index => $item)
                @php
                    $amount =
                        ((float) ($item['unit_price'] ?? 0))
                        * ((int) ($item['quantity'] ?? 0));
                @endphp
                @if (!empty($item['description']) || $amount > 0)
                    <div class="flex justify-between py-2 text-sm">

                        <div class="min-w-0 pr-4">

                            <div class="font-medium text-gray-800 truncate">
                                {{ $item['description'] ?: 'Unnamed Item' }}
                            </div>

                            <div class="text-xs text-gray-500">
                                {{ $item['quantity'] ?? 0 }}
                                ×
                                ₱{{ number_format($item['unit_price'] ?? 0, 2) }}
                            </div>

                        </div>

                        <div class="font-medium shrink-0">
                            ₱{{ number_format($amount, 2) }}
                        </div>

                    </div>

                @endif

            @empty

                <div class="py-4 text-center text-sm text-gray-400">
                    No items added
                </div>

            @endforelse

        </div>


        {{-- Total --}}
        @php
            $total = collect($items)->sum(function ($item) {
                return ((float) ($item['unit_price'] ?? 0))
                    * ((int) ($item['quantity'] ?? 0));
            });
        @endphp

        <div class="border-t border-dashed border-gray-300 mt-3 pt-4">

            <div class="flex justify-between items-center">

                <span class="text-base font-semibold text-gray-700">
                    TOTAL
                </span>

                <span class="text-2xl font-bold text-gray-900">
                    ₱{{ number_format($total, 2) }}
                </span>

            </div>

        </div>

    </div>


    {{-- Fixed Bottom Summary --}}
    <div class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 shadow-lg">
        <div class="max-w-3xl mx-auto px-4 py-3">

            @php
                $total = collect($items)->sum(function ($item) {
                    return ((float) ($item['unit_price'] ?? 0))
                        * ((int) ($item['quantity'] ?? 0));
                });
            @endphp

            <div class="flex items-center justify-between gap-4">

                {{-- Total --}}
                <div class="min-w-0">
                    <div class="text-xs text-gray-500">
                        Total Amount
                    </div>

                    <div class="text-xl font-bold text-gray-900">
                        ₱{{ number_format($total, 2) }}
                    </div>
                </div>

                {{-- Submit --}}
                <button
                    type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    class="shrink-0 px-6 py-3 bg-gray-900 text-white rounded-lg font-semibold hover:bg-gray-800 disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="save">
                        Submit Request
                    </span>

                    <span wire:loading wire:target="save">
                        Submitting...
                    </span>
                </button>

            </div>

        </div>
    </div>

</div>