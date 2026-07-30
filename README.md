# petaSpasial

WebGIS Aplikasi Peta Spasial Interaktif — Laravel 11 + PostGIS + Leaflet.js

## Requirements

- PHP 8.2+
- Composer
- PostgreSQL 16+ with PostGIS
- Node.js & NPM
- GDAL (untuk import SHP)

## Setup

```bash
cp .env.example .env
# edit .env: database, ACTIVE_REGENCY_ID=1 (Sukabumi)
composer install
npm install && npm run build
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Default Users

| Role | Email | Password |
|------|-------|----------|
| **Administrator** | `admin@peta-spasial.test` | `password` |
| **Operator** | `operator@peta-spasial.test` | `password` |
| **Viewer** | `viewer@peta-spasial.test` | `password` |

## Seed Data

- 7 kategori layer (Infrastruktur, Bangunan, Perairan, Lahan, dll)
- Kota Sukabumi + 7 kecamatan + 33 kelurahan
- 3 role (Administrator, Operator, Viewer) + 11 permissions

## Tech Stack

- **Backend:** Laravel 11, Spatie Laravel-permission, DomPDF, Laravel Excel
- **Frontend:** Blade, Tailwind CSS 3, Alpine.js, Leaflet.js, Chart.js
- **Database:** PostgreSQL 16+, PostGIS 3.6+
