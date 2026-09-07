<a
    href="{{ route('audits.requests') }}"
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
                d="M9 12.75l2.25 2.25L15 11.25
                M12 3.75a8.25 8.25 0 100 16.5
                8.25 8.25 0 000-16.5z"
            />
        </svg>
    </div>
    <h2 class="mt-4 text-base font-semibold leading-5 text-slate-600 sm:mt-5 sm:text-lg">
        Audit Requests
    </h2>
    <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
        Review procurement requests, verify approvals, and track audit history.
    </p>
    <div class="mt-auto pt-3 text-xs font-semibold text-amber-600 sm:text-sm">
        Review
        <span class="transition group-hover:ml-1">
            →
        </span>
    </div>
</a>