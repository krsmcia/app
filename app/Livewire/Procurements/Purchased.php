<?php

namespace App\Livewire\Procurements;

use App\Models\PurchaseRequest;
use App\Models\PurchaseWorkflow;
use App\Models\PurchaseWorkflowItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Services\PurchaseWorkflowService;
use Livewire\Attributes\On;

class Purchased extends Component
{
    use WithPagination, WithoutUrlPagination, WithFileUploads;
    public function render()
    {
        $purchase_actions = auth()->user()
            ->purchaseActions()
            ->where('action', 'ordered')
            ->doesntHave('receivedItemPhotos')
            ->with([
                'purchaseWorkflowItem.purchaseItem.item.primaryImage',
                'purchaseWorkflowItem.purchaseItem.item.itemVendors.vendor',
                'purchaseWorkflowItem.purchaseItem.purchaseRequest.user',
                'purchaseWorkflowItem.purchaseItem.purchaseRequest.department',
            ])
            ->latest()
            ->paginate(10);

        return view('livewire.procurements.purchased', [
            'purchase_actions' => $purchase_actions,
        ]);
    }
}
