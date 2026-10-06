<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [];
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }
    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function getTransactionTypeAttribute(): string
    {
        return match (true) {
            $this->from_user_id !== null
                && $this->vendor_id !== null
                => 'Purchasing',

            $this->from_user_id !== null
                && $this->to_user_id !== null
                => 'Release',

            default => ucfirst($this->type ?? 'Other'),
        };
    }
    //여기 더 지켜봐야함. 이게 1:1인지 1:n인지 확인 필요
    public function purchaseRequestTransactions()
    {
        return $this->hasMany(PurchaseRequestTransaction::class);
    }
    public function purchaseItemTransactions()
    {
        return $this->hasMany(PurchaseItemTransaction::class);
    }
}
