<?php

namespace App\Models;

use App\Enums\PurchaseRequestStatus;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    protected $guarded = [];
    protected function casts(): array
    {
        return [
            'status' => PurchaseRequestStatus::class,
        ];
    }
    //
}
