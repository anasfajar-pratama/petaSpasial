# SPESIFIKASI TEKNIS — petaSpasial

> WebGIS Aplikasi Peta Spasial Interaktif  
> Versi: 1.0  
> Status: Draft

---

## 1. Gambaran Umum

| Item | Detail |
|------|--------|
| Nama Aplikasi | **petaSpasial** |
| Tujuan | Aplikasi WebGIS untuk menyimpan, mengelola, menganalisis, dan menampilkan data spasial dalam bentuk peta interaktif |
| Target Pengguna | Pemerintah, perusahaan, organisasi — untuk melihat persebaran data berdasarkan lokasi |
| Contoh Kasus | Aset perusahaan, infrastruktur jalan, bangunan, lahan, perizinan, proyek, batas administrasi, persebaran pelanggan, titik bencana |

---

## 2. Technology Stack

### 2.1 Backend
| Komponen | Teknologi |
|----------|-----------|
| Framework | **Laravel 11** |
| PHP | **PHP 8.2+** |
| Package Auth | **Laravel Breeze** (Blade + Tailwind) |
| Role & Permission | **Spatie Laravel-permission** |
| Package GIS | **Laravel Excel** (export), **Barryvdh DomPDF** (cetak), **League/Csv** |
| Package Tambahan | **Intervention Image** (foto), **Spatie Media Library** (dokumen) |

### 2.2 Frontend
| Komponen | Teknologi |
|----------|-----------|
| Template Engine | **Blade** |
| CSS Framework | **Tailwind CSS 3.x** |
| Map Library | **Leaflet.js 1.9** |
| Leaflet Plugin | **Leaflet Draw** (digitasi), **Leaflet Search**, **Leaflet Measure**, **Leaflet Cluster**, **Leaflet Fullscreen** |
| Chart | **Chart.js** (dashboard statistik) |
| JavaScript | **Vanilla JS + Alpine.js** (untuk interaktivitas UI) |

### 2.3 Database
| Komponen | Teknologi |
|----------|-----------|
| DBMS | **PostgreSQL 16+** |
| Ekstensi Spasial | **PostGIS 3.4+** |
| Konektor | **Laravel Eloquent + DB::raw / Query Builder** |

### 2.4 Server & Tools
| Komponen | Teknologi |
|----------|-----------|
| Web Server | **Nginx** atau **Apache** |
| Import SHP | **GDAL (ogr2ogr)** |
| Tool GIS Desktop | **QGIS** (opsional) |
| Version Control | **Git** |
| Cache | **Redis** (opsional) |

---

## 3. Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────┐
│                     Browser (Client)                      │
│  ┌─────────────┐  ┌──────────┐  ┌────────────────────┐  │
│  │  Blade UI   │  │ Leaflet  │  │  Alpine.js / JS    │  │
│  │  Tailwind   │  │   Map    │  │  (interaktivitas)  │  │
│  └─────────────┘  └──────────┘  └────────────────────┘  │
└─────────────────────────┬───────────────────────────────┘
                          │ HTTP/HTTPS
                          ▼
