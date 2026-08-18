<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FinancialInstitution;
class FinancialInstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        FinancialInstitution::insert([
            // E-Wallets
            ['name' => 'GCash', 'financial_institution_type_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            // Traditional / Commercial Banks
            ['name' => 'BDO Unibank', 'financial_institution_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BPI', 'financial_institution_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Metrobank', 'financial_institution_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'LandBank', 'financial_institution_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PNB', 'financial_institution_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Security Bank', 'financial_institution_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'China Bank', 'financial_institution_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'RCBC', 'financial_institution_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'EastWest Bank', 'financial_institution_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'UnionBank', 'financial_institution_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bank of Commerce', 'financial_institution_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PBCOM', 'financial_institution_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Philippine Veterans Bank', 'financial_institution_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maybank Philippines', 'financial_institution_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PSBank', 'financial_institution_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Asia United Bank (AUB)', 'financial_institution_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            // Digital Banks
            ['name' => 'CIMB Bank Philippines', 'financial_institution_type_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maya Bank', 'financial_institution_type_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GoTyme Bank', 'financial_institution_type_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'MariBank Philippines', 'financial_institution_type_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tonik Bank', 'financial_institution_type_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'UNO Digital Bank', 'financial_institution_type_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
