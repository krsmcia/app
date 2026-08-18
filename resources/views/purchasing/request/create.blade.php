<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Purchase Request') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Submit a request for purchasing goods or services.
        </p>
    </x-slot>
    <div
        class="max-w-3xl mx-auto px-4 pt-6 pb-28"
        x-data="{
            submitting: false,
            disbursementTypeId: @js(old('disbursement_type_id')),
            accountNumber: @js(old('account_number', '')),
            accountName: @js(old('account_name', '')),
            financialInstitutionModal: false,
            financialInstitutionId: @js(old('financial_institution_id')),
            items: [],
            title: @js(old('title', '')),
            errors: @js($errors->toArray()),
            init() {
                const oldItems = @js(old('items'));
                if (oldItems && Object.keys(oldItems).length > 0) {
                    this.items = Object.values(oldItems).map(item => ({
                        description: item.description ?? '',
                        unit_price: Number(item.unit_price ?? 0),
                        quantity: Number(item.quantity ?? 1),
                        remarks: item.remarks ?? '',
                        image: null,
                        imagePreview: null,
                    }))
                }
                if (this.items.length === 0) {
                    this.addItem()
                }
            },
            addItem() {
                this.items.push({
                    description: '',
                    unit_price: null,
                    quantity: 1,
                    remarks: '',
                    image: null,
                    imagePreview: null,
                })
            },
            removeItem(index) {
                if (this.items.length <= 1) {
                    return
                }
                const item = this.items[index]
                if (item.imagePreview) {
                    URL.revokeObjectURL(item.imagePreview)
                }
                this.items.splice(index, 1)
            },
            amount(item) {
                return (
                    Number(item.unit_price || 0) *
                    Number(item.quantity || 0)
                )
            },
            total() {
                return this.items.reduce((total, item) => {
                    return total + this.amount(item)
                }, 0)
            },
            formatMoney(value) {
                return Number(value || 0).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                })
            },
            previewImage(index, event) {
                const file = event.target.files[0]
                if (!file) {
                    return
                }
                if (this.items[index].imagePreview) {
                    URL.revokeObjectURL(
                        this.items[index].imagePreview
                    )
                }
                this.items[index].image = file
                this.items[index].imagePreview =
                    URL.createObjectURL(file)
            },
            removeImage(index) {
                const item = this.items[index]
                if (item.imagePreview) {
                    URL.revokeObjectURL(item.imagePreview)
                }
                item.image = null
                item.imagePreview = null
                const input = this.$refs[`image-${index}`]
                if (input) {
                    input.value = ''
                }
            },
            fieldError(field) {
                return this.errors[field]?.[0] ?? ''
            },
            itemError(index, field) {
                return this.errors[`items.${index}.${field}`]?.[0] ?? ''
            },
            hasItemError(index, field) {
                return !!this.itemError(index, field)
            },
            canSubmit() {
                if (!this.disbursementTypeId) {
                    return false
                }
                // Title required
                if (!this.title.trim()) {
                    return false
                }
                if (this.items.length === 0) {
                    return false
                }
                if (Number(this.disbursementTypeId) === Number(@js($bankTransferTypeId))) {
                    if (!this.financialInstitutionId) {
                        return false
                    }

                    if (!this.accountNumber.trim()) {
                        return false
                    }

                    if (!this.accountName.trim()) {
                        return false
                    }
                }
                return this.items.every(item => {
                    const description = String(item.description ?? '').trim()
                    const unitPrice = Number(item.unit_price)
                    const quantity = Number(item.quantity)

                    return (
                        description.length > 0 &&
                        item.unit_price !== null &&
                        item.unit_price !== '' &&
                        Number.isFinite(unitPrice) &&
                        unitPrice >= 0 &&
                        Number.isFinite(quantity) &&
                        quantity >= 1
                    )
                })
            },
            submit() {
                this.submitting = true
            },
        }"
        x-init="init()"
    >
        {{-- Validation Summary --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
                <div class="flex items-start gap-3">
                    <div class="text-red-500 text-lg">
                        ⚠
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-red-800">
                            Please check the form and try again.
                        </h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        {{-- Form --}}
        <form
            method="POST"
            action="{{ route('purchasing.request.store') }}"
            enctype="multipart/form-data"
            @submit="submit()"
        >
            @csrf
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-5">
                <h2 class="text-lg font-semibold text-gray-900 mb-5">
                    Request Information
                </h2>
                <div class="space-y-5">
                    {{-- Disbursement Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Disbursement Type
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach ($disbursementTypes as $type)
                                <label class="cursor-pointer">
                                    <input
                                        type="radio"
                                        name="disbursement_type_id"
                                        value="{{ $type->id }}"
                                        x-model="disbursementTypeId"
                                        @click="
                                            if (Number($event.target.value) === Number(@js($bankTransferTypeId))) {
                                                financialInstitutionModal = true
                                            } else {
                                                financialInstitutionId = null
                                            }
                                        "
                                        class="peer sr-only"
                                    >
                                    <div
                                        class="
                                            flex items-center justify-center
                                            min-h-12 px-4 py-3
                                            border border-gray-300
                                            rounded-xl
                                            bg-white
                                            transition
                                            peer-checked:border-indigo-600
                                            peer-checked:bg-indigo-50
                                            peer-checked:text-indigo-700
                                            hover:bg-gray-50
                                            active:scale-[0.98]
                                        "
                                    >
                                        <span class="text-sm font-medium">
                                            {{ $type->name }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('disbursement_type_id')
                            <p class="mt-1 text-sm text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    {{-- Financial Institution --}}
                    <template
                        x-if="Number(disbursementTypeId) === Number(@js($bankTransferTypeId))"
                    >
                        <div class="space-y-4">
                            {{-- Financial Institution --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Financial Institution
                                </label>
                                <button
                                    type="button"
                                    @click="financialInstitutionModal = true"
                                    class="
                                        w-full
                                        flex items-center justify-between
                                        px-4 py-3
                                        border border-gray-300
                                        rounded-xl
                                        bg-white
                                        hover:bg-gray-50
                                        transition
                                    "
                                >
                                    <span
                                        class="text-sm"
                                        :class="
                                            financialInstitutionId
                                                ? 'text-gray-900'
                                                : 'text-gray-400'
                                        "
                                        x-text="
                                            financialInstitutionId
                                                ? @js(
                                                    $financialInstitutions
                                                        ->pluck('name', 'id')
                                                        ->toArray()
                                                )[financialInstitutionId]
                                                : 'Select financial institution'
                                        "
                                    ></span>
                                    <span class="text-gray-400">
                                        →
                                    </span>
                                </button>
                                <input
                                    type="hidden"
                                    name="financial_institution_id"
                                    x-model="financialInstitutionId"
                                >
                                @error('financial_institution_id')
                                    <p class="mt-1 text-sm text-red-500">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            {{-- Account Number --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Account Number
                                </label>
                                <input
                                    type="text"
                                    name="account_number"
                                    x-model="accountNumber"
                                    class="
                                        w-full
                                        rounded-lg
                                        border-gray-300
                                        focus:border-indigo-500
                                        focus:ring-indigo-500
                                    "
                                    placeholder="Enter account number"
                                >
                                @error('account_number')
                                    <p class="mt-1 text-sm text-red-500">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            {{-- Account Name --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Account Name
                                </label>
                                <input
                                    type="text"
                                    name="account_name"
                                    x-model="accountName"
                                    class="
                                        w-full
                                        rounded-lg
                                        border-gray-300
                                        focus:border-indigo-500
                                        focus:ring-indigo-500
                                    "
                                    placeholder="Enter account holder name"
                                >
                                @error('account_name')
                                    <p class="mt-1 text-sm text-red-500">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </template>
                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Title
                        </label>
                        <input
                            type="text"
                            name="title"
                            x-model="title"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="e.g. Office Supplies Purchase"
                        >

                        @error('title')
                            <p class="mt-1 text-sm text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    {{-- Purpose --}}
                    <div>
                        <label
                            for="purpose"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Purpose / Reason
                        </label>
                        <textarea
                            id="purpose"
                            name="purpose"
                            rows="4"
                            class="
                                w-full rounded-lg
                                border-gray-300
                                focus:border-indigo-500
                                focus:ring-indigo-500
                            "
                            placeholder="Why do you need this purchase?"
                        >{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <p class="mt-1 text-sm text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-5">
                {{-- Sticky Header --}}
                <div
                    class="
                        sticky top-12 z-20
                        bg-white
                        py-3 mb-5
                        border-b border-gray-100
                    "
                >
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
                            @click="addItem()"
                            class="
                                shrink-0
                                inline-flex items-center
                                px-3 py-2
                                text-sm font-medium
                                rounded-lg
                                bg-gray-900 text-white
                                hover:bg-gray-800
                            "
                        >
                            + Add Item
                        </button>
                    </div>
                </div>
                {{-- Items --}}
                <div class="space-y-4">
                    <template
                        x-for="(item, index) in items"
                        :key="index"
                    >
                        <div class="border border-gray-200 rounded-xl p-4">
                            {{-- Item Header --}}
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="
                                            flex items-center justify-center
                                            w-7 h-7
                                            rounded-full
                                            bg-gray-100
                                            text-sm font-semibold
                                        "
                                        x-text="index + 1"
                                    ></span>
                                    <span class="font-medium text-gray-900">
                                        Item
                                    </span>
                                </div>
                                <button
                                    type="button"
                                    x-show="items.length > 1"
                                    @click="removeItem(index)"
                                    class="
                                        text-sm
                                        text-red-500
                                        hover:text-red-700
                                    "
                                >
                                    Remove
                                </button>
                            </div>
                            {{-- Description --}}
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Description
                                </label>
                                <input
                                    type="text"
                                    :name="`items[${index}][description]`"
                                    x-model="item.description"
                                    class="
                                        w-full rounded-lg
                                        border-gray-300
                                        focus:border-indigo-500
                                        focus:ring-indigo-500
                                    "
                                    :class="{
                                        'border-red-500 focus:border-red-500 focus:ring-red-500':
                                            hasItemError(index, 'description')
                                    }"
                                    placeholder="e.g. A4 Bond Paper"
                                >
                                <p
                                    x-show="hasItemError(index, 'description')"
                                    x-text="itemError(index, 'description')"
                                    class="mt-1 text-sm text-red-500"
                                ></p>
                            </div>
                            {{-- Item Photo --}}
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Item Photo
                                </label>
                                <div class="flex items-start gap-4">
                                    {{-- Preview --}}
                                    <template x-if="item.imagePreview">
                                        <div class="relative">
                                            <img
                                                :src="item.imagePreview"
                                                class="
                                                    w-28 h-28
                                                    object-cover
                                                    rounded-lg
                                                    border border-gray-200
                                                "
                                            >
                                            <button
                                                type="button"
                                                @click="removeImage(index)"
                                                class="
                                                    absolute
                                                    -top-2 -right-2
                                                    w-6 h-6
                                                    rounded-full
                                                    bg-red-500
                                                    text-white
                                                    text-sm
                                                    flex items-center justify-center
                                                    hover:bg-red-600
                                                "
                                            >
                                                ×
                                            </button>
                                        </div>
                                    </template>
                                    {{-- Upload --}}
                                    <template x-if="!item.imagePreview">
                                        <label
                                            class="
                                                w-28 h-28
                                                border-2 border-dashed
                                                border-gray-300
                                                rounded-lg
                                                flex flex-col
                                                items-center justify-center
                                                cursor-pointer
                                                hover:bg-gray-50
                                                transition
                                            "
                                        >
                                            <span class="text-2xl">
                                                📷
                                            </span>
                                            <span class="text-xs text-gray-500 mt-1">
                                                Add Photo
                                            </span>
                                            <input
                                                type="file"
                                                accept="image/jpeg,image/png,image/webp"
                                                :name="`items[${index}][image]`"
                                                :x-ref="`image-${index}`"
                                                @change="previewImage(index, $event)"
                                                class="hidden"
                                            >
                                        </label>
                                    </template>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">
                                    JPG, PNG or WEBP. Max 5MB.
                                </p>
                                <p
                                    x-show="hasItemError(index, 'image')"
                                    x-text="itemError(index, 'image')"
                                    class="mt-1 text-sm text-red-500"
                                ></p>
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
                                            class="
                                                absolute
                                                left-3 top-1/2
                                                -translate-y-1/2
                                                text-gray-500
                                            "
                                        >
                                            ₱
                                        </span>
                                        <input
                                            type="number"
                                            @wheel.prevent
                                            :name="`items[${index}][unit_price]`"
                                            x-model.number="item.unit_price"
                                            min="0"
                                            step="0.01"
                                            class="
                                                w-full pl-8
                                                rounded-lg
                                                border-gray-300
                                                focus:border-indigo-500
                                                focus:ring-indigo-500
                                            "
                                            :class="{
                                                'border-red-500 focus:border-red-500 focus:ring-red-500':
                                                    hasItemError(index, 'unit_price')
                                            }"
                                        >
                                    </div>
                                    <p
                                        x-show="hasItemError(index, 'unit_price')"
                                        x-text="itemError(index, 'unit_price')"
                                        class="mt-1 text-sm text-red-500"
                                    ></p>
                                </div>
                                {{-- Quantity --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Quantity
                                    </label>
                                    <input
                                        type="number"
                                        @wheel.prevent
                                        :name="`items[${index}][quantity]`"
                                        x-model.number="item.quantity"
                                        min="1"
                                        step="1"
                                        class="
                                            w-full rounded-lg
                                            border-gray-300
                                            focus:border-indigo-500
                                            focus:ring-indigo-500
                                        "
                                        :class="{
                                            'border-red-500 focus:border-red-500 focus:ring-red-500':
                                                hasItemError(index, 'quantity')
                                        }"
                                    >
                                    <p
                                        x-show="hasItemError(index, 'quantity')"
                                        x-text="itemError(index, 'quantity')"
                                        class="mt-1 text-sm text-red-500"
                                    ></p>
                                </div>
                            </div>
                            {{-- Amount --}}
                            <div
                                class="
                                    mt-4
                                    flex justify-between items-center
                                    bg-gray-50
                                    rounded-lg
                                    px-4 py-3
                                "
                            >
                                <span class="text-sm text-gray-600">
                                    Amount
                                </span>
                                <span class="font-semibold text-gray-900">
                                    ₱<span
                                        x-text="formatMoney(amount(item))"
                                    ></span>
                                </span>
                            </div>
                            {{-- Remarks --}}
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Remarks
                                </label>
                                <input
                                    type="text"
                                    :name="`items[${index}][remarks]`"
                                    x-model="item.remarks"
                                    class="
                                        w-full rounded-lg
                                        border-gray-300
                                        focus:border-indigo-500
                                        focus:ring-indigo-500
                                    "
                                    :class="{
                                        'border-red-500 focus:border-red-500 focus:ring-red-500':
                                            hasItemError(index, 'remarks')
                                    }"
                                    placeholder="Optional"
                                >
                                <p
                                    x-show="hasItemError(index, 'remarks')"
                                    x-text="itemError(index, 'remarks')"
                                    class="mt-1 text-sm text-red-500"
                                ></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            {{--
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-5">
                    <h2 class="text-lg font-semibold text-gray-900 mb-1">
                        Attachments
                    </h2>
                    <p class="text-sm text-gray-500 mb-4">
                        Attach quotation, receipt, image or other supporting documents.
                    </p>
                    <label
                        class="
                            block
                            border-2 border-dashed
                            border-gray-300
                            rounded-xl
                            p-6
                            text-center
                            cursor-pointer
                            hover:bg-gray-50
                            transition
                        "
                    >
                        <div class="text-gray-400 text-3xl mb-2">
                            📎
                        </div>
                        <p class="text-sm text-gray-600">
                            Upload supporting documents
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            PDF, Word, Excel or image files
                        </p>
                        <input
                            type="file"
                            name="attachments[]"
                            multiple
                            accept="
                                .pdf,
                                .doc,
                                .docx,
                                .xls,
                                .xlsx,
                                .jpg,
                                .jpeg,
                                .png,
                                .webp
                            "
                            class="hidden"
                        >
                    </label>
                    @if ($errors->has('attachments.*'))
                        <p class="mt-2 text-sm text-red-500">
                            Please check your attachment files.
                        </p>
                    @endif
                </div>
            --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-5">
                <div class="text-center mb-5">
                    <div class="text-xs tracking-widest text-gray-400 uppercase">
                        Purchase Request
                    </div>
                </div>
                {{-- Receipt Items --}}
                <div class="border-t border-dashed border-gray-300 pt-4">
                    <template
                        x-for="(item, index) in items"
                        :key="`summary-${index}`"
                    >
                        <template x-if="item.description || amount(item) > 0">
                            <div class="flex justify-between py-2 text-sm">
                                <div class="min-w-0 pr-4">
                                    <div
                                        class="font-medium text-gray-800 truncate"
                                        x-text="item.description || 'Unnamed Item'"
                                    ></div>
                                    <div class="text-xs text-gray-500">
                                        <span x-text="item.quantity || 0"></span>
                                        ×
                                        ₱<span
                                            x-text="formatMoney(item.unit_price)"
                                        ></span>
                                    </div>
                                </div>
                                <div class="font-medium shrink-0">
                                    ₱<span
                                        x-text="formatMoney(amount(item))"
                                    ></span>
                                </div>
                            </div>
                        </template>
                    </template>
                    {{-- Empty --}}
                    <div
                        x-show="items.length === 0"
                        class="py-4 text-center text-sm text-gray-400"
                    >
                        No items added
                    </div>
                </div>
                {{-- Total --}}
                <div class="border-t border-dashed border-gray-300 mt-3 pt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-base font-semibold text-gray-700">
                            TOTAL
                        </span>
                        <span class="text-2xl font-bold text-gray-900">
                            ₱<span
                                x-text="formatMoney(total())"
                            ></span>
                        </span>
                    </div>
                </div>
            </div>
            <div
                class="
                    fixed
                    bottom-0
                    left-0
                    right-0
                    z-50
                    bg-white
                    border-t border-gray-200
                    shadow-lg
                "
            >
                <div class="max-w-3xl mx-auto px-4 py-3">
                    <div class="flex items-center justify-between gap-4">
                        {{-- Total --}}
                        <div class="min-w-0">
                            <div class="text-xs text-gray-500">
                                Total Amount
                            </div>
                            <div class="text-xl font-bold text-gray-900">
                                ₱<span
                                    x-text="formatMoney(total())"
                                ></span>
                            </div>
                        </div>
                        {{-- Financial Institution Modal --}}
                        <div
                            x-show="financialInstitutionModal"
                            x-cloak
                            x-effect="
                                document.body.classList.toggle(
                                    'overflow-hidden',
                                    financialInstitutionModal
                                )
                            "
                            class="fixed inset-0 z-[100] flex items-center justify-center px-4"
                        >
                            {{-- Backdrop --}}
                            <div
                                class="absolute inset-0 bg-black/50"
                                @click="financialInstitutionModal = false"
                            ></div>

                            {{-- Modal --}}
                            <div
                                class="
                                    relative
                                    w-full
                                    max-w-2xl
                                    max-h-[80vh]
                                    bg-white
                                    rounded-2xl
                                    shadow-xl
                                    overflow-hidden
                                "
                                @click.stop
                            >
                                {{-- Header --}}
                                <div
                                    class="
                                        flex items-center justify-between
                                        px-5 py-4
                                        border-b border-gray-200
                                    "
                                >
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            Select Financial Institution
                                        </h3>

                                        <p class="text-sm text-gray-500 mt-1">
                                            Select the bank or e-wallet for the transfer.
                                        </p>
                                    </div>

                                    <button
                                        type="button"
                                        @click="financialInstitutionModal = false"
                                        class="
                                            w-8 h-8
                                            flex items-center justify-center
                                            rounded-full
                                            text-gray-400
                                            hover:bg-gray-100
                                            hover:text-gray-600
                                        "
                                    >
                                        ×
                                    </button>
                                </div>

                                {{-- Body --}}
                                <div class="p-5 overflow-y-auto max-h-[60vh]">
                                    @foreach (
                                        $financialInstitutions
                                            ->groupBy('financial_institution_type_id')
                                            as $typeId => $institutions
                                    )

                                        @php
                                            $type = $institutions->first()->financialInstitutionType;
                                        @endphp

                                        <div class="mb-6 last:mb-0">
                                            <h4
                                                class="
                                                    text-xs
                                                    font-semibold
                                                    tracking-wider
                                                    text-gray-400
                                                    uppercase
                                                    mb-3
                                                "
                                            >
                                                {{ $type->name }}
                                            </h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                @foreach ($institutions as $institution)
                                                    <button
                                                        type="button"
                                                        @click="
                                                            financialInstitutionId = {{ $institution->id }};
                                                            financialInstitutionModal = false;
                                                        "
                                                        class="
                                                            flex items-center justify-between
                                                            text-left
                                                            px-4 py-3
                                                            border border-gray-200
                                                            rounded-xl
                                                            hover:border-indigo-500
                                                            hover:bg-indigo-50
                                                            transition
                                                        "
                                                        :class="{
                                                            'border-indigo-600 bg-indigo-50':
                                                                Number(financialInstitutionId) ===
                                                                {{ $institution->id }}
                                                        }"
                                                    >
                                                        <span
                                                            class="text-sm font-medium"
                                                            :class="{
                                                                'text-indigo-700':
                                                                    Number(financialInstitutionId) ===
                                                                    {{ $institution->id }}
                                                            }"
                                                        >
                                                            {{ $institution->name }}
                                                        </span>

                                                        <span
                                                            x-show="
                                                                Number(financialInstitutionId) ===
                                                                {{ $institution->id }}
                                                            "
                                                            class="text-indigo-600"
                                                        >
                                                            ✓
                                                        </span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        {{-- Submit --}}
                        <button
                            type="submit"
                            :disabled="submitting || !canSubmit()"
                            :class="{
                                'opacity-50 cursor-not-allowed': submitting || !canSubmit(),
                                'hover:bg-gray-800': !submitting && canSubmit()
                            }"
                            class="
                                shrink-0
                                px-6 py-3
                                bg-gray-900
                                text-white
                                rounded-lg
                                font-semibold
                                disabled:opacity-50
                                disabled:cursor-not-allowed
                            "
                        >
                            <span x-show="!submitting">
                                Submit Request
                            </span>

                            <span
                                x-show="submitting"
                                x-cloak
                            >
                                Submitting...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>