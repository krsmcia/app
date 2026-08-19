<?php

namespace App\Livewire\Procurements;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Categories extends Component
{
    public string $search = '';

    public bool $showCreateModal = false;
    public bool $showEditModal = false;

    public string $name = '';
    public string $code = '';
    public string $description = '';

    public ?int $parentId = null;

    public bool $isActive = true;

    public int $sortOrder = 0;

    public ?int $editingCategoryId = null;

    public function updatedSearch(): void
    {
        // Search is handled in render().
    }

    public function openCreateModal(?int $parentId = null): void
    {
        $this->resetValidation();

        $this->reset([
            'name',
            'code',
            'description',
            'editingCategoryId',
            'sortOrder',
        ]);

        $this->parentId = $parentId;
        $this->isActive = true;
        $this->sortOrder = 0;

        $this->showCreateModal = true;
    }

    public function createCategory(): void
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'required',
                'string',
                'max:255',
                'unique:categories,code',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'parentId' => [
                'nullable',
                'integer',
                'exists:categories,id',
            ],

            'sortOrder' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        Category::create([
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description ?: null,
            'parent_id' => $this->parentId,
            'is_active' => $this->isActive,
            'sort_order' => $this->sortOrder,
        ]);

        $this->showCreateModal = false;

        $this->resetForm();

        session()->flash(
            'success',
            'Category created successfully.'
        );
    }

    public function editCategory(int $categoryId): void
    {
        $category = Category::findOrFail($categoryId);

        $this->resetValidation();

        $this->editingCategoryId = $category->id;

        $this->name = $category->name;
        $this->code = $category->code;
        $this->description = $category->description ?? '';

        $this->parentId = $category->parent_id;

        $this->isActive = $category->is_active;
        $this->sortOrder = $category->sort_order;

        $this->showEditModal = true;
    }

    public function updateCategory(): void
    {
        $category = Category::findOrFail(
            $this->editingCategoryId
        );

        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'required',
                'string',
                'max:255',
                'unique:categories,code,' . $category->id,
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'parentId' => [
                'nullable',
                'integer',
                'exists:categories,id',
            ],

            'sortOrder' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        // Prevent selecting itself as parent.
        if ($this->parentId === $category->id) {
            $this->addError(
                'parentId',
                'A category cannot be its own parent.'
            );

            return;
        }

        // Prevent moving category under one of its descendants.
        if (
            $this->parentId &&
            $category->descendants()
                ->whereKey($this->parentId)
                ->exists()
        ) {
            $this->addError(
                'parentId',
                'A category cannot be moved under one of its descendants.'
            );

            return;
        }

        $category->update([
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description ?: null,
            'parent_id' => $this->parentId,
            'is_active' => $this->isActive,
            'sort_order' => $this->sortOrder,
        ]);

        $this->showEditModal = false;

        $this->resetForm();

        session()->flash(
            'success',
            'Category updated successfully.'
        );
    }

    public function deleteCategory(int $categoryId): void
    {
        $category = Category::withCount('children')
            ->findOrFail($categoryId);

        if ($category->children_count > 0) {
            session()->flash(
                'error',
                'You cannot delete a category that has child categories.'
            );

            return;
        }

        $category->delete();

        session()->flash(
            'success',
            'Category deleted successfully.'
        );
    }

    public function reorderCategories(
        array $orderedIds,
        ?int $parentId = null
    ): void {
        DB::transaction(function () use (
            $orderedIds,
            $parentId
        ) {
            foreach ($orderedIds as $index => $id) {
                Category::query()
                    ->whereKey($id)
                    ->where('parent_id', $parentId)
                    ->update([
                        'sort_order' => $index + 1,
                    ]);
            }
        });
    }

    protected function resetForm(): void
    {
        $this->reset([
            'name',
            'code',
            'description',
            'parentId',
            'editingCategoryId',
            'sortOrder',
        ]);

        $this->isActive = true;
        $this->sortOrder = 0;
    }
    public function render()
    {
        $categories = Category::query()
            ->whereNull('parent_id')
            ->with([
                'childrenRecursive' => function ($query) {
                    $query
                        ->orderBy('sort_order')
                        ->orderBy('name');
                },
            ])
            ->withCount('children')
            ->when(
                $this->search,
                function ($query) {
                    $search = '%' . $this->search . '%';

                    $query->where(function ($query) use ($search) {
                        $query
                            ->where(
                                'name',
                                'like',
                                $search
                            )
                            ->orWhere(
                                'code',
                                'like',
                                $search
                            );
                    });
                }
            )
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $parentCategories = Category::query()
            ->when(
                $this->editingCategoryId,
                function ($query) {
                    $query->where(
                        'id',
                        '!=',
                        $this->editingCategoryId
                    );
                }
            )
            ->orderBy('name')
            ->get();
        return view('livewire.procurements.categories',
            [
                'categories' => $categories,
                'parentCategories' => $parentCategories,
            ]
        );
    }
}