┌─────────────────────────────────────────────────────────┐
│                    Laravel 12                             │
│  ┌─────────────┐  ┌──────────┐  ┌────────────────────┐  │
│  │  Routes     │  │  Blade   │  │  Controllers       │  │
│  │  (web/api)  │  │  Views   │  │  (CRUD + GIS)     │  │
│  └─────────────┘  └──────────┘  └────────────────────┘  │
│  ┌─────────────┐  ┌──────────┐  ┌────────────────────┐  │
│  │  Middleware  │  │  Policy  │  │  Spatie Roles     │  │
│  │  (role)     │  │  (Gate)  │  │  (Admin/Opr/Viewer)│  │
│  └─────────────┘  └──────────┘  └────────────────────┘  │
│  ┌─────────────────────────────────────────────────────┐ │
│  │  Services: ImportService, ExportService, GeoService  │ │
│  └─────────────────────────────────────────────────────┘ │
└─────────────────────────┬───────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│              PostgreSQL + PostGIS                         │
│  ┌──────────┐  ┌───────────┐  ┌──────────────────────┐ │
│  │  Tabel   │  │  Tabel    │  │  Tabel Spasial       │ │
│  │  Master  │  │  Relasi   │  │  (geometry(Point,    │ │
│  │  (users, │  │  (layer_  │  │   LineString,        │ │
│  │  roles,  │  │  kategori,│  │   Polygon,           │ │
│  │  wilayah)│  │  dll)     │  │   MultiPolygon))     │ │
│  └──────────┘  └───────────┘  └──────────────────────┘ │
│  ┌─────────────────────────────────────────────────────┐ │
│  │  Spatial Indexes (GIST)                              │ │
│  └─────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────┘
```

### Alur Data

1. **Input**: User → Browser (Leaflet Draw / Upload SHP/GeoJSON) → Laravel → PostGIS
2. **Output**: PostGIS → Laravel (GeoJSON) → Leaflet (Render Map)
3. **Import SHP**: SHP file → GDAL (ogr2ogr) → PostGIS → Laravel → Leaflet
4. **Export**: PostGIS → Laravel → GeoJSON/SHP/CSV/PDF/Excel → Download

### 3.3 Konsep Region Aktif (Configurable Region)

Aplikasi dirancang untuk fokus pada **1 kabupaten/kota aktif** dalam satu waktu.

| Konsep | Implementasi |
|--------|-------------|
| **Aktifkan 1 daerah** | `ACTIVE_REGENCY_ID=1` (Sukabumi) di `.env` |
| **Ganti daerah** | Ganti `ACTIVE_REGENCY_ID` + seed data wilayah baru |
| **Filter otomatis** | Global Scope Eloquent mem-filter `districts` & `villages` hanya untuk regency aktif |
| **Data spasial** | Bisa milik berbagai regency, tetap tersimpan di database |
| **Pengembangan** | Jika nanti butuh multi-regency, cukup nonaktifkan Global Scope |

**Contoh alur ganti daerah (Sukabumi → Karawang):**
```
1. Tambah record "Karawang" di tabel regencies
2. Seed kecamatan & kelurahan Karawang
3. Ubah .env: ACTIVE_REGENCY_ID=2
4. Selesai — semua dropdown, filter, dan query otomatis指向 Karawang
```

---

## 4. Hak Akses & Role

### 4.1 Role Definition

| Role | Deskripsi |
|------|-----------|
| **Administrator** | Akses penuh: manage user, role, layer, import, hapus data, semua laporan |
| **Operator** | Menambah/mengubah data spasial, upload GeoJSON & SHP |
| **Viewer** | Melihat peta, mencari lokasi, filter data, download (jika diizinkan) |

### 4.2 Matrix Permission

| Fitur | Administrator | Operator | Viewer |
|-------|:---:|:---:|:---:|
| Login & Logout | ✅ | ✅ | ✅ |
| Dashboard | ✅ | ✅ | ✅ |
| Lihat Peta Interaktif | ✅ | ✅ | ✅ |
| Manage User | ✅ | ❌ | ❌ |
| Manage Role | ✅ | ❌ | ❌ |
| Manage Layer (CRUD) | ✅ | ❌ | ❌ |
| Manage Wilayah | ✅ | ❌ | ❌ |
| Tambah Data Spasial | ✅ | ✅ | ❌ |
| Edit Data Spasial | ✅ | ✅ | ❌ |
| Hapus Data Spasial | ✅ | ❌ | ❌ |
| Import SHP/GeoJSON | ✅ | ✅ | ❌ |
| Export Data | ✅ | ✅ | ✅ (jika diizinkan) |
| Cetak Peta PDF | ✅ | ✅ | ✅ |
| Lihat Laporan | ✅ | ❌ | ❌ |
| Pengaturan | ✅ | ❌ | ❌ |

---

## 5. Modul Aplikasi

### 5.1 Modul Auth
- Halaman Login
- Halaman Register (opsional, bisa dimatikan)
- Logout
- Reset Password (via email)
- Middleware role-based routing

### 5.2 Modul Dashboard
- Ringkasan statistik:
  - Jumlah layer
  - Jumlah objek (point, line, polygon)
  - Jumlah wilayah
  - Statistik per kategori
- Peta ringkas (mini map)
- Grafik Chart.js (objek per wilayah, per kategori, persebaran)

### 5.3 Modul Master Data

#### 5.3.1 Manajemen User
- CRUD User
- Assign Role (Admin/Operator/Viewer)
- Filter & Search User

#### 5.3.2 Manajemen Role
- CRUD Role (via Spatie)
- Assign Permission ke Role

#### 5.3.3 Manajemen Kategori Layer
- CRUD Kategori
- Fields: nama, deskripsi, ikon, warna default

#### 5.3.4 Manajemen Wilayah
- **Regencies** (kabupaten/kota) — CRUD sederhana (nama, kode). Digunakan sebagai master daerah.
- **Kecamatan** — CRUD (relasi ke regency), data di-scope ke regency aktif via Global Scope
- **Kelurahan/Desa** — CRUD (relasi ke kecamatan)
- **Konfigurasi aktif**: `ACTIVE_REGENCY_ID` di `.env` — menentukan regency yang sedang aktif
  - Semua query kecamatan/kelurahan otomatis filtered ke regency ini
  - Ganti daerah (misal Sukabumi → Karawang): cukup ganti `.env` + seed data kecamatan/kelurahan baru

### 5.4 Modul Manajemen Layer

#### 5.4.1 Struktur Layer
Setiap layer memiliki:
- Nama layer
- Kategori (relasi ke kategori)
- Tipe geometri (Point / LineString / Polygon / MultiPolygon)
- Warna (untuk styling di peta)
- Ikon marker (untuk point)
- Status aktif/nonaktif
- Deskripsi
- Opacity
- Urutan (ordering)

#### 5.4.2 Fitur CRUD Layer
- Tambah layer (dengan pilihan tipe geometri)
- Edit layer (ubah warna, ikon, nama, dll)
- Hapus layer (dengan konfirmasi, hapus juga data spasial di dalamnya)
- Aktif/Nonaktifkan layer
- Duplikasi layer (struktur saja atau dengan data)

### 5.5 Modul Data Spasial

#### 5.5.1 Struktur Data Spasial
Setiap record data spasial memiliki:

**Geometri (PostGIS):**
- `geometry` — kolom dengan tipe geometry(Point, 4326) / geometry(LineString, 4326) / geometry(Polygon, 4326) / geometry(MultiPolygon, 4326)
- `geom_type` — enum: point, line, polygon, multipolygon
- `srid` — 4326 (WGS 84)

**Atribut:**
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint | Primary Key |
| layer_id | bigint | Foreign Key ke layers |
| nama | varchar(255) | Nama objek |
| deskripsi | text | Deskripsi |
| kategori_id | bigint | Foreign Key ke kategoris |
| kecamatan_id | bigint | FK ke kecamatan (nullable) |
| kelurahan_id | bigint | FK ke kelurahan (nullable) |
| status | varchar(50) | Status (aktif/nonaktif/draft, dll) |
| tahun | integer | Tahun data |
| luas | decimal(15,4) | Luas (m²) — untuk polygon |
| foto | text | Path foto (nullable) |
| dokumen | text | Path dokumen (nullable) |
| created_by | bigint | FK ke users |
| created_at | timestamp | |
| updated_at | timestamp | |

#### 5.5.2 CRUD Data Spasial
- Tambah via Leaflet Draw (digitasi langsung di peta)
- Tambah via form (input koordinat manual)
- Edit geometri (drag vertex, resize polygon)
- Edit atribut (form)
- Hapus data
- View popup informasi saat klik objek

### 5.6 Modul Peta (Halaman Peta Interaktif)

#### 5.6.1 Layout
- Fullscreen map
- Floating panel (tools, layer control, legend)
- Bottom bar (koordinat mouse, scale, zoom level)

#### 5.6.2 Fitur Standar
| Fitur | Plugin/Cara |
|-------|------------|
| Zoom In/Out | Leaflet bawaan |
| Pan | Leaflet bawaan |
| Fullscreen | Leaflet.fullscreen |
| Scale bar | Leaflet.control.scale |
| Layer Control | Custom: daftar layer dengan checkbox + warna |
| Basemap | OpenStreetMap default + Satellite (Esri) + Dark mode |
| Search lokasi | Nominatim (Leaflet Geocoding) |
| Legend | Custom legend per layer |
| Popup informasi | Leaflet Popup (bootstrap styled) |
| Koordinat mouse | Leaflet MouseCoordinate |
| Digitasi | Leaflet.draw (point, line, polygon) |
| Pengukuran jarak | Leaflet Measure |
| Pengukuran luas | Leaflet Measure |
| Cluster marker | Leaflet.markercluster (opsional) |
| Heatmap | Leaflet.heat (opsional) |

#### 5.6.3 GeoJSON API Endpoint
- `GET /api/map/layers` — daftar layer aktif dengan style
- `GET /api/map/{layerId}/data` — GeoJSON data spasial untuk layer tertentu
- `GET /api/map/data?bbox=...` — data dalam bounding box (performance)

### 5.7 Modul Import

#### 5.7.1 Format Didukung
| Format | Method | Library |
|--------|--------|---------|
| **GeoJSON** | Upload file, parse & insert | `json_decode()` + PostGIS `ST_GeomFromGeoJSON` |
| **SHP (Shapefile)** | Upload zip, ekstrak, konversi via GDAL | `ogr2ogr` |
| **KML** | Upload file, konversi via GDAL | `ogr2ogr` |
| **CSV** | Upload, parse lat/lon, insert point | `League\Csv` + PostGIS `ST_SetSRID(ST_MakePoint(lon,lat), 4326)` |

#### 5.7.2 Alur Import SHP
```
User upload .zip (berisi .shp, .shx, .dbf, .prj)
        ↓
