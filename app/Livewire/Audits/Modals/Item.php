<?php

namespace App\Livewire\Audits\Modals;

use Livewire\Component;
use Livewire\Attributes\On;
class Item extends Component
{
    public $itemModal;
    #[On('open-item')]
    public function openModal($itemId = false)
    {
        //
        $this->itemModal = true;
    }
    public function render()
    {
        return view('livewire.audits.modals.item');
    }
}
