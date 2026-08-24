<?php

namespace App\Livewire;

use App\Models\Item;
use Livewire\Component;

class Wishlist extends Component
{
    public array $wishlistIds = [];

    public function setWishlist(array $ids): void
    {
        $this->wishlistIds = collect($ids)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    public function render()
    {
        $wishlistItems = empty($this->wishlistIds)
            ? collect()
            : Item::query()
                ->where('is_active', true)
                ->with('primaryImage')
                ->whereIn('id', $this->wishlistIds)
                ->get();

        return view('livewire.wishlist', [
            'wishlistItems' => $wishlistItems,
        ]);
    }
}