Laravel terima file, simpan ke storage/temp
        ↓
Extract zip
        ↓
Panggil shell_exec("ogr2ogr -f PostgreSQL PG:... file.shp -nln target_table -lco GEOMETRY_NAME=geometry -lco FID=id -t_srs EPSG:4326")
        ↓
Baca hasil import, mapping kolom ke atribut
        ↓
Insert ke tabel spasial dengan layer_id sesuai
        ↓
Hapus file temp
        ↓
Redirect dengan notifikasi sukses + jumlah data terimport
```

#### 5.7.3 Validasi Import
- Validasi tipe geometri sesuai layer target
- Validasi file size (max 50MB)
- Validasi format file
- Preview data sebelum import (opsional)

### 5.8 Modul Export

| Format | Method |
|--------|--------|
| **GeoJSON** | `DB::select("SELECT jsonb_build_object(...)")` |
| **SHP** | Export dari PostGIS via `pgsql2shp` atau QGIS server |
| **CSV** | Laravel Excel atau League\Csv |
| **PDF** | DomPDF + render map static (snapshot) + tabel atribut |
| **Excel** | Laravel Excel (maatwebsite/laravel-excel) |

### 5.9 Modul Pencarian & Filter

#### 5.9.1 Pencarian
- Search by nama (autocomplete, partial match via ILIKE)
- Search by kategori (dropdown filter)
- Search by wilayah (dropdown kecamatan/kelurahan)
- Search by status
- Search by radius (buffer): klik titik, input radius meter → tampilkan semua dalam radius

#### 5.9.2 Filter Layer
- Checkbox show/hide per layer
- Filter atribut dalam layer (berdasarkan field yang ada)

### 5.10 Modul Dashboard Statistik
- Grafik Chart.js:
  - Bar chart: jumlah objek per layer
  - Pie chart: distribusi per kategori
  - Bar chart: jumlah objek per kecamatan
  - Line chart: tren data per tahun
- Cards: total layer, total objek, total wilayah
- Mini map

### 5.11 Modul Laporan
- Rekap data per layer
- Rekap data per wilayah
- Rekap data per kategori
- Cetak PDF (tabel + peta statis)
- Export Excel rekap

### 5.12 Modul Pengaturan
- Pengaturan umum aplikasi (nama, logo, favicon)
- Pengaturan peta (default center, default zoom, basemap default)
- Pengaturan import (max file size, allowed formats)

---

## 6. Database Design

### 6.1 Entity Relationship Diagram (Textual)

```
users ────┬── model_has_roles ──── roles
          │
          └── model_has_permissions ──── permissions

