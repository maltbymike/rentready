<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Misc
        $miscPermission = Permission::create(['name' => 'N/A']);

        // User Model
        $userPermission1 = Permission::create(['name' => 'user: create']);
        $userPermission2 = Permission::create(['name' => 'user: read']);
        $userPermission3 = Permission::create(['name' => 'user: update']);
        $userPermission4 = Permission::create(['name' => 'user: delete']);

        // Role Model
        $rolePermission1 = Permission::create(['name' => 'role: create']);
        $rolePermission2 = Permission::create(['name' => 'role: read']);
        $rolePermission3 = Permission::create(['name' => 'role: update']);
        $rolePermission4 = Permission::create(['name' => 'role: delete']);

        // Permission Model
        $permission1 = Permission::create(['name' => 'permission: create']);
        $permission2 = Permission::create(['name' => 'permission: read']);
        $permission3 = Permission::create(['name' => 'permission: update']);
        $permission4 = Permission::create(['name' => 'permission: delete']);

        // Admins
        $adminPermission1 = Permission::create(['name' => 'admin: read']);
        $adminPermission2 = Permission::create(['name' => 'admin: update']);

        // Create Roles
        $userRole = Role::create(['name' => 'user'])->syncPermissions([
            $miscPermission,
        ]);
        $superAdminRole = Role::create(['name' => 'super-admin'])->syncPermissions([
            $userPermission1,
            $userPermission2,
            $userPermission3,
            $userPermission4,
            $rolePermission1,
            $rolePermission2,
            $rolePermission3,
            $rolePermission4,
            $permission1,
            $permission2,
            $permission3,
            $permission4,
            $adminPermission1,
            $adminPermission2,
        ]);
        $adminRole = Role::create(['name' => 'admin'])->syncPermissions([
            $userPermission1,
            $userPermission2,
            $userPermission3,
            $userPermission4,
            $rolePermission1,
            $rolePermission2,
            $rolePermission3,
            $rolePermission4,
            $permission1,
            $permission2,
            $permission3,
            $permission4,
            $adminPermission1,
            $adminPermission2,
        ]);
        $moderatorRole = Role::create(['name' => 'moderator'])->syncPermissions([
            $userPermission2,
            $rolePermission2,
            $permission2,
            $adminPermission1,
        ]);
        $developerRole = Role::create(['name' => 'developer'])->syncPermissions([
            $adminPermission1,
        ]);

        User::create([
            'name' => 'Super Admin',
            'is_admin' => 1,
            'email' => 'super@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ])->assignRole($superAdminRole);
        User::create([
            'name' => 'Admin',
            'is_admin' => 1,
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ])->assignRole($adminRole);
        User::create([
            'name' => 'Moderator',
            'is_admin' => 1,
            'email' => 'moderator@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ])->assignRole($moderatorRole);
        User::create([
            'name' => 'Developer',
            'is_admin' => 1,
            'email' => 'developer@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ])->assignRole($developerRole);

        for ($i=1; $i < 20; $i++) {
            User::create([
                'name' => 'User ' . $i,
                'is_admin' => 0,
                'email' => 'test' . $i . '@test.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ])->assignRole($userRole);    
        }
    }
}
