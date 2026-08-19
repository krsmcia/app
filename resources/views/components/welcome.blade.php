<div class="p-2">
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        @if(auth()->user()->hasRole('super-admin') || auth()->user()->department?->code === 'hr')
            {{-- Team Management --}}
            <a
                href="{{ route('departments') }}"
                class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm
                    transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md"
            >
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6 text-indigo-600"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                        />
                    </svg>
                </div>

                <h2 class="mt-5 text-lg font-semibold text-gray-900">
                    Department Management
                </h2>

                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Manage departments, department members, and department assignments.
                </p>

                <div class="mt-5 text-sm font-semibold text-indigo-600">
                    Manage departments
                    <span class="transition group-hover:ml-1">→</span>
                </div>
            </a>
            {{-- Employee Management --}}
            <a
                href="{{ route('employees') }}"
                class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm
                    transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md"
            >
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6 text-blue-600"
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
                <h2 class="mt-5 text-lg font-semibold text-gray-900">
                    Employee Management
                </h2>
                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Manage employees, departments, positions, and HR information.
                </p>
                <div class="mt-5 text-sm font-semibold text-indigo-600">
                    Manage employees
                    <span class="transition group-hover:ml-1">→</span>
                </div>
            </a>
            
        @endif
        @if(auth()->user()->hasRole('super-admin') || auth()->user()->department?->code === 'procurement')
            <a
                href="{{ route('procurements.categories') }}"
                class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm
                    transition-all duration-200 hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md"
            >
                {{-- Icon --}}
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6 text-indigo-600"
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

                {{-- Content --}}
                <h2 class="mt-5 text-lg font-semibold text-slate-600">
                    Procurement Categories
                </h2>

                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Manage procurement categories, organize category hierarchies,
                    and control their display order.
                </p>

                {{-- Action --}}
                <div class="mt-5 flex items-center text-sm font-semibold text-indigo-600">
                    Manage categories

                    <span class="ml-1 transition-transform duration-200 group-hover:translate-x-1">
                        →
                    </span>
                </div>
            </a>
            <a
                href="{{ route('procurements.items') }}"
                class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm
                    transition-all duration-200 hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md"
            >
                {{-- Icon --}}
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6 text-indigo-600"
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

                {{-- Content --}}
                <h2 class="mt-5 text-lg font-semibold text-slate-600">
                    Procurement Items
                </h2>

                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Manage procurement items, assign categories, and maintain item details and status.
                </p>

                {{-- Action --}}
                <div class="mt-5 flex items-center text-sm font-semibold text-indigo-600">
                    Manage items

                    <span class="ml-1 transition-transform duration-200 group-hover:translate-x-1">
                        →
                    </span>
                </div>
            </a>
            <a
                href="{{ route('procurements.vendors') }}"
                class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm
                    transition-all duration-200 hover:-translate-y-1 hover:border-emerald-200 hover:shadow-md"
            >
                {{-- Icon --}}
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6 text-emerald-600"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M18 18.72a9.094 9.094 0 003.742-.61
                            9.094 9.094 0 00-3.742-.61m0 1.22
                            a9.094 9.094 0 01-3.742-.61
                            9.094 9.094 0 013.742-.61m0 1.22
                            v-1.22m0 0a9.094 9.094 0 00-3.742-.61
                            M6 18.72a9.094 9.094 0 01-3.742-.61
                            9.094 9.094 0 013.742-.61m0 1.22
                            v-1.22m0 0a9.094 9.094 0 013.742-.61"
                        />
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

                {{-- Content --}}
                <h2 class="mt-5 text-lg font-semibold text-slate-600">
                    Vendors
                </h2>

                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Manage vendors, contact information, supplied items, purchasing details, and vendor status.
                </p>

                {{-- Action --}}
                <div class="mt-5 flex items-center text-sm font-semibold text-emerald-600">
                    Manage vendors

                    <span class="ml-1 transition-transform duration-200 group-hover:translate-x-1">
                        →
                    </span>
                </div>
            </a>
        @endif
        {{-- My Requests --}}
        <a
            href=""
            class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm
                transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md"
        >
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6 text-slate-600"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 0H6.375A2.625 2.625 0 003.75 4.875v14.25a2.625 2.625 0 002.625 2.625h10.5a2.625 2.625 0 002.625-2.625V15M8.25 3.75H9m-1.5 9h7.5m-7.5 3h4.5"
                    />
                </svg>
            </div>
            <h2 class="mt-5 text-lg font-semibold text-slate-600">
                My Requests
            </h2>
            <p class="mt-2 text-sm leading-6 text-gray-500">
                View and manage the purchasing requests you have submitted.
            </p>
            <div class="mt-5 text-sm font-semibold text-indigo-600">
                View requests
                <span class="transition group-hover:ml-1">→</span>
            </div>
        </a>
        {{-- New Request --}}
        <a
            href="{{route('purchasing.request.create')}}"
            class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm
                transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md"
        >
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6 text-emerald-600"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 4.5v15m7.5-7.5h-15"
                    />
                </svg>
            </div>
            <h2 class="mt-5 text-lg font-semibold text-emerald-600">
                New Request
            </h2>
            <p class="mt-2 text-sm leading-6 text-gray-500">
                Submit a new purchasing request for your department.
            </p>
            <div class="mt-5 text-sm font-semibold text-green-600">
                Create request
                <span class="transition group-hover:ml-1">→</span>
            </div>
        </a>
        <a
            href=""
            class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm
                transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md"
        >
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6 text-amber-600"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 6v6l4 2.25M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
            </div>
            <h2 class="mt-5 text-lg font-semibold text-gray-900">
                Pending Approval
            </h2>
            <p class="mt-2 text-sm leading-6 text-gray-500">
                Review purchasing requests that are waiting for approval.
            </p>
            <div class="mt-5 text-sm font-semibold text-amber-600">
                Review requests
                <span class="transition group-hover:ml-1">→</span>
            </div>
        </a>
        <a
            href=""
            class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm
                transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md"
        >
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6 text-purple-600"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75m-3-7.5a9 9 0 110 18 9 9 0 010-18z"
                    />
                </svg>
            </div>
            <h2 class="mt-5 text-lg font-semibold text-gray-900">
                Purchasing History
            </h2>
            <p class="mt-2 text-sm leading-6 text-gray-500">
                Review completed and previously processed requests.
            </p>
            <div class="mt-5 text-sm font-semibold text-slate-600">
                View history
                <span class="transition group-hover:ml-1">→</span>
            </div>
        </a>
        @if(auth()->user()->hasRole(['supervisor', 'team-leader']))
            <a
                href=""
                class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm
                    transition hover:-translate-y-1 hover:border-orange-200 hover:shadow-md"
            >
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6 text-orange-600"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>
                </div>

                <h2 class="mt-5 text-lg font-semibold text-gray-900">
                    Requests to Review
                </h2>

                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Review purchasing requests that are waiting for your action.
                </p>

                <div class="mt-5 flex items-center justify-between">
                    <span class="text-sm font-semibold text-orange-600">
                        Review requests
                        <span class="transition group-hover:ml-1">→</span>
                    </span>

                    <span class="inline-flex min-w-7 items-center justify-center rounded-full bg-orange-100 px-2 py-1 text-xs font-bold text-orange-700">
                        3
                    </span>
                </div>
            </a>
            <a
                href=""
                class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm
                    transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md"
            >
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6 text-slate-600"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75m-3-7.5a9 9 0 110 18 9 9 0 010-18z"
                        />
                    </svg>
                </div>
                <h2 class="mt-5 text-lg font-semibold text-gray-900">
                    Approval History
                </h2>
                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Review completed and previously processed requests.
                </p>
                <div class="mt-5 text-sm font-semibold text-purple-600">
                    View history
                    <span class="transition group-hover:ml-1">→</span>
                </div>
            </a>
        @endif
    </div>
    <div class="mt-10 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Recent Requests
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Your most recent purchasing requests.
                </p>
            </div>
            <a
                href=""
                class="text-sm font-semibold text-indigo-600 hover:text-indigo-700"
            >
                View all
            </a>
        </div>
        <div class="divide-y divide-gray-100">

            {{-- Request --}}
            <div class="flex items-center justify-between px-6 py-5">

                <div class="flex items-center gap-4">

                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-50">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-5 text-gray-500"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>

                    </div>

                    <div>

                        <p class="text-sm font-semibold text-gray-900">
                            Office Supplies
                        </p>

                        <p class="mt-1 text-xs text-gray-500">
                            Request #PR-0001 · Aug 10, 2026
                        </p>

                    </div>

                </div>


                <span
                    class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700"
                >
                    Pending
                </span>

            </div>


            {{-- Request --}}
            <div class="flex items-center justify-between px-6 py-5">

                <div class="flex items-center gap-4">

                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-50">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-5 text-gray-500"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.293.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>

                    </div>

                    <div>

                        <p class="text-sm font-semibold text-gray-900">
                            Computer Equipment
                        </p>

                        <p class="mt-1 text-xs text-gray-500">
                            Request #PR-0002 · Aug 08, 2026
                        </p>

                    </div>

                </div>


                <span
                    class="rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700"
                >
                    Approved
                </span>

            </div>


            {{-- Empty state --}}
            {{--

            <div class="px-6 py-12 text-center">

                <div class="mx-auto flex h-12 w-12 items-center justify-center
                            rounded-full bg-gray-100">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6 text-gray-400"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.293.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>

                </div>

                <p class="mt-4 text-sm font-medium text-gray-900">
                    No purchasing requests yet
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    Create your first purchasing request to get started.
                </p>

            </div>

            --}}

        </div>

    </div>


    {{-- Help / Information --}}
    <div class="mt-10 rounded-2xl border border-indigo-100 bg-indigo-50 p-6">

        <div class="flex gap-4">

            <div class="flex h-10 w-10 shrink-0 items-center justify-center
                        rounded-lg bg-white">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-5 text-indigo-600"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.451.999-1.451 1.823v.75M12 18h.008v.008H12V18z"
                    />
                </svg>

            </div>

            <div>

                <h3 class="text-sm font-semibold text-indigo-900">
                    Need help with a purchasing request?
                </h3>

                <p class="mt-1 text-sm leading-6 text-indigo-700">
                    Please make sure your request includes the required
                    information, estimated cost, and supporting documents
                    before submitting.
                </p>

            </div>

        </div>

    </div>
</div>
