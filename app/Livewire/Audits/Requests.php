<?php

namespace App\Livewire\Audits;

use App\Models\PurchaseRequest;
use App\Models\PurchaseWorkflow;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Requests extends Component
{
    public function render()
    {
        $user = Auth::user();
        $requests = PurchaseRequest::query()
            ->with([
                'user',
                'department',
                'purchaseWorkflows.purchaseWorkflowItems.purchaseItem.item.primaryImage',
                'purchaseWorkflows.purchaseWorkflowItems.purchaseItem.item.itemVendors.vendor',
            ])
            ->whereHas('purchaseWorkflows', function ($query) {
                $query
                    ->where('step', 'audit')
                    ->where('status', 'pending');
            })
            ->latest()
            ->paginate(12);

        return view('livewire.audits.requests', [
            'requests' => $requests,
        ]);
    }
}