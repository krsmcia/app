<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class MyRequests extends Component
{
    use WithPagination;
    
    public function render()
    {
        $requests = auth()->user()->purchaseRequests()->latest()->paginate(12);
        return view('livewire.my-requests', ['requests' => $requests]);
    }
}
