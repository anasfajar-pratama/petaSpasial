<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            KategoriSeeder::class,
            WilayahSeeder::class,
            LayerSeeder::class,
            InformasiSeeder::class,
        ]);

        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@peta-spasial.test',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('Administrator');

        $operator = User::create([
            'name' => 'Operator',
            'email' => 'operator@peta-spasial.test',
            'password' => bcrypt('password'),
        ]);
        $operator->assignRole('Operator');

        $viewer = User::create([
            'name' => 'Viewer',
            'email' => 'viewer@peta-spasial.test',
            'password' => bcrypt('password'),
        ]);
        $viewer->assignRole('Viewer');
    }
}
