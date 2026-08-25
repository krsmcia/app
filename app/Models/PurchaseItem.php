<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $guarded = [];
    protected $casts = [
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
    ];
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function itemVendor()
    {
        return $this->belongsTo(ItemVendor::class);
    }

    public function workflowItems()
    {
        return $this->hasMany(
            PurchaseWorkflowItem::class
        );
    }
}