regencies ────┬── districts ────┬── villages
              │                 │
              │                 └── data_spasial (village_id)
              └── data_spasial (district_id)

layers ────┬── kategori_layers
           │
           └── data_spasial (layer_id)

kategori_layers ────┬── data_spasial (kategori_id)

data_spasial ────┬── created_by ──── users
                 │
                 └── geometry (PostGIS spatial column)
```

### 6.2 Table Structure

#### `users` (Laravel default + Breeze)
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| name | varchar(255) | |
| email | varchar(255) | Unique |
| password | varchar(255) | |
| email_verified_at | timestamp | Nullable |
| is_active | boolean | Default true |
| remember_token | varchar(100) | |
| timestamps | | |

#### `layers`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| nama | varchar(255) | Nama layer |
| slug | varchar(255) | Unique, untuk routing/API |
| kategori_id | bigint | FK → kategori_layers |
| geom_type | varchar(20) | 'Point','LineString','Polygon','MultiPolygon' |
| warna | varchar(9) | Hex color (#RRGGBB atau #RRGGBBAA) |
| icon_marker | varchar(100) | Nama icon untuk point layer |
| deskripsi | text | Nullable |
| is_active | boolean | Default true |
| opacity | decimal(3,2) | Default 1.00 |
| order | integer | Urutan tampil |
| created_by | bigint | FK → users |
| timestamps | | |

#### `kategori_layers`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| nama | varchar(255) | |
| deskripsi | text | Nullable |
| icon | varchar(100) | Nullable |
| warna_default | varchar(9) | Hex color |
| timestamps | | |

#### `data_spasial`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| layer_id | bigint | FK → layers |
| nama | varchar(255) | |
| deskripsi | text | Nullable |
| kategori_id | bigint | FK → kategori_layers (nullable) |
| district_id | bigint | FK → districts (nullable) |
| village_id | bigint | FK → villages (nullable) |
| status | varchar(50) | Default 'aktif' |
| tahun | integer | Nullable |
| luas | decimal(15,4) | Nullable, dihitung otomatis untuk polygon |
| foto | text | Nullable, path file |
| dokumen | text | Nullable, path file |
| geometry | geometry(Geometry, 4326) | **Spatial column** |
| created_by | bigint | FK → users |
| timestamps | | |

**Indexes:**
- `spatial_index` — GIST index on `geometry`
- `idx_layer_id` — on `layer_id`
- `idx_kategori_id` — on `kategori_id`
- `idx_district_id` — on `district_id`
- `idx_village_id` — on `village_id`
- `idx_nama_trgm` — trigram index on `nama` for fast search

#### `regencies`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| kode | varchar(10) | Unique, kode BPS |
| nama | varchar(255) | Nama kabupaten/kota |
| is_active | boolean | Default false, diatur via .env |
| timestamps | | |

> **Catatan**: Hanya 1 regency aktif dalam satu waktu (via `ACTIVE_REGENCY_ID` di `.env`).  
> `districts` dan `villages` di-scope otomatis ke regency aktif via Global Scope Eloquent.

#### `districts`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| regency_id | bigint | FK → regencies |
| kode | varchar(10) | Unique |
| nama | varchar(255) | |
| timestamps | | |

#### `villages`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| district_id | bigint | FK → districts |
| kode | varchar(10) | Unique |
| nama | varchar(255) | |
| timestamps | | |

### 6.3 Spatial Queries (Key Examples)

**Get GeoJSON untuk Leaflet:**
```sql
SELECT jsonb_build_object(
    'type', 'FeatureCollection',
    'features', jsonb_agg(
        jsonb_build_object(
            'type', 'Feature',
            'geometry', ST_AsGeoJSON(ds.geometry)::jsonb,
            'properties', jsonb_build_object(
                'id', ds.id,
                'nama', ds.nama,
                'deskripsi', ds.deskripsi,
                'status', ds.status,
                'tahun', ds.tahun,
                'luas', ds.luas,
                'layer_id', ds.layer_id,
                'kategori_id', ds.kategori_id
            )
        )
    )
) AS geojson
FROM data_spasial ds
WHERE ds.layer_id = ?;
```

**Cari dalam radius (buffer):**
```sql
SELECT ds.*
FROM data_spasial ds
WHERE ST_DWithin(
    ds.geometry::geography,
    ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography,
    ?  -- radius in meters
);
```

**Hitung luas polygon:**
```sql
SELECT ST_Area(geometry::geography) AS luas_m2
FROM data_spasial
WHERE id = ?;
```

**Filter by bounding box:**
```sql
SELECT *
FROM data_spasial
WHERE geometry && ST_MakeEnvelope(?, ?, ?, ?, 4326);
```

---

## 7. API Endpoints

### 7.1 Web Routes (Blade Pages)

| Method | URL | Controller | Middleware | Deskripsi |
|--------|-----|-----------|------------|-----------|
| GET | `/` | HomeController | auth | Redirect ke dashboard |
| GET | `/dashboard` | DashboardController | auth | Halaman dashboard |
| GET | `/login` | Auth\LoginController | guest | Halaman login |
| POST | `/login` | Auth\LoginController | guest | Proses login |
| POST | `/logout` | Auth\LogoutController | auth | Logout |

### 7.2 Master Data Routes

| Method | URL | Deskripsi |
|--------|-----|-----------|
| GET | `/admin/users` | Manajemen user |
| GET/POST | `/admin/users/create` | Tambah user |
| GET/PUT | `/admin/users/{id}/edit` | Edit user |
| DELETE | `/admin/users/{id}` | Hapus user |
| GET | `/admin/roles` | Manajemen role |
| GET/POST | `/admin/roles/create` | Tambah role |
| GET/PUT | `/admin/roles/{id}/edit` | Edit role |
| GET | `/admin/layers` | Manajemen layer |
| GET/POST | `/admin/layers/create` | Tambah layer |
| GET/PUT | `/admin/layers/{id}/edit` | Edit layer |
| DELETE | `/admin/layers/{id}` | Hapus layer |
| GET | `/admin/kategori` | Manajemen kategori |
| GET/POST | `/admin/kategori/create` | Tambah kategori |
| GET/PUT | `/admin/kategori/{id}/edit` | Edit kategori |
| DELETE | `/admin/kategori/{id}` | Hapus kategori |
| GET | `/admin/wilayah` | Manajemen wilayah |

### 7.3 Data Spasial Routes

| Method | URL | Deskripsi |
|--------|-----|-----------|
| GET | `/data-spasial` | Index data spasial (table) |
| GET | `/data-spasial/create` | Form tambah (manual) |
| POST | `/data-spasial` | Store data baru |
| GET | `/data-spasial/{id}/edit` | Edit data |
| PUT | `/data-spasial/{id}` | Update data |
| DELETE | `/data-spasial/{id}` | Hapus data |

### 7.4 Map API Routes (JSON)

| Method | URL | Deskripsi |
|--------|-----|-----------|
| GET | `/api/map/layers` | Daftar layer aktif + style |
| GET | `/api/map/{layerId}/data` | GeoJSON data per layer |
| GET | `/api/map/data?bbox=...` | Data dalam bounding box |
| GET | `/api/map/search?q=...` | Pencarian lokasi |
| GET | `/api/map/search-radius?lat=...&lng=...&radius=...` | Search by radius |
| POST | `/api/map/digitasi` | Simpan hasil digitasi |
| PUT | `/api/map/digitasi/{id}` | Update geometri |
| DELETE | `/api/map/digitasi/{id}` | Hapus geometri |

### 7.5 Import/Export Routes

| Method | URL | Deskripsi |
|--------|-----|-----------|
| GET | `/import` | Halaman import |
| POST | `/import/geojson` | Upload GeoJSON |
| POST | `/import/shp` | Upload SHP (zip) |
| POST | `/import/kml` | Upload KML |
| POST | `/import/csv` | Upload CSV |
| GET | `/export/{layerId}/geojson` | Export GeoJSON |
| GET | `/export/{layerId}/shp` | Export SHP |
| GET | `/export/{layerId}/csv` | Export CSV |
| GET | `/export/{layerId}/pdf` | Export PDF |

### 7.6 Laporan Routes

| Method | URL | Deskripsi |
|--------|-----|-----------|
| GET | `/laporan/data` | Rekap data |
| GET | `/laporan/wilayah` | Rekap per wilayah |
| GET | `/laporan/layer` | Rekap per layer |
| GET | `/laporan/cetak/{type}` | Cetak PDF/Excel |

---

## 8. Struktur Menu Navigasi

```
Dashboard
│
├── Master Data
│   ├── User
│   ├── Role
│   ├── Layer
│   ├── Kategori
│   └── Wilayah
│
├── Data Spasial
│   ├── Titik
│   ├── Garis
│   ├── Polygon
│   ├── Import
│   └── Export
│
├── Peta
│   ├── Peta Interaktif
│   ├── Basemap
│   └── Layer
│
├── Statistik
│
├── Laporan
│
└── Pengaturan
```

---

## 9. Desain UI/UX

### 9.1 Layout Umum (Admin Area)
- Sidebar navigasi (kiri) — collapsible, dark theme
- Top bar — user info, notifikasi
- Main content area — Tailwind card, tabel, form

### 9.2 Halaman Peta (Fullscreen)
```
┌─────────────────────────────────────────────────────────────┐
│  ┌───────┬─────────────────────────────────────────────┐  │
│  │       │                                             │  │
│  │ TOGGLE│                                             │  │
│  │ PANEL │              PETA LEAFLET                   │  │
│  │       │           (FULL AREA)                       │  │
│  │  -    │                                             │  │
│  │ Layer │     ┌───────────────────────┐               │  │
│  │ Ctrl  │     │ Floating Tool Panel  │               │  │
│  │  ☑ Jln│     │ [Draw] [Measure]     │               │  │
│  │  ☑ Sng│     │ [Search] [Legend]    │               │  │
│  │  ☑ Bgn│     └───────────────────────┘               │  │
│  │       │                                             │  │
│  │  -    │               ┌──────────────────────┐      │  │
│  │       │               │ Bottom Bar           │      │  │
│  │       │               │ Lat:... Lng:... Zoom │      │  │
│  │       │               │ Scale: 1:...        │      │  │
│  │       │               └──────────────────────┘      │  │
│  └───────┴─────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### 9.3 Floating Panel Tools (Peta)
- **Draw Tools** (Point, Line, Polygon) — terlihat hanya untuk Admin/Operator
- **Search Box** — dengan autocomplete
- **Layer Legend** — daftar layer dengan warna
- **Measure Tool** — jarak & luas
- **Fullscreen Toggle**
- **Basemap Switcher** (Street, Satellite, Dark)

