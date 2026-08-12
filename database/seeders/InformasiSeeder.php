<?php

namespace Database\Seeders;

use App\Models\Informasi;
use Illuminate\Database\Seeder;

class InformasiSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // ===== BERITA =====
            ['tipe' => 'berita', 'urutan' => 1, 'tanggal' => '2026-07-25', 'judul' => 'Peluncuran Sistem Informasi Geospasial Kota Sukabumi', 'isi' => 'Pemerintah Kota Sukabumi resmi meluncurkan portal data spasial terintegrasi untuk mendukung perencanaan pembangunan berbasis data. Sistem ini menyediakan akses publik ke berbagai data spasial termasuk batas administrasi, infrastruktur, dan tata ruang.'],
            ['tipe' => 'berita', 'urutan' => 2, 'tanggal' => '2026-07-18', 'judul' => 'Pembaruan Data RTRW Kota Sukabumi 2022-2042', 'isi' => 'Data Rencana Tata Ruang Wilayah (RTRW) Kota Sukabumi periode 2022-2042 telah diperbarui dan dapat diakses melalui platform ini. Masyarakat dapat melihat rencana pola ruang, jaringan infrastruktur, dan kawasan strategis.'],
            ['tipe' => 'berita', 'urutan' => 3, 'tanggal' => '2026-07-10', 'judul' => 'Bimtek Penggunaan Portal Spasial bagi OPD', 'isi' => 'Dinas Komunikasi dan Informatika Kota Sukabumi menyelenggarakan bimbingan teknis penggunaan portal data spasial bagi seluruh Organisasi Perangkat Daerah (OPD) untuk mendukung digitalisasi perencanaan daerah.'],

            // ===== INFOGRAFIS =====
            ['tipe' => 'infografis', 'urutan' => 1, 'judul' => 'Distribusi Objek Spasial per Kategori', 'isi' => 'Grafik batang distribusi objek spasial berdasarkan kategori layer.'],
            ['tipe' => 'infografis', 'urutan' => 2, 'judul' => 'Sebaran Data per Kecamatan', 'isi' => 'Visualisasi sebaran data spasial di 7 kecamatan Kota Sukabumi.'],
            ['tipe' => 'infografis', 'urutan' => 3, 'judul' => 'Komposisi Jenis Geometri', 'isi' => 'Proporsi data Point, Line, dan Polygon dalam sistem.'],
            ['tipe' => 'infografis', 'urutan' => 4, 'judul' => 'Cakupan Data per Tahun', 'isi' => 'Perkembangan pengumpulan data spasial dari tahun ke tahun.'],

            // ===== PANDUAN TEKNIS =====
            ['tipe' => 'panduan', 'urutan' => 1, 'judul' => 'Navigasi Peta', 'isi' => 'Gunakan scroll untuk zoom in/out, drag untuk menggeser peta. Klik pada objek untuk melihat informasi detail. Gunakan kotak pencarian untuk mencari lokasi atau alamat tertentu.'],
            ['tipe' => 'panduan', 'urutan' => 2, 'judul' => 'Mengelola Layer', 'isi' => 'Panel layer di sisi kanan peta menampilkan seluruh layer yang tersedia. Centang layer untuk menampilkan atau menyembunyikannya. Urutan layer dapat diubah sesuai kebutuhan.'],
            ['tipe' => 'panduan', 'urutan' => 3, 'judul' => 'Menggambar dan Digitasi', 'isi' => 'Fitur digitasi memungkinkan pengguna untuk menambahkan titik, garis, atau poligon langsung di atas peta. Gunakan alat gambar yang tersedia di toolbar untuk membuat objek spasial baru.'],
            ['tipe' => 'panduan', 'urutan' => 4, 'judul' => 'Import dan Export Data', 'isi' => 'Data spasial dapat diimport dalam format SHP (Shapefile), GeoJSON, CSV, dan KML. Export data tersedia dalam format SHP, GeoJSON, dan Excel. Pastikan file SHP memiliki ekstensi .shp, .shx, dan .dbf yang lengkap.'],
            ['tipe' => 'panduan', 'urutan' => 5, 'judul' => 'Analisis Spasial', 'isi' => 'Fitur analisis meliputi buffer (zona penyangga), pengukuran jarak dan luas, serta pencarian berdasarkan radius. Hasil analisis dapat ditampilkan langsung di peta dan diexport untuk laporan.'],

            // ===== RISET & PUBLIKASI =====
            ['tipe' => 'riset', 'urutan' => 1, 'jenis' => 'Jurnal', 'tahun' => 2025, 'penulis' => 'Tim Riset BIG', 'judul' => 'Analisis Spasial Kerentanan Bencana di Kota Sukabumi', 'isi' => 'Penelitian ini mengintegrasikan data historis bencana dengan data infrastruktur untuk memetakan tingkat kerentanan wilayah terhadap berbagai jenis bencana alam di Kota Sukabumi.'],
            ['tipe' => 'riset', 'urutan' => 2, 'jenis' => 'Laporan Teknis', 'tahun' => 2025, 'penulis' => 'Dinas Perumahan Kota Sukabumi', 'judul' => 'Pemetaan Kawasan Kumuh Terintegrasi SIG', 'isi' => 'Laporan teknis pemetaan kawasan kumuh di 7 kecamatan menggunakan sistem informasi geografis, mencakup identifikasi luasan, tingkat kekumuhan, dan rekomendasi penanganan.'],
            ['tipe' => 'riset', 'urutan' => 3, 'jenis' => 'Prosiding', 'tahun' => 2024, 'penulis' => 'Kelompok Riset Transportasi UNSUK', 'judul' => 'Optimasi Jaringan Transportasi Umum Berbasis Data Spasial', 'isi' => 'Kajian rute trayek angkutan umum di Kota Sukabumi menggunakan analisis jaringan dan data sebaran permukiman penduduk untuk optimalisasi pelayanan transportasi publik.'],
            ['tipe' => 'riset', 'urutan' => 4, 'jenis' => 'Buku', 'tahun' => 2022, 'penulis' => 'Bappeda Kota Sukabumi', 'judul' => 'Rencana Tata Ruang Wilayah Kota Sukabumi 2022-2042', 'isi' => 'Dokumen perencanaan tata ruang yang memuat arahan struktur ruang, pola ruang, kawasan strategis, dan peraturan zonasi untuk pembangunan Kota Sukabumi.'],
        ];

        foreach ($items as $item) {
            Informasi::updateOrCreate(
                ['tipe' => $item['tipe'], 'judul' => $item['judul']],
                $item + ['is_active' => true]
            );
        }
    }
}
