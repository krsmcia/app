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
        Accounting
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