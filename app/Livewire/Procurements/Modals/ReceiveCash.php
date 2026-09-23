<?php

namespace App\Livewire\Procurements\Modals;

use App\Models\PurchaseWorkflowItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use App\Services\PurchaseWorkflowService;
use Livewire\Component;

class ReceiveCash extends Component
{
    public bool $show = false;

    public ?PurchaseWorkflowItem $workflowItem = null;

    public string $remark = '';

    #[On('receive-cash')]
    public function receiveCash(int $workflowItemId): void
    {
        $this->reset([
            'remark',
        ]);

        $this->resetValidation();

        $this->workflowItem = PurchaseWorkflowItem::query()
            ->with([
                'purchaseItem.purchaseItemTransactions.transaction.fromUser',
                'purchaseItem.purchaseRequest',
            ])
            ->findOrFail($workflowItemId);

        $this->show = true;
    }

    public function close(): void
    {
        $this->reset([
            'show',
            'workflowItem',
            'remark',
        ]);

        $this->resetValidation();
    }

    public function receive(): void
    {
        $this->validate([
            'remark' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        abort_unless($this->workflowItem, 404);

        $purchaseItem = $this->workflowItem->purchaseItem;

        /*
         * 가장 최근 transaction = 현재 Money Holder 확인
         */
        $latestItemTransaction = $purchaseItem
            ->purchaseItemTransactions
            ->sortByDesc('created_at')
            ->first();

        $currentTransaction = $latestItemTransaction?->transaction;

        abort_unless($currentTransaction, 403);

        /*
         * 현재 Money Holder
         */
        $moneyHolderId = $currentTransaction->to_user_id;

        /*
         * 현재 로그인한 사람이 이미 Money Holder라면
         * 자기 자신에게 받을 수 없음
         */
        abort_if(
            $moneyHolderId === Auth::id(),
            422,
            'You are already the current money holder.'
        );

        /*
         * 로그인한 사용자가 procurement 팀원인지 확인
         */
        $isProcurementMember = Auth::user()
            ->departments()
            ->where('code', 'procurement')
            ->exists();

        abort_unless($isProcurementMember, 403);

        DB::transaction(function () use (
            $purchaseItem,
            $currentTransaction,
            $moneyHolderId
        ) {
            /*
             * Money Holder → 현재 로그인 사용자
             */
            $transaction = Transaction::create([
                'from_user_id' => $moneyHolderId,
                'to_user_id' => Auth::id(),
                'vendor_id' => $currentTransaction->vendor_id,
                'type' => 'transfer',
                'amount' => $currentTransaction->amount,
                'remark' => $this->remark,
                'created_by' => Auth::id(),
            ]);

            /*
             * Purchase Item과 transaction 연결
             */
            $purchaseItem->purchaseItemTransactions()->create([
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
            ]);
        });

        $this->close();

        $this->dispatch('cash-received');
    }

    public function render()
    {
        return view('livewire.procurements.modals.receive-cash');
    }
}