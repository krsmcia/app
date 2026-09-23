<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItemTransaction extends Model
{
    protected $guarded = [];
    public function purchaseItem()
    {
        return $this->belongsTo(PurchaseItem::class);
    }
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
