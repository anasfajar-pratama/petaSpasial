# TIMELINE PENGERJAAN — petaSpasial

> Estimasi pengerjaan: **34 hari kerja**  
> Dimulai: sesuai kesepakatan  
> Teknologi: Laravel 11 + PostGIS + Leaflet.js

---

## Ringkasan Fase

| Fase | Modul | Hari | Status |
|------|-------|:----:|:------:|
| **1** | Foundation: Setup Proyek & Database | 3 | ✅ |
| **2** | Auth & Role Management | 3 | ✅ |
| **3** | Master Data (User, Role, Kategori, Wilayah) | 3 | ✅ |
| **4** | Manajemen Layer | 3 | ✅ |
| **5** | Halaman Peta Interaktif (Leaflet) | 5 | ✅ |
| **6** | Digitasi Peta (CRUD Spasial) | 4 | ✅ |
| **7** | Import Data (SHP/GeoJSON/KML/CSV) | 4 | ✅ |
| **8** | Export Data (GeoJSON/SHP/CSV/PDF/Excel) | 2 | ✅ |
| **9** | Dashboard Statistik | 2 | ✅ |
| **10** | Pencarian & Filter | 2 | ✅ |
| **11** | Laporan & Cetak PDF | 2 | ✅ |
| **12** | Pengaturan, Testing, & Finalisasi | 1 | ⬜ |
| | **Total** | **34 hari** | |

> Note: Estimasi sudah termasuk buffer. Progress akan diupdate setiap hari.

---

## Detail Tahapan

---

### FASE 1: Foundation — Setup Proyek & Database (Hari 1–3)

#### Hari 1: Setup Lingkungan
- [x] Install Laravel 11 via Composer
- [x] Konfigurasi `.env` (database, app name, dll)
- [x] Install Laravel Breeze (Blade + Tailwind)
- [x] Install Spatie Laravel-permission
- [x] Install package pendukung (DomPDF, CSV)
- [x] Setup Vite + NPM dependencies (Leaflet, Alpine, Chart.js, dll)
- [x] Konfigurasi Tailwind CSS
- [x] Tambah `ACTIVE_REGENCY_ID=1` di `.env` (untuk region config)
- [ ] Test: `php artisan serve` — halaman welcome muncul

#### Hari 2: Database PostGIS
- [x] Install PostgreSQL (jika belum ada) — sudah terinstall 17
- [x] Buat database `peta_spasial`
- [x] Enable ekstensi PostGIS 3.6.2
- [x] Test koneksi Laravel ke PostgreSQL
- [x] Buat migration: users (default Breeze)
- [x] Buat migration: tabel roles + permissions (Spatie)
- [x] Test: migrate sukses ✅

#### Hari 3: Struktur Database Inti
- [x] Migration users, cache, jobs (bawaan Laravel) ✅
- [x] Migration permission_tables (Spatie) ✅
- [ ] Migration: `kategori_layers` *(dibuat saat Fase 3)*
- [ ] Migration: `layers` *(dibuat saat Fase 3)*
- [ ] Migration: `regencies`, `districts`, `villages` *(dibuat saat Fase 3)*
- [ ] Migration: `data_spasial` dengan geometry PostGIS *(dibuat saat Fase 3)*
- [ ] Setup Model relationships
- [ ] Setup GIST spatial index

**Milestone Fase 1 ✅** — Proyek Laravel siap, PostGIS terhubung, struktur database terbentuk.

---

### FASE 2: Auth & Role Management (Hari 4–6)

#### Hari 4: Laravel Breeze
- [x] Install & configure Breeze (Blade stack)
- [x] Setup login/logout
- [x] Setup register (opsional, bisa dimatikan)
- [x] Setup password reset via email
- [x] Test: bisa login/logout ✅
- [x] Layout admin: sidebar (dark) + topbar + content area ✅

#### Hari 5: Role & Permission (Spatie)
- [x] Seeder: buat roles (Administrator, Operator, Viewer)
- [x] Seeder: buat 11 permissions (manage-users, manage-layers, import-data, dll)
- [x] Assign permissions ke roles sesuai matrix
- [x] Register Spatie middleware (`role`, `permission`) di bootstrap/app.php
- [x] Route group admin dengan middleware `role:Administrator`
- [x] Grup terpisah untuk `role:Administrator|Operator`

