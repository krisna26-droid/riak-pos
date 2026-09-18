<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Buat Role
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $adminRole      = Role::firstOrCreate(['name' => 'admin']);
        $cashierRole    = Role::firstOrCreate(['name' => 'cashier']);

        // 2. Buat Hak Akses (Permissions)
        $permissions = [
            'view-reports',
            'manage-products',
            'manage-users',
            'process-pos',
            'void-transactions',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 3. Sinkronisasi Izin ke Peran
        $cashierRole->syncPermissions(['process-pos']);
        $adminRole->syncPermissions(['process-pos', 'manage-products', 'view-reports', 'void-transactions']);
        $superAdminRole->syncPermissions(Permission::all());

        // 4. Buat Akun Default untuk Setiap Level

        // Super Admin (Owner)
        $superAdmin = User::updateOrCreate(
            ['email' => 'owner@riakcoffee.test'],
            [
                'name' => 'Owner Riak',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->syncRoles(['super-admin']);

        // Admin (Manager)
        $admin = User::updateOrCreate(
            ['email' => 'admin@riakcoffee.test'],
            [
                'name' => 'Manager Riak',
                'password' => Hash::make('password'),
            ]
        );
        $admin->syncRoles(['admin']);

        // Kasir
        $cashier = User::updateOrCreate(
            ['email' => 'kasir@riakcoffee.test'],
            [
                'name' => 'Kasir 1',
                'password' => Hash::make('password'),
            ]
        );
        $cashier->syncRoles(['cashier']);
    }
}