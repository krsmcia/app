<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialInstitutionType extends Model
{
    //
    protected $guarded = [];
    public function financialInstitutions()
    {
        return $this->hasMany(FinancialInstitution::class);
    }
}
