<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivedItemPhoto extends Model
{
    protected $guarded = [];
    public function purchaseAction()
    {
        return $this->belongsTo(PurchaseAction::class);
    }
}
