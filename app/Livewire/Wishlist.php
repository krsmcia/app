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
        $wishlistItems = Item::query()
            ->where('is_active', true)
            ->whereIn('id', $this->wishlistIds)
            ->with('primaryImage')
            ->get();

        return view('livewire.wishlist', [
            'wishlistItems' => $wishlistItems,
        ]);
    }
}