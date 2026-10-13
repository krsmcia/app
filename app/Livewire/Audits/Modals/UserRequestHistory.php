<?php
namespace App\Livewire\Audits\Modals;
use App\Models\PurchaseItem;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
class UserRequestHistory extends Component
{
    use WithPagination, WithoutUrlPagination;
    public bool $userRequestHistoryModal = false;
    public ?User $user = null;
    public ?int $userId = null;
    public int $perPage = 12;
    public ?string $from = null;
    public ?string $to = null;
    public string $title = '';
    #[On('open-user-request-history')]
    public function openModal($userId)
    {
        $this->resetPage();
        $this->userId = (int) $userId;
        $this->user = User::with([
            'department',
        ])->findOrFail($this->userId);
        $this->userRequestHistoryModal = true;
    }
    public function closeModal()
    {
        $this->userRequestHistoryModal = false;
        $this->resetPage();
        $this->user = null;
        $this->userId = null;
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
    public function render()
    {
        $requestsCount = 0;
        $groupedItems = new LengthAwarePaginator(
            collect(),
            0,
            $this->perPage,
            $this->getPage()
        );
        if ($this->userId) {
            /*
             * ---------------------------------------------------------
             * Total request count
             * ---------------------------------------------------------
             */
            $requestsCountQuery = $this->user->purchaseRequests();
            if ($this->from) {
                $requestsCountQuery->whereDate('created_at', '>=', $this->from);
            }
            if ($this->to) {
                $requestsCountQuery->whereDate('created_at', '<=', $this->to);
            }
            $requestsCount = $requestsCountQuery->count();
            /*
            |--------------------------------------------------------------------------
            | 1. Find unique product IDs requested by this user
            |--------------------------------------------------------------------------
            */
            $itemIdsQuery = PurchaseItem::query()
                ->whereHas('purchaseRequest', function ($query) {
                    $query->where('user_id', $this->userId)
                        ->when($this->from, fn ($q) =>
                            $q->whereDate('created_at', '>=', $this->from)
                        )
                        ->when($this->to, fn ($q) =>
                            $q->whereDate('created_at', '<=', $this->to)
                        );
                })
                ->when(trim($this->title) !== '', function ($query) {
                    $search = trim($this->title);

                    $query->where(function ($q) use ($search) {
                        $q->where('item_name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%")
                            ->orWhere('vendor_name', 'like', "%{$search}%");
                    });
                })
                ->whereNotNull('item_id')
                ->select('item_id')
                ->distinct();

            /*
            |--------------------------------------------------------------------------
            | 2. Count unique products, not purchase requests
            |--------------------------------------------------------------------------
            */
            $totalProducts = (clone $itemIdsQuery)->count('item_id');

            /*
            |--------------------------------------------------------------------------
            | 3. Paginate unique product IDs
            |--------------------------------------------------------------------------
            */
            $itemIds = (clone $itemIdsQuery)
                ->orderBy('item_id')
                ->forPage($this->getPage(), $this->perPage)
                ->pluck('item_id');

            /*
            |--------------------------------------------------------------------------
            | 4. Load all matching purchase records for current-page products
            |--------------------------------------------------------------------------
            */
            if ($itemIds->isNotEmpty()) {
                $purchaseItems = PurchaseItem::query()
                    ->with([
                        'item.primaryImage',
                        'itemVendor.vendor',
                        'purchaseRequest',
                    ])
                    ->whereIn('item_id', $itemIds)
                    ->whereHas('purchaseRequest', function ($query) {
                        $query->where('user_id', $this->userId)
                            ->when($this->from, fn ($q) =>
                                $q->whereDate('created_at', '>=', $this->from)
                            )
                            ->when($this->to, fn ($q) =>
                                $q->whereDate('created_at', '<=', $this->to)
                            );
                    })
                    ->orderByDesc('id')
                    ->get();

                /*
                |--------------------------------------------------------------------------
                | 5. Group all records by product ID
                |--------------------------------------------------------------------------
                */
                $groupedCollection = $purchaseItems
                    ->groupBy('item_id')
                    ->map(function ($items) {
                        $latestItem = $items->sortByDesc('id')->first();

                        $requests = $items
                            ->pluck('purchaseRequest')
                            ->filter()
                            ->unique('id');

                        $latestRequest = $requests
                            ->sortByDesc('created_at')
                            ->first();

                        return [
                            'item' => $latestItem->item,
                            'purchase_item' => $latestItem,
                            'request_count' => $requests->count(),
                            'total_quantity' => $items->sum(
                                fn ($purchaseItem) =>
                                    (float) $purchaseItem->quantity
                            ),
                            'last_requested_at' => $latestRequest?->created_at,
                            'last_request_no' => $latestRequest?->request_no,
                        ];
                    });

                // Keep products in the same order as the paginated IDs.
                $groupedCollection = $itemIds
                    ->map(fn ($itemId) => $groupedCollection->get($itemId))
                    ->filter()
                    ->values();

            } else {
                $groupedCollection = collect();
            }

            /*
            |--------------------------------------------------------------------------
            | 6. Create paginator for grouped products
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
            'livewire.audits.modals.user-request-history',
            [
                'requestsCount' => $requestsCount,
                'groupedItems' => $groupedItems,
            ]
        );
    }
}