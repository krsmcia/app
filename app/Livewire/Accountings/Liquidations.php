<?php
namespace App\Livewire\Accountings;
use App\Models\User;
use App\Models\PurchaseItem;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
class Liquidations extends Component
{
    use WithPagination, WithoutUrlPagination;
    public string $search = '';
    public string $fromDate = '';
    public string $toDate = '';
    public function mount(): void
    {
        $this->fromDate = now()
            ->subMonths(2)
            ->startOfMonth()
            ->format('Y-m-d');
        $this->toDate = now()->format('Y-m-d');
    }
    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updatedFromDate(): void
    {
        $this->resetPage();
    }
    public function updatedToDate(): void
    {
        $this->resetPage();
    }
    public function render()
    {
        $items = PurchaseItem::query()
            // Only items whose latest workflow status is "purchased".
            ->whereHas('purchaseWorkflowItems', function ($query) {
                $query
                    /*
                    ->where('status', 'purchased')
                    ->whereRaw('
                        purchase_workflow_items.id = (
                            SELECT MAX(pwi.id)
                            FROM purchase_workflow_items AS pwi
                            WHERE pwi.purchase_item_id = purchase_workflow_items.purchase_item_id
                        )
                    ');
                    */
                    ->where('status', 'purchased');
            })
            ->with([
                'purchaseRequest',
                'item',
            ])
            // Accounting → Procurement
            ->withSum([
                'purchaseItemTransactions as released_amount' => function ($query) {
                    $query->whereHas('transaction', function ($q) {
                        $q->where('type', 'released');
                    });
                },
            ], 'amount')
            // Procurement → Vendor
            // Accounting → Vendor purchases are excluded.
            ->withSum([
                'purchaseItemTransactions as purchased_amount' => function ($query) {
                    $query->whereHas('transaction', function ($q) {
                        $q->where('type', 'purchased')
                            ->whereNotNull('from_user_id')
                            ->whereNull('to_user_id')
                            ->whereNotNull('vendor_id');
                    });
                },
            ], 'amount')
            // Procurement → Accounting
            ->withSum([
                'purchaseItemTransactions as returned_amount' => function ($query) {
                    $query->whereHas('transaction', function ($q) {
                        $q->where('type', 'returned');
                    });
                },
            ], 'amount')
            // Filter by Purchase Request creation date.
            // Transaction dates are intentionally NOT filtered.
            ->when($this->fromDate || $this->toDate, function (Builder $query) {
                $query->whereHas('purchaseRequest', function ($q) {
                    if ($this->fromDate) {
                        $q->whereDate('created_at', '>=', $this->fromDate);
                    }
                    if ($this->toDate) {
                        $q->whereDate('created_at', '<=', $this->toDate);
                    }
                });
            })
            // Search
            ->when($this->search, function (Builder $query) {
                $search = '%' . $this->search . '%';
                $query->where(function ($q) use ($search) {
                    $q->whereHas('item', function ($itemQuery) use ($search) {
                        $itemQuery
                            ->where('name', 'like', $search)
                            ->orWhere('sku', 'like', $search);
                    })
                    ->orWhereHas('purchaseRequest', function ($requestQuery) use ($search) {
                        $requestQuery->where('request_no', 'like', $search);
                    });
                });
            })
            // Liquidation:
            // released + returned - purchased != 0
            ->havingRaw('
                COALESCE(released_amount, 0)
                - COALESCE(returned_amount, 0)
                - COALESCE(purchased_amount, 0) != 0
            ')
            ->latest('id')
            ->paginate(20,['*'], 'itemsPage');
        $procurementUsers = User::query()
            ->whereHas('departments', function ($query) {
                $query->where('code', 'procurement');
            })
            ->orderBy('name')
            ->paginate(20, ['*'], 'usersPage');
        return view('livewire.accountings.liquidations', [
            'items' => $items,
            'procurementUsers' => $procurementUsers,
        ]);
    }
}