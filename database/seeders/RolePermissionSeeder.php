<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create comprehensive permissions for full system management
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Role & Permission Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'assign roles',
            'assign permissions',
            
            // Dashboard Access
            'access admin dashboard',
            'access user dashboard',
            
            // Profile Management
            'edit profile',
            'view profile',
            
            // Audit & Logs
            'view audit logs',
            'export audit logs',
            'delete audit logs',
            
            // System Settings
            'manage settings',
            'view reports',
            'export reports',
            
            // Hidrologi Management
            'view hidrologi',
            'create hidrologi',
            'edit hidrologi',
            'delete hidrologi',
            'download hidrologi files',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Super Admin role with ALL permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        $this->command->info('Super Admin role created with all permissions.');
        $this->command->info('Total permissions created: ' . count($permissions));
    }
}
