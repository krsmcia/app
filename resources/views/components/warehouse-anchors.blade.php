<a
    href="{{ route('warehouses.warehouses') }}"
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
                d="M3.75 21h16.5M4.5 21V8.25L12 3l7.5 5.25V21M8.25 21v-6.75h7.5V21M7.5 10.5h.008v.008H7.5V10.5Zm4.5 0h.008v.008H12V10.5Zm4.5 0h.008v.008H16.5V10.5Z"
            />
        </svg>
    </div>
    <h2 class="mt-4 text-base font-semibold leading-5 text-emerald-600 sm:mt-5 sm:text-lg">
        Warehouse Management
    </h2>
    <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
        Manage warehouse locations and organize inventory across your facilities.
    </p>
    <div class="mt-auto pt-3 text-xs font-semibold text-emerald-600 sm:text-sm">
        Manage warehouse
        <span class="transition group-hover:ml-1">→</span>
    </div>
</a>
{{-- Inventory management --}}
<a
    href="{{ route('warehouses.inventory-management') }}"
    class="group flex aspect-square flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-sm
        transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-md
        sm:p-5 lg:aspect-auto lg:p-6"
>
    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 sm:h-11 sm:w-11">
        {{-- Cube / Inventory icon --}}
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
                d="m21 7.5-9-5-9 5m18 0v9l-9 5-9-5v-9m18 0-9 5m-9-5 9 5m0 0v9"
            />
        </svg>
    </div>
    <h2 class="mt-4 text-base font-semibold leading-5 text-emerald-600 sm:mt-5 sm:text-lg">
        Inventory Management
    </h2>
    <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
        Manage warehouse locations and check available inventory across your departments.
    </p>
    <div class="mt-auto pt-3 text-xs font-semibold text-emerald-600 sm:text-sm">
        View inventory
        <span class="transition group-hover:ml-1">→</span>
    </div>
</a>