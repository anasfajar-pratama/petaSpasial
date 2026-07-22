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
            ['nama' => 'Bangunan', 'deskripsi' => 'Gedung, kantor, sekolah, rumah sakit', 'icon' => 'building', 'warna_default' => '#3498db'],
            ['nama' => 'Perairan', 'deskripsi' => 'Sungai, danau, waduk', 'icon' => 'water', 'warna_default' => '#2980b9'],
            ['nama' => 'Lahan', 'deskripsi' => 'Sawah, perkebunan, hutan', 'icon' => 'tree', 'warna_default' => '#27ae60'],
            ['nama' => 'Fasilitas Umum', 'deskripsi' => 'Pasar, taman, tempat ibadah', 'icon' => 'marker', 'warna_default' => '#f39c12'],
            ['nama' => 'Perizinan', 'deskripsi' => 'Lokasi perizinan usaha, proyek', 'icon' => 'file', 'warna_default' => '#9b59b6'],
            ['nama' => 'Batas Administrasi', 'deskripsi' => 'Batas kecamatan, kelurahan', 'icon' => 'boundary', 'warna_default' => '#e67e22'],
        ];

        foreach ($kategori as $item) {
            KategoriLayer::create($item);
        }
    }
}
