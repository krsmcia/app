<?php

namespace App\Livewire\Audits\Modals;

use App\Models\Department;
use App\Models\PurchaseItem;
use App\Models\PurchaseRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class PurchaseRequestHistory extends Component
{
    use WithPagination, WithoutUrlPagination;

    private string $paginationPageName = 'purchaseHistoryPage';
    public bool $purchaseRequestHistoryModal = false;

    public ?int $departmentId = null;
    public ?int $itemId = null;

    public ?string $from = null;
    public ?string $to = null;

    public int $perPage = 10;

    #[On('open-purchase-request-history')]
    public function openModal(
        $departmentId = null,
        $itemId = null,
        $from = null,
        $to = null,
    ): void {
        $this->resetPage($this->paginationPageName);

        $this->departmentId = $departmentId
            ? (int) $departmentId
            : null;

        $this->itemId = $itemId
            ? (int) $itemId
            : null;

        $this->from = $from;
        $this->to = $to;

        if (! $this->departmentId || ! $this->itemId) {
            return;
        }

        // Verify that the selected department and item exist.
        Department::findOrFail($this->departmentId);
        PurchaseItem::where('item_id', $this->itemId)
            ->whereHas('purchaseRequest', function ($query) {
                $query->where(
                    'department_id',
                    $this->departmentId
                );
            })
            ->firstOrFail();

        $this->purchaseRequestHistoryModal = true;
    }

    public function closeModal(): void
    {
        $this->purchaseRequestHistoryModal = false;

        $this->reset([
            'departmentId',
            'itemId',
            'from',
            'to',
        ]);

        $this->resetPage($this->paginationPageName);
    }

    public function render()
    {
        $requests = new LengthAwarePaginator(
            collect(),
            0,
            $this->perPage,
            $this->getPage()
        );

        $productName = null;

        if ($this->departmentId && $this->itemId) {
            $baseQuery = PurchaseRequest::query()
                ->where('department_id', $this->departmentId)
                ->whereHas('purchaseItems', function ($query) {
                    $query->where('item_id', $this->itemId);
                })
                ->when($this->from, function ($query) {
                    $query->whereDate(
                        'created_at',
                        '>=',
                        $this->from
                    );
                })
                ->when($this->to, function ($query) {
                    $query->whereDate(
                        'created_at',
                        '<=',
                        $this->to
                    );
                });

            // Do not filter by approval status:
            // pending, approved and rejected requests must all be visible.
            $requests = (clone $baseQuery)
                ->with(['user', 'department'])
                ->latest('created_at')
                ->paginate(
                    $this->perPage,
                    ['*'],
                    $this->paginationPageName,
                    $this->getPage($this->paginationPageName)
                );

            $productName = PurchaseItem::query()
                ->where('item_id', $this->itemId)
                ->whereHas('purchaseRequest', function ($query) {
                    $query->where(
                        'department_id',
                        $this->departmentId
                    );
                })
                ->latest('id')
                ->value('item_name');
        }

        return view(
            'livewire.audits.modals.purchase-request-history',
            [
                'requests' => $requests,
                'productName' => $productName,
            ]
        );
    }
}