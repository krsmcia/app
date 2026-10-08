<?php
namespace App\Livewire\Accountings;
use Livewire\Component;
use App\Models\Transaction;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
class Transactions extends Component
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
        $transactions = Transaction::query()
        ->with([
            'fromUser:id,name,email',
            'toUser:id,name,email',
            'vendor:id,name',
            'purchaseItemTransactions.purchaseItem.item.primaryImage'
        ])
        ->when($this->fromDate, function ($query) {
            $query->whereDate('created_at', '>=', $this->fromDate);
        })
        ->when($this->toDate, function ($query) {
            $query->whereDate('created_at', '<=', $this->toDate);
        })
        ->when(trim($this->search) !== '', function ($query) {
            $search = '%' . trim($this->search) . '%';
            $query->where(function ($query) use ($search) {
                $query
                    ->where('type', 'like', $search)
                    ->orWhere('remark', 'like', $search)
                    ->orWhereHas('fromUser', function ($query) use ($search) {
                        $query->where('name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    })
                    ->orWhereHas('toUser', function ($query) use ($search) {
                        $query->where('name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    })
                    ->orWhereHas('vendor', function ($query) use ($search) {
                        $query->where('name', 'like', $search);
                    });
            });
        })
        ->latest()
        ->paginate(100);
        return view('livewire.accountings.transactions',['transactions' => $transactions]);
    }
}
