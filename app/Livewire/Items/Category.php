<?php

namespace App\Livewire\Items;

use App\Models\Category as CategoryModel;
use App\Models\Item;
use Illuminate\Pagination\Cursor;
use Livewire\Component;

class Category extends Component
{
    public ?CategoryModel $category = null;

    public int $perPage = 20;

    public array $items = [];

    public ?string $nextCursor = null;

    public bool $hasMore = true;

    public function mount(?CategoryModel $category = null): void
    {
        $this->category = $category;

        $this->loadItems();
    }

    public function loadMore(): void
    {
        if (! $this->hasMore || ! $this->nextCursor) {
            return;
        }

        $this->loadItems($this->nextCursor);
    }

    protected function loadItems(?string $cursor = null): void
    {
        $query = Item::query()
            ->with('primaryImage')
            ->whereHas('categories', function ($query) {
                $query->where('categories.id', $this->category->id);
            })
            ->orderByDesc('items.id');

        if ($cursor) {
            $cursor = Cursor::fromEncoded($cursor);
        }

        $results = $query->cursorPaginate(
            perPage: $this->perPage,
            columns: ['items.*'],
            cursorName: 'cursor',
            cursor: $cursor,
        );

        foreach ($results->items() as $item) {
            $this->items[] = [
                'id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'image' => $item->image_url,
                'unit' => $item->unit,
                'brand' => $item->brand,
                'color' => $item->color,
                'size' => $item->size,
            ];
        }

        if ($results->nextCursor()) {
            $this->nextCursor = $results->nextCursor()->encode();
            $this->hasMore = true;
        } else {
            $this->nextCursor = null;
            $this->hasMore = false;
        }
    }

    public function render()
    {
        $total = Item::query()
            ->whereHas('categories', function ($query) {
                $query->where('categories.id', $this->category->id);
            })
            ->count();

        return view('livewire.items.category', [
            'items' => $this->items,
            'total' => $total,
        ]);
    }
}