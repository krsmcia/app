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
            ['name'=> 'Procurement', 'code' => 'procurement', 'created_at' => now(), 'updated_at' => now()],
            ['name'=> 'Construction', 'code' => 'construction', 'created_at' => now(), 'updated_at' => now()],
            ['name'=> 'Admin Office', 'code' => 'admin-office', 'created_at' => now(), 'updated_at' => now()],
            ['name'=> 'Housekeeping and Loundry', 'code' => 'hal', 'created_at' => now(), 'updated_at' => now()],
            ['name'=> 'CMart And Louba', 'code' => 'cal', 'created_at' => now(), 'updated_at' => now()],
            ['name'=> 'Clinic', 'code' => 'clinic', 'created_at' => now(), 'updated_at' => now()],
            ['name'=> 'F&B', 'code' => 'fab', 'created_at' => now(), 'updated_at' => now()],
            ['name'=> 'Academic', 'code' => 'academic', 'created_at' => now(), 'updated_at' => now()],
            ['name'=> 'Maintenance', 'code' => 'maintenance', 'created_at' => now(), 'updated_at' => now()],
            ['name'=> 'Very Good House', 'code' => 'vgh', 'created_at' => now(), 'updated_at' => now()],
            ['name'=> 'Midori', 'code' => 'midori', 'created_at' => now(), 'updated_at' => now()],
            ['name'=> 'Audit', 'code' => 'audit', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}