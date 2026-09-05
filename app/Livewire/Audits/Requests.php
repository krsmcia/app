<?php

namespace App\Livewire\Audits;

use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Requests extends Component
{
    use WithPagination;

    public function render()
    {
        $user = Auth::user();

        $requests = PurchaseRequest::query()
            ->whereHas('purchaseWorkflows', function ($query) {
                $query
                    ->where('step', 'audit')
                    ->where('status', 'pending')
                    ->whereHas('purchaseWorkflowItems', function ($query) {
                        $query->where('status', 'pending');
                    });
            })
            ->with([
                'user',
                'department',

                // ⭐ audit 단계에서 pending인 purchase item만
                'purchaseItems' => function ($query) {
                    $query
                        ->whereHas('purchaseWorkflowItems', function ($query) {
                            $query
                                ->where('status', 'pending')
                                ->whereHas('purchaseWorkflow', function ($query) {
                                    $query
                                        ->where('step', 'audit')
                                        ->where('status', 'pending');
                                });
                        })
                        ->with([
                            'item.primaryImage',

                            // ⭐ audit + pending workflow item만
                            'purchaseWorkflowItems' => function ($query) {
                                $query
                                    ->where('status', 'pending')
                                    ->whereHas('purchaseWorkflow', function ($query) {
                                        $query
                                            ->where('step', 'audit')
                                            ->where('status', 'pending');
                                    });
                            },
                        ]);
                },

                // audit pending workflow 자체
                'purchaseWorkflows' => function ($query) {
                    $query
                        ->where('step', 'audit')
                        ->where('status', 'pending')
                        ->with([
                            'purchaseWorkflowItems' => function ($query) {
                                $query
                                    ->where('status', 'pending');
                            },
                        ]);
                },
            ])
            ->latest()
            ->paginate(12);

        $requests->getCollection()->transform(function ($request) {

            $workflow = $request->purchaseWorkflows->first();

            $request->audit_workflow = $workflow;

            /*
             * ⭐ 기존처럼 workflow item을 기준으로 만들지 않고
             * purchaseItems를 기준으로 처리
             *
             * 이미 위에서 audit + pending인 purchaseItems만
             * 가져왔기 때문에 여기에는 승인 대상 item만 존재
             */
            $workflowItems = collect();

            if ($workflow) {
                $workflowItems = $workflow->purchaseWorkflowItems
                    ->filter(function ($workflowItem) {
                        return $workflowItem->status === 'pending';
                    });
            }

            $request->items = $request->purchaseItems->map(
                function ($purchaseItem) use ($workflowItems) {

                    $item = $purchaseItem->item;

                    /*
                     * 해당 purchaseItem에 연결된
                     * audit pending workflow item
                     */
                    $workflowItem = $purchaseItem->purchaseWorkflowItems
                        ->first(function ($item) {
                            return $item->status === 'pending';
                        });

                    return [
                        'workflow_item' => $workflowItem,
                        'purchase_item' => $purchaseItem,
                        'item' => $item,

                        'image' => $item?->primaryImage
                            ? \Storage::url($item->primaryImage->path)
                            : asset('images/default-item.png'),

                        'item_name' => $item?->item_name,
                        'sku' => $item?->sku,

                        'quantity' => $purchaseItem->quantity ?? 0,
                        'vendor_name' => $purchaseItem->vendor_name,
                        'unit_price' => $purchaseItem->unit_price ?? 0,

                        'preferred_vendor' => $workflowItem?->preferred_vendor,
                    ];
                }
            );

            return $request;
        });

        return view('livewire.audits.requests', [
            'requests' => $requests,
        ]);
    }

    public function approve(int $workflowId)
    {
        // approve logic
    }
}