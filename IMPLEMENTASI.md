# IMPLEMENTASI — petaSpasial

> Panduan hosting dan deployment aplikasi WebGIS petaSpasial  
> Teknologi: Laravel 11 + PostgreSQL/PostGIS + Leaflet.js

---
import by cli : php artisan import:shp-sample

## Daftar Isi

1. [Kebutuhan Server](#1-kebutuhan-server)
2. [Persiapan Server (Ubuntu 22.04/24.04)](#2-persiapan-server-ubuntu-22042404)
3. [Instalasi Aplikasi](#3-instalasi-aplikasi)
4. [Konfigurasi PostGIS](#4-konfigurasi-postgis)
5. [Konfigurasi Web Server (Nginx)](#5-konfigurasi-web-server-nginx)
6. [Konfigurasi Environment](#6-konfigurasi-environment)
7. [Migrasi & Seed Database](#7-migrasi--seed-database)
8. [Storage & Symfony Link](#8-storage--symfony-link)
9. [Laravel Queue & Scheduler](#9-laravel-queue--scheduler)
10. [SSL/HTTPS (Let's Encrypt)](#10-sslhttps-lets-encrypt)
11. [Maintenance & Backup](#11-maintenance--backup)
12. [Troubleshooting](#12-troubleshooting)

---

## 1. Kebutuhan Server

| Komponen | Spesifikasi Minimal | Rekomendasi |
|----------|-------------------|-------------|
| CPU | 2 Core | 4 Core |
| RAM | 4 GB | 8 GB |
| Storage | 50 GB SSD | 100 GB SSD |
| OS | Ubuntu 22.04 / 24.04 LTS | Ubuntu 24.04 LTS |
| PHP | 8.3+ | 8.3 |
| PostgreSQL | 16+ | 17 |
| PostGIS | 3.4+ | 3.6 |
| Web Server | Nginx | Nginx |
| Node.js | 20 LTS | 22 LTS |
| Composer | 2.x | 2.x |

### Software yang Perlu Diinstall

- PHP 8.3 + ekstensi: `pgsql`, `pdo_pgsql`, `mbstring`, `xml`, `curl`, `gd`, `imagick`, `zip`, `bcmath`, `intl`, `fileinfo`
- PostgreSQL 16+ dengan ekstensi PostGIS
- Nginx
- Composer
- Node.js + npm
- GDAL (untuk import/export SHP)
- Supervisor (untuk queue worker)

---

## 2. Persiapan Server (Ubuntu 22.04/24.04)

### 2.1 Update Sistem

```bash
sudo apt update && sudo apt upgrade -y
```

### 2.2 Install PHP 8.3 & Ekstensi

```bash
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update

sudo apt install -y php8.3 php8.3-cli php8.3-fpm \
    php8.3-pgsql php8.3-mbstring php8.3-xml php8.3-curl \
    php8.3-gd php8.3-imagick php8.3-zip php8.3-bcmath \
    php8.3-intl php8.3-fileinfo php8.3-soap
```

### 2.3 Install Composer

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"
```

### 2.4 Install Node.js & npm

```bash
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs
```

### 2.5 Install Nginx

```bash
sudo apt install -y nginx
```

### 2.6 Install PostgreSQL & PostGIS

```bash
sudo apt install -y postgresql postgresql-contrib postgis postgresql-16-postgis-3
```

### 2.7 Install GDAL

```bash
sudo apt install -y gdal-bin libgdal-dev php8.3-gdal
```

### 2.8 Install Supervisor

```bash
sudo apt install -y supervisor
```

---

## 3. Instalasi Aplikasi

### 3.1 Clone atau Upload Aplikasi

Jika menggunakan Git:

```bash
cd /var/www
git clone https://github.com/your-org/petaSpasial.git
cd petaSpasial
```

Atau upload via SCP:

```bash
# Dari lokal
scp -r petaSpasial.zip user@server:/var/www/
ssh user@server
cd /var/www
unzip petaSpasial.zip
```

### 3.2 Install PHP Dependencies

```bash
cd /var/www/petaSpasial
composer install --no-dev --optimize-autoloader
```

### 3.3 Install NPM & Build Asset

```bash
npm install --production
npm run build
```

### 3.4 Set Permission

```bash
sudo chown -R www-data:www-data /var/www/petaSpasial
sudo chmod -R 755 /var/www/petaSpasial/storage
sudo chmod -R 755 /var/www/petaSpasial/bootstrap/cache
```

---

## 4. Konfigurasi PostGIS

### 4.1 Buat Database & User

```bash
sudo -u postgres psql
```

```sql
CREATE USER petaspasial WITH PASSWORD 'strong_password_here';
CREATE DATABASE peta_spasial OWNER petaspasial;
\c peta_spasial
CREATE EXTENSION IF NOT EXISTS postgis;
CREATE EXTENSION IF NOT EXISTS postgis_topology;
GRANT ALL ON ALL TABLES IN SCHEMA public TO petaspasial;
GRANT ALL ON ALL SEQUENCES IN SCHEMA public TO petaspasial;
\q
```

### 4.2 Verifikasi PostGIS

```bash
sudo -u postgres psql -d peta_spasial -c "SELECT postgis_version();"
```

---

## 5. Konfigurasi Web Server (Nginx)

### 5.1 Buat File Konfigurasi

```nginx
sudo nano /etc/nginx/sites-available/petaSpasial
```

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/petaSpasial/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # Upload size
    client_max_body_size 50M;
}
```

### 5.2 Aktifkan Site

```bash
sudo ln -s /etc/nginx/sites-available/petaSpasial /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

## 6. Konfigurasi Environment

### 6.1 Copy & Edit .env

```bash
cd /var/www/petaSpasial
cp .env.example .env
nano .env
```

### 6.2 Isi Konfigurasi Penting

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=peta_spasial
DB_USERNAME=petaspasial
DB_PASSWORD=strong_password_here

SESSION_DOMAIN=.yourdomain.com
SESSION_SECURE_COOKIE=true
SANCTUM_STATEFUL_DOMAINS=yourdomain.com

LOG_LEVEL=warning
```

### 6.3 Generate App Key

```bash
php artisan key:generate
```

---

## 7. Migrasi & Seed Database

```bash
php artisan migrate --force
php artisan db:seed --force
```

Jika ingin seed ulang dari awal (reset data):

```bash
php artisan migrate:fresh --seed --force
```

---

## 8. Storage & Symfony Link

### 8.1 Buat Storage Link

```bash
php artisan storage:link
```

### 8.2 Buat Direktori Tambahan

```bash
mkdir -p storage/app/imports
mkdir -p storage/app/exports
mkdir -p storage/app/temp
mkdir -p storage/app/contoh
mkdir -p storage/app/public/foto
mkdir -p storage/app/public/dokumen
chmod -R 775 storage/app/imports
chmod -R 775 storage/app/exports
chmod -R 775 storage/app/temp
chmod -R 775 storage/app/public/foto
chmod -R 775 storage/app/public/dokumen
```

---

## 9. Laravel Queue & Scheduler

### 9.1 Queue Worker (Supervisor)

Buat file konfigurasi Supervisor:

```bash
sudo nano /etc/supervisor/conf.d/petaspasial-worker.conf
```

```ini
[program:petaspasial-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/petaSpasial/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/petaSpasial/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start petaspasial-worker:*
```

### 9.2 Cron Job (Scheduler)

Tambahkan ke crontab:

```bash
sudo crontab -e -u www-data
```

```
* * * * * cd /var/www/petaSpasial && php artisan schedule:run >> /dev/null 2>&1
```

---

## 10. SSL/HTTPS (Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

Verifikasi auto-renewal:

```bash
sudo certbot renew --dry-run
```

---

## 11. Maintenance & Backup

### 11.1 Backup Database

Buat script backup:

```bash
sudo nano /usr/local/bin/backup-petaspasial.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/petaspasial"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p "$BACKUP_DIR"

# Backup database
pg_dump -U petaspasial -h localhost peta_spasial | gzip > "$BACKUP_DIR/db_$DATE.sql.gz"

# Backup storage
tar -czf "$BACKUP_DIR/storage_$DATE.tar.gz" -C /var/www/petaSpasial storage/app/public

# Hapus backup lebih dari 30 hari
find "$BACKUP_DIR" -name "*.gz" -mtime +30 -delete
```

```bash
sudo chmod +x /usr/local/bin/backup-petaspasial.sh
```

Tambahkan ke crontab:

```bash
sudo crontab -e
```

```
0 2 * * * /usr/local/bin/backup-petaspasial.sh
```

### 11.2 Restore Database

```bash
gunzip -c /var/backups/petaspasial/db_20240101_120000.sql.gz | psql -U petaspasial -h localhost peta_spasial
```

---

## 12. Troubleshooting

### 12.1 PostGIS Connection Failed

```bash
# Cek PostgreSQL status
sudo systemctl status postgresql

# Test koneksi
psql -U petaspasial -h localhost -d peta_spasial -c "SELECT 1"

# Cek pg_hba.conf
sudo nano /etc/postgresql/16/main/pg_hba.conf
# Pastikan ada: local   all   petaspasial   md5
```

### 12.2 502 Bad Gateway (Nginx → PHP-FPM)

```bash
# Cek PHP-FPM status
sudo systemctl status php8.3-fpm

# Cek log
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/php8.3-fpm.log
```

### 12.3 Asset Tidak Termuat (404)

```bash
# Pastikan symlink storage ada
ls -la public/storage

# Build ulang asset
npm run build
```

### 12.4 500 Server Error

```bash
# Cek log Laravel
tail -f /var/www/petaSpasial/storage/logs/laravel.log

# Generate ulang cache
php artisan optimize:clear
php artisan optimize
```

### 12.5 Import SHP Gagal (GDAL)

```bash
# Cek GDAL tersedia
which ogr2ogr
ogr2ogr --version

# Cek path GDAL di .env
# GDAL_PATH="/usr/bin"
```

### 12.6 Permission Error (Cannot Write)

```bash
sudo chown -R www-data:www-data /var/www/petaSpasial/storage
sudo chmod -R 775 /var/www/petaSpasial/storage
```

---

## Lampiran

### A. Struktur Direktori Penting

```
/var/www/petaSpasial/
├── app/                    # Kode aplikasi (PHP)
├── bootstrap/              # Bootstrap cache
├── config/                 # Konfigurasi Laravel
├── database/               # Migrasi & seeder
├── public/                 # Document root
│   ├── build/              # Asset Vite (hasil build)
│   └── storage/            # Symlink ke storage/app/public
├── resources/              # View, JS, CSS
│   └── js/                 # Entry points: app.js, map.js, dashboard.js
├── routes/                 # Route definitions
├── storage/                # File upload, log, cache
│   ├── app/
│   │   ├── imports/        # File import sementara
│   │   ├── exports/        # File export sementara
│   │   ├── temp/           # Temp files (SHP processing)
│   │   ├── public/
│   │   │   ├── foto/       # Upload foto
│   │   │   └── dokumen/    # Upload dokumen
│   │   └── contoh/         # File contoh import
│   └── logs/               # Laravel logs
└── vendor/                 # Composer dependencies
```

### B. Perintah Berguna

| Tujuan | Perintah |
|--------|----------|
| Maintenance mode ON | `php artisan down --secret="token123"` |
| Maintenance mode OFF | `php artisan up` |
| Clear cache | `php artisan optimize:clear` |
| Cache config | `php artisan config:cache` |
| Cache route | `php artisan route:cache` |
| Restart queue | `sudo supervisorctl restart petaspasial-worker:*` |
| Cek status queue | `sudo supervisorctl status` |

---

> **petaSpasial** — Sistem Informasi Geografis Data Spasial  
> Versi 1.0 — Dokumen Implementasi
