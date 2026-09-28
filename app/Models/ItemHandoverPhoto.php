<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemHandoverPhoto extends Model
{
    protected $guarded = [];
    public function purchaseAction()
    {
        return $this->belongsTo(PurchaseAction::class);
    }
}
