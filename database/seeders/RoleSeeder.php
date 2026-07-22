<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage-users',
            'manage-roles',
            'manage-layers',
            'manage-kategori',
            'manage-wilayah',
            'import-data',
            'export-data',
            'view-laporan',
            'cetak-pdf',
            'manage-pengaturan',
            'digitasi-peta',
        ];

        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm);
        }

        $adminRole = Role::findOrCreate('Administrator');
        $adminRole->syncPermissions(Permission::all());

        $operatorRole = Role::findOrCreate('Operator');
        $operatorRole->syncPermissions([
            'manage-layers',
            'manage-kategori',
            'import-data',
            'export-data',
            'digitasi-peta',
            'cetak-pdf',
        ]);

        $viewerRole = Role::findOrCreate('Viewer');
        $viewerRole->syncPermissions([
            'export-data',
            'cetak-pdf',
        ]);
    }
}
