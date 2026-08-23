
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>{{ config('app.name', 'Purchasing Request') }}</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffffff">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900 touch-manipulation">
    <div class="min-h-screen">
        {{-- Navigation --}}
        <div class="fixed top-0 left-0 right-0 z-50">
            <nav class="border-b border-gray-200 bg-white">
                <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6 lg:px-8">
                    {{-- Logo --}}
                    <a href="{{ url('/') }}">
                        <x-application-logo />
                    </a>
                    {{-- Navigation --}}
                    <div class="flex items-center gap-3">
                        @auth
                            <span class="hidden text-sm text-gray-500 sm:block">
                                {{ Auth::user()->name }}
                            </span>
                            <a
                                href="{{ url('/dashboard') }}"
                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold
                                    text-white transition hover:bg-blue-700"
                            >
                                Dashboard
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="rounded-lg px-4 py-2 text-sm font-medium
                                    text-gray-600 transition hover:bg-gray-100
                                    hover:text-gray-900"
                            >
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold
                                        text-white transition hover:bg-blue-700"
                                >
                                    Register
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </nav>
        </div>
        
        {{-- Hero --}}
        <main class="pt-16">
            <section class="relative overflow-hidden">
                {{-- Background decoration --}}
                <div class="pointer-events-none absolute inset-0">
                    <div class="absolute -right-40 -top-40 h-96 w-96 rounded-full
                                bg-blue-100 opacity-50 blur-3xl">
                    </div>
                    <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full
                                bg-indigo-100 opacity-50 blur-3xl">
                    </div>
                </div>
                <div class="relative mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-28">
                    <div class="grid items-center gap-16 lg:grid-cols-2">
                        {{-- Left --}}
                        <div>
                            <div class="mb-6 inline-flex items-center gap-2 rounded-full
                                        border border-blue-100 bg-blue-50 px-3 py-1.5
                                        text-sm font-medium text-blue-600">
                                <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                                Internal Purchasing System
                            </div>
                            <h1 class="text-5xl font-bold tracking-tight text-gray-900 sm:text-6xl">

                                Purchasing
                                <span class="block text-blue-600">
                                    Request Form
                                </span>

                            </h1>
                            <p class="mt-6 max-w-xl text-lg leading-8 text-gray-600">
                                A simple and efficient way to submit,
                                manage, and track your purchasing requests
                                within the organization.
                            </p>
                            {{-- CTA --}}
                            <div class="mt-8 flex flex-wrap items-center gap-3">
                                @auth
                                    <a
                                        href="{{ url('/dashboard') }}"
                                        class="inline-flex items-center gap-2 rounded-lg
                                               bg-blue-600 px-5 py-3 text-sm font-semibold
                                               text-white shadow-sm transition
                                               hover:bg-blue-700
                                               focus:outline-none
                                               focus:ring-2 focus:ring-blue-500
                                               focus:ring-offset-2"
                                    >
                                        Go to Dashboard
                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 12h14m-6-6l6 6-6 6"
                                            />
                                        </svg>
                                    </a>
                                @else
                                    <a
                                        href="{{ route('login') }}"
                                        class="inline-flex items-center gap-2 rounded-lg
                                               bg-blue-600 px-5 py-3 text-sm font-semibold
                                               text-white shadow-sm transition
                                               hover:bg-blue-700
                                               focus:outline-none
                                               focus:ring-2 focus:ring-blue-500
                                               focus:ring-offset-2"
                                    >
                                        Get Started
                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 12h14m-6-6l6 6-6 6"
                                            />
                                        </svg>
                                    </a>
                                @endauth
                            </div>
                        </div>


                        {{-- Right Card --}}
                        <div>

                            <div class="rounded-2xl border border-gray-200 bg-white p-7
                                        shadow-xl shadow-gray-200/50">

                                <div class="mb-7">

                                    <div class="flex items-center justify-between">

                                        <div>

                                            <h2 class="text-lg font-semibold text-gray-900">
                                                Purchasing Process
                                            </h2>

                                            <p class="mt-1 text-sm text-gray-500">
                                                Simple and transparent
                                            </p>

                                        </div>

                                        <div class="flex h-10 w-10 items-center justify-center
                                                    rounded-lg bg-blue-50">

                                            <svg
                                                class="h-5 w-5 text-blue-600"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a3 3 0 016 0M9 5h6"
                                                />
                                            </svg>

                                        </div>

                                    </div>

                                </div>


                                {{-- Step 1 --}}
                                <div class="flex gap-4 border-b border-gray-100 py-5">

                                    <div class="flex h-10 w-10 shrink-0 items-center
                                                justify-center rounded-lg bg-blue-50
                                                text-sm font-bold text-blue-600">
                                        1
                                    </div>

                                    <div>
                                        <h3 class="font-semibold text-gray-900">
                                            Submit Request
                                        </h3>

                                        <p class="mt-1 text-sm leading-5 text-gray-500">
                                            Provide the details of the item or service
                                            you need to purchase.
                                        </p>
                                    </div>

                                </div>


                                {{-- Step 2 --}}
                                <div class="flex gap-4 border-b border-gray-100 py-5">

                                    <div class="flex h-10 w-10 shrink-0 items-center
                                                justify-center rounded-lg bg-amber-50
                                                text-sm font-bold text-amber-600">
                                        2
                                    </div>

                                    <div>
                                        <h3 class="font-semibold text-gray-900">
                                            Review & Approval
                                        </h3>

                                        <p class="mt-1 text-sm leading-5 text-gray-500">
                                            The request is reviewed by the appropriate
                                            team or manager.
                                        </p>
                                    </div>

                                </div>


                                {{-- Step 3 --}}
                                <div class="flex gap-4 py-5">

                                    <div class="flex h-10 w-10 shrink-0 items-center
                                                justify-center rounded-lg bg-green-50
                                                text-sm font-bold text-green-600">
                                        3
                                    </div>

                                    <div>
                                        <h3 class="font-semibold text-gray-900">
                                            Purchasing
                                        </h3>

                                        <p class="mt-1 text-sm leading-5 text-gray-500">
                                            Approved requests proceed to the
                                            purchasing process.
                                        </p>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </section>


            {{-- Features --}}
            <section class="border-t border-gray-200 bg-white">

                <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">

                    <div class="grid gap-8 md:grid-cols-3">


                        {{-- Feature 1 --}}
                        <div>

                            <div class="flex h-11 w-11 items-center justify-center
                                        rounded-lg bg-blue-50 text-blue-600">

                                <svg
                                    class="h-6 w-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4v16m8-8H4"
                                    />
                                </svg>

                            </div>

                            <h3 class="mt-4 text-base font-semibold text-gray-900">
                                Easy Submission
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-gray-500">
                                Submit purchasing requests with all the
                                required information in one place.
                            </p>

                        </div>


                        {{-- Feature 2 --}}
                        <div>

                            <div class="flex h-11 w-11 items-center justify-center
                                        rounded-lg bg-green-50 text-green-600">

                                <svg
                                    class="h-6 w-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 12c0 5.591 3.824 10.291 9 11.622C17.176 22.291 21 17.591 21 12c0-1.042-.133-2.053-.382-3.016z"
                                    />
                                </svg>

                            </div>

                            <h3 class="mt-4 text-base font-semibold text-gray-900">
                                Approval Workflow
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-gray-500">
                                Requests follow a clear approval process
                                before purchasing.
                            </p>

                        </div>


                        {{-- Feature 3 --}}
                        <div>

                            <div class="flex h-11 w-11 items-center justify-center
                                        rounded-lg bg-purple-50 text-purple-600">

                                <svg
                                    class="h-6 w-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>

                            </div>

                            <h3 class="mt-4 text-base font-semibold text-gray-900">
                                Request Tracking
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-gray-500">
                                Keep track of your requests and their
                                current approval status.
                            </p>

                        </div>

                    </div>

                </div>

            </section>

        </main>


        {{-- Footer --}}
        <footer class="border-t border-gray-200 bg-white">

            <div class="mx-auto flex max-w-7xl items-center justify-between
                        px-6 py-6 lg:px-8">

                <p class="text-xs text-gray-400">
                    Internal Purchasing System
                </p>

                <p class="text-xs text-gray-400">
                    © {{ date('Y') }} {{ config('app.name') }}
                </p>

            </div>

        </footer>

    </div>

</body>
</html>
