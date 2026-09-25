<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseAction extends Model
{
    protected $guarded = [];
    public function purchaseWorkflowItem()
    {
        return $this->belongsTo(PurchaseWorkflowItem::class);
    }
    public function cashRelease()
    {
        return $this->hasOne(CashRelease::class);
    }
    public function receivedItemPhotos()
    {
        return $this->hasMany(ReceivedItemPhoto::class);
    }
    public function releasedItemPhotos()
    {
        return $this->hasMany(ReleasedItemPhoto::class);
    }
}
