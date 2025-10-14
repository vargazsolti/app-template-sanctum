<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Mindig ürítsük a Spatie cache-t a műveletek előtt/után
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // --- ALL PERMISSIONS YOU USE ---
        $permissions = [
            // Admin area gate + page/action-level perms
            'admin.access',
            'admin.access.roles-permissions',
            'admin.access.roles.index',
            'admin.access.roles.store',
            'admin.access.roles.destroy',
            'admin.access.roles.permissions.attach',
            'admin.access.roles.permissions.detach',
            'admin.access.users',
            'admin.access.users.roles.attach',
            'admin.access.users.roles.detach',
            'admin.access.users.permissions.give',
            'admin.access.users.permissions.revoke',

            // Business CRUD perms (API)
            'users.read',
            'users.write',
            'user_basedata.read',
            'user_basedata.write',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        // Roles
        $admin   = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $user    = Role::firstOrCreate(['name' => 'user']);

        // Assign perms to roles
        // admin: everything
        $admin->syncPermissions(Permission::all());

        // manager: business CRUD, de NEM admin access
        $manager->syncPermissions([
            'users.read', 'users.write',
            'user_basedata.read', 'user_basedata.write',
        ]);

        // user: read-only
        $user->syncPermissions([
            'users.read',
            'user_basedata.read',
        ]);

        // Cache reset a végén is
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
