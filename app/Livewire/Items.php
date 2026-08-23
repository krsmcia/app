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

    public array $quantities = [];

    public array $wishlist = [];

    public array $cart = [];

    public function mount(): void
    {
        $this->wishlist = session()->get('wishlist', []);
        $this->cart = session()->get('cart', []);

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
        $this->quantities = [];
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
            ];

            $this->quantities[$item->id] ??= 1;
        }

        if ($results->nextCursor()) {
            $this->nextCursor = $results->nextCursor()->encode();
            $this->hasMore = true;
        } else {
            $this->nextCursor = null;
            $this->hasMore = false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Wishlist
    |--------------------------------------------------------------------------
    */

    public function toggleWishlist(int $itemId): void
    {
        if (in_array($itemId, $this->wishlist)) {
            $this->wishlist = array_values(
                array_diff($this->wishlist, [$itemId])
            );
        } else {
            $this->wishlist[] = $itemId;
        }

        session()->put('wishlist', $this->wishlist);

        $this->dispatch('wishlist-updated');
    }

    public function isWishlisted(int $itemId): bool
    {
        return in_array($itemId, $this->wishlist);
    }

    /*
    |--------------------------------------------------------------------------
    | Quantity
    |--------------------------------------------------------------------------
    */

    public function increaseQuantity(int $itemId): void
    {
        $this->quantities[$itemId] = min(
            ($this->quantities[$itemId] ?? 1) + 1,
            999
        );
    }

    public function decreaseQuantity(int $itemId): void
    {
        $this->quantities[$itemId] = max(
            ($this->quantities[$itemId] ?? 1) - 1,
            1
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Cart
    |--------------------------------------------------------------------------
    */

    public function addToCart(int $itemId): void
    {
        $quantity = max(
            1,
            (int) ($this->quantities[$itemId] ?? 1)
        );

        if (isset($this->cart[$itemId])) {
            $this->cart[$itemId]['quantity'] += $quantity;
        } else {
            $item = collect($this->items)
                ->firstWhere('id', $itemId);

            if (! $item) {
                return;
            }

            $this->cart[$itemId] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'sku' => $item['sku'],
                'image' => $item['image'],
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $this->cart);

        // 다음번에는 1개부터 시작
        $this->quantities[$itemId] = 1;

        $this->dispatch('cart-updated');

        $this->dispatch(
            'cart-added',
            message: 'Added to cart'
        );
    }

    public function getCartCountProperty(): int
    {
        return collect($this->cart)
            ->sum('quantity');
    }

    public function getWishlistCountProperty(): int
    {
        return count($this->wishlist);
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