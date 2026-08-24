<?php

namespace App\Livewire;

use App\Models\Item;
use Livewire\Component;

class Wishlist extends Component
{
    public array $wishlistIds = [];

    public function mount(): void
    {
        // localStorage는 JS에서 가져오므로
        // 최초에는 빈 배열로 시작합니다.
        $this->wishlistIds = [];
    }

    public function setWishlist(array $ids): void
    {
        $this->wishlistIds = collect($ids)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    public function getWishlistItemsProperty()
    {
        if (empty($this->wishlistIds)) {
            return collect();
        }

        return Item::query()
            ->whereIn('id', $this->wishlistIds)
            ->get();
    }

    public function render()
    {
        return view('livewire.wishlist');
    }
}