#### Hari 6: Gate & Policy
- [x] Sidebar menu dinamis: @role menyesuaikan tampilan menu
- [x] Admin bisa akses semua menu (12 route admin)
- [x] Operator bisa akses: Data Spasial, Import, Export, Peta, Statistik
- [x] Viewer hanya bisa: Dashboard, Peta, Statistik
- [ ] Buat Gate untuk aksi spesifik (edit data, hapus data, dll) — menyusul

**Milestone Fase 2 ✅** — Auth lengkap, role & permission berfungsi, layout siap.

---

### FASE 3: Master Data (Hari 7–9)

#### Hari 7: Manajemen User
- [x] Controller: `Admin\UserController`
- [x] View: index user (tabel + search)
- [x] View: form create user
- [x] View: form edit user
- [x] Assign role ke user (checkbox/dropdown)
- [x] Fitur aktif/nonaktif user

#### Hari 8: Manajemen Role & Permission
- [x] Controller: `Admin\RoleController`
- [x] View: index roles
- [x] View: create/edit role dengan permission checklist
- [x] Validasi: tidak bisa hapus role yang masih dipakai

#### Hari 9: Kategori Layer & Wilayah
- [x] Controller: `Admin\KategoriController`
- [x] CRUD kategori (nama, deskripsi, ikon, warna default)
- [x] Validation rules
- [x] Controller: `Admin\WilayahController`
- [x] CRUD sederhana Regencies (nama, kode)
- [x] CRUD Kecamatan (filter by regency aktif)
- [x] CRUD Kelurahan (filter by kecamatan)
- [x] Seeder: data kecamatan + kelurahan Sukabumi
- [ ] Setup Global Scope untuk filter regency aktif — *menyusul*

**Milestone Fase 3 ✅** — Master data lengkap: user, role, kategori, wilayah (scope Sukabumi).

---

### FASE 4: Manajemen Layer (Hari 10–12)

#### Hari 10: CRUD Layer
- [x] Migration & Model Layer ✅
- [x] Relasi layer → kategori ✅
- [x] Controller: `Admin\LayerController` ✅
- [x] Index layer dengan filtering by kategori ✅
- [x] Form create: nama, kategori, tipe geometri, warna, ikon ✅
- [x] Form edit: semua field ✅
- [x] Soft delete layer ✅

#### Hari 11: Fitur Layer
- [x] Toggle aktif/nonaktif ✅
- [x] Atur urutan layer (input number di form) ✅
- [x] Atur opacity layer (input number di form) ✅

#### Hari 12: Integrasi Layer
- [x] Layer seeder: 18 layer contoh (Jalan, Sungai, Bangunan, dll) ✅
- [x] Relasi layer → kategori ✅
- [x] Relasi layer → data spasial ✅
- [ ] Validasi: tipe geometri layer harus sesuai dengan data yang diinput — *menyusul*

**Milestone Fase 4 ✅** — Layer management siap digunakan.

---

### FASE 5: Halaman Peta Interaktif (Hari 13–17)

#### Hari 13: Layout Peta
- [x] Integrasi Leaflet.js via npm + Vite ✅
- [x] Halaman `/map` — fullscreen layout (tanpa sidebar) ✅
- [x] Floating panel kiri (layer list + basemap switcher) ✅
- [x] Bottom bar (koordinat, zoom level) ✅
- [x] Basemap: OpenStreetMap layer ✅
- [x] Basemap switcher: Street / Satellite / Dark ✅

#### Hari 14: API GeoJSON
- [x] Controller: `MapController` ✅
- [x] Route: `GET /api/map/layers` — daftar layer aktif ✅
- [x] Route: `GET /api/map/{layer}/data` — GeoJSON data spasial ✅
- [x] Route: `GET /api/map/data?bbox=...` — bounding box filter ✅
- [x] Load data ke Leaflet via AJAX ✅

#### Hari 15: Fitur Peta Dasar
- [x] Zoom controls ✅
- [x] Fullscreen toggle ✅
- [x] Scale bar ✅
- [x] Mouse coordinate display ✅
- [x] Popup informasi saat klik objek ✅
- [x] Layer control (show/hide layer + warna) via floating panel ✅

