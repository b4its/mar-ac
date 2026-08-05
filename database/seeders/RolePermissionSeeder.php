<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'kelola master data',
            'kelola pengguna',
            'input laporan perawatan',
            'input laporan kerusakan',
            'input laporan perbaikan',
            'verifikasi laporan',
            'approve laporan',
            'kelola jadwal',
            'lihat dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($permissions);

        $teknisi = Role::firstOrCreate(['name' => 'teknisi']);
        $teknisi->syncPermissions([
            'input laporan perawatan',
            'input laporan kerusakan',
            'input laporan perbaikan',
        ]);
    }
}
