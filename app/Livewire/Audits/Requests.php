<?php

namespace App\Livewire\Audits;

use App\Models\Item;
use App\Models\Vendor;
use App\Models\ItemVendor;
use App\Models\PurchaseWorkflow;
use App\Models\PurchaseWorkflowItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Livewire\Component;

class Requests extends Component
{
    public function render()
    {
        return view('livewire.audits.requests');
    }
}
