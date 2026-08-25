<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseWorkflow extends Model
{
    protected $guarded = [];
    protected $casts = [
        'acted_at' => 'datetime',
    ];
    public function purchaseRequest()
    {
        return $this->belongsTo(
            PurchaseRequest::class
        );
    }

    public function purchaseWorkflowItems()
    {
        return $this->hasMany(
            PurchaseWorkflowItem::class
        );
    }
}
