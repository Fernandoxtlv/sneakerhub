<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Products
            'view products',
            'create products',
            'edit products',
            'delete products',
            'manage stock',

            // Categories
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',

            // Brands
            'view brands',
            'create brands',
            'edit brands',
            'delete brands',

            // Orders
            'view orders',
            'manage orders',
            'process orders',

            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Reports
            'view reports',
            'export reports',

            // Settings
            'manage settings',

            // Payments
            'view payments',
            'confirm payments',

            // Coupons
            'manage coupons',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Owner - has all permissions
        $owner = Role::create(['name' => 'owner']);
        $owner->givePermissionTo(Permission::all());

        // Admin - has most permissions except settings
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            'view products',
            'create products',
            'edit products',
            'delete products',
            'manage stock',
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            'view brands',
            'create brands',
            'edit brands',
            'delete brands',
            'view orders',
            'manage orders',
            'process orders',
            'view users',
            'create users',
            'edit users',
            'view reports',
            'export reports',
            'view payments',
            'confirm payments',
            'manage coupons',
        ]);

        // Worker - limited permissions for order processing
        $worker = Role::create(['name' => 'worker']);
        $worker->givePermissionTo([
            'view products',
            'manage stock',
            'view orders',
            'process orders',
            'view payments',
            'confirm payments',
        ]);

        // Client - no admin permissions (uses public routes)
        Role::create(['name' => 'client']);
    }
}
