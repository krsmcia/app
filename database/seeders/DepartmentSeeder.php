<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::insert([
            ['name'=> 'HR', 'code' => 'hr', 'created_at' => now(), 'updated_at' => now()],
            ['name'=> 'IT', 'code' => 'it', 'created_at' => now(), 'updated_at' => now()],
            ['name'=> 'Audit', 'code' => 'audit', 'created_at' => now(), 'updated_at' => now()],
            ['name'=> 'Finance', 'code' => 'finance', 'created_at' => now(), 'updated_at' => now()],
            ['name'=> 'Marketing', 'code' => 'marketing', 'created_at' => now(), 'updated_at' => now()],
            ['name'=> 'Academic', 'code' => 'academic', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}