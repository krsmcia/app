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
             * ---------------------------------------------------------
             * Product IDs requested by this user
             *
             * IMPORTANT:
             * We paginate PRODUCTS, not purchase requests.
             * ---------------------------------------------------------
             */
            $itemIdsQuery = PurchaseItem::query()
                ->whereHas('purchaseRequest', function ($query) {
                    $query->where('user_id', $this->userId);

                    if ($this->from) {
                        $query->whereDate('created_at', '>=', $this->from);
                    }

                    if ($this->to) {
                        $query->whereDate('created_at', '<=', $this->to);
                    }
                })
                ->when(
                    trim($this->title) !== '',
                    function ($query) {
                        $search = trim($this->title);

                        $query->where(function ($query) use ($search) {
                            $query
                                ->where('item_name', 'like', '%' . $search . '%')
                                ->orWhere('sku', 'like', '%' . $search . '%')
                                ->orWhere('vendor_name', 'like', '%' . $search . '%');
                        });
                    }
                )
                ->whereNotNull('item_id')
                ->select('item_id')
                ->distinct();


            /*
             * ---------------------------------------------------------
             * Count unique products
             * ---------------------------------------------------------
             */
            $totalProducts = (clone $itemIdsQuery)->count();


            /*
             * ---------------------------------------------------------
             * Get current page item IDs
             * ---------------------------------------------------------
             */
            $itemIds = $itemIdsQuery
                ->orderBy('item_id')
                ->forPage(
                    $this->getPage(),
                    $this->perPage
                )
                ->pluck('item_id');


            if ($itemIds->isNotEmpty()) {

                /*
                 * -----------------------------------------------------
                 * Get all purchase items belonging to the products
                 * on the current page.
                 *
                 * This is NOT paginated.
                 *
                 * We need all history for these products so that
                 * total quantity / request count are accurate.
                 * -----------------------------------------------------
                 */
                $purchaseItems = PurchaseItem::query()
                    ->with([
                        'item.primaryImage',
                        'itemVendor.vendor',
                        'purchaseRequest',
                    ])
                    ->whereHas('purchaseRequest', function ($query) {
                        $query->where('user_id', $this->userId);

                        if ($this->from) {
                            $query->whereDate('created_at', '>=', $this->from);
                        }

                        if ($this->to) {
                            $query->whereDate('created_at', '<=', $this->to);
                        }
                    })
                    ->whereIn('item_id', $itemIds)
                    ->orderByDesc(
                        DB::raw('COALESCE(created_at, id)')
                    )
                    ->get();


                /*
                 * -----------------------------------------------------
                 * Group by product
                 * -----------------------------------------------------
                 */
                $groupedItems = $purchaseItems
                    ->groupBy('item_id')
                    ->map(function ($items) {

                        $firstItem = $items->first();

                        $requests = $items
                            ->map(function ($purchaseItem) {

                                return $purchaseItem->purchaseRequest;

                            })
                            ->filter();


                        return [
                            'item' => $firstItem->item,

                            'purchase_item' => $firstItem,

                            'request_count' => $requests
                                ->pluck('id')
                                ->unique()
                                ->count(),

                            'total_quantity' => $items->sum(
                                fn ($purchaseItem) =>
                                    (float) $purchaseItem->quantity
                            ),

                            'last_requested_at' => $requests
                                ->max('created_at'),

                            'last_request_no' => $requests
                                ->sortByDesc('created_at')
                                ->first()?->request_no,
                        ];
                    });


                /*
                 * -----------------------------------------------------
                 * Keep database ordering
                 * -----------------------------------------------------
                 */
                $groupedItems = $itemIds
                    ->map(function ($itemId) use ($groupedItems) {
                        return $groupedItems->get($itemId);
                    })
                    ->filter()
                    ->values();


                /*
                 * -----------------------------------------------------
                 * Convert to paginator
                 *
                 * This paginator is LOCAL.
                 * It is NOT stored in a Livewire public property.
                 * -----------------------------------------------------
                 */
                $groupedItems = new LengthAwarePaginator(
                    $groupedItems,
                    $totalProducts,
                    $this->perPage,
                    $this->getPage(),
                    [
                        'path' => request()->url(),
                        'query' => request()->query(),
                    ]
                );
            } else {

                $groupedItems = new LengthAwarePaginator(
                    collect(),
                    $totalProducts,
                    $this->perPage,
                    $this->getPage()
                );
            }
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