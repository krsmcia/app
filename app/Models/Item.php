<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'sku',
        'barcode',
        'name',
        'description',
        'unit',
        'brand',
        'color',
        'size',
        'is_active',
    ];
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
    public function itemImages()
    {
        return $this->hasMany(ItemImage::class)
            ->orderBy('sort_order');
    }
    public function primaryImage()
    {
        return $this->hasOne(ItemImage::class)
            ->where('is_primary', true);
    }
    public function getImageUrlAttribute(): string
    {
        if ($this->primaryImage) {
            return asset( 'storage/'.$this->primaryImage->path);
        }

        return asset('images/default-item.png');
    }
    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'item_vendors')
            ->withPivot([
                'vendor_sku',
                'unit_price',
                'minimum_order_qty',
                'lead_time',
                'is_preferred',
            ])
            ->withTimestamps();
    }
}