---

## 10. Keamanan

| Aspek | Implementasi |
|-------|-------------|
| Authentication | Laravel Breeze (session-based) |
| Authorization | Spatie Laravel-permission + Middleware |
| CSRF | Laravel @csrf di semua form |
| XSS | Blade {{ }} escaping |
| SQL Injection | Eloquent ORM (parameter binding) |
| File Upload | Validasi ekstensi, size limit, store di private storage |
| Rate Limiting | Laravel throttle middleware untuk API |
| HTTPS | Force HTTPS di production |
| Logging | Laravel Activity Log (opsional) |
| Backup | Schedule backup database + storage |

---

## 11. Fitur Lanjutan (Post-MVP)

Fitur berikut dapat ditambahkan setelah rilis pertama:

1. **Versioning data spasial** — track perubahan geometri & atribut
2. **Workflow approval** — data perlu disetujui sebelum tampil
3. **Audit log** — riwayat perubahan setiap objek
4. **Integrasi drone & citra satelit**
5. **WFS/WMS** — share data ke QGIS/ArcGIS
6. **Monitoring real-time** — GPS/IoT tracking
7. **Analisis spasial** — overlay, buffering, intersection
8. **API publik** — untuk integrasi eksternal
9. **Portal publik** — halaman publik terbatas

