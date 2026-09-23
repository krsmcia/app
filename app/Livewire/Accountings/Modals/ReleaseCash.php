<?php

namespace App\Livewire\Accountings\Modals;

use App\Models\PurchaseWorkflow;
use App\Models\User;
use App\Models\Transaction;
use App\Models\PurchaseRequestTransaction;
use App\Models\PurchaseItemTransaction;
use App\Services\PurchaseWorkflowService;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ReleaseCash extends Component
{
    use WithPagination, WithoutUrlPagination;

    public int $cashReleaseAmount = 0;
    public $cashItems = [];
    public string $search = '';
    public $workflow = null;
    public bool $cashHandoverModal = false;
    public ?int $recipientUserId = null;

    #[On('cash-handover-modal')]
    public function openModal(int $workflowId): void
    {
        $this->workflow = PurchaseWorkflow::query()
            ->with([
                'purchaseWorkflowItems.purchaseItem.itemVendor.disbursementType',
                'purchaseWorkflowItems.purchaseItem',
                'purchaseRequest',
            ])
            ->findOrFail($workflowId);

        $this->search = '';
        $this->recipientUserId = null;

        $this->resetPage();

        $this->calculateCashTotal();

        $this->cashHandoverModal = true;
    }

    private function calculateCashTotal(): void
    {
        $this->cashItems = $this->workflow
            ->purchaseWorkflowItems
            ->where('status', 'pending')
            ->filter(function ($workflowItem) {
                return $this->isCash($workflowItem);
            })
            ->values();

        $this->cashReleaseAmount = $this->cashItems->sum(
            fn ($workflowItem) => $this->calculateCashAmount(
                $workflowItem->purchaseItem
            )
        );
    }

    /**
     * Check whether this purchase item is Cash.
     */
    private function isCash($workflowItem): bool
    {
        return strcasecmp(
            trim(
                $workflowItem->purchaseItem->disbursement_type_name ?? ''
            ),
            'cash'
        ) === 0;
    }

    /**
     * Calculate the actual release amount for an item.
     *
     * Original:
     * amount × quantity
     *
     * Final:
     * original + shipping_fee - discount
     *
     * Cash items are rounded up to the next peso.
     */
    private function calculateCashAmount($purchaseItem): int
    {
        $amount = (float) ($purchaseItem->amount ?? 0);
        $quantity = (int) ($purchaseItem->quantity ?? 1);
        $discount = (float) ($purchaseItem->discount ?? 0);
        $shippingFee = (float) ($purchaseItem->shipping_fee ?? 0);

        $total =
            ($amount * $quantity)
            + $shippingFee
            - $discount;

        $whole = floor($total);
        $decimal = $total - $whole;

        return $decimal >= 0.45
            ? (int) $whole + 1
            : (int) $whole;
    }

    public function releaseCash(): void
    {
        $this->validate([
            'recipientUserId' => [
                'required',
                'integer',
                'exists:users,id',
            ],
        ]);

        $workflow = PurchaseWorkflow::query()
            ->with([
                'purchaseWorkflowItems.purchaseItem',
                'purchaseRequest',
            ])
            ->whereKey($this->workflow->id)
            ->where('step', 'accounting')
            ->where('status', 'pending')
            ->firstOrFail();

        $recipient = User::query()
            ->whereKey($this->recipientUserId)
            ->whereHas('departments', function ($query) {
                $query->where('code', 'procurement');
            })
            ->firstOrFail();

        DB::transaction(function () use ($workflow, $recipient) {

            /*
             * Get pending Cash items only.
             */
            $cashItems = $workflow->purchaseWorkflowItems
                ->where('status', 'pending')
                ->filter(function ($workflowItem) {
                    return $this->isCash($workflowItem);
                })
                ->values();

            if ($cashItems->isEmpty()) {
                return;
            }

            /*
             * Calculate each item first,
             * then sum the rounded amounts.
             */
            $totalAmount = $cashItems->sum(function ($workflowItem) {
                return $this->calculateCashAmount(
                    $workflowItem->purchaseItem
                );
            });

            /*
             * One physical cash handover
             * = one Transaction.
             */
            $transaction = Transaction::create([
                'from_user_id' => auth()->id(),
                'to_user_id' => $recipient->id,
                'type' => 'released',
                'amount' => $totalAmount,
                'remark' => sprintf(
                    'Initial cash fund for %s',
                    $workflow->purchaseRequest->request_no
                ),
                'created_by' => auth()->id(),
            ]);

            /*
             * One PurchaseRequest ↔ Transaction connection.
             */
            PurchaseRequestTransaction::create([
                'purchase_request_id' => $workflow->purchase_request_id,
                'transaction_id' => $transaction->id,
            ]);

            foreach ($cashItems as $workflowItem) {
                $purchaseItem = $workflowItem->purchaseItem;

                /*
                 * Calculate the exact amount released
                 * for this item.
                 */
                $amount = $this->calculateCashAmount(
                    $purchaseItem
                );

                /*
                 * Workflow item
                 */
                $workflowItem->update([
                    'status' => 'fund released',
                    'acted_at' => now(),
                ]);

                /*
                 * Action history
                 */
                $workflowItem->purchaseActions()->create([
                    'action' => 'fund released',
                    'acted_by' => auth()->id(),
                    'acted_at' => now(),
                ]);

                /*
                 * Transaction ↔ Item
                 */
                PurchaseItemTransaction::create([
                    'purchase_item_id' => $purchaseItem->id,
                    'transaction_id' => $transaction->id,
                    'amount' => $amount,
                ]);
            }

            /*
             * Complete Accounting workflow
             * if there are no pending items left.
             */
            app(PurchaseWorkflowService::class)
                ->completeAccountingWorkflowIfFinished($workflow);
        });

        $this->cashHandoverModal = false;

        $this->dispatch('cash-released')
            ->to(\App\Livewire\Accountings\Requests::class);
    }

    public function updatedSearch(): void
    {
        $this->recipientUserId = null;
        $this->resetPage();
    }

    public function render()
    {
        $users = User::query()
            ->whereHas('departments', function ($query) {
                $query->where('name', 'procurement');
            })
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query
                        ->where(
                            'name',
                            'like',
                            '%' . $this->search . '%'
                        )
                        ->orWhere(
                            'email',
                            'like',
                            '%' . $this->search . '%'
                        );
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view(
            'livewire.accountings.modals.release-cash',
            [
                'users' => $users,
            ]
        );
    }
}