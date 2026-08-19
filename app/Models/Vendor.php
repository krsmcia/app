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
}
