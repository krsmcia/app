<?php
namespace App\Livewire\Procurements;
use App\Models\PurchaseItem;
use App\Models\PurchaseItemTransaction;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
class Liquidations extends Component
{
    use WithPagination, WithoutUrlPagination;
    public string $search = '';
    public string $purchaseDate = '';
    public bool $showLiquidationModal = false;
    public ?int $accountingUserId = null;
    public float $totalLiquidationAmount = 0;
    public function mount(): void
    {
        $this->purchaseDate = now()->subDay()->toDateString();
    }
    protected function purchaseDateRange(): array
    {
        $date = Carbon::parse($this->purchaseDate);
        return [
            $date->copy()->startOfDay(),
            $date->copy()->endOfDay(),
        ];
    }
    /**
     * Purchase Date에 해당하는 Procurement user's purchased items.
     */
    protected function purchaseItemIdsForDate(): array
    {
        [$start, $end] = $this->purchaseDateRange();
        return PurchaseItemTransaction::query()
            ->whereHas('transaction', function ($query) use ($start, $end) {
                $query
                    ->where('type', 'purchased')
                    ->where('from_user_id', auth()->id())
                    ->whereNull('to_user_id')
                    ->whereNotNull('vendor_id')
                    ->whereBetween('created_at', [$start, $end]);
            })
            ->pluck('purchase_item_id')
            ->unique()
            ->values()
            ->all();
    }
    /**
     * Cash In:
     *
     * released  -> Accounting / previous fund -> Procurement
     * transfer  -> another user -> Procurement
     */
    protected function isCashInTransaction(
        ?Transaction $transaction,
        int $userId
    ): bool {
        if (! $transaction) {
            return false;
        }
        return $transaction->to_user_id === $userId
            && in_array($transaction->type, ['released', 'transfer'], true);
    }
    protected function isPurchasedTransaction(
        ?Transaction $transaction,
        int $userId
    ): bool {
        if (! $transaction) {
            return false;
        }
        return $transaction->type === 'purchased'
            && $transaction->from_user_id === $userId
            && is_null($transaction->to_user_id)
            && ! is_null($transaction->vendor_id);
    }
    protected function isReturnedTransaction(
        ?Transaction $transaction,
        int $userId
    ): bool {
        if (! $transaction) {
            return false;
        }
        return $transaction->type === 'returned'
            && $transaction->from_user_id === $userId
            && ! is_null($transaction->to_user_id);
    }
    /**
     * Purchase Date 전체 Cash Pool.
     *
     * Cash In = released + transfer
     * Cash Out = purchased
     */
    protected function calculateDateTotals(): array
    {
        $itemIds = $this->purchaseItemIdsForDate();
        if (empty($itemIds)) {
            return [
                'cash_in' => 0,
                'purchased' => 0,
                'returned' => 0,
                'balance' => 0,
            ];
        }
        $rows = PurchaseItemTransaction::query()
            ->whereIn('purchase_item_id', $itemIds)
            ->with('transaction')
            ->get();
        $cashIn = 0;
        $purchased = 0;
        $returned = 0;
        foreach ($rows as $row) {
            $transaction = $row->transaction;
            if ($this->isCashInTransaction(
                $transaction,
                auth()->id()
            )) {
                $cashIn += (float) $row->amount;
            }
            if ($this->isPurchasedTransaction(
                $transaction,
                auth()->id()
            )) {
                $purchased += (float) $row->amount;
            }
            if (
                $transaction?->type === 'returned'
                && $transaction->from_user_id === auth()->id()
                && $transaction->to_user_id === $this->accountingUserId
            ) {
                $returned += (float) $row->amount;
            }
        }
        $balance = round(
            $cashIn - $purchased - $returned,
            2
        );
        return [
            'cash_in' => $cashIn,
            'purchased' => $purchased,
            'returned' => $returned,
            'balance' => $balance,
        ];
    }
    public function updatedPurchaseDate(): void
    {
        $this->resetPage();
        $this->totalLiquidationAmount =
            max(0, -$this->calculateDateTotals()['balance']);
    }
    public function openLiquidationModal(): void
    {
        $totals = $this->calculateDateTotals();
        $this->totalLiquidationAmount = max(0, -$totals['balance']);
        $this->accountingUserId = null;
        $this->showLiquidationModal = true;
    }
    public function liquidateDate(int $accountingUserId): void
    {
        $this->accountingUserId = $accountingUserId;
        $this->validate([
            'accountingUserId' => ['required', 'integer', 'exists:users,id'],
        ]);
        $itemIds = $this->purchaseItemIdsForDate();
        if (empty($itemIds)) {
            return;
        }
        $userId = auth()->id();
        DB::transaction(function () use ($itemIds, $userId) {
            $rows = PurchaseItemTransaction::query()
                ->whereIn('purchase_item_id', $itemIds)
                ->with('transaction')
                ->lockForUpdate()
                ->get();
            /*
            * ---------------------------------------------------------
            * 1. Item별 현재 balance 계산
            *
            * balance = Cash In - Purchased
            *
            * balance < 0  => 부족 => Release 필요
            * balance > 0  => 남음 => Return 필요
            * ---------------------------------------------------------
            */
            $itemBalances = [];
            foreach ($itemIds as $itemId) {
                $cashIn = 0;
                $purchased = 0;
                foreach ($rows->where('purchase_item_id', $itemId) as $row) {
                    $transaction = $row->transaction;
                    if ($this->isCashInTransaction(
                        $transaction,
                        $userId
                    )) {
                        $cashIn += (float) $row->amount;
                    }
                    if ($this->isPurchasedTransaction(
                        $transaction,
                        $userId
                    )) {
                        $purchased += (float) $row->amount;
                    }
                }
                $balance = round($cashIn - $purchased, 2);
                $itemBalances[$itemId] = [
                    'cash_in' => $cashIn,
                    'purchased' => $purchased,
                    'balance' => $balance,
                ];
            }
            /*
            * ---------------------------------------------------------
            * 2. 부족한 Item
            *
            * 예:
            * purchased 300
            * cash in   248
            * balance   -52
            *
            * => ₱52 Release
            * ---------------------------------------------------------
            */
            foreach ($itemBalances as $itemId => $data) {
                if ($data['balance'] >= 0) {
                    continue;
                }
                $amount = round(abs($data['balance']), 2);
                if ($amount <= 0) {
                    continue;
                }
                $transaction = Transaction::create([
                    'from_user_id' => $this->accountingUserId,
                    'to_user_id' => $userId,
                    'vendor_id' => null,
                    'type' => 'released',
                    'amount' => $amount,
                    'created_by' => $userId,
                    'remark' => sprintf(
                        'Liquidation release for %s - %s',
                        Carbon::parse($this->purchaseDate)->format('M d, Y'),
                        $this->purchaseDate
                    ),
                ]);
                PurchaseItemTransaction::create([
                    'purchase_item_id' => $itemId,
                    'transaction_id' => $transaction->id,
                    'amount' => $amount,
                ]);
            }
            /*
            * ---------------------------------------------------------
            * 3. 남는 Item
            *
            * 예:
            * cash in   240
            * purchased 200
            * balance   +40
            *
            * => ₱40 Return
            * ---------------------------------------------------------
            */
            foreach ($itemBalances as $itemId => $data) {
                if ($data['balance'] <= 0) {
                    continue;
                }
                $amount = round($data['balance'], 2);
                if ($amount <= 0) {
                    continue;
                }
                $transaction = Transaction::create([
                    'from_user_id' => $userId,
                    'to_user_id' => $this->accountingUserId,
                    'vendor_id' => null,
                    'type' => 'returned',
                    'amount' => $amount,
                    'created_by' => $userId,
                    'remark' => sprintf(
                        'Liquidation return for %s - %s',
                        Carbon::parse($this->purchaseDate)->format('M d, Y'),
                        $this->purchaseDate
                    ),
                ]);
                PurchaseItemTransaction::create([
                    'purchase_item_id' => $itemId,
                    'transaction_id' => $transaction->id,
                    'amount' => $amount,
                ]);
            }
        });
        $this->showLiquidationModal = false;
        $this->accountingUserId = null;
        $this->totalLiquidationAmount = 0;
        $this->dispatch(
            'notify',
            message: 'Liquidation completed successfully.'
        );
    }
    public function render()
    {
        $itemIds = $this->purchaseItemIdsForDate();
        $query = PurchaseItem::query()
            ->whereIn('id', $itemIds)
            ->with([
                'purchaseRequest',
                'item',
                'purchaseItemTransactions.transaction',
            ]);
        if ($this->search !== '') {
            $search = '%' . $this->search . '%';
            $query->where(function ($query) use ($search) {
                $query
                    ->where('item_name', 'like', $search)
                    ->orWhere('sku', 'like', $search)
                    ->orWhereHas('purchaseRequest', function ($q) use ($search) {
                        $q->where('request_no', 'like', $search);
                    });
            });
        }
        $items = $query
            ->orderBy('id')
            ->paginate(12);
        $accountingUsers = User::query()
            ->whereHas('department', function ($query) {
                $query->where('name', 'Accounting');
            })
            ->orderBy('name')
            ->get();
        $totals = $this->calculateDateTotals();
        $this->totalLiquidationAmount = max(
            0,
            -$totals['balance']
        );
        return view('livewire.procurements.liquidations', [
            'items' => $items,
            'accountingUsers' => $accountingUsers,
            'totalCashIn' => $totals['cash_in'],
            'totalPurchased' => $totals['purchased'],
            'totalBalance' => $totals['balance'],
        ]);
    }
}