<?php

namespace App\Livewire\Audits\Modals;

use Livewire\Component;
use Livewire\Attributes\On;
class DepartmentRequestHistory extends Component
{
    public $departmentRequestHistoryModal;
    #[On('open-department-request-history')]
    public function openModal($departmentId = false)
    {
        $this->departmentRequestHistoryModal = true;
    }
    public function render()
    {
        return view('livewire.audits.modals.department-request-history');
    }
}
