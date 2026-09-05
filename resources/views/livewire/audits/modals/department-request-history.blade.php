<x-dialog-modal wire:model.live="departmentRequestHistoryModal" maxWidth="7xl">
    <x-slot name="title">
        
    </x-slot>

    <x-slot name="content">
        
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button wire:click="$set('departmentRequestHistoryModal', false)" wire:loading.attr="disabled">
            {{ __('Close') }}
        </x-secondary-button>
    </x-slot>
</x-dialog-modal>