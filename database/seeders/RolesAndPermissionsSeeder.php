<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'approve-users',
            'suspend-users',

            // Settings Management
            'manage-settings',
            'view-settings',

            // Activity Log
            'view-activity-log',

            // Client Portal Permissions
            'access-client-portal',
            'view-own-profile',
            'edit-own-profile',
            'view-own-activity',
            'view-own-notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Admin role - has all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        // Agent role - can view and manage users but not settings
        $agentRole = Role::firstOrCreate(['name' => 'agent']);
        $agentRole->syncPermissions([
            'view-users',
            'view-activity-log',
        ]);

        // Client role - restricted permissions for client portal access
        $clientRole = Role::firstOrCreate(['name' => 'client']);
        $clientRole->syncPermissions([
            'access-client-portal',
            'view-own-profile',
            'edit-own-profile',
            'view-own-activity',
            'view-own-notifications',
        ]);
    }
}
