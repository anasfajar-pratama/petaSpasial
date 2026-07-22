<?php

namespace Database\Seeders;

use App\Models\Layer;
use App\Models\KategoriLayer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LayerSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('Administrator')->first();
        if (!$admin) return;

        $kategori = KategoriLayer::pluck('id', 'nama');

        $layers = [
            ['nama' => 'Jalan', 'kategori' => 'Infrastruktur', 'geom_type' => 'LineString', 'warna' => '#e74c3c', 'order' => 1],
            ['nama' => 'Jembatan', 'kategori' => 'Infrastruktur', 'geom_type' => 'LineString', 'warna' => '#c0392b', 'order' => 2],
            ['nama' => 'Drainase', 'kategori' => 'Infrastruktur', 'geom_type' => 'LineString', 'warna' => '#95a5a6', 'order' => 3],
            ['nama' => 'Gedung Kantor', 'kategori' => 'Bangunan', 'geom_type' => 'Point', 'warna' => '#3498db', 'order' => 4],
            ['nama' => 'Sekolah', 'kategori' => 'Bangunan', 'geom_type' => 'Point', 'warna' => '#2980b9', 'order' => 5],
            ['nama' => 'Rumah Sakit', 'kategori' => 'Bangunan', 'geom_type' => 'Point', 'warna' => '#9b59b6', 'order' => 6],
            ['nama' => 'Tempat Ibadah', 'kategori' => 'Fasilitas Umum', 'geom_type' => 'Point', 'warna' => '#f39c12', 'order' => 7],
            ['nama' => 'Pasar', 'kategori' => 'Fasilitas Umum', 'geom_type' => 'Point', 'warna' => '#e67e22', 'order' => 8],
            ['nama' => 'Taman', 'kategori' => 'Fasilitas Umum', 'geom_type' => 'Polygon', 'warna' => '#2ecc71', 'order' => 9],
            ['nama' => 'Sungai', 'kategori' => 'Perairan', 'geom_type' => 'LineString', 'warna' => '#2980b9', 'order' => 10],
            ['nama' => 'Danau/Waduk', 'kategori' => 'Perairan', 'geom_type' => 'Polygon', 'warna' => '#1abc9c', 'order' => 11],
            ['nama' => 'Sawah', 'kategori' => 'Lahan', 'geom_type' => 'Polygon', 'warna' => '#27ae60', 'order' => 12],
            ['nama' => 'Perkebunan', 'kategori' => 'Lahan', 'geom_type' => 'Polygon', 'warna' => '#229954', 'order' => 13],
            ['nama' => 'Hutan', 'kategori' => 'Lahan', 'geom_type' => 'Polygon', 'warna' => '#1e8449', 'order' => 14],
            ['nama' => 'Batas Kecamatan', 'kategori' => 'Batas Administrasi', 'geom_type' => 'LineString', 'warna' => '#e67e22', 'order' => 15],
            ['nama' => 'Batas Desa', 'kategori' => 'Batas Administrasi', 'geom_type' => 'LineString', 'warna' => '#d35400', 'order' => 16],
            ['nama' => 'Lokasi Proyek', 'kategori' => 'Perizinan', 'geom_type' => 'Point', 'warna' => '#8e44ad', 'order' => 17],
            ['nama' => 'Perizinan Usaha', 'kategori' => 'Perizinan', 'geom_type' => 'Point', 'warna' => '#7d3c98', 'order' => 18],
        ];

        foreach ($layers as $item) {
            $katId = $kategori[$item['kategori']] ?? null;
            if (!$katId) continue;

            Layer::create([
                'nama' => $item['nama'],
                'slug' => Str::slug($item['nama']) . '-' . Str::random(4),
                'kategori_id' => $katId,
                'geom_type' => $item['geom_type'],
                'warna' => $item['warna'],
                'order' => $item['order'],
                'created_by' => $admin->id,
                'is_active' => true,
                'opacity' => 1.00,
            ]);
        }
    }
}
