<?php

namespace App\Livewire\Procurements;

use App\Models\Item;
use App\Models\ItemImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ItemsImport;

class Items extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';

    public string $statusFilter = '';

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

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

    public $excelFile = null;

    public bool $showImportModal = false;

    public array $existingImages = [];
    public array $images = [];

    protected function rules(): array
    {
        return [
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('items', 'sku')->ignore($this->editingItemId),
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('items', 'barcode')->ignore($this->editingItemId),
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
            $this->sortDirection = $this->sortDirection === 'asc'
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
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(): void
    {
        $this->resetForm();

        $this->isActive = true;

        $this->resetValidation();

        $this->showCreateModal = true;
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(int $id): void
    {
        $item = Item::findOrFail($id);

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

        $this->resetValidation();
        $this->existingImages = $item->itemImages
            ->map(fn ($image) => [
                'id' => $image->id,
                'path' => $image->path,
                'is_primary' => $image->is_primary,
            ])
            ->toArray();
        $this->images = [];
        $this->showEditModal = true;
    }


    /*
    |--------------------------------------------------------------------------
    | Create / Update
    |--------------------------------------------------------------------------
    */

    public function createItem(): void
    {
        $this->editingItemId = null;

        $validated = $this->validate();

        $item = Item::create([
            'sku' => $validated['sku'],
            'barcode' => $validated['barcode'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'unit' => $validated['unit'],
            'brand' => $validated['brand'],
            'color' => $validated['color'],
            'size' => $validated['size'],
            'is_active' => $validated['isActive'],
        ]);

        foreach ($this->images as $index => $image) {
            $path = $image->store('items', 'public');

            $item->itemImages()->create([
                'path' => $path,
                'sort_order' => $index,
                'is_primary' => $index === 0,
            ]);
        }

        $this->showCreateModal = false;

        $this->resetForm();
        $this->resetValidation();

        session()->flash(
            'success',
            'Item created successfully.'
        );
    }


    public function updateItem(): void
    {
        $item = Item::findOrFail($this->editingItemId);

        $validated = $this->validate();

        $item->update([
            'sku' => $validated['sku'],
            'barcode' => $validated['barcode'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'unit' => $validated['unit'],
            'brand' => $validated['brand'],
            'color' => $validated['color'],
            'size' => $validated['size'],
            'is_active' => $validated['isActive'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Upload New Images
        |--------------------------------------------------------------------------
        */

        $existingImageCount = $item->itemImages()->count();

        foreach ($this->images as $index => $image) {

            $path = $image->store('items', 'public');

            $item->itemImages()->create([
                'path' => $path,
                'sort_order' => $existingImageCount + $index,
                'is_primary' => $existingImageCount === 0 && $index === 0,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Close Modal
        |--------------------------------------------------------------------------
        */

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
    | Delete
    |--------------------------------------------------------------------------
    */

    public function deleteItem(int $id): void
    {
        $item = Item::findOrFail($id);

        $item->delete();

        session()->flash(
            'success',
            'Item deleted successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Excel Import
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


    /*
    |--------------------------------------------------------------------------
    | Form
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
    }
    public function deleteImage(int $imageId): void
    {
        $image = ItemImage::where('item_id', $this->editingItemId)
            ->findOrFail($imageId);
        Storage::disk('public')->delete($image->path);
        $image->delete();
        $this->existingImages = collect($this->existingImages)
            ->reject(fn ($item) => $item['id'] === $imageId)
            ->values()
            ->toArray();
    }
    public function setPrimaryImage(int $imageId): void
    {
        $image = ItemImage::where('item_id', $this->editingItemId)
            ->findOrFail($imageId);

        ItemImage::where('item_id', $this->editingItemId)
            ->update([
                'is_primary' => false,
            ]);

        $image->update([
            'is_primary' => true,
        ]);

        $this->existingImages = collect($this->existingImages)
            ->map(function ($item) use ($imageId) {
                $item['is_primary'] = $item['id'] === $imageId;

                return $item;
            })
            ->toArray();
    }
    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $items = Item::query()

            ->when($this->search !== '', function ($query) {

                $query->where(function ($query) {

                    $query
                        ->where('sku', 'like', "%{$this->search}%")
                        ->orWhere('barcode', 'like', "%{$this->search}%")
                        ->orWhere('name', 'like', "%{$this->search}%")
                        ->orWhere('brand', 'like', "%{$this->search}%")
                        ->orWhere('color', 'like', "%{$this->search}%")
                        ->orWhere('size', 'like', "%{$this->search}%")
                        ->orWhere('unit', 'like', "%{$this->search}%");

                });

            })

            ->when($this->statusFilter !== '', function ($query) {

                $query->where(
                    'is_active',
                    $this->statusFilter === 'active'
                );

            })

            ->orderBy(
                $this->sortField,
                $this->sortDirection
            )

            ->paginate(15);

        return view(
            'livewire.procurements.items',
            [
                'items' => $items,
            ]
        );
    }
}