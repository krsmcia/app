<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialInstitution extends Model
{
    //
    protected $guarded = [];
    public function financialInstitutionType()
    {
        return $this->belongsTo(FinancialInstitutionType::class);
    }
}
