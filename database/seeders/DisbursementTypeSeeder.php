<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DisbursementType;
class DisbursementTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DisbursementType::insert([
            ['name' => 'Bank Transfer', 'requires_account' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cash', 'requires_account' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Creadit Card', 'requires_account' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Check', 'requires_account' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
