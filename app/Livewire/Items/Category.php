<?php

namespace App\Livewire\Items;
use App\Models\Category as CategoryModel;
use App\Models\Item;
use Livewire\Component;
use Livewire\WithPagination;

class Category extends Component
{
    use WithPagination;
    public ?CategoryModel $category = null;

    public function mount(?CategoryModel $category = null): void
    {
        $this->category = $category;
    }

    public function render()
    {
        $items = Item::query()
            ->with([
                'itemImages',
                'vendors',
            ])
            ->whereHas('categories', function ($query) {
                $query->where('categories.id', $this->category->id);
            })
            ->latest('items.id')
            ->paginate(12);

        return view('livewire.items.category', [
            'items' => $items,
        ]);
    }
}