#### Hari 16: Legend & Styling
- [x] Legend dinamis berdasarkan layer aktif (icon geometri + warna per layer)
- [x] Styling per layer:
  - Point: circle marker dengan warna layer
  - Line: warna + weight sesuai setting layer
  - Polygon: fill color + opacity sesuai setting layer
- [x] Fitur zoom to layer / zoom to all data (tombol magnify + Zoom All)

#### Hari 17: Basemap & Performa
- [x] Basemap: OpenStreetMap, Esri Satellite, CartoDB Dark
- [x] Implementasi marker cluster (untuk point)
- [x] Lazy load data per bounding box (fetch ulang otomatis saat peta digeser)
- [ ] Cache GeoJSON response (Redis optional)
- [ ] Test performa dengan 10.000+ data

**Milestone Fase 5 ✅** — Peta interaktif berfungsi penuh dengan semua fitur dasar.

---

### FASE 6: Digitasi Peta — CRUD Spasial (Hari 18–21) ✅

#### Hari 18: Leaflet Draw
- [x] Install & konfigurasi Leaflet.draw ✅
- [x] Toolbar digitasi (Point, LineString, Polygon) ✅
- [x] Context: hanya muncul untuk role Admin/Operator ✅
- [x] Pilih layer tujuan sebelum digitasi ✅

#### Hari 19: Simpan Digitasi
- [x] Controller: `POST /api/map/digitasi` ✅
- [x] Validasi tipe geometri sesuai layer ✅
- [x] Insert geometri + atribut ke `data_spasial` ✅
- [x] Form atribut setelah digitasi (modal) ✅
- [x] Upload foto langsung saat digitasi ✅

#### Hari 20: Edit & Delete Geometri
- [x] PUT/DELETE API endpoints ✅
- [x] Edit atribut via popup (Edit/Hapus buttons di popup) ✅
- [ ] Edit geometri drag vertex — *menyusul (butuh Leaflet.Editable)*

#### Hari 21: Form Manual & Validasi
- [x] Form tambah data spasial manual (input koordinat) ✅
- [x] Form edit data spasial (non-peta) ✅
- [x] Halaman index data spasial (tabel) ✅
- [x] Filter data spasial per layer di tabel ✅
- [x] Bulk delete (checkbox) ✅

**Milestone Fase 6 ✅** — CRUD data spasial lengkap via peta maupun form.

---

### FASE 7: Import Data (Hari 22–25) ✅

#### Hari 22: Import GeoJSON
- [x] Controller: `ImportController`
- [x] Halaman import dengan pilihan format
- [x] Upload & parse GeoJSON
- [x] Validasi: file size, format, tipe geometri
- [x] Insert ke `data_spasial`
- [x] Report: jumlah sukses, gagal, duplikat

#### Hari 23: Import SHP via GDAL
- [x] Install GDAL (jika belum ada)
- [x] Test `ogr2ogr` via command line
- [x] Upload zip (shp, shx, dbf, prj)
- [x] Extract → konversi via `ogr2ogr` → insert ke DB
- [x] Mapping kolom otomatis
- [ ] Preview data sebelum import (opsional)

#### Hari 24: Import KML & CSV
- [x] Import KML via GDAL
- [x] Import CSV (lat/lon) — parse & insert sebagai Point
- [x] Column mapping untuk CSV (pilih kolom lat, lon, nama, dll)
- [x] Validasi koordinat (latitude: -90 s/d 90, longitude: -180 s/d 180)

#### Hari 25: Improve Import
- [ ] Progress bar upload (AJAX)
- [x] Error handling & user feedback
- [x] Log import history (tabel import_logs)
- [x] Duplicate detection (by nama + layer + ST_Equals geometry)

**Milestone Fase 7 ✅** — Import data dari berbagai format berfungsi.

---

### FASE 8: Export Data (Hari 26–27) ✅

#### Hari 26: Export GeoJSON, SHP, CSV
- [x] Export GeoJSON (download file)
- [x] Export SHP (via GDAL ogr2ogr)
- [x] Export CSV (attribut + koordinat)
- [x] Filter data sebelum export (by layer, kategori, wilayah)

