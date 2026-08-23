<?php

namespace App\Livewire\Procurements;

use App\Imports\ItemsImport;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Vendor;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Items extends Component
{
    use WithPagination;
    use WithFileUploads;
    /*
    |--------------------------------------------------------------------------
    | List
    |--------------------------------------------------------------------------
    */
    public string $search = '';
    public string $statusFilter = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';
    /*
    |--------------------------------------------------------------------------
    | Item Form
    |--------------------------------------------------------------------------
    */
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public ?int $editingItemId = null;
    public string $sku = '';
    public string $barcode = '';
    public string $name = '';
    public string $description = '';
    public string $unit = '';
    public string $brand = '';
    public string $color = '';
    public string $size = '';
    public bool $isActive = true;
    /*
    |--------------------------------------------------------------------------
    | Images
    |--------------------------------------------------------------------------
    */
    public array $images = [];
    public array $existingImages = [];
    /*
    |--------------------------------------------------------------------------
    | Excel
    |--------------------------------------------------------------------------
    */
    public $excelFile = null;
    public bool $showImportModal = false;
    /*
    |--------------------------------------------------------------------------
    | Vendor Modal
    |--------------------------------------------------------------------------
    */
    public bool $showVendorModal = false;
    public ?int $vendorItemId = null;
    public ?Item $vendorItem = null;
    public string $vendorSearch = '';
    public array $vendorSearchResults = [];
    public array $vendorForms = [];
    /*
    | Vendor editing form
    |
    | [
    |     vendor_id => [
    |         vendor_sku => '',
    |         unit_price => '',
    |         minimum_order_qty => 1,
    |         lead_time => '',
    |     ]
    | ]
    */
    /*
    |--------------------------------------------------------------------------
    | Category Modal
    |--------------------------------------------------------------------------
    */
    
    public bool $showCategoryModal = false;
    public ?int $categoryItemId = null;
    public ?Item $categoryItem = null;
    public array $selectedCategories = [];
    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */
    protected function rules(): array
    {
        return [
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('items', 'sku')
                    ->ignore($this->editingItemId),
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('items', 'barcode')
                    ->ignore($this->editingItemId),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'unit' => [
                'required',
                'string',
                'max:30',
            ],
            'brand' => [
                'nullable',
                'string',
                'max:255',
            ],
            'color' => [
                'nullable',
                'string',
                'max:255',
            ],
            'size' => [
                'nullable',
                'string',
                'max:255',
            ],
            'isActive' => [
                'boolean',
            ],
            'images' => [
                'nullable',
                'array',
                'max:10',
            ],
            'images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }
    /*
    |--------------------------------------------------------------------------
    | Search / Pagination
    |--------------------------------------------------------------------------
    */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }
    /*
    |--------------------------------------------------------------------------
    | Vendor Search
    |--------------------------------------------------------------------------
    */
    public function updatedVendorSearch(): void
    {
        $this->searchVendors();
    }
    public function searchVendors(): void
    {
        $search = trim($this->vendorSearch);
        if (strlen($search) < 2 || ! $this->vendorItemId) {
            $this->vendorSearchResults = [];
            return;
        }
        $attachedVendorIds = $this->vendorItem
            ? $this->vendorItem->vendors
                ->pluck('id')
                ->all()
            : [];
        $this->vendorSearchResults = Vendor::query()
            ->where('is_active', true)
            ->when(
                ! empty($attachedVendorIds),
                fn ($query) => $query->whereNotIn('id', $attachedVendorIds)
            )
            ->where(function ($query) use ($search) {
                $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(fn (Vendor $vendor) => [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'code' => $vendor->code,
            ])
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Sort
    |--------------------------------------------------------------------------
    */

    public function sortBy(string $field): void
    {
        $allowedFields = [
            'sku',
            'barcode',
            'name',
            'unit',
            'brand',
            'color',
            'size',
            'is_active',
            'created_at',
        ];
        if (! in_array($field, $allowedFields, true)) {
            return;
        }
        if ($this->sortField === $field) {
            $this->sortDirection =
                $this->sortDirection === 'asc'
                    ? 'desc'
                    : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }
    /*
    |--------------------------------------------------------------------------
    | Create Item
    |--------------------------------------------------------------------------
    */
    public function create(): void
    {
        $this->resetForm();
        $this->isActive = true;
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function createItem(): void
    {
        $this->editingItemId = null;
        $validated = $this->validate();
        DB::transaction(function () use ($validated) {
            $item = Item::create([
                'sku' => $validated['sku'],
                'barcode' => $validated['barcode'] ?? null,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'unit' => $validated['unit'],
                'brand' => $validated['brand'] ?? null,
                'color' => $validated['color'] ?? null,
                'size' => $validated['size'] ?? null,
                'is_active' => $validated['isActive'],
            ]);
            $this->storeNewImages($item);
        });
        $this->showCreateModal = false;
        $this->resetForm();
        $this->resetValidation();
        session()->flash(
            'success',
            'Item created successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Item
    |--------------------------------------------------------------------------
    */

    public function edit(int $id): void
    {
        $item = Item::with('itemImages')
            ->findOrFail($id);
        $this->editingItemId = $item->id;
        $this->sku = $item->sku;
        $this->barcode = $item->barcode ?? '';
        $this->name = $item->name;
        $this->description = $item->description ?? '';
        $this->unit = $item->unit;
        $this->brand = $item->brand ?? '';
        $this->color = $item->color ?? '';
        $this->size = $item->size ?? '';
        $this->isActive = (bool) $item->is_active;
        $this->existingImages = $item->itemImages
            ->sortBy('sort_order')
            ->map(fn (ItemImage $image) => [
                'id' => $image->id,
                'path' => $image->path,
                'is_primary' => (bool) $image->is_primary,
            ])
            ->values()
            ->toArray();
        $this->images = [];
        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function updateItem(): void
    {
        $item = Item::findOrFail($this->editingItemId);
        $validated = $this->validate();
        DB::transaction(function () use ($item, $validated) {
            $item->update([
                'sku' => $validated['sku'],
                'barcode' => $validated['barcode'] ?? null,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'unit' => $validated['unit'],
                'brand' => $validated['brand'] ?? null,
                'color' => $validated['color'] ?? null,
                'size' => $validated['size'] ?? null,
                'is_active' => $validated['isActive'],
            ]);
            $this->storeNewImages($item);
        });
        $this->showEditModal = false;
        $this->resetForm();
        $this->resetValidation();
        session()->flash(
            'success',
            'Item updated successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Image Handling
    |--------------------------------------------------------------------------
    */

    private function storeNewImages(Item $item): void
    {
        if (empty($this->images)) {
            return;
        }
        $lastSortOrder = $item->itemImages()
            ->max('sort_order');
        $nextSortOrder = $lastSortOrder === null
            ? 0
            : ((int) $lastSortOrder + 1);
        $hasPrimary = $item->itemImages()
            ->where('is_primary', true)
            ->exists();
        foreach ($this->images as $image) {
            $path = $image->store(
                "items/{$item->id}",
                'public'
            );
            $item->itemImages()->create([
                'path' => $path,
                'sort_order' => $nextSortOrder++,
                'is_primary' => ! $hasPrimary,
            ]);
            $hasPrimary = true;
        }
    }

    public function deleteImage(int $imageId): void
    {
        abort_unless(
            $this->editingItemId,
            404
        );
        $image = ItemImage::query()
            ->where('item_id', $this->editingItemId)
            ->findOrFail($imageId);
        $wasPrimary = (bool) $image->is_primary;
        Storage::disk('public')
            ->delete($image->path);
        $image->delete();
        if ($wasPrimary) {
            $nextImage = ItemImage::query()
                ->where('item_id', $this->editingItemId)
                ->orderBy('sort_order')
                ->first();
            if ($nextImage) {
                $nextImage->update([
                    'is_primary' => true,
                ]);
            }
        }
        $this->refreshExistingImages();
    }
    public function setPrimaryImage(int $imageId): void
    {
        abort_unless(
            $this->editingItemId,
            404
        );
        $image = ItemImage::query()
            ->where('item_id', $this->editingItemId)
            ->findOrFail($imageId);
        DB::transaction(function () use ($image) {
            ItemImage::query()
                ->where('item_id', $this->editingItemId)
                ->update([
                    'is_primary' => false,
                ]);
            $image->update([
                'is_primary' => true,
            ]);
        });
        $this->refreshExistingImages();
    }
    private function refreshExistingImages(): void
    {
        if (! $this->editingItemId) {
            $this->existingImages = [];
            return;
        }
        $this->existingImages = ItemImage::query()
            ->where('item_id', $this->editingItemId)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (ItemImage $image) => [
                'id' => $image->id,
                'path' => $image->path,
                'is_primary' => (bool) $image->is_primary,
            ])
            ->values()
            ->toArray();
    }
    /*
    |--------------------------------------------------------------------------
    | Delete Item
    |--------------------------------------------------------------------------
    */
    public function deleteItem(int $id): void
    {
        $item = Item::with('itemImages')
            ->findOrFail($id);
        DB::transaction(function () use ($item) {
            foreach ($item->itemImages as $image) {
                Storage::disk('public')
                    ->delete($image->path);
            }
            $item->delete();
        });
        session()->flash(
            'success',
            'Item deleted successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Vendor Management
    |--------------------------------------------------------------------------
    */

    public function manageVendors(int $itemId): void
    {
        $this->vendorItemId = $itemId;
        $this->vendorItem = Item::with([
            'vendors',
        ])->findOrFail($itemId);
        $this->vendorSearch = '';
        $this->vendorSearchResults = [];
        $this->initializeVendorForms();
        $this->showVendorModal = true;
    }
    public function manageCategories(int $itemId): void
    {
        $this->categoryItemId = $itemId;
        $this->categoryItem = Item::with('categories')
            ->findOrFail($itemId);
        $this->selectedCategories = $this->categoryItem
            ->categories
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
        $this->showCategoryModal = true;
    }
    private function initializeVendorForms(): void
    {
        $this->vendorForms = [];
        if (! $this->vendorItem) {
            return;
        }
        foreach ($this->vendorItem->vendors as $vendor) {
            $this->vendorForms[$vendor->id] = [
                'vendor_sku' => $vendor->pivot->vendor_sku ?? '',
                'unit_price' => $vendor->pivot->unit_price !== null
                    ? (string) $vendor->pivot->unit_price
                    : '',
                'minimum_order_qty' => $vendor->pivot->minimum_order_qty ?? 1,
                'lead_time' => $vendor->pivot->lead_time ?? '',
            ];
        }
    }

    public function addVendor(int $vendorId): void
    {
        if (! $this->vendorItemId) {
            return;
        }
        $item = Item::findOrFail($this->vendorItemId);
        if (
            $item->vendors()
                ->where('vendor_id', $vendorId)
                ->exists()
        ) {
            return;
        }
        $isFirstVendor = ! $item->vendors()->exists();
        $item->vendors()->attach($vendorId, [
            'minimum_order_qty' => 1,
            'is_preferred' => $isFirstVendor,
        ]);
        $this->reloadVendorItem();
        $this->vendorSearch = '';
        $this->vendorSearchResults = [];
    }

    public function removeVendor(int $vendorId): void
    {
        if (! $this->vendorItemId) {
            return;
        }
        $item = Item::findOrFail($this->vendorItemId);
        $wasPreferred = $item->vendors()
            ->where('vendor_id', $vendorId)
            ->wherePivot('is_preferred', true)
            ->exists();
        DB::transaction(function () use (
            $item,
            $vendorId,
            $wasPreferred
        ) {
            $item->vendors()->detach($vendorId);
            if ($wasPreferred) {
                $nextVendor = $item->vendors()
                    ->orderBy('name')
                    ->first();
                if ($nextVendor) {
                    $item->vendors()->updateExistingPivot(
                        $nextVendor->id,
                        [
                            'is_preferred' => true,
                        ]
                    );
                }
            }
        });
        $this->reloadVendorItem();
    }

    public function setPreferredVendor(int $vendorId): void
    {
        if (! $this->vendorItemId) {
            return;
        }
        $item = Item::findOrFail($this->vendorItemId);
        abort_unless(
            $item->vendors()
                ->where('vendor_id', $vendorId)
                ->exists(),
            404
        );
        DB::transaction(function () use ($item, $vendorId) {
            $item->vendors()->updateExistingPivot(
                $item->vendors()->pluck('vendors.id')->all(),
                [
                    'is_preferred' => false,
                ]
            );
            $item->vendors()->updateExistingPivot(
                $vendorId,
                [
                    'is_preferred' => true,
                ]
            );
        });
        $this->reloadVendorItem();
    }

    public function updateVendor(int $vendorId): void
    {
        if (! $this->vendorItemId) {
            return;
        }
        $item = Item::findOrFail($this->vendorItemId);
        abort_unless(
            $item->vendors()
                ->where('vendor_id', $vendorId)
                ->exists(),
            404
        );
        $form = $this->vendorForms[$vendorId] ?? [];
        $validated = validator(
            $form,
            [
                'vendor_sku' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'unit_price' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'minimum_order_qty' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'lead_time' => [
                    'nullable',
                    'integer',
                    'min:0',
                ],
            ]
        )->validate();
        $item->vendors()->updateExistingPivot(
            $vendorId,
            [
                'vendor_sku' => $validated['vendor_sku'] ?? null,
                'unit_price' => $validated['unit_price'] ?? null,
                'minimum_order_qty' => $validated['minimum_order_qty'],
                'lead_time' => $validated['lead_time'] ?? null,
            ]
        );
        $this->reloadVendorItem();
        session()->flash(
            'success',
            'Vendor information updated successfully.'
        );
    }

    private function reloadVendorItem(): void
    {
        if (! $this->vendorItemId) {
            $this->vendorItem = null;
            $this->vendorForms = [];
            return;
        }
        $this->vendorItem = Item::with([
            'vendors',
        ])->findOrFail($this->vendorItemId);
        $this->initializeVendorForms();
    }

    /*
    |--------------------------------------------------------------------------
    | Modal
    |--------------------------------------------------------------------------
    */

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function closeVendorModal(): void
    {
        $this->showVendorModal = false;
        $this->vendorItemId = null;
        $this->vendorItem = null;
        $this->vendorSearch = '';
        $this->vendorSearchResults = [];
        $this->vendorForms = [];
    }

    /*
    |--------------------------------------------------------------------------
    | Form Reset
    |--------------------------------------------------------------------------
    */

    private function resetForm(): void
    {
        $this->editingItemId = null;
        $this->sku = '';
        $this->barcode = '';
        $this->name = '';
        $this->description = '';
        $this->unit = '';
        $this->brand = '';
        $this->color = '';
        $this->size = '';
        $this->isActive = true;
        $this->images = [];
        $this->existingImages = [];
    }

    /*
    |--------------------------------------------------------------------------
    | Excel
    |--------------------------------------------------------------------------
    */

    public function openImportModal(): void
    {
        $this->excelFile = null;
        $this->resetValidation();
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->excelFile = null;
        $this->resetValidation();
    }

    public function importExcel(): void
    {
        $this->validate([
            'excelFile' => [
                'required',
                'file',
                'extensions:xlsx,xls,csv',
                'max:51200',
            ],
        ]);
        Excel::import(
            new ItemsImport,
            $this->excelFile
        );
        $this->showImportModal = false;
        $this->excelFile = null;
        session()->flash(
            'success',
            'Items imported successfully.'
        );
        $this->resetPage();
    }
    public function saveCategories(): void
    {
        if (! $this->categoryItemId) {
            return;
        }
        $item = Item::findOrFail($this->categoryItemId);
        $categoryIds = collect($this->selectedCategories)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();
        $item->categories()->sync($categoryIds);
        $this->categoryItem = $item->load('categories');
        session()->flash(
            'success',
            'Categories updated successfully.'
        );
    }
    public function closeCategoryModal(): void
    {
        $this->showCategoryModal = false;
        $this->categoryItemId = null;
        $this->categoryItem = null;
        $this->selectedCategories = [];
    }
    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $items = Item::query()
            ->with([
                'primaryImage',
                'categories',
            ])
            ->when(
                $this->search !== '',
                function ($query) {
                    $query->where(function ($query) {
                        $query
                            ->where(
                                'sku',
                                'like',
                                "%{$this->search}%"
                            )
                            ->orWhere(
                                'barcode',
                                'like',
                                "%{$this->search}%"
                            )
                            ->orWhere(
                                'name',
                                'like',
                                "%{$this->search}%"
                            )
                            ->orWhere(
                                'brand',
                                'like',
                                "%{$this->search}%"
                            )
                            ->orWhere(
                                'color',
                                'like',
                                "%{$this->search}%"
                            )
                            ->orWhere(
                                'size',
                                'like',
                                "%{$this->search}%"
                            )
                            ->orWhere(
                                'unit',
                                'like',
                                "%{$this->search}%"
                            );
                    });
                }
            )
            ->when(
                $this->statusFilter !== '',
                function ($query) {
                    $query->where(
                        'is_active',
                        $this->statusFilter === 'active'
                    );
                }
            )
            ->orderBy(
                $this->sortField,
                $this->sortDirection
            )
            ->paginate(15);
        $categories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with('childrenRecursive')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        return view(
            'livewire.procurements.items',
            [
                'items' => $items,
                'categories' => $categories,
            ]
        );
    }
}