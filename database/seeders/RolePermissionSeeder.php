<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Request
            'purchase-request.create',
            'purchase-request.view',
            'purchase-request.update',
            'purchase-request.submit',
            // Workflow
            'purchase-request.review',
            'purchase-request.approve',
            'purchase-request.reject',
            'purchase-request.request-revision',
            // Additional
            'purchase-request.attach',
            'purchase-request.note',
            'purchase-request.audit',
            // User management
            'user.view',
            'user.create',
            'user.update',
            'user.deactivate',
            // Department management
            'department.view',
            'department.create',
            'department.update',
            'department.manage-members',
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
            ]);
        }
        Role::firstOrCreate(['name' => 'staff']);
        Role::firstOrCreate(['name' => 'team-leader']);
        Role::firstOrCreate(['name' => 'supervisor']);
        Role::firstOrCreate(['name' => 'head']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'super-admin']);

        Role::findByName('staff')->syncPermissions([
            'purchase-request.create',
            'purchase-request.view',
            'purchase-request.update',
            'purchase-request.submit',
            'purchase-request.attach',
        ]);

        Role::findByName('team-leader')->syncPermissions([
            'purchase-request.create',
            'purchase-request.view',
            'purchase-request.review',
            'purchase-request.approve',
            'purchase-request.reject',
            'purchase-request.request-revision',
            'purchase-request.attach',
            'purchase-request.note',
        ]);

        Role::findByName('supervisor')->syncPermissions([
            'purchase-request.create',
            'purchase-request.view',
            'purchase-request.review',
            'purchase-request.approve',
            'purchase-request.reject',
            'purchase-request.request-revision',
            'purchase-request.attach',
            'purchase-request.note',
        ]);

        Role::findByName('head')->syncPermissions([
            'purchase-request.view',
            'purchase-request.approve',
            'purchase-request.reject',
            'purchase-request.request-revision',
            'purchase-request.attach',
            'purchase-request.note',
        ]);
    }
}
