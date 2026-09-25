<div
    x-data="{
        recipientUserId: null
    }"
    x-init="recipientUserId = null"
>
    <x-dialog-modal wire:model.live="commentModal">
        <x-slot name="title">
            <div>
                <div class="text-lg font-semibold text-gray-900">
                    Complete Purchasing Item
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Please provide the payment or accounting information for this item.
                </p>
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-5">

                {{-- comment --}}
                <div
                    x-data="{
                        count: {{ strlen($comment ?? '') }}
                    }"
                >
                    <label
                        for="comment"
                        class="block text-sm font-medium text-gray-700"
                    >
                        {{ __('Comment') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="comment"
                        wire:model.defer="comment"
                        x-on:input="count = $event.target.value.length"
                        rows="4"
                        maxlength="500"
                        class="mt-2 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none"
                        placeholder="e.g. Transaction number, voucher number, approval number..."
                    ></textarea>
                    @error('comment')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                    <div class="mt-1 text-right text-xs text-gray-400">
                        <span x-text="count"></span>/500
                    </div>
                </div>
            </div>
            {{-- Recipient --}}
            <div>
                <div class="grid lg:flex items-center justify-between gap-2">
                    <label class="text-xs font-semibold text-gray-800 sm:text-sm">
                        Item Recipient
                        <span class="text-red-500">*</span>
                    </label>
                    <span class="shrink-0 text-[9px] font-medium uppercase tracking-wide text-gray-400 sm:text-[10px]">
                        Select the Procurement department member who will receive this item.
                    </span>
                </div>
                {{-- Search --}}
                <div class="mt-1.5 sm:mt-2">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        x-on:input="recipientUserId = null"
                        placeholder="Search name or email..."
                        class="block w-full rounded-lg border-gray-300 px-2.5 py-2 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:px-3 sm:py-2.5 sm:text-sm"
                    >
                </div>

            </div>
            {{-- User List --}}
            <div class="mt-2.5 overflow-hidden rounded-xl border border-gray-200">
                @forelse ($users as $user)
                    <label
                        wire:key="procurement-user-{{ $user->id }}"
                        class="flex cursor-pointer items-center gap-2.5 border-b border-gray-100 px-2.5 py-2.5 last:border-b-0 hover:bg-gray-50 sm:gap-3 sm:px-3 sm:py-2.5"
                        :class="recipientUserId == {{ $user->id }} ? 'bg-indigo-50' : ''"
                    >
                        {{-- Radio --}}
                        <input
                            type="radio"
                            name="recipient_user"
                            value="{{ $user->id }}"
                            x-model="recipientUserId"
                            class="h-4 w-4 shrink-0 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                        {{-- User --}}
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-xs font-medium text-gray-900 sm:text-sm">
                                {{ $user->name }}
                            </div>
                            <div class="truncate text-[10px] text-gray-500 sm:text-xs">
                                {{ $user->email }}
                            </div>
                        </div>
                    </label>
                @empty
                    <div class="p-4 text-center text-xs text-gray-500 sm:p-5 sm:text-sm">
                        No Procurement team members found.
                    </div>
                @endforelse
            </div>


            {{-- Validation Error --}}
            @error('recipientUserId')

                <p class="mt-1.5 text-[10px] text-red-600 sm:text-xs">
                    Please select the person who will receive the item.
                </p>

            @enderror


            {{-- Pagination --}}
            @if ($users->hasPages())

                <div class="mt-2.5">
                    {{ $users->links() }}
                </div>

            @endif
        </x-slot>

        <x-slot name="footer">
            <div class="flex w-full justify-end gap-2">
                <x-secondary-button
                    type="button"
                    wire:click="$set('commentModal', false)"
                    wire:loading.attr="disabled"
                >
                    Cancel
                </x-secondary-button>

                <x-approve-button
                    type="button"
                    x-bind:disabled="!recipientUserId"
                    x-on:click="
                        $wire.recipientUserId = recipientUserId;
                        $wire.complete();
                    "
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="complete">
                        {{ __('Complete Purchase') }}
                    </span>

                    <span wire:loading wire:target="complete">
                        {{ __('Processing...') }}
                    </span>
                </x-approve-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>