<div>
    <x-dialog-modal wire:model="showModal" maxWidth="2xl">
        <x-slot name="title">
            Budget Receiver Photos
        </x-slot>

        <x-slot name="content">
            <div class="max-h-[70vh] space-y-4 overflow-y-auto pr-2">
                @forelse ($photos as $index => $photo)
                    <div class="overflow-hidden rounded-lg border border-gray-200">
                        <div class="border-b border-gray-100 bg-gray-50 px-3 py-2">
                            <span class="text-sm font-medium text-gray-700">
                                Photo {{ $index + 1 }}
                            </span>
                        </div>

                        <div class="bg-gray-50 p-2">
                            <img
                                src="{{ route('accountings.budget-photos.show', $photo['id']) }}"
                                alt="Budget receiver photo {{ $index + 1 }}"
                                class="mx-auto h-auto max-w-full rounded-md object-contain"
                            >
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-gray-500">
                        No photos available.
                    </div>
                @endforelse
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="close">
                Close
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>
</div>