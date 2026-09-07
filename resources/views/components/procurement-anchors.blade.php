<a
    href="{{ route('procurements.categories') }}"
    class="group flex aspect-square flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-sm
        transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md
        sm:p-5 lg:aspect-auto lg:p-6"
>
    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 sm:h-11 sm:w-11">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 text-indigo-600 sm:size-6"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M3.75 6.75h5.25l2.25 2.25h9v8.25
                a1.5 1.5 0 01-1.5 1.5H5.25
                a1.5 1.5 0 01-1.5-1.5V6.75z"
            />
        </svg>
    </div>
    <h2 class="mt-4 text-base font-semibold leading-5 text-slate-600 sm:mt-5 sm:text-lg">
        Procurement Categories
    </h2>
    <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
        Manage procurement categories, organize category hierarchies, and control their display order.
    </p>
    <div class="mt-auto pt-3 text-xs font-semibold text-indigo-600 sm:text-sm">
        Manage
        <span class="transition group-hover:ml-1">
            →
        </span>
    </div>
</a>
{{-- Procurement Items --}}
<a
    href="{{ route('procurements.items') }}"
    class="group flex aspect-square flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-sm
        transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md
        sm:p-5 lg:aspect-auto lg:p-6"
>
    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 sm:h-11 sm:w-11">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 text-indigo-600 sm:size-6"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M9.568 3.068A2.25 2.25 0 0111.159 2.5h1.682
                a2.25 2.25 0 011.591.659l6.409 6.409
                a2.25 2.25 0 010 3.182l-7.409 7.409
                a2.25 2.25 0 01-3.182 0L3.841 13.75
                a2.25 2.25 0 010-3.182l5.727-5.727z"
            />
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M13.5 7.5h.008v.008H13.5V7.5z"
            />
        </svg>
    </div>
    <h2 class="mt-4 text-base font-semibold leading-5 text-slate-600 sm:mt-5 sm:text-lg">
        Procurement Items
    </h2>
    <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
        Manage procurement items, assign categories, and maintain item details and status.
    </p>
    <div class="mt-auto pt-3 text-xs font-semibold text-indigo-600 sm:text-sm">
        Manage
        <span class="transition group-hover:ml-1">
            →
        </span>
    </div>
</a>
{{-- Vendors --}}
<a
    href="{{ route('procurements.vendors') }}"
    class="group flex aspect-square flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-sm
        transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-md
        sm:p-5 lg:aspect-auto lg:p-6"
>
    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 sm:h-11 sm:w-11">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 text-emerald-600 sm:size-6"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"
            />
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M4.5 19.5a7.5 7.5 0 0115 0"
            />
        </svg>
    </div>
    <h2 class="mt-4 text-base font-semibold leading-5 text-slate-600 sm:mt-5 sm:text-lg">
        Vendors
    </h2>
    <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
        Manage vendors, contact information, supplied items, and purchasing details.
    </p>
    <div class="mt-auto pt-3 text-xs font-semibold text-emerald-600 sm:text-sm">
        Manage
        <span class="transition group-hover:ml-1">
            →
        </span>
    </div>
</a>
{{-- Requests --}}
<a
    href="{{route('procurements.requests')}}"
    class="group flex aspect-square flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-sm
        transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-md
        sm:p-5 lg:aspect-auto lg:p-6"
>
    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 sm:h-11 sm:w-11">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 text-amber-600 sm:size-6"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 6v6l4 2.25M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
        </svg>
    </div>
    <h2 class="mt-4 text-base font-semibold leading-5 text-gray-900 sm:mt-5 sm:text-lg">
        Pending Purchase
    </h2>
    <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
        Review purchasing requests waiting for approval.
    </p>
    <div class="mt-auto pt-3 text-xs font-semibold text-amber-600 sm:text-sm">
        Review requests
        <span class="transition group-hover:ml-1">→</span>
    </div>
</a>
{{-- Approved Purchases --}}
<a
    href="{{ route('procurements.approved') }}"
    class="group flex aspect-square flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-sm
        transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-md
        sm:p-5 lg:aspect-auto lg:p-6"
>
    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 sm:h-11 sm:w-11">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 text-emerald-600 sm:size-6"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M9 12.75l2.25 2.25L15.75 9
                M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
        </svg>
    </div>

    <h2 class="mt-4 text-base font-semibold leading-5 text-slate-600 sm:mt-5 sm:text-lg">
        Approved Purchases
    </h2>

    <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
        View approved purchase items and track purchases ready for the next accounting process.
    </p>

    <div class="mt-auto pt-3 text-xs font-semibold text-emerald-600 sm:text-sm">
        View approved
        <span class="transition group-hover:ml-1">
            →
        </span>
    </div>
</a>