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

        // Permissions
        $accessAdminPanel = Permission::create(['name' => 'Access Admin Panel']);

        $manageUsers = Permission::create(['name' => 'Manage Users']);
        $manageRoles = Permission::create(['name' => 'Manage Roles']);

        $manageTimeclockEntries = Permission::create(['name' => 'Manage Timeclock Entries']);
        
        // Create Roles
        $adminRole = Role::create(['name' => 'Administrator'])->syncPermissions([
            $accessAdminPanel,
        ]);
        $employeeRole = Role::create(['name' => 'Employee'])->syncPermissions([
            $accessAdminPanel,
        ]);
        $managerRole = Role::create(['name' => 'Manager'])->syncPermissions([
            $accessAdminPanel,
            $manageUsers,
            $manageTimeclockEntries,
        ]);
        $timeclockUserRole = Role::create(['name' => 'Timeclock User']);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'pin' => '1234',
            'remember_token' => Str::random(10),
        ])->assignRole($adminRole);
        User::create([
            'name' => 'Manager',
            'email' => 'manager@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'pin' => '1234',
            'remember_token' => Str::random(10),
        ])->assignRole([
            $employeeRole,
            $managerRole,
            $timeclockUserRole,
        ]);
        User::create([
            'name' => 'Employee',
            'email' => 'employee@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'pin' => '1234',
            'remember_token' => Str::random(10),
        ])->assignRole([
            $employeeRole,
            $timeclockUserRole,
        ]);
        User::create([
            'name' => 'Unprivileged User',
            'email' => 'unprivileged@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'pin' => '1234',
            'remember_token' => Str::random(10),
        ]);

    }
}
