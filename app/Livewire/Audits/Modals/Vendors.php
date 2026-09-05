<?php

namespace App\Livewire\Audits\Modals;

use Livewire\Component;
use Livewire\Attributes\On;
class Vendors extends Component
{
    public $vendorModal;
    #[On('open-vendor')]
    public function openModal($vendorId = false)
    {
        dd($vendorId);
        $this->vendorModal = true;
    }
    public function render()
    {
        return view('livewire.audits.modals.vendors');
    }
}
