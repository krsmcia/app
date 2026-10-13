<?php
namespace App\Livewire\Audits\Modals;
use App\Models\Department;
use App\Models\PurchaseItem;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
class DepartmentRequestHistory extends Component
{
    use WithPagination, WithoutUrlPagination;
    public bool $departmentRequestHistoryModal = false;
    public ?Department $department = null;
    public ?int $departmentId = null;
    public int $perPage = 12;
    public ?string $from = null;
    public ?string $to = null;
    public string $title = '';
    #[On('open-department-request-history')]
    public function openModal($departmentId = null)
    {
        $this->resetPage();
        $this->from = null;
        $this->to = null;
        $this->title = '';
        if (!$departmentId) {
            return;
        }
        $this->departmentId = (int) $departmentId;
        $this->department = Department::findOrFail(
            $this->departmentId
        );
        $this->departmentRequestHistoryModal = true;
    }
    public function closeModal()
    {
        $this->departmentRequestHistoryModal = false;
        $this->resetPage();
        $this->department = null;
        $this->departmentId = null;
        $this->from = null;
        $this->to = null;
        $this->title = '';
    }
    public function updatedFrom()
    {
        $this->resetPage();
    }
    public function updatedTo()
    {
        $this->resetPage();
    }
    public function updatedTitle()
    {
        $this->resetPage();
    }
    public function clearFilters()
    {
        $this->reset([
            'from',
            'to',
            'title',
        ]);
        $this->resetPage();
    }
    public function openProductHistory(int $itemId)
    {
        $this->dispatch(
            'open-purchase-request-history',
            departmentId: $this->departmentId,
            itemId: $itemId,
            from: $this->from,
            to: $this->to,
        );
    }
    public function render()
    {
        $requestsCount = 0;
        $groupedItems = new LengthAwarePaginator(
            collect(),
            0,
            $this->perPage,
            $this->getPage()
        );
        if ($this->departmentId) {
            /*
            |--------------------------------------------------------------------------
            | 1. Count purchase requests for this department
            |--------------------------------------------------------------------------
            */
            $requestsCountQuery = $this->department->purchaseRequests();
            if ($this->from) {
                $requestsCountQuery->whereDate(
                    'created_at',
                    '>=',
                    $this->from
                );
            }
            if ($this->to) {
                $requestsCountQuery->whereDate(
                    'created_at',
                    '<=',
                    $this->to
                );
            }
            $requestsCount = $requestsCountQuery->count();
            /*
            |--------------------------------------------------------------------------
            | 2. Get unique product IDs requested by this department
            |--------------------------------------------------------------------------
            */
            $itemIdsQuery = PurchaseItem::query()
                ->whereHas('purchaseRequest', function ($query) {
                    $query->where(
                        'department_id',
                        $this->departmentId
                    );
                    if ($this->from) {
                        $query->whereDate(
                            'created_at',
                            '>=',
                            $this->from
                        );
                    }
                    if ($this->to) {
                        $query->whereDate(
                            'created_at',
                            '<=',
                            $this->to
                        );
                    }
                })
                ->when(trim($this->title) !== '', function ($query) {
                    $search = trim($this->title);
                    $query->where(function ($query) use ($search) {
                        $query
                            ->where(
                                'item_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'sku',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'vendor_name',
                                'like',
                                "%{$search}%"
                            );
                    });
                })
                ->whereNotNull('item_id')
                ->select('item_id')
                ->distinct();
            /*
            |--------------------------------------------------------------------------
            | 3. Count unique products
            |--------------------------------------------------------------------------
            */
            $totalProducts = (clone $itemIdsQuery)
                ->count('item_id');
            /*
            |--------------------------------------------------------------------------
            | 4. Paginate product IDs, not purchase requests
            |--------------------------------------------------------------------------
            */
            $itemIds = (clone $itemIdsQuery)
                ->orderBy('item_id')
                ->forPage(
                    $this->getPage(),
                    $this->perPage
                )
                ->pluck('item_id');
            /*
            |--------------------------------------------------------------------------
            | 5. Load all matching history for products on this page
            |--------------------------------------------------------------------------
            */
            if ($itemIds->isNotEmpty()) {
                $purchaseItems = PurchaseItem::query()
                    ->with([
                        'item.primaryImage',
                        'itemVendor.vendor',
                        'purchaseRequest.user',
                        'purchaseRequest.department',
                    ])
                    ->whereIn('item_id', $itemIds)
                    ->whereHas('purchaseRequest', function ($query) {
                        $query->where(
                            'department_id',
                            $this->departmentId
                        );
                        if ($this->from) {
                            $query->whereDate(
                                'created_at',
                                '>=',
                                $this->from
                            );
                        }
                        if ($this->to) {
                            $query->whereDate(
                                'created_at',
                                '<=',
                                $this->to
                            );
                        }
                    })
                    ->orderByDesc('id')
                    ->get();
                /*
                |--------------------------------------------------------------------------
                | 6. Group purchase items by item_id
                |--------------------------------------------------------------------------
                */
                $groupedCollection = $purchaseItems
                    ->groupBy('item_id')
                    ->map(function ($items) {
                        // Latest purchase-item record
                        $latestItem = $items->first();
                        // Unique purchase requests for this product
                        $requests = $items
                            ->pluck('purchaseRequest')
                            ->filter()
                            ->unique('id')
                            ->values();
                        // Latest purchase request
                        $latestRequest = $requests
                            ->sortByDesc('created_at')
                            ->first();
                        // Unique users who requested this product
                        $requesters = $requests
                            ->pluck('user')
                            ->filter()
                            ->unique('id')
                            ->values();
                        return [
                            'item' => $latestItem->item,
                            'purchase_item' => $latestItem,
                            'request_count' => $requests->count(),
                            'total_quantity' => $items->sum(
                                fn ($purchaseItem) =>
                                    (float) $purchaseItem->quantity
                            ),
                            'last_requested_at' =>
                                $latestRequest?->created_at,
                            'last_request_no' =>
                                $latestRequest?->request_no,
                            'requesters' => $requesters,
                            'requester_count' => $requests
                                ->pluck('user_id')
                                ->filter()
                                ->unique()
                                ->count(),
                            'requests' => $requests
                                ->sortByDesc('created_at')
                                ->values(),
                        ];
                    });
                /*
                |--------------------------------------------------------------------------
                | 7. Preserve the order of paginated product IDs
                |--------------------------------------------------------------------------
                */
                $groupedCollection = $itemIds
                    ->map(
                        fn ($itemId) => $groupedCollection->get($itemId)
                    )
                    ->filter()
                    ->values();
            } else {
                $groupedCollection = collect();
            }
            /*
            |--------------------------------------------------------------------------
            | 8. Build the paginator
            |--------------------------------------------------------------------------
            */
            $groupedItems = new LengthAwarePaginator(
                $groupedCollection,
                $totalProducts,
                $this->perPage,
                $this->getPage(),
                [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ]
            );
        }
        return view(
            'livewire.audits.modals.department-request-history',
            [
                'requestsCount' => $requestsCount,
                'groupedItems' => $groupedItems,
            ]
        );
    }
}