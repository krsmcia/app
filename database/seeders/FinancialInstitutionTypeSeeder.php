<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FinancialInstitutionType;
class FinancialInstitutionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        FinancialInstitutionType::insert([
            ['name' => 'E-Wallets', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'bank', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'digital_bank', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
