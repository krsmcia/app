<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'code',
        'name',
        'legal_name',
        'type',
        'contact_person',
        'email',
        'phone',
        'website',
        'address',
        'tax_number',
        'payment_terms',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_vendors')
            ->withPivot([
                'vendor_sku',
                'unit_price',
                'minimum_order_qty',
                'lead_time',
                'is_preferred',
            ])
            ->withTimestamps();
    }
    public function itemVendors()
    {
        return $this->hasMany(ItemVendor::class);
    }
}
