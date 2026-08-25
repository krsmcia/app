<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemVendor extends Model
{
    protected $guarded = [];
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
