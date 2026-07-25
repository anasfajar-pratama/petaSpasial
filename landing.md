# RENCANA LANDING & GUEST VIEW — petaSpasial

> Tujuan: Menyediakan akses publik agar pengguna dapat melihat peta, data, dan statistik tanpa login.
> Benchmark: Jakarta Satu Geoportal (https://jakartasatu.jakarta.go.id/geoportal)

---

## Daftar Isi

1. [Ringkasan & Tujuan](#1-ringkasan--tujuan)
2. [Peta Situs (Site Map)](#2-peta-situs-site-map)
3. [Detail Halaman](#3-detail-halaman)
4. [Route & Endpoint Baru](#4-route--endpoint-baru)
5. [Komponen yang Dibuat & Diubah](#5-komponen-yang-dibuat--diubah)
6. [Timeline Pengerjaan](#6-timeline-pengerjaan)

---

## 1. Ringkasan & Tujuan

### Masalah
Semua halaman peta, statistik, dan data saat ini hanya bisa diakses setelah login (`middleware auth`). Pengguna umum (guest) hanya melihat redirect ke `/login`.

### Solusi
Membuka **4 halaman publik** + **beberapa API endpoint publik (read-only)**, sehingga:
- Masyarakat bisa melihat peta interaktif tanpa registrasi
- Data spasial tetap aman (read-only, tidak ada CRUD publik)
- Pengguna tertarik bisa login untuk mengelola data

### Target Pengguna Guest
| Tipe | Kebutuhan |
|------|-----------|
| **Masyarakat umum** | Lihat peta, cari lokasi, lihat info data |
| **Instansi/desa** | Lihat data spasial wilayah, download informasi publik |
| **Calon Operator** | Lihat dulu isi aplikasi sebelum minta akses |

---

## 2. Peta Situs (Site Map)

```
petaSpasial — Guest (Publik)
│
├── 🏠 Beranda (/)                         [halaman baru]
│   ├── Hero section
│   ├── Statistik ringkas (3 card)
│   └── Footer
│
├── 🗺️ Peta (/peta)                        [halaman baru — adaptasi dari map/index]
│   ├── Peta interaktif Leaflet (fullscreen)
│   ├── Panel kiri: layer list + basemap switcher
│   ├── Search box + filter (kategori, kecamatan, status)
│   ├── Radius/buffer search
│   ├── Klik fitur → popup detail (nama, deskripsi, status, foto)
│   └── Alat ukur (measure distance/area)        [baru]
│
├── 📊 Statistik (/statistik-publik)             [halaman baru]
│   ├── Cards: total layer, total objek, total wilayah
│   ├── Bar chart: objek per layer
│   ├── Pie chart: distribusi per kategori
│   └── Filter (tahun, kecamatan)
│
├── 📋 Layer (/layer/{slug})                     [halaman baru]
│   ├── Info layer: nama, kategori, tipe geometri, deskripsi
│   ├── Tabel data spasial (paginate)
│   └── Tombol "Lihat di Peta"
│
├── 📍 Detail Data (/data/{id})                  [halaman baru]
│   ├── Semua atribut: nama, deskripsi, status, tahun, luas
│   ├── Foto (jika ada)
│   ├── Layer asal → link ke /layer/{slug}
│   └── Peta mini lokasi
│
├── 🔐 Login (/login)                            [sudah ada]
│   ├── Form login
│   ├── Link register
│   └── Lupa password
│
└── 📝 Register (/register)                      [sudah ada]
```

### Navigasi Guest (Navbar Publik)
| Menu | Route | Keterangan |
|------|-------|------------|
| Beranda | `/` | Logo + nama app |
| Peta | `/peta` | |
| Statistik | `/statistik-publik` | |

---

## 3. Detail Halaman

### 3.1 Beranda (`/`)
**Layout:** `layouts/guest.blade.php` — tanpa sidebar, navbar minimalis

**Bagian:**
1. **Hero** — nama aplikasi + tagline "Sistem Informasi Geospasial [Wilayah]" + deskripsi singkat
2. **Statistik Ringkas** — 3 card berisi:
   - Total Objek Spasial (dari DB)
   - Total Layer (dari DB)
   - Total Kecamatan (dari DB)
3. **Footer** — kontak, hak cipta

**Backend:**
- `GuestController@beranda` — ambil statistik (total objek, total layer, total kecamatan)

### 3.2 Peta Publik (`/peta`)
**Layout:** Standalone (seperti `map/index.blade.php` saat ini)

**Fitur:**
- Sama seperti peta yang sudah ada, **tanpa**:
  - Digitasi (draw) toolbar
  - Tombol edit/hapus di popup
  - Link ke dashboard
- **Ditambahkan:**
  - Alat ukur (Leaflet Measure Control)

**Backend:**
- API endpoint yang sudah ada DIBUKA untuk guest:
  - `GET /api/publik/layers` — daftar layer aktif
  - `GET /api/publik/{layer}/data` — GeoJSON data per layer
  - `GET /api/publik/data?bbox=...` — bounding box
  - `GET /api/publik/search?q=...` — search + filter
  - `POST /api/publik/buffer` — radius search
  - `GET /api/publik/data-single?id=...` — detail satu data

### 3.3 Statistik Publik (`/statistik-publik`)
**Layout:** `layouts/guest.blade.php`

**Fitur:**
- Sama seperti halaman statistik yang sudah ada untuk auth
- Tanpa link ke admin
- Filter: tahun, kecamatan

**Backend:**
- `GuestController@statistik` — ambil data statistik

### 3.4 Detail Layer (`/layer/{slug}`)
**Layout:** `layouts/guest.blade.php`

**Bagian:**
- Header: nama layer, badge tipe geometri, kategori
- Deskripsi
- Tabel data spasial (paginate, 20 per halaman)
- Tombol "Lihat di Peta" → `/peta?layer={id}`

**Backend:**
- `GuestController@layer` — ambil data layer + data spasial paginate
- API: `GET /api/publik/layer/{slug}/data?page=...`

### 3.5 Detail Data (`/data/{id}`)
**Layout:** `layouts/guest.blade.php`

**Bagian:**
- Nama data spasial (sebagai judul)
- Atribut: deskripsi, status, tahun, luas, layer asal (link)
- Foto (jika ada)
- Peta mini (300px, Leaflet, satu fitur)
- Tombol "Lihat di Peta" → `/peta?id={id}`

**Backend:**
- `GuestController@detailData`

---

## 4. Route & Endpoint Baru

### 4.1 Web Routes (Guest — Tanpa Middleware Auth)

| Method | Route | Controller | Method | View |
|--------|-------|-----------|--------|------|
| GET | `/` | `GuestController` | `beranda` | `guest.beranda` |
| GET | `/peta` | `GuestController` | `peta` | `guest.peta` |
| GET | `/statistik-publik` | `GuestController` | `statistik` | `guest.statistik` |
| GET | `/layer/{slug}` | `GuestController` | `layer` | `guest.layer` |
| GET | `/data/{id}` | `GuestController` | `detailData` | `guest.detail-data` |

### 4.2 API Endpoints Publik (Read-Only)

| Method | Route | Controller | Method | Notes |
|--------|-------|-----------|--------|-------|
| GET | `/api/publik/statistik-ringkas` | `GuestController` | `statistikRingkas` | Untuk hero |
| GET | `/api/publik/layers` | `MapPublicController` | `layers` | Sama seperti MapController |
| GET | `/api/publik/{layer}/data` | `MapPublicController` | `data` | Sama |
| GET | `/api/publik/data` | `MapPublicController` | `bbox` | Sama |
| GET | `/api/publik/search` | `MapPublicController` | `search` | Sama |
| POST | `/api/publik/buffer` | `MapPublicController` | `buffer` | Sama |
| GET | `/api/publik/data-single` | `MapPublicController` | `single` | Sama |

> API publik adalah subset read-only dari endpoint yang sudah ada di MapController. Tidak ada POST/PUT/DELETE.

---

## 5. Komponen yang Dibuat & Diubah

### 5.1 File Baru

| File | Keterangan |
|------|-----------|
| `app/Http/Controllers/GuestController.php` | Controller untuk halaman publik |
| `app/Http/Controllers/MapPublicController.php` | Controller API publik (extend MapController) |
| `resources/views/guest/beranda.blade.php` | Halaman beranda publik |
| `resources/views/guest/peta.blade.php` | Halaman peta publik (adaptasi dari `map/index.blade.php`) |
| `resources/views/guest/statistik.blade.php` | Halaman statistik publik |
| `resources/views/guest/layer.blade.php` | Halaman detail layer |
| `resources/views/guest/detail-data.blade.php` | Halaman detail data spasial |
| `resources/views/components/layouts/guest.blade.php` | Layout publik dengan navbar guest |

### 5.2 File yang Diubah

| File | Perubahan |
|------|-----------|
| `routes/web.php` | Tambah route publik + API publik (sebelum middleware auth) |
| `app/Http/Controllers/MapController.php` | Extract logic ke trait atau method static agar bisa dipakai ulang |
| `resources/views/map/index.blade.php` | Tidak diubah (tetap untuk auth) |
| `resources/views/statistik/index.blade.php` | Tidak diubah (tetap untuk auth) |

### 5.3 Layout Guest Publik

Desain navbar guest:
```
┌──────────────────────────────────────────────────┐
│ [Logo] petaSpasial    Beranda  Peta  Statistik   │
└──────────────────────────────────────────────────┘
```
- Warna: sesuai tema aplikasi (biru/hijau khas GIS)
- Responsive: hamburger menu di mobile
- Tanpa sidebar admin

---

## 6. Timeline Pengerjaan

Urutan eksekusi dari **paling dasar** ke **pelengkap**.

---

### Hari 1 — Foundation

| No | Task | File |
|:--:|------|------|
| 1 | Buat `GuestController` dengan method `beranda` | `app/Http/Controllers/GuestController.php` |
| 2 | Buat layout publik (navbar: logo + 3 menu, tanpa login) | `resources/views/components/layouts/guest.blade.php` |
| 3 | Ubah route `/` → `GuestController@beranda` (ganti redirect `/login`) | `routes/web.php` |
| 4 | Buat view beranda: hero + 3 card statistik + footer | `resources/views/guest/beranda.blade.php` |

**Hasil:** Guest buka website → lihat halaman beranda dengan statistik.

---

### Hari 2 — API Publik

| No | Task | File |
|:--:|------|------|
| 5 | Buat `MapPublicController` — method read-only (layers, data, bbox, search, buffer, single) | `app/Http/Controllers/MapPublicController.php` |
| 6 | Tambah route grup `/api/publik/*` tanpa middleware auth | `routes/web.php` |

**Hasil:** API publik siap dikonsumsi oleh view peta.

---

### Hari 3 — Peta Publik

| No | Task | File |
|:--:|------|------|
| 7 | Buat view peta publik adaptasi dari `map/index.blade.php` | `resources/views/guest/peta.blade.php` |
| 8 | Hapus digitasi toolbar, tombol edit/hapus, link dashboard | `guest/peta.blade.php` |
| 9 | Integrasi Leaflet Measure (alat ukur) | `guest/peta.blade.php` + `resources/js/` |

**Hasil:** Guest bisa lihat peta, klik fitur, search, filter, dan ukur jarak/luas.

---

### Hari 4 — Statistik Publik

| No | Task | File |
|:--:|------|------|
| 10 | Buat view statistik publik (adaptasi dari `statistik/index.blade.php`) | `resources/views/guest/statistik.blade.php` |
| 11 | Tambah method `statistik` di `GuestController` | `GuestController.php` |
| 12 | Tambah route `/statistik-publik` | `routes/web.php` |

**Hasil:** Guest bisa lihat dashboard statistik & grafik.

---

### Hari 5 — Detail Layer & Data

| No | Task | File |
|:--:|------|------|
| 13 | Buat view detail layer (info + tabel data) | `resources/views/guest/layer.blade.php` |
| 14 | Buat view detail data spasial (atribut + foto + peta mini) | `resources/views/guest/detail-data.blade.php` |
| 15 | Tambah method `layer` & `detailData` di `GuestController` | `GuestController.php` |
| 16 | Tambah route `/layer/{slug}` & `/data/{id}` | `routes/web.php` |

**Hasil:** Guest bisa lihat detail per layer & per data spasial.

---

### Hari 6 — Poles & Integrasi

| No | Task |
|:--:|------|
| 17 | Link navigasi antar halaman (peta → detail, detail → peta) |
| 18 | Hero beranda → tombol "Lihat Peta" & "Lihat Statistik" |
| 19 | Responsive design check (mobile) |
| 20 | Footer: kontak, hak cipta |
| 21 | Uji coba semua route publik (tidak boleh error 500) |

---

### Ringkasan

```
Hari 1: Foundation          ████████░░░░░░░░  4 task
Hari 2: API Publik          ████░░░░░░░░░░░░  2 task
Hari 3: Peta Publik         ██████░░░░░░░░░░  3 task
Hari 4: Statistik Publik    ██████░░░░░░░░░░  3 task
Hari 5: Detail Layer/Data   ████████░░░░░░░░  4 task
Hari 6: Poles & Integrasi   ██████████░░░░░░  5 task
                          ─────────────────
Total:                      6 hari — 21 task
```

### Daftar Pustaka / Referensi

- Layout publik terinspirasi dari Jakarta Satu Geoportal
- Peta publik menggunakan Leaflet.js (existing)
- Alat ukur: Leaflet Measure Control (npm: `leaflet-measure`)
