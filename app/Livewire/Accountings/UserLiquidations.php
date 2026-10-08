<?php

namespace App\Livewire\Accountings;

use App\Models\PurchaseItem;
use App\Models\PurchaseItemTransaction;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class UserLiquidations extends Component
{
    use WithPagination, WithoutUrlPagination;

    public User $user;

    public string $search = '';

    public string $purchaseDate = '';

    public bool $showLiquidationModal = false;

    public float $totalReleased = 0;

    public float $totalPurchased = 0;

    public float $totalReturned = 0;

    public float $totalReturnAmount = 0;

    public function mount(User $user): void
    {
        $this->user = $user;

        $this->purchaseDate = now()
            ->subDay()
            ->toDateString();
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
     * 해당 User가 해당 날짜에 실제 구매한 Purchase Items.
     *
     * 금액이 0인 item도 거래가 존재하면 포함한다.
     */
    protected function purchaseItemIdsForDate(): array
    {
        [$start, $end] = $this->purchaseDateRange();

        return PurchaseItemTransaction::query()
            ->whereHas('transaction', function ($query) use ($start, $end) {
                $query
                    ->where('type', 'purchased')
                    ->where('from_user_id', $this->user->id)
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
     * 해당 User가 해당 날짜에 받은 Released 금액.
     */
    protected function calculateReleased(): float
    {
        [$start, $end] = $this->purchaseDateRange();

        return (float) Transaction::query()
            ->where('type', 'released')
            ->where('to_user_id', $this->user->id)
            ->whereNull('vendor_id')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');
    }

    /**
     * 해당 User가 해당 날짜에 실제 구매한 총액.
     */
    protected function calculatePurchased(): float
    {
        [$start, $end] = $this->purchaseDateRange();

        return (float) PurchaseItemTransaction::query()
            ->whereHas('transaction', function ($query) use ($start, $end) {
                $query
                    ->where('type', 'purchased')
                    ->where('from_user_id', $this->user->id)
                    ->whereNull('to_user_id')
                    ->whereNotNull('vendor_id')
                    ->whereBetween('created_at', [$start, $end]);
            })
            ->sum('amount');
    }

    /**
     * 해당 User가 해당 liquidation에 대해 이미 반환한 금액.
     *
     * returned transaction은 Purchase Date와 생성 날짜가
     * 다를 수 있으므로 PurchaseItemTransaction을 통해
     * 해당 Purchase Items와 연결된 반환만 계산한다.
     */
    protected function calculateReturned(): float
    {
        $itemIds = $this->purchaseItemIdsForDate();

        if (empty($itemIds)) {
            return 0;
        }

        return (float) PurchaseItemTransaction::query()
            ->whereIn('purchase_item_id', $itemIds)
            ->whereHas('transaction', function ($query) {
                $query
                    ->where('type', 'returned')
                    ->where('from_user_id', $this->user->id)
                    ->where('to_user_id', auth()->id());
            })
            ->sum('amount');
    }

    /**
     * Liquidation totals.
     *
     * Return Due = Released - Purchased
     * Remaining = Return Due - Already Returned
     */
    protected function calculateDateTotals(): array
    {
        $released = round($this->calculateReleased(), 2);
        $purchased = round($this->calculatePurchased(), 2);
        $returned = round($this->calculateReturned(), 2);

        $returnDue = max(
            0,
            round($released - $purchased, 2)
        );

        $returnBalance = max(
            0,
            round($returnDue - $returned, 2)
        );

        return [
            'released' => $released,
            'purchased' => $purchased,
            'returned' => $returned,
            'return_due' => $returnDue,
            'return_balance' => $returnBalance,
        ];
    }

    public function updatedPurchaseDate(): void
    {
        $this->resetPage();

        $this->refreshTotals();
    }

    protected function refreshTotals(): void
    {
        $totals = $this->calculateDateTotals();

        $this->totalReleased = $totals['released'];
        $this->totalPurchased = $totals['purchased'];
        $this->totalReturned = $totals['returned'];
        $this->totalReturnAmount = $totals['return_balance'];
    }

    public function openLiquidationModal(): void
    {
        $this->refreshTotals();

        if ($this->totalReturnAmount <= 0) {
            return;
        }

        $this->showLiquidationModal = true;
    }

    public function closeLiquidationModal(): void
    {
        $this->showLiquidationModal = false;
    }

    /**
     * Accounting receives the remaining liquidation balance.
     */
    public function confirmLiquidation(): void
    {
        DB::transaction(function () {
            /*
             * Recalculate inside the transaction so the amount
             * cannot be based on stale Livewire state.
             */
            $totals = $this->calculateDateTotals();

            $amount = $totals['return_balance'];

            if ($amount <= 0) {
                return;
            }

            $itemIds = $this->purchaseItemIdsForDate();

            if (empty($itemIds)) {
                return;
            }

            $transaction = Transaction::create([
                'from_user_id' => $this->user->id,
                'to_user_id' => auth()->id(),
                'vendor_id' => null,
                'type' => 'returned',
                'amount' => $amount,
                'remark' => sprintf(
                    'Liquidation return for purchase date %s',
                    $this->purchaseDate
                ),
                'created_by' => auth()->id(),
            ]);

            /*
             * Link the returned transaction to the user's
             * purchase items for this liquidation.
             *
             * The return amount is a date-level balance,
             * so allocation is only for traceability.
             */
            $remaining = $amount;

            $purchaseRows = PurchaseItemTransaction::query()
                ->whereIn('purchase_item_id', $itemIds)
                ->whereHas('transaction', function ($query) {
                    $query
                        ->where('type', 'purchased')
                        ->where('from_user_id', $this->user->id)
                        ->whereNull('to_user_id')
                        ->whereNotNull('vendor_id');
                })
                ->orderBy('id')
                ->get();

            foreach ($purchaseRows as $purchaseRow) {
                if ($remaining <= 0) {
                    break;
                }

                $allocation = min(
                    $remaining,
                    (float) $purchaseRow->amount
                );

                PurchaseItemTransaction::create([
                    'purchase_item_id' => $purchaseRow->purchase_item_id,
                    'transaction_id' => $transaction->id,
                    'amount' => $allocation,
                ]);

                $remaining = round(
                    $remaining - $allocation,
                    2
                );
            }
        });

        $this->showLiquidationModal = false;

        $this->refreshTotals();

        $this->dispatch(
            'notify',
            message: 'Returned funds received successfully.'
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
                    ->orWhereHas(
                        'purchaseRequest',
                        function ($q) use ($search) {
                            $q->where(
                                'request_no',
                                'like',
                                $search
                            );
                        }
                    );
            });
        }

        $items = $query
            ->orderBy('id')
            ->paginate(12);

        $totals = $this->calculateDateTotals();

        $this->totalReleased = $totals['released'];
        $this->totalPurchased = $totals['purchased'];
        $this->totalReturned = $totals['returned'];
        $this->totalReturnAmount = $totals['return_balance'];

        return view('livewire.accountings.user-liquidations', [
            'items' => $items,
            'totalReleased' => $totals['released'],
            'totalPurchased' => $totals['purchased'],
            'totalReturned' => $totals['returned'],
            'totalReturnAmount' => $totals['return_balance'],
        ]);
    }
}