<?php

namespace App\Livewire;

use App\Models\Item;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class Items extends Component
{
    use WithPagination;
    #[Url]
    public string $search = '';
    public function render()
    {
        $items = Item::query()
            ->with([
                'primaryImage',
                'vendors',
            ])
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
            ->latest('items.id')
            ->paginate(20);
        return view('livewire.items', [
            'items' => $items,
        ]);
    }
}