<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseWorkflowItem extends Model
{
    protected $guarded = [];
    protected $casts = [
        'acted_at' => 'datetime',
    ];

    public function workflow()
    {
        return $this->belongsTo(
            PurchaseWorkflow::class,
            'purchase_workflow_id'
        );
    }

    public function purchaseItem()
    {
        return $this->belongsTo(
            PurchaseItem::class
        );
    }
}
