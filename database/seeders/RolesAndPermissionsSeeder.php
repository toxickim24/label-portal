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
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Admin role - has all permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Agent role - can view and manage users but not settings
        $agentRole = Role::create(['name' => 'agent']);
        $agentRole->givePermissionTo([
            'view-users',
            'view-activity-log',
        ]);

        // Client role - basic permissions only
        $clientRole = Role::create(['name' => 'client']);
        // Clients have no special permissions by default
    }
}
