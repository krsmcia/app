<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequestTransaction extends Model
{
    protected $guarded = [];
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
