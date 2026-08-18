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
                    'name' => 'IT Staff',
                    'email' => 'it-staff@ciamactan.com',
                    'password' => 'password',
                    'role' => 'staff',
                    'departments' => ['it'],
                    'current_department' => 'it',
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
                    'name' => 'HR Team-leader',
                    'email' => 'hr-team-leader@ciamactan.com',
                    'password' => 'password',
                    'role' => 'team-leader',
                    'departments' => ['hr'],
                    'current_department' => 'hr',
                ],
                [
                    'name' => 'IT Team-leader',
                    'email' => 'it-team-leader@ciamactan.com',
                    'password' => 'password',
                    'role' => 'team-leader',
                    'departments' => ['it'],
                    'current_department' => 'it',
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
                    'name' => 'HR Supervisor',
                    'email' => 'hr-supervisor@ciamactan.com',
                    'password' => 'password',
                    'role' => 'supervisor',
                    'departments' => ['hr'],
                    'current_department' => 'hr',
                ],
                [
                    'name' => 'IT Supervisor',
                    'email' => 'it-supervisor@ciamactan.com',
                    'password' => 'password',
                    'role' => 'supervisor',
                    'departments' => ['it'],
                    'current_department' => 'it',
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
                    'name' => 'HR Head',
                    'email' => 'hr-head@ciamactan.com',
                    'password' => 'password',
                    'role' => 'head',
                    'departments' => ['hr'],
                    'current_department' => 'hr',
                ],
                [
                    'name' => 'IT Head',
                    'email' => 'it-head@ciamactan.com',
                    'password' => 'password',
                    'role' => 'head',
                    'departments' => ['it'],
                    'current_department' => 'it',
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
                    'name' => 'Admin',
                    'email' => 'admin@ciamactan.com',
                    'password' => 'password',
                    'role' => 'admin',
                    'departments' => [],
                    'current_department' => '',
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