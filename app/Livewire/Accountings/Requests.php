<?php

namespace App\Livewire\Accountings;

use App\Models\PurchaseRequest;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
class Requests extends Component
{
    use WithPagination;
    private function roundCashAmount(float $amount): int
    {
        $whole = floor($amount);
        $decimal = $amount - $whole;

        return $decimal >= 0.45
            ? (int) $whole + 1
            : (int) $whole;
    }
    #[On('cash-released')]
    public function refreshRequests(): void
    {
        // render()가 다시 실행되도록 상태만 갱신
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
                                        'purchaseItem.itemVendor.disbursementType',
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
        $requests->getCollection()->transform(function ($request) {
            $workflow = $request->purchaseWorkflows->first();
            $request->account_workflow = $workflow;
            if (!$workflow) {
                $request->items = collect();
                $request->account_total = 0;

                return $request;
            }
            /*
            * Accounting 화면의 pending items
            */
            $request->items = $workflow->purchaseWorkflowItems->map(
                function ($workflowItem) {
                    $purchaseItem = $workflowItem->purchaseItem;
                    $unit_price = (float) ($purchaseItem->unit_price ?? 0);
                    $quantity = (int) ($purchaseItem->quantity ?? 1);
                    $amount = (float) $unit_price * $quantity;
                    $discount = (float) ($purchaseItem->discount ?? 0);
                    $shippingFee = (float) ($purchaseItem->shipping_fee ?? 0);
                    /*
                    * 실제 계산 금액
                    */
                    $calculatedTotal =
                        $amount
                        + $shippingFee
                        - $discount;

                    /*
                    * Disbursement type 확인
                    */
                    $disbursementType = $purchaseItem->itemVendor?->disbursementType;

                    $isCash = strtolower(
                        trim($disbursementType?->name ?? '')
                    ) === 'cash';

                    /*
                    * Cash인 경우에만 소수점 올림
                    */
                    $releaseTotal = $isCash
                        ? $this->roundCashAmount($calculatedTotal)
                        : $calculatedTotal;

                    /*
                    * Blade에서 사용
                    */
                    $workflowItem->original_total = $amount;
                    $workflowItem->calculated_total = $calculatedTotal;
                    $workflowItem->release_total = $releaseTotal;
                    $workflowItem->is_cash = $isCash;

                    return $workflowItem;
                }
            );

            /*
            * 각 item의 release 금액 합산
            */
            $request->account_total = $request->items->sum(
                fn ($workflowItem) => $workflowItem->release_total
            );

            $request->account_total = max(0, $request->account_total);

            return $request;
        });

        return view('livewire.accountings.requests', [
            'requests' => $requests,
        ]);
    }
}
