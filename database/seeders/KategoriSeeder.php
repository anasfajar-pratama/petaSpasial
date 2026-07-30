<?php

namespace Database\Seeders;

use App\Models\KategoriLayer;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['nama' => 'Infrastruktur', 'deskripsi' => 'Jalan, jembatan, drainase, dll', 'icon' => 'road', 'warna_default' => '#e74c3c'],
            ['nama' => 'Perizinan', 'deskripsi' => 'Lokasi perizinan usaha, proyek', 'icon' => 'file', 'warna_default' => '#9b59b6'],
            ['nama' => 'Batas Administrasi', 'deskripsi' => 'Batas kecamatan, kelurahan', 'icon' => 'boundary', 'warna_default' => '#e67e22'],
            ['nama' => 'Toponimi', 'deskripsi' => 'Nama tempat dan fasilitas umum', 'icon' => 'tag', 'warna_default' => '#8b5cf6'],
            ['nama' => 'Angkutan', 'deskripsi' => 'Transportasi dan angkutan umum', 'icon' => 'bus', 'warna_default' => '#ff9800'],
            ['nama' => 'Permukiman', 'deskripsi' => 'Permukiman dan perumahan', 'icon' => 'home', 'warna_default' => '#e91e63'],
            ['nama' => 'Kesehatan', 'deskripsi' => 'Fasilitas dan data kesehatan', 'icon' => 'heart', 'warna_default' => '#00bcd4'],
            ['nama' => 'Tata Ruang', 'deskripsi' => 'Perencanaan tata ruang', 'icon' => 'map', 'warna_default' => '#4caf50'],
            ['nama' => 'Sosial', 'deskripsi' => 'Data sosial ekonomi', 'icon' => 'users', 'warna_default' => '#795548'],
        ];

        foreach ($kategori as $item) {
            KategoriLayer::create($item);
        }
    }
}
