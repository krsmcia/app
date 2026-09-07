<a
    href="{{ route('departments') }}"
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
                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
            />
        </svg>
    </div>
    <h2 class="mt-4 text-base font-semibold leading-5 text-gray-900 sm:mt-5 sm:text-lg">
        Department Management
    </h2>
    <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
        Manage departments, department members, and department assignments.
    </p>
    <div class="mt-auto pt-3 text-xs font-semibold text-indigo-600 sm:text-sm">
        Manage
        <span class="transition group-hover:ml-1">→</span>
    </div>
</a>
{{-- Employee Management --}}
<a
    href="{{ route('employees') }}"
    class="group flex aspect-square flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-sm
        transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md
        sm:p-5 lg:aspect-auto lg:p-6"
>
    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 sm:h-11 sm:w-11">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 text-blue-600 sm:size-6"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15 19.128a9.38 9.38 0 002.625.372
                9.337 9.337 0 004.121-.952
                4.125 4.125 0 00-7.533-2.493
                M15 19.128v-.003
                c0-1.113-.285-2.25-.875-3.197
                M15 19.128a9.37 9.37 0 01-3.75.75
                M18 8.25a3 3 0 11-6 0 3 3 0 016 0z
                M9.75 8.25a3 3 0 11-6 0 3 3 0 016 0z
                M3.75 19.128a9.37 9.37 0 003.75.75
                M3.75 19.128v-.003
                c0-1.113.285-2.25.875-3.197
                m0 0a4.125 4.125 0 017.533-2.493"
            />
        </svg>
    </div>
    <h2 class="mt-4 text-base font-semibold leading-5 text-gray-900 sm:mt-5 sm:text-lg">
        Employee Management
    </h2>
    <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
        Manage employees, departments, positions, and HR information.
    </p>
    <div class="mt-auto pt-3 text-xs font-semibold text-indigo-600 sm:text-sm">
        Manage
        <span class="transition group-hover:ml-1">→</span>
    </div>
</a>