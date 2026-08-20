<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemImage extends Model
{
    protected $fillable = [
        'item_id',
        'path',
        'sort_order',
        'is_primary',
    ];
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
