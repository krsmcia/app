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

class Approved extends Component
{
    use WithPagination, WithoutUrlPagination, WithFileUploads;

    public ?int $selectedWorkflowItemId = null;
    public string $search = '';
    public string $remark = '';
    public bool $remarkModal = false;
    public bool $showCashReleaseModal = false;

    public bool $showAttachReceiptModal = false;
    public $recipientPhoto;
    
    public function openPlaceOrderModal(int $workflowItemId): void
    {
        $this->selectedWorkflowItemId = $workflowItemId;
        $this->remark = '';
        $this->remarkModal = true;
    }
    public function purchaseItem()
    {
        $this->validate([
            'remark' => ['required', 'string', 'max:500'],
        ]);
        $workflowItem = PurchaseWorkflowItem::with('purchaseItem')
            ->findOrFail($this->selectedWorkflowItemId);
        abort_unless($workflowItem->status === 'pending', 403);
        DB::transaction(function () use ($workflowItem) {
            $workflowItem->update([
                'status' => 'ordered',
                'acted_at' => now(),
            ]);
            $workflowItem->purchaseItem->update([
                'remark' => $this->remark,
            ]);
        });
        $this->reset([
            'remarkModal',
            'selectedWorkflowItemId',
            'remark',
        ]);
    }

    //Upload receiver photo
    
    
    public function openCashReleaseModal(int $workflowItemId): void
    {
        $this->selectedWorkflowItemId = $workflowItemId;
        $this->resetValidation();
        $this->showCashReleaseModal = true;
    }
    public function openAttachReceiptModal(int $workflowItemId): void
    {
        $this->selectedWorkflowItemId = $workflowItemId;
        $this->recipientPhoto = null;

        $this->resetValidation();

        $this->showAttachReceiptModal = true;

        $this->dispatch('reset-recipient-photo');
    }
    public function releaseCash(): void
    {
        $this->validate([
            'recipientPhoto' => [
                'required',
                'image',
                'max:5120',
            ],
        ]);
        $workflowItem = PurchaseWorkflowItem::with('purchaseItem')
            ->findOrFail($this->selectedWorkflowItemId);
        abort_unless($workflowItem->status === 'pending', 403);
        DB::transaction(function () use ($workflowItem) {
            $path = $this->recipientPhoto->store(
                'procurements/cash-receipts',
                'public'
            );
            $workflowItem->update([
                'status' => 'completed',
                'acted_at' => now(),
            ]);
            $workflowItem->purchaseItem->update([
                'cash_recipient_photo' => $path,
            ]);
        });
        $this->reset([
            'showCashReleaseModal',
            'selectedWorkflowItemId',
            'recipientPhoto',
        ]);
    }
    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function render()
    {
        $requests = PurchaseRequest::query()
            ->with([
                'user',
                'department',
                'purchaseWorkflows' => function ($query) {
                    $query
                        ->where('step', 'fund released')
                        ->where('status', 'pending')
                        ->with([
                            'purchaseWorkflowItems' => function ($query) {
                                $query
                                    ->where('status', 'pending')
                                    ->with([
                                        'purchaseItem.item.primaryImage',
                                        'purchaseItem.itemVendor.vendor',
                                    ]);
                            },
                        ]);
                },
            ])
            ->whereHas('purchaseWorkflows', function ($query) {
                $query
                    ->where('step', 'fund released')
                    ->where('status', 'pending')
                    ->whereHas('purchaseWorkflowItems', function ($query) {
                        $query->where('status', ['pending', 'ordered']);
                    });
            })
            ->latest()
            ->paginate(12);
        $requests->getCollection()->transform(
            function ($request) {
                $workflow = $request->purchaseWorkflows->first();
                $request->audit_workflow = $workflow;
                if (!$workflow) {
                    $request->audit_total = 0;

                    return $request;
                }
                $request->items = $workflow->purchaseWorkflowItems;
                $request->audit_total = $workflow->purchaseWorkflowItems
                    ->sum(function ($workflowItem) {
                        $purchaseItem = $workflowItem->purchaseItem;
                        return (float) ($purchaseItem->amount ?? 0);
                    });
                return $request;
            }
        );
        return view('livewire.procurements.approved', [
            'requests' => $requests,
        ]);
    }
}