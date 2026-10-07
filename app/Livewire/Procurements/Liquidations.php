<?php

namespace App\Livewire\Procurements;

use App\Models\PurchaseItem;
use App\Models\PurchaseItemTransaction;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class Liquidations extends Component
{
    use WithPagination, WithoutUrlPagination;

    public string $search = '';
    /**
     * Selected purchase date.
     *
     * This is based on purchased transaction created_at.
     */
    public ?string $purchaseDate = null;
    public bool $showLiquidationModal = false;
    public ?int $selectedItemId = null;
    public ?PurchaseItem $selectedItem = null;
    public float $remainingAmount = 0;
    public ?int $accountingUserId = null;
    public function mount(): void
    {
        /*
         * Default to yesterday.
         *
         * Yesterday is the first date that can be liquidated.
         */
        $this->purchaseDate = now('Asia/Manila')
            ->subDay()
            ->toDateString();

        $this->resetLiquidation();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Open liquidation confirmation modal for one item.
     */
    public function openLiquidation(int $purchaseItemId): void
    {
        $this->resetValidation();

        $this->selectedItem = PurchaseItem::query()
            ->with([
                'purchaseRequest',
                'item',
            ])
            ->findOrFail($purchaseItemId);

        $this->selectedItemId = $this->selectedItem->id;

        $this->remainingAmount = $this->calculateRemaining(
            $this->selectedItem->id
        );

        if ($this->remainingAmount <= 0) {
            $this->resetLiquidation();

            $this->dispatch(
                'notify',
                message: 'There is no remaining amount to liquidate.'
            );

            return;
        }

        $this->accountingUserId = null;

        $this->showLiquidationModal = true;
    }

    /**
     * Reset modal state.
     */
    private function resetLiquidation(): void
    {
        $this->showLiquidationModal = false;
        $this->selectedItemId = null;
        $this->selectedItem = null;
        $this->remainingAmount = 0;
        $this->accountingUserId = null;

        $this->resetValidation();
    }

    /**
     * Get the start/end datetime for the selected purchase date.
     *
     * The date is interpreted using Asia/Manila.
     */
    private function purchaseDateRange(): array
    {
        $date = Carbon::parse(
            $this->purchaseDate,
            'Asia/Manila'
        );

        return [
            $date->copy()->startOfDay(),
            $date->copy()->endOfDay(),
        ];
    }

    /**
     * Calculate remaining cash for one purchase item
     * belonging to the currently authenticated Procurement user.
     *
     * Positive:
     * Procurement has money that should be returned to Accounting.
     *
     * Negative:
     * Procurement needs additional money from Accounting.
     */
    private function calculateRemaining(int $purchaseItemId): float
    {
        $userId = auth()->id();

        $itemTransactions = PurchaseItemTransaction::query()
            ->where('purchase_item_id', $purchaseItemId)
            ->with('transaction')
            ->get();

        $received = $itemTransactions
            ->filter(function ($itemTransaction) use ($userId) {
                $transaction = $itemTransaction->transaction;

                return (
                    $transaction->type === 'released'
                    && (int) $transaction->to_user_id === $userId
                ) || (
                    $transaction->type === 'transfer'
                    && (int) $transaction->to_user_id === $userId
                );
            })
            ->sum('amount');

        $purchased = $itemTransactions
            ->filter(function ($itemTransaction) use ($userId) {
                $transaction = $itemTransaction->transaction;

                return $transaction->type === 'purchased'
                    && (int) $transaction->from_user_id === $userId
                    && $transaction->to_user_id === null
                    && $transaction->vendor_id !== null;
            })
            ->sum('amount');

        $transferredOut = $itemTransactions
            ->filter(function ($itemTransaction) use ($userId) {
                $transaction = $itemTransaction->transaction;

                return $transaction->type === 'transfer'
                    && (int) $transaction->from_user_id === $userId
                    && $transaction->to_user_id !== null;
            })
            ->sum('amount');

        $returned = $itemTransactions
            ->filter(function ($itemTransaction) use ($userId) {
                $transaction = $itemTransaction->transaction;

                return $transaction->type === 'returned'
                    && (int) $transaction->from_user_id === $userId
                    && $transaction->vendor_id === null;
            })
            ->sum('amount');

        return round(
            (float) $received
            - (float) $purchased
            - (float) $transferredOut
            - (float) $returned,
            2
        );
    }

    /**
     * Liquidate every purchase item purchased on the selected date.
     *
     * The current page is NOT used here.
     *
     * All items for the selected purchase date are processed.
     */
    public function liquidateAll(): void
    {
        $this->validate([
            'purchaseDate' => [
                'required',
                'date',
            ],
            'accountingUserId' => [
                'required',
                'integer',
                'exists:users,id',
            ],
        ]);
        $today = now('Asia/Manila')->toDateString();
        /*
         * Today's purchases are still active.
         * They can be viewed but cannot be liquidated.
         */
        if ($this->purchaseDate >= $today) {
            $this->addError(
                'purchaseDate',
                'Today\'s purchases cannot be liquidated. They can be liquidated tomorrow.'
            );
            return;
        }
        $procurementUserId = auth()->id();
        $accountingUser = User::query()
            ->whereKey($this->accountingUserId)
            ->whereHas('departments', function ($query) {
                $query->where('code', 'accounting');
            })
            ->firstOrFail();
        [$start, $end] = $this->purchaseDateRange();
        /*
         * Get every PurchaseItem that has a purchased transaction
         * on the selected date.
         *
         * There is intentionally NO 100-item limit.
         */
        $purchaseItemIds = PurchaseItemTransaction::query()
            ->whereHas('transaction', function ($query) use ($start, $end) {
                $query
                    ->where('type', 'purchased')
                    ->whereBetween('created_at', [$start, $end]);
            })
            ->distinct()
            ->pluck('purchase_item_id');
        if ($purchaseItemIds->isEmpty()) {
            $this->dispatch(
                'notify',
                message: 'There are no purchased items for the selected date.'
            );
            return;
        }
        $liquidatedCount = 0;
        $returnedTotal = 0;
        DB::transaction(function () use (
            $purchaseItemIds,
            $procurementUserId,
            $accountingUser,
            &$liquidatedCount,
            &$returnedTotal
        ) {
            /*
             * Process in chunks so that 1,000+ items do not need
             * to be loaded into memory at once.
             */
            $purchaseItemIds
                ->chunk(500)
                ->each(function ($ids) use (
                    $procurementUserId,
                    $accountingUser,
                    &$liquidatedCount,
                    &$returnedTotal
                ) {
                    foreach ($ids as $purchaseItemId) {
                        /*
                         * Lock this item's transaction records before
                         * calculating the final remaining amount.
                         */
                        $itemTransactions = PurchaseItemTransaction::query()
                            ->where('purchase_item_id', $purchaseItemId)
                            ->with('transaction')
                            ->lockForUpdate()
                            ->get();

                        $received = $itemTransactions
                            ->filter(function ($itemTransaction) use ($procurementUserId) {
                                $transaction = $itemTransaction->transaction;

                                return (
                                    $transaction->type === 'released'
                                    && (int) $transaction->to_user_id === $procurementUserId
                                ) || (
                                    $transaction->type === 'transfer'
                                    && (int) $transaction->to_user_id === $procurementUserId
                                );
                            })
                            ->sum('amount');

                        $purchased = $itemTransactions
                            ->filter(function ($itemTransaction) use ($procurementUserId) {
                                $transaction = $itemTransaction->transaction;

                                return $transaction->type === 'purchased'
                                    && (int) $transaction->from_user_id === $procurementUserId
                                    && $transaction->to_user_id === null
                                    && $transaction->vendor_id !== null;
                            })
                            ->sum('amount');

                        $transferredOut = $itemTransactions
                            ->filter(function ($itemTransaction) use ($procurementUserId) {
                                $transaction = $itemTransaction->transaction;

                                return $transaction->type === 'transfer'
                                    && (int) $transaction->from_user_id === $procurementUserId
                                    && $transaction->to_user_id !== null;
                            })
                            ->sum('amount');

                        $returned = $itemTransactions
                            ->filter(function ($itemTransaction) use ($procurementUserId) {
                                $transaction = $itemTransaction->transaction;

                                return $transaction->type === 'returned'
                                    && (int) $transaction->from_user_id === $procurementUserId
                                    && $transaction->vendor_id === null;
                            })
                            ->sum('amount');

                        $remaining = round(
                            (float) $received
                            - (float) $purchased
                            - (float) $transferredOut
                            - (float) $returned,
                            2
                        );

                        /*
                         * Positive remaining means Procurement has
                         * unused money that must be returned to Accounting.
                         */
                        if ($remaining <= 0) {
                            continue;
                        }

                        $purchaseItem = PurchaseItem::query()
                            ->with('purchaseRequest')
                            ->find($purchaseItemId);

                        if (! $purchaseItem) {
                            continue;
                        }

                        /*
                         * One physical cash return
                         * = one Transaction.
                         */
                        $transaction = Transaction::create([
                            'from_user_id' => $procurementUserId,
                            'to_user_id' => $accountingUser->id,
                            'vendor_id' => null,
                            'type' => 'returned',
                            'amount' => $remaining,
                            'remark' => sprintf(
                                'Cash liquidation for %s - %s',
                                $purchaseItem->purchaseRequest?->request_no
                                    ?? 'Purchase Item',
                                $purchaseItem->item_name
                            ),
                            'created_by' => $procurementUserId,
                        ]);

                        /*
                         * Connect returned money to this PurchaseItem.
                         */
                        PurchaseItemTransaction::create([
                            'purchase_item_id' => $purchaseItem->id,
                            'transaction_id' => $transaction->id,
                            'amount' => $remaining,
                        ]);

                        $liquidatedCount++;
                        $returnedTotal += $remaining;
                    }
                });
        });

        $this->resetLiquidation();

        session()->flash(
            'success',
            sprintf(
                'Liquidation completed. %d item(s), ₱%s returned to Accounting.',
                $liquidatedCount,
                number_format($returnedTotal, 2)
            )
        );

        $this->resetPage();
    }

    /**
     * Keep the existing single-item liquidation functionality.
     */
    public function liquidate(): void
    {
        $this->validate([
            'accountingUserId' => [
                'required',
                'integer',
                'exists:users,id',
            ],
        ]);

        $today = now('Asia/Manila')->toDateString();

        if (
            $this->purchaseDate === null
            || $this->purchaseDate >= $today
        ) {
            abort(
                422,
                'Today\'s purchases cannot be liquidated.'
            );
        }

        $procurementUserId = auth()->id();

        $accountingUser = User::query()
            ->whereKey($this->accountingUserId)
            ->whereHas('departments', function ($query) {
                $query->where('code', 'accounting');
            })
            ->firstOrFail();

        $purchaseItem = PurchaseItem::query()
            ->with('purchaseRequest')
            ->findOrFail($this->selectedItemId);

        DB::transaction(function () use (
            $procurementUserId,
            $accountingUser,
            $purchaseItem
        ) {
            $itemTransactions = PurchaseItemTransaction::query()
                ->where('purchase_item_id', $purchaseItem->id)
                ->with('transaction')
                ->lockForUpdate()
                ->get();

            $received = $itemTransactions
                ->filter(function ($itemTransaction) use ($procurementUserId) {
                    $transaction = $itemTransaction->transaction;

                    return (
                        $transaction->type === 'released'
                        && (int) $transaction->to_user_id === $procurementUserId
                    ) || (
                        $transaction->type === 'transfer'
                        && (int) $transaction->to_user_id === $procurementUserId
                    );
                })
                ->sum('amount');

            $purchased = $itemTransactions
                ->filter(function ($itemTransaction) use ($procurementUserId) {
                    $transaction = $itemTransaction->transaction;

                    return $transaction->type === 'purchased'
                        && (int) $transaction->from_user_id === $procurementUserId
                        && $transaction->to_user_id === null
                        && $transaction->vendor_id !== null;
                })
                ->sum('amount');

            $transferredOut = $itemTransactions
                ->filter(function ($itemTransaction) use ($procurementUserId) {
                    $transaction = $itemTransaction->transaction;

                    return $transaction->type === 'transfer'
                        && (int) $transaction->from_user_id === $procurementUserId
                        && $transaction->to_user_id !== null;
                })
                ->sum('amount');

            $returned = $itemTransactions
                ->filter(function ($itemTransaction) use ($procurementUserId) {
                    $transaction = $itemTransaction->transaction;

                    return $transaction->type === 'returned'
                        && (int) $transaction->from_user_id === $procurementUserId
                        && $transaction->vendor_id === null;
                })
                ->sum('amount');

            $remaining = round(
                (float) $received
                - (float) $purchased
                - (float) $transferredOut
                - (float) $returned,
                2
            );

            if ($remaining <= 0) {
                abort(
                    422,
                    'There is no remaining amount to liquidate.'
                );
            }

            $transaction = Transaction::create([
                'from_user_id' => $procurementUserId,
                'to_user_id' => $accountingUser->id,
                'vendor_id' => null,
                'type' => 'returned',
                'amount' => $remaining,
                'remark' => sprintf(
                    'Cash liquidation for %s - %s',
                    $purchaseItem->purchaseRequest?->request_no
                        ?? 'Purchase Item',
                    $purchaseItem->item_name
                ),
                'created_by' => $procurementUserId,
            ]);

            PurchaseItemTransaction::create([
                'purchase_item_id' => $purchaseItem->id,
                'transaction_id' => $transaction->id,
                'amount' => $remaining,
            ]);
        });

        $this->resetLiquidation();

        session()->flash(
            'success',
            'Cash liquidation completed successfully.'
        );

        $this->resetPage();
    }

    public function render()
    {
        $items = collect();
        /*
         * Only query items after a purchase date has been selected.
         */
        if ($this->purchaseDate) {
            [$start, $end] = $this->purchaseDateRange();

            $userId = auth()->id();

            $items = PurchaseItem::query()
                ->with([
                    'purchaseRequest',
                    'item',
                ])
                /*
                 * The selected date is based on the PURCHASED
                 * transaction date.
                 */
                ->whereHas('purchaseItemTransactions.transaction', function ($query) use (
                    $start,
                    $end,
                    $userId
                ) {
                    $query
                        ->where('type', 'purchased')
                        ->where('from_user_id', $userId)
                        ->whereNull('to_user_id')
                        ->whereNotNull('vendor_id')
                        ->whereBetween('created_at', [$start, $end]);
                })
                /*
                 * Calculate the current remaining balance for display.
                 */
                ->withSum([
                    'purchaseItemTransactions as received_released_amount' => function ($query) use ($userId) {
                        $query->whereHas('transaction', function ($q) use ($userId) {
                            $q->where('type', 'released')
                                ->where('to_user_id', $userId);
                        });
                    },
                ], 'amount')
                ->withSum([
                    'purchaseItemTransactions as received_transfer_amount' => function ($query) use ($userId) {
                        $query->whereHas('transaction', function ($q) use ($userId) {
                            $q->where('type', 'transfer')
                                ->where('to_user_id', $userId);
                        });
                    },
                ], 'amount')
                ->withSum([
                    'purchaseItemTransactions as purchased_amount' => function ($query) use ($userId) {
                        $query->whereHas('transaction', function ($q) use ($userId) {
                            $q->where('type', 'purchased')
                                ->where('from_user_id', $userId)
                                ->whereNull('to_user_id')
                                ->whereNotNull('vendor_id');
                        });
                    },
                ], 'amount')
                ->withSum([
                    'purchaseItemTransactions as transferred_out_amount' => function ($query) use ($userId) {
                        $query->whereHas('transaction', function ($q) use ($userId) {
                            $q->where('type', 'transfer')
                                ->where('from_user_id', $userId)
                                ->whereNotNull('to_user_id');
                        });
                    },
                ], 'amount')
                ->withSum([
                    'purchaseItemTransactions as returned_amount' => function ($query) use ($userId) {
                        $query->whereHas('transaction', function ($q) use ($userId) {
                            $q->where('type', 'returned')
                                ->where('from_user_id', $userId)
                                ->whereNull('vendor_id');
                        });
                    },
                ], 'amount')
                ->when($this->search, function (Builder $query) {
                    $search = '%' . $this->search . '%';
                    $query->where(function ($q) use ($search) {
                        $q->whereHas('purchaseRequest', function ($requestQuery) use ($search) {
                            $requestQuery->where(
                                'request_no',
                                'like',
                                $search
                            );
                        })
                        ->orWhere('item_name', 'like', $search)
                        ->orWhere('sku', 'like', $search);
                    });
                })
                /*
                 * Keep items with either positive or negative balance.
                 *
                 * Positive = Procurement should return money.
                 * Negative = Procurement needs money.
                 */
                ->havingRaw('
                    COALESCE(received_released_amount, 0)
                    + COALESCE(received_transfer_amount, 0)
                    - COALESCE(purchased_amount, 0)
                    - COALESCE(transferred_out_amount, 0)
                    - COALESCE(returned_amount, 0) != 0
                ')
                ->latest('id')
                ->paginate(20);
        }
        $accountingUsers = User::query()
            ->whereHas('departments', function ($query) {
                $query->where('code', 'accounting');
            })
            ->orderBy('name')
            ->get();
        return view('livewire.procurements.liquidations', [
            'items' => $items,
            'accountingUsers' => $accountingUsers,
        ]);
    }
}
