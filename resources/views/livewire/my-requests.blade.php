<div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">
                My Requests
            </h1>
        </div>
        <div class="text-sm text-gray-500">
            {{ $requests->count() }} requests
        </div>
    </div>
    <div class="">
        @if(count($requests)>0)
            <div class="space-y-4">
                @foreach ($requests as $request)
                    <div class="flex flex-col">
                        <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
                            {{-- Header --}}
                            <div class="shrink-0 border-b bg-gray-50 px-4 py-3">
                                <div class="flex items-center justify-between w-full">
                                    <div class="grid sm:flex items-center sm:gap-2">
                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ $request->request_no }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ $request->count() }} items
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ $request->count() }} pending items
                                        </span>
                                    </div>
                                    <x-button
                                        x-on:click="$dispatch('open-request-history', { purchaseRequestId: {{ $request->id }} })" wire:loading.attr="disabled"
                                    >
                                        {{__('details')}}
                                    </x-button>
                                </div>
                                <div class="mt-0.5 text-xs text-gray-500">
                                    {{ $request->user->name }}
                                    · {{ $request->department?->name ?? '-' }}
                                </div>
                                @if ($request->remark)
                                    <div class="mt-2 rounded-md bg-white px-2.5 py-2 text-xs text-gray-600">
                                        <span class="font-medium text-gray-700">Remark:</span>
                                        {{ $request->remark }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-2">
                {{ $requests->links() }}
            </div>
        @else
            <div class="col-span-full rounded-lg border border-dashed bg-white py-12 text-center text-sm text-gray-500">
                No pending approvals.
            </div>
        @endif
    </div>
    <livewire:my-requests.modals.details />
</div>