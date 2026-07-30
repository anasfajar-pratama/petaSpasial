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
            ['nama' => 'Histori Bencana', 'kategori' => 'Infrastruktur', 'geom_type' => 'Point', 'warna' => '#f59e0b', 'order' => 2],
            ['nama' => 'IMB', 'kategori' => 'Perizinan', 'geom_type' => 'Point', 'warna' => '#3b82f6', 'order' => 3],
            ['nama' => 'Batas Administrasi', 'kategori' => 'Batas Administrasi', 'geom_type' => 'Polygon', 'warna' => '#dc2626', 'order' => 4],
            ['nama' => 'Batas Administrasi Garis', 'kategori' => 'Batas Administrasi', 'geom_type' => 'LineString', 'warna' => '#dc2626', 'order' => 5],
            ['nama' => 'Toponimi', 'kategori' => 'Toponimi', 'geom_type' => 'Point', 'warna' => '#8b5cf6', 'order' => 6],
            ['nama' => 'Trayek Angkot', 'kategori' => 'Angkutan', 'geom_type' => 'LineString', 'warna' => '#ff9800', 'order' => 7],
            ['nama' => 'Kumuh', 'kategori' => 'Permukiman', 'geom_type' => 'Polygon', 'warna' => '#e91e63', 'order' => 8],
            ['nama' => 'Stunting', 'kategori' => 'Kesehatan', 'geom_type' => 'Point', 'warna' => '#00bcd4', 'order' => 9],
            ['nama' => 'Kemiskinan GP Cikole', 'kategori' => 'Sosial', 'geom_type' => 'Point', 'warna' => '#795548', 'order' => 10],
            ['nama' => 'Kemiskinan Warudoyong', 'kategori' => 'Sosial', 'geom_type' => 'Point', 'warna' => '#8d6e63', 'order' => 11],
            ['nama' => 'Penetapan Kawasan Strategis', 'kategori' => 'Tata Ruang', 'geom_type' => 'Polygon', 'warna' => '#4caf50', 'order' => 12],
            ['nama' => 'Rencana Pola Ruang', 'kategori' => 'Tata Ruang', 'geom_type' => 'Polygon', 'warna' => '#388e3c', 'order' => 13],
            ['nama' => 'Rencana Sistem Jaringan Energi', 'kategori' => 'Tata Ruang', 'geom_type' => 'LineString', 'warna' => '#ffc107', 'order' => 14],
            ['nama' => 'Sistem Infrastruktur Energi', 'kategori' => 'Tata Ruang', 'geom_type' => 'Point', 'warna' => '#ff9800', 'order' => 15],
            ['nama' => 'Sistem Infrastruktur Perkotaan', 'kategori' => 'Tata Ruang', 'geom_type' => 'Point', 'warna' => '#9c27b0', 'order' => 16],
            ['nama' => 'Sistem Infrastruktur Sumber Daya Air', 'kategori' => 'Tata Ruang', 'geom_type' => 'Point', 'warna' => '#2196f3', 'order' => 17],
            ['nama' => 'Sistem Infrastruktur Telekomunikasi', 'kategori' => 'Tata Ruang', 'geom_type' => 'Point', 'warna' => '#607d8b', 'order' => 18],
            ['nama' => 'Sistem Infrastruktur Transportasi', 'kategori' => 'Tata Ruang', 'geom_type' => 'Point', 'warna' => '#795548', 'order' => 19],
            ['nama' => 'Sistem Jaringan Infrastruktur Perkotaan', 'kategori' => 'Tata Ruang', 'geom_type' => 'LineString', 'warna' => '#673ab7', 'order' => 20],
            ['nama' => 'Sistem Jaringan Sumber Daya Air', 'kategori' => 'Tata Ruang', 'geom_type' => 'LineString', 'warna' => '#03a9f4', 'order' => 21],
            ['nama' => 'Sistem Jaringan Telekomunikasi', 'kategori' => 'Tata Ruang', 'geom_type' => 'LineString', 'warna' => '#009688', 'order' => 22],
            ['nama' => 'Sistem Jaringan Transportasi', 'kategori' => 'Tata Ruang', 'geom_type' => 'LineString', 'warna' => '#8bc34a', 'order' => 23],
            ['nama' => 'Sistem Pusat Pelayanan', 'kategori' => 'Tata Ruang', 'geom_type' => 'Point', 'warna' => '#ff5722', 'order' => 24],
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
