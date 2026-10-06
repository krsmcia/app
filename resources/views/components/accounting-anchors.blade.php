<a
    href="{{ route('accountings.requests') }}"
    class="group flex aspect-square flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-sm
        transition hover:-translate-y-1 hover:border-sky-200 hover:shadow-md
        sm:p-5 lg:aspect-auto lg:p-6"
>
    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 sm:h-11 sm:w-11">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 text-sky-600 sm:size-6"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M2.25 18.75a60.07 60.07 0 0115.797 2.101
                .75.75 0 00.953-.721V18.75
                M2.25 4.5v.75A.75.75 0 003 6h11.25
                a.75.75 0 00.75-.75V4.5
                M2.25 4.5A2.25 2.25 0 014.5 2.25h13.5
                a2.25 2.25 0 012.25 2.25v15.75
                a2.25 2.25 0 01-2.25 2.25H4.5
                a2.25 2.25 0 01-2.25-2.25V4.5z"
            />
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M6.75 9.75h6.75M6.75 13.5h4.5"
            />
        </svg>
    </div>

    <h2 class="mt-4 text-base font-semibold leading-5 text-slate-600 sm:mt-5 sm:text-lg">
        Budget Requests
    </h2>

    <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
        Review purchase requests, verify payment details, and manage accounting processes.
    </p>

    <div class="mt-auto pt-3 text-xs font-semibold text-sky-600 sm:text-sm">
        Review
        <span class="transition group-hover:ml-1">
            →
        </span>
    </div>
</a>
<a
    href="{{ route('accountings.transactions') }}"
    class="group flex aspect-square flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-sm
        transition hover:-translate-y-1 hover:border-violet-200 hover:shadow-md
        sm:p-5 lg:aspect-auto lg:p-6"
>
    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 sm:h-11 sm:w-11">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 text-violet-600 sm:size-6"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5
                A4.5 4.5 0 0021 12V9"
            />
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M16.5 3L21 7.5m0 0L16.5 12M21 7.5H7.5
                A4.5 4.5 0 003 12v3"
            />
        </svg>
    </div>

    <h2 class="mt-4 text-base font-semibold leading-5 text-slate-600 sm:mt-5 sm:text-lg">
        Transactions
    </h2>

    <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
        View and track cash transfers, payments, returns, and other financial transactions.
    </p>

    <div class="mt-auto pt-3 text-xs font-semibold text-violet-600 sm:text-sm">
        Review
        <span class="transition group-hover:ml-1">
            →
        </span>
    </div>
</a>
<a
    href="{{ route('accountings.liquidations') }}"
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
                d="M9 14.25l2.25 2.25L15 12.75"
            />
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M6.75 3.75h10.5A2.25 2.25 0 0119.5 6v12a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 18V6a2.25 2.25 0 012.25-2.25z"
            />
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M8.25 7.5h7.5M8.25 10.5h7.5"
            />
        </svg>
    </div>

    <h2 class="mt-4 text-base font-semibold leading-5 text-slate-600 sm:mt-5 sm:text-lg">
        Cash Liquidations
    </h2>

    <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
        Review submitted receipts, verify actual expenses, and complete cash purchase liquidations.
    </p>

    <div class="mt-auto pt-3 text-xs font-semibold text-emerald-600 sm:text-sm">
        Review
        <span class="transition group-hover:ml-1">
            →
        </span>
    </div>
</a>