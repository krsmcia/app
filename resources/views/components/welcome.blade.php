<div class="p-2">
    <div class="grid grid-cols-2 gap-2 sm:gap-4 lg:grid-cols-4">
        @if(auth()->user()->hasRole('super-admin') || auth()->user()->departments->contains('code', 'hr'))
            <x-hr-anchors />
        @endif
        @if(auth()->user()->hasRole('super-admin') || auth()->user()->departments->contains('code', 'procurement'))
            <x-procurement-anchors />
        @endif
        @if(auth()->user()->hasRole('super-admin') || auth()->user()->departments->contains('code', 'warehouse'))
            <x-warehouse-anchors />
        @endif
        @if(auth()->user()->hasRole('super-admin') || auth()->user()->departments->contains('code', 'audit'))
            <x-audit-anchors />
        @endif
        @if(auth()->user()->hasRole(['super-admin', 'head']))
            <x-head-anchors />
        @endif
        @if(auth()->user()->hasRole(['super-admin', 'admin']))
            <x-admin-anchors />
        @endif
        @if(auth()->user()->hasRole('super-admin') || auth()->user()->departments->contains('code', 'accounting'))
            <x-accounting-anchors />
        @endif
        @if(!auth()->user()->hasRole(['staff', 'head']))
            {{-- Pending Approval --}}
            <a
                href="{{route('pending-approval')}}"
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
                    Pending Approval
                </h2>
                <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
                    Review purchasing requests waiting for approval.
                </p>
                <div class="mt-auto pt-3 text-xs font-semibold text-amber-600 sm:text-sm">
                    Review requests
                    <span class="transition group-hover:ml-1">→</span>
                </div>
            </a>
        @endif
        {{--
            @if(auth()->user()->hasRole(['supervisor', 'team-leader']))
                <a
                    href=""
                    class="group flex aspect-square flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-sm
                        transition hover:-translate-y-1 hover:border-orange-200 hover:shadow-md
                        sm:p-5 lg:aspect-auto lg:p-6"
                >
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-50 sm:h-11 sm:w-11">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-5 text-orange-600 sm:size-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                                a1 1 0 01.707.293l5.414 5.414
                                a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                    </div>
                    <h2 class="mt-4 text-base font-semibold leading-5 text-gray-900 sm:mt-5 sm:text-lg">
                        Requests to Review
                    </h2>
                    <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
                        Review purchasing requests waiting for your action.
                    </p>
                    <div class="mt-auto flex items-center justify-between pt-3">
                        <span class="text-xs font-semibold text-orange-600 sm:text-sm">
                            Review
                            <span class="transition group-hover:ml-1">→</span>
                        </span>
                        <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-orange-100 px-2 py-1 text-[10px] font-bold text-orange-700 sm:min-w-7 sm:text-xs">
                            3
                        </span>
                    </div>
                </a>
                <a
                    href=""
                    class="group flex aspect-square flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-sm
                        transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md
                        sm:p-5 lg:aspect-auto lg:p-6"
                >
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 sm:h-11 sm:w-11">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-5 text-slate-600 sm:size-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75m-3-7.5a9 9 0 110 18 9 9 0 010-18z"
                            />
                        </svg>
                    </div>
                    <h2 class="mt-4 text-base font-semibold leading-5 text-gray-900 sm:mt-5 sm:text-lg">
                        Approval History
                    </h2>
                    <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
                        Review completed and previously processed requests.
                    </p>
                    <div class="mt-auto pt-3 text-xs font-semibold text-purple-600 sm:text-sm">
                        View history
                        <span class="transition group-hover:ml-1">→</span>
                    </div>
                </a>
            @endif
        --}}
        {{-- New Request --}}
        <a
            href="{{ route('items') }}"
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
                        d="M12 4.5v15m7.5-7.5h-15"
                    />
                </svg>
            </div>
            <h2 class="mt-4 text-base font-semibold leading-5 text-emerald-600 sm:mt-5 sm:text-lg">
                New Request
            </h2>
            <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
                Submit a new purchasing request for your department.
            </p>
            <div class="mt-auto pt-3 text-xs font-semibold text-emerald-600 sm:text-sm">
                Create request
                <span class="transition group-hover:ml-1">→</span>
            </div>
        </a>
        {{-- My Requests --}}
        <a
            href="{{route('my-requests')}}"
            class="group flex aspect-square flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-sm
                transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md
                sm:p-5 lg:aspect-auto lg:p-6"
        >
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 sm:h-11 sm:w-11">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-5 text-slate-600 sm:size-6"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5
                        a1.125 1.125 0 01-1.125-1.125v-1.5
                        a3.375 3.375 0 00-3.375-3.375H8.25
                        m0 0H6.375A2.625 2.625 0 003.75 4.875v14.25
                        a2.625 2.625 0 002.625 2.625h10.5
                        a2.625 2.625 0 002.625-2.625V15
                        M8.25 3.75H9m-1.5 9h7.5m-7.5 3h4.5"
                    />
                </svg>
            </div>
            <h2 class="mt-4 text-base font-semibold leading-5 text-slate-600 sm:mt-5 sm:text-lg">
                My Requests
            </h2>
            <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
                View and manage the purchasing requests you have submitted.
            </p>
            <div class="mt-auto pt-3 text-xs font-semibold text-indigo-600 sm:text-sm">
                View requests
                <span class="transition group-hover:ml-1">→</span>
            </div>
        </a>
        {{-- Received Items --}}
        <a
            href="{{route('received-items')}}"
            class="group flex aspect-square flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-sm
                transition hover:-translate-y-1 hover:border-purple-200 hover:shadow-md
                sm:p-5 lg:aspect-auto lg:p-6"
        >
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-purple-50 sm:h-11 sm:w-11">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-5 text-purple-600 sm:size-6"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75m-3-7.5a9 9 0 110 18 9 9 0 010-18z"
                    />
                </svg>
            </div>
            <h2 class="mt-4 text-base font-semibold leading-5 text-gray-900 sm:mt-5 sm:text-lg">
                Received Items
            </h2>
            <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500 sm:text-sm sm:leading-6">
                Review completed and previously processed requests.
            </p>
            <div class="mt-auto pt-3 text-xs font-semibold text-slate-600 sm:text-sm">
                View history
                <span class="transition group-hover:ml-1">→</span>
            </div>
        </a>
    </div>
</div>
