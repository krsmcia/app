<?php

namespace App\Livewire;

use App\Models\Item;
use Illuminate\Pagination\Cursor;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\Url;

class Items extends Component
{
    #[Url]
    public string $search = '';
    public int $perPage = 20;
    public array $items = [];
    public ?string $nextCursor = null;
    public bool $hasMore = true;
    /*
    |--------------------------------------------------------------------------
    | Shopping
    |--------------------------------------------------------------------------
    */
    public function mount(): void
    {
        $this->loadItems();
    }
    public function updatedSearch(): void
    {
        $this->resetItems();

        $this->loadItems();
    }
    public function loadMore(): void
    {
        if (! $this->hasMore || ! $this->nextCursor) {
            return;
        }
        $this->loadItems($this->nextCursor);
    }
    protected function resetItems(): void
    {
        $this->items = [];
        $this->nextCursor = null;
        $this->hasMore = true;
    }
    protected function loadItems(?string $cursor = null): void
    {
        $query = Item::query()
            ->with('primaryImage')
            ->when(
                filled($this->search),
                function ($query) {
                    $search = trim($this->search);

                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
                }
            )
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
                'image' => $item->primaryImage
                    ? Storage::url($item->primaryImage->path)
                    : asset('images/default-item.png'),
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
            ->when(
                filled($this->search),
                function ($query) {
                    $search = trim($this->search);

                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
                }
            )
            ->count();
        return view('livewire.items', [
            'items' => $this->items,
            'total' => $total,
        ]);
    }
}