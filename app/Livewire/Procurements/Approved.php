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

class Approved extends Component
{
    use WithPagination, WithoutUrlPagination;

    public string $search = '';
    public string $remark = '';
    public $remarkModal;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public bool $placeOrderModal = false;
    public ?int $selectedWorkflowItemId = null;
    public string $purchaseReference = '';
    public function openPlaceOrderModal(int $workflowItemId): void
    {
        $this->selectedWorkflowItemId = $workflowItemId;
        $this->purchaseReference = '';
        $this->placeOrderModal = true;
    }
    public function purchaseItem(): void
    {
        $this->validate([
            'purchaseReference' => ['required', 'string', 'max:255'],
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
                'remark' => $this->purchaseReference,
            ]);
        });
        $this->reset([
            'placeOrderModal',
            'selectedWorkflowItemId',
            'purchaseReference',
        ]);
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