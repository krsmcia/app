<?php

namespace App\Livewire\Audits\Modals;

use App\Models\Department;
use App\Models\PurchaseItem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
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
             * ---------------------------------------------------------
             * Total request count
             * ---------------------------------------------------------
             *
             * Count ALL purchase requests made by users
             * belonging to this department.
             */
            $requestsCountQuery = $this->department
                ->purchaseRequests();

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
             * ---------------------------------------------------------
             * Product IDs requested by this department
             * ---------------------------------------------------------
             *
             * We paginate PRODUCTS, not purchase requests.
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
                ->when(
                    trim($this->title) !== '',
                    function ($query) {

                        $search = trim($this->title);

                        $query->where(function ($query) use ($search) {

                            $query
                                ->where(
                                    'item_name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'sku',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'vendor_name',
                                    'like',
                                    '%' . $search . '%'
                                );
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
                 * Get ALL purchase items for the products
                 * on the current page.
                 *
                 * This is intentionally NOT paginated.
                 * -----------------------------------------------------
                 */
                $purchaseItems = PurchaseItem::query()
                    ->with([
                        'item.primaryImage',
                        'itemVendor.vendor',
                        'purchaseRequest.user',
                        'purchaseRequest.department',
                    ])
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
                            ->map(
                                fn ($purchaseItem) =>
                                    $purchaseItem->purchaseRequest
                            )
                            ->filter();

                        return [
                            /*
                             * Product
                             */
                            'item' => $firstItem->item,

                            /*
                             * First purchase item
                             *
                             * Useful for SKU / vendor / item information.
                             */
                            'purchase_item' => $firstItem,

                            /*
                             * Number of unique purchase requests
                             */
                            'request_count' => $requests
                                ->pluck('id')
                                ->unique()
                                ->count(),

                            /*
                             * Total quantity requested
                             * by this department
                             */
                            'total_quantity' => $items->sum(
                                fn ($purchaseItem) =>
                                    (float) $purchaseItem->quantity
                            ),

                            /*
                             * Last requested date
                             */
                            'last_requested_at' => $requests
                                ->max('created_at'),

                            /*
                             * Last request number
                             */
                            'last_request_no' => $requests
                                ->sortByDesc('created_at')
                                ->first()?->request_no,

                            /*
                             * Users who requested this product
                             *
                             * Useful for Audit.
                             */
                            'requesters' => $requests
                                ->pluck('user')
                                ->filter()
                                ->unique('id')
                                ->values(),

                            /*
                             * Number of unique users who requested it
                             */
                            'requester_count' => $requests
                                ->pluck('user_id')
                                ->filter()
                                ->unique()
                                ->count(),

                            /*
                             * Complete request history for this product
                             *
                             * Useful if the Blade needs to expand
                             * the product and show individual requests.
                             */
                            'requests' => $requests
                                ->sortByDesc('created_at')
                                ->values(),
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
            'livewire.audits.modals.department-request-history',
            [
                'requestsCount' => $requestsCount,
                'groupedItems' => $groupedItems,
            ]
        );
    }
}