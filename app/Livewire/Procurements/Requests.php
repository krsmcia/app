<?php

namespace App\Livewire\Procurements;

use App\Models\PurchaseWorkflow;
use Livewire\Component;

class Requests extends Component
{
    public function render()
    {
        $workflows = PurchaseWorkflow::query()
            ->with([
                'purchaseRequest.user',
                'purchaseRequest.department',
                'purchaseWorkflowItems.purchaseItem.item.primaryImage',
            ])
            ->where('step', 'procurement')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('livewire.procurements.requests', [
            'workflows' => $workflows,
        ]);
    }
}