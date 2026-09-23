<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
class PurchaseWorkflowItem extends Model
{
    protected $guarded = [];
    protected $casts = [
        'acted_at' => 'datetime',
    ];

    public function purchaseWorkflow()
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
    public function purchaseActions()
    {
        return $this->hasMany(PurchaseAction::class);
    }
    public function receivedItemPhotos()
    {
        return $this->hasManyThrough(ReceivedItemPhoto::class, PurchaseAction::class);
    }
}