#### Hari 27: Export PDF & Excel
- [x] Export PDF (tabel atribut via DomPDF)
- [x] Export Excel (Laravel Excel) — tabel + header
- [x] Halaman export dengan preview & konfigurasi

**Milestone Fase 8 ✅** — Export multi-format berfungsi.

---

### FASE 9: Dashboard Statistik (Hari 28–29) ✅

#### Hari 28: Cards & Data
- [x] Card: total layer
- [x] Card: total objek spasial (point, line, polygon)
- [x] Card: total wilayah
- [x] Card: total kategori
- [x] Query statistik dari database

#### Hari 29: Grafik Chart.js
- [x] Bar chart: objek per layer
- [x] Pie chart: distribusi per kategori
- [x] Bar chart: objek per kecamatan
- [x] Line chart: tren data per tahun
- [x] Mini map di dashboard (Leaflet interaktif, klik → buka halaman peta)
- [x] Filter statistik (by tahun, by wilayah) — dropdown + reset

**Milestone Fase 9 ✅** — Dashboard dengan grafik interaktif.

---

### FASE 10: Pencarian & Filter (Hari 30–31)

#### Hari 30: Search
- [x] Search box di halaman peta (autocomplete)
- [x] Search by nama (ILIKE)
- [x] Search by kategori (dropdown)
- [x] Search by status
- [x] Highlight & zoom ke hasil pencarian

#### Hari 31: Filter & Buffer
- [x] Filter by layer (checkbox)
- [x] Filter by wilayah (kecamatan/kelurahan)
- [x] Filter by radius (buffer):
  - Klik titik di peta → input radius meter
  - API: `ST_DWithin` query
  - Tampilkan hasil dalam radius
- [x] Clear all filters

**Milestone Fase 10 ✅** — Pencarian & filter spasial berfungsi.

---

### FASE 11: Laporan & Cetak PDF (Hari 32–33)

#### Hari 32: Halaman Laporan
- [x] Rekap data per layer (tabel + total)
- [x] Rekap data per wilayah
- [x] Rekap data per kategori
- [x] Filter laporan (by tanggal, by layer, by wilayah)

#### Hari 33: Cetak
- [x] Cetak PDF laporan (DomPDF)
- [x] Template PDF: kop surat, tabel, footer
- [ ] Peta statis di PDF (screenshot atau canvas to image) — *menyusul*
- [x] Export Excel laporan

**Milestone Fase 11 ✅** — Laporan & cetak siap.

---

### FASE 12: Pengaturan, Testing, Finalisasi (Hari 34)

#### Hari 34: Finalisasi
- [ ] Halaman pengaturan umum (nama app, logo, favicon)
- [ ] Pengaturan peta (default center: lat/lng, default zoom)
- [ ] Unit test untuk model & service (PHPUnit)
- [ ] Test semua CRUD, import/export, role/permissions
- [ ] Responsive design check
- [ ] Bug fixing
- [ ] Dokumentasi penggunaan (readme)

**Milestone Fase 12 ✅** — Aplikasi siap digunakan!

---

## Dependency Antar Fase

```
Fase 1 ──┬── Fase 2 ──┬── Fase 3 ──┬── Fase 4 ──┬── Fase 5 ──┬── Fase 6 ──┬── Fase 8
          │            │             │             │             │            │
          │            │             │             │             │            └── Fase 11
          │            │             │             │             │
          │            │             │             │             └── Fase 7 ──┐
          │            │             │             │                          │
          │            │             │             └── Fase 10 ───────────────┼── Fase 8
          │            │             │                                        │
          │            │             └── Fase 9 ──────────────────────────────┴── Fase 11
          │            │
          │            └── Fase 12 (paling akhir — gabung pengaturan + testing)
          │
          └── Semua fase
```

## Legend Status

| Status | Arti |
|--------|------|
| ⬜ | Belum dimulai |
| 🔄 | Sedang dikerjakan |
| ✅ | Selesai |
| ❌ | Terkendala / perlu revisi |

---

> Timeline ini bersifat dinamis dan dapat disesuaikan dengan kebutuhan & ketersediaan waktu.
