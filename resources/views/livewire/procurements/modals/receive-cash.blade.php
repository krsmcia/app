<div>
    <x-dialog-modal wire:model.live="show">
        <x-slot name="title">
            <div>
                <div class="text-lg font-semibold text-gray-900">
                    Receive Money
                </div>

                <p class="mt-1 text-sm text-gray-500">
                    Receive the current cash balance from the current money holder.
                </p>
            </div>
        </x-slot>
        <x-slot name="content">
            @if ($workflowItem)
                @php
                    $latestItemTransaction = $workflowItem->purchaseItem
                        ->purchaseItemTransactions
                        ->sortByDesc('created_at')
                        ->first();

                    $currentHolder = $latestItemTransaction?->transaction?->toUser;
                    $amount = $latestItemTransaction?->amount ?? 0;
                @endphp
                <div class="space-y-5">
                    {{-- Current Holder --}}
                    <div class="rounded-lg border border-indigo-100 bg-indigo-50 px-4 py-3">
                        <div class="text-[10px] font-semibold uppercase tracking-wide text-indigo-500">
                            Current Money Holder
                        </div>
                        @if ($currentHolder)
                            <div class="mt-2 flex items-center gap-3">
                                <img
                                    src="{{ $currentHolder->profile_photo_url }}"
                                    alt="{{ $currentHolder->name }}"
                                    class="size-9 rounded-full object-cover"
                                >
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold text-gray-900">
                                        {{ $currentHolder->name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Money Holder
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    {{-- Receive Amount --}}
                    <div class="rounded-lg border border-emerald-100 bg-emerald-50 px-4 py-3">
                        <div class="text-[10px] font-semibold uppercase tracking-wide text-emerald-600">
                            Amount to Receive
                        </div>
                        <div class="mt-1 text-2xl font-extrabold tracking-tight text-emerald-900">
                            ₱{{ number_format($amount, 2) }}
                        </div>
                    </div>
                    {{-- Remark --}}
                    <div>
                        <label
                            for="receiveRemark"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Remark
                        </label>
                        <textarea
                            id="receiveRemark"
                            wire:model.defer="remark"
                            rows="3"
                            maxlength="500"
                            class="mt-2 block w-full rounded-lg border-gray-300 text-sm shadow-sm
                                focus:border-indigo-500 focus:ring-indigo-500 resize-none"
                            placeholder="Optional receive note..."
                        ></textarea>
                        @error('remark')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            @endif
        </x-slot>
        <x-slot name="footer">
            <div class="flex w-full justify-end gap-2">
                <x-secondary-button
                    type="button"
                    wire:click="close"
                    wire:loading.attr="disabled"
                >
                    Cancel
                </x-secondary-button>
                <x-approve-button
                    type="button"
                    wire:click="receive"
                    wire:loading.attr="disabled"
                    wire:target="receive"
                >
                    <span wire:loading.remove wire:target="receive">
                        Receive Money
                    </span>
                    <span wire:loading wire:target="receive">
                        Receiving...
                    </span>
                </x-approve-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>