<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@ciamactan.com',
                'password' => 'password',
                'role' => 'super-admin',
                'departments' => [],
                'current_department' => '',
            ],
        ];

        if (app()->environment('local')) {
            $users = array_merge($users, [
                [
                    'name' => 'HR Staff',
                    'email' => 'hr-staff@ciamactan.com',
                    'password' => 'password',
                    'role' => 'staff',
                    'departments' => ['hr'],
                    'current_department' => 'hr',
                ],
                [
                    'name' => 'Audit Staff',
                    'email' => 'audit-staff@ciamactan.com',
                    'password' => 'password',
                    'role' => 'staff',
                    'departments' => ['audit'],
                    'current_department' => 'audit',
                ],
                [
                    'name' => 'Procurement Staff',
                    'email' => 'procurement-staff@ciamactan.com',
                    'password' => 'password',
                    'role' => 'staff',
                    'departments' => ['procurement'],
                    'current_department' => 'procurement',
                ],
                [
                    'name' => 'HR Team-leader',
                    'email' => 'hr-team-leader@ciamactan.com',
                    'password' => 'password',
                    'role' => 'team-leader',
                    'departments' => ['hr'],
                    'current_department' => 'hr',
                ],
                [
                    'name' => 'Audit Team-leader',
                    'email' => 'audit-team-leader@ciamactan.com',
                    'password' => 'password',
                    'role' => 'team-leader',
                    'departments' => ['audit'],
                    'current_department' => 'audit',
                ],
                [
                    'name' => 'Procurement Staff',
                    'email' => 'procurement-team-leader@ciamactan.com',
                    'password' => 'password',
                    'role' => 'team-leader',
                    'departments' => ['procurement'],
                    'current_department' => 'procurement',
                ],
                [
                    'name' => 'HR Supervisor',
                    'email' => 'hr-supervisor@ciamactan.com',
                    'password' => 'password',
                    'role' => 'supervisor',
                    'departments' => ['hr'],
                    'current_department' => 'hr',
                ],
                [
                    'name' => 'Audit Supervisor',
                    'email' => 'audit-supervisor@ciamactan.com',
                    'password' => 'password',
                    'role' => 'supervisor',
                    'departments' => ['audit'],
                    'current_department' => 'audit',
                ],
                [
                    'name' => 'Procurement Staff',
                    'email' => 'procurement-supervisor@ciamactan.com',
                    'password' => 'password',
                    'role' => 'supervisor',
                    'departments' => ['procurement'],
                    'current_department' => 'procurement',
                ],
                [
                    'name' => 'HR Head',
                    'email' => 'hr-head@ciamactan.com',
                    'password' => 'password',
                    'role' => 'head',
                    'departments' => ['hr'],
                    'current_department' => 'hr',
                ],
                [
                    'name' => 'Audit Head',
                    'email' => 'audit-head@ciamactan.com',
                    'password' => 'password',
                    'role' => 'head',
                    'departments' => ['audit'],
                    'current_department' => 'audit',
                ],
                [
                    'name' => 'Procurement Head',
                    'email' => 'procurement-head@ciamactan.com',
                    'password' => 'password',
                    'role' => 'head',
                    'departments' => ['procurement'],
                    'current_department' => 'procurement',
                ],
            ]);
        }

        foreach ($users as $data) {
            // Current Department
            $currentDepartmentId = null;

            if (!empty($data['current_department'])) {
                $currentDepartmentId = Department::where(
                    'code',
                    $data['current_department']
                )->value('id');
            }

            $user = User::updateOrCreate(
                [
                    'email' => $data['email'],
                ],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                    'current_department_id' => $currentDepartmentId,
                ]
            );

            $user->syncRoles([$data['role']]);

            // Department 관계 설정
            if (!empty($data['departments'])) {
                $departmentIds = Department::whereIn(
                    'code',
                    $data['departments']
                )->pluck('id');

                $user->departments()->sync($departmentIds);
            } else {
                $user->departments()->detach();
            }
        }
    }
}