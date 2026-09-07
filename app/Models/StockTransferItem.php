<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferItem extends Model
{
    protected $guarded = [];
    protected $casts = [
        'quantity' => 'decimal:2',
        'reserved_quantity' => 'decimal:2',
        'reorder_point' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function getAvailableQuantityAttribute()
    {
        return max(
            0,
            (float) $this->quantity - (float) $this->reserved_quantity
        );
    }

    public function getStockStatusAttribute(): string
    {
        if ((float) $this->quantity <= 0) {
            return 'out';
        }

        if ((float) $this->quantity <= (float) $this->reorder_point) {
            return 'low';
        }

        return 'available';
    }
}