---

## 12. Struktur Folder Proyek (Laravel)

```
petaSpasial/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── UserController.php
│   │   │   │   ├── RoleController.php
│   │   │   │   ├── LayerController.php
│   │   │   │   ├── KategoriController.php
│   │   │   │   └── WilayahController.php
│   │   │   ├── DataSpasialController.php
│   │   │   ├── MapController.php
│   │   │   ├── ImportController.php
│   │   │   ├── ExportController.php
│   │   │   ├── LaporanController.php
│   │   │   ├── DashboardController.php
│   │   │   └── PengaturanController.php
│   │   ├── Middleware/
│   │   │   └── CheckRole.php
│   │   └── Requests/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Layer.php
│   │   ├── KategoriLayer.php
│   │   ├── DataSpasial.php
│   │   ├── Regency.php
│   │   ├── District.php
│   │   └── Village.php
│   └── Services/
│       ├── GeoService.php
│       ├── ImportService.php
│       └── ExportService.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   │   ├── RoleSeeder.php
│   │   ├── UserSeeder.php
│   │   ├── WilayahSeeder.php
│   │   └── KategoriSeeder.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── admin/
│   │   │   ├── users/
│   │   │   ├── roles/
│   │   │   ├── layers/
│   │   │   ├── kategori/
│   │   │   └── wilayah/
│   │   ├── data-spasial/
│   │   ├── map/
│   │   ├── import/
│   │   ├── export/
│   │   ├── laporan/
│   │   └── pengaturan/
│   ├── js/
│   │   ├── app.js
│   │   ├── map.js
│   │   ├── digitasi.js
│   │   └── chart.js
│   └── css/
│       └── app.css
├── routes/
│   ├── web.php
│   └── api.php
├── storage/
│   └── app/
│       ├── imports/
│       ├── exports/
│       └── temp/
└── config/
    └── permission.php
```

---

## 13. Dependency PHP (Composer)

```json
{
    "require": {
        "php": "^8.3",
        "laravel/framework": "^11.0",
        "laravel/breeze": "^2.0",
        "spatie/laravel-permission": "^6.0",
        "barryvdh/laravel-dompdf": "^3.0",
        "maatwebsite/laravel-excel": "^3.1",
        "league/csv": "^9.0",
        "intervention/image": "^3.0",
        "spatie/laravel-medialibrary": "^11.0",
        "spatie/laravel-activitylog": "^4.0"
    }
}
```

## 14. Dependency NPM (Frontend)

```json
{
    "devDependencies": {
        "tailwindcss": "^3.4",
        "alpinejs": "^3.13",
        "chart.js": "^4.4",
        "leaflet": "^1.9",
        "leaflet-draw": "^1.0",
        "leaflet.fullscreen": "^3.0",
        "leaflet.markercluster": "^1.5",
        "leaflet-measure": "^3.1",
        "leaflet-search": "^3.0",
        "leaflet.mousecoordinate": "^1.0",
        "axios": "^1.7",
        "laravel-vite-plugin": "^1.0",
        "vite": "^6.0"
    }
}
```

---

> Dokumen ini akan terus diperbarui seiring perkembangan aplikasi.
