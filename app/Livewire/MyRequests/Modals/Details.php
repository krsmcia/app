<?php

namespace App\Livewire\MyRequests\Modals;

use Livewire\Attributes\On;
use Livewire\Component;

class Details extends Component
{
    public $historyModal = false;
    public $request;
    #[On('open-request-history')]
    public function openModal($purchaseRequestId)
    {
        $this->request = auth()->user()->purchaseRequests()
            ->with([
                'department',

                'purchaseItems' => function ($query) {
                    $query->with([
                        'item.primaryImage',
                        'itemVendor.vendor',

                        'purchaseWorkflowItems' => function ($query) {
                            $query->with('purchaseWorkflow');
                        },
                    ]);
                },

                'purchaseWorkflows' => function ($query) {
                    $query->with([
                        'purchaseWorkflowItems.purchaseItem',
                    ]);
                },
            ])
            ->where('id', $purchaseRequestId)
            ->first();

        $this->historyModal = true;
    }
    public function closeModal()
    {
        $this->historyModal = false;
    }
    public function render()
    {
        return view('livewire.my-requests.modals.details');
    }
}
