<?php

namespace App\Livewire\Accountings;

use App\Models\PurchaseRequest;
use App\Models\PurchaseWorkflow;
use App\Models\PurchaseWorkflowItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Requests extends Component
{
    use WithPagination;
    public bool $remarkModal = false;
    public string $remark = '';
    public ?int $releaseWorkflowItemId = null;

    public function openRemarkModal($workflowItemId)
    {
        $this->releaseWorkflowItemId = $workflowItemId;
        $this->remark = '';
        $this->resetValidation();
        $this->remarkModal = true;
    }

    public function render()
    {
        $requests = PurchaseRequest::query()
            ->with([
                'user',
                'department',
                'purchaseWorkflows' => function ($query) {
                    $query
                        ->where('step', 'accounting')
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
                    ->where('step', 'accounting')
                    ->where('status', 'pending')
                    ->whereHas('purchaseWorkflowItems', function ($query) {
                        $query->where('status', 'pending');
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
                /*
                 * Audit 화면에는 pending item만 존재
                 */
                $request->items = $workflow->purchaseWorkflowItems;
                /*
                 * 중요:
                 *
                 * Audit에서는 vendor의 현재 가격을 다시 계산하지 않는다.
                 *
                 * Procurement 단계에서 purchase_items.amount에
                 * 확정된 snapshot 가격이 저장되어 있기 때문이다.
                 */
                $request->audit_total = $workflow->purchaseWorkflowItems
                    ->sum(function ($workflowItem) {

                        $purchaseItem = $workflowItem->purchaseItem;

                        return (float) ($purchaseItem->amount ?? 0);
                    });
                return $request;
            }
        );
        return view('livewire.accountings.requests', [
            'requests' => $requests,
        ]);
    }
}
