<?php

namespace App\Console\Commands;

use App\Models\Layer;
use App\Models\DataSpasial;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportShpSample extends Command
{
    protected $signature = 'import:shp-sample';
    protected $description = 'Auto-import SHP data dari folder SHP/ ke database';

    private string $gdal;
    private string $tempDir;
    private int $adminId = 1;

    private array $colorPalette = [
        '#dc2626', '#f59e0b', '#3b82f6', '#8b5cf6', '#e91e63',
        '#00bcd4', '#ff9800', '#4caf50', '#795548', '#9c27b0',
        '#2196f3', '#607d8b', '#673ab7', '#03a9f4', '#009688',
        '#8bc34a', '#ff5722', '#ffc107', '#388e3c', '#e67e22',
    ];
    private int $colorIndex = 0;

    public function handle()
    {
        $this->gdal = config('gdal.bin');
        $this->tempDir = storage_path('app/temp/preimport-' . uniqid());
        mkdir($this->tempDir, 0755, true);

        $processedZips = [];

        // Phase 1: Process hardcoded definitions
        $definitions = $this->getDefinitions();
        foreach ($definitions as $def) {
            $processedZips[$def['zip']] = true;
            $this->processDefinition($def);
        }

        // Phase 2: Auto-scan new zips
        $this->autoScan($processedZips);

        $this->rmdirRecursive($this->tempDir);
        $this->info('Done.');
    }

    private function getDefinitions(): array
    {
        $katId = fn($name) => \App\Models\KategoriLayer::where('nama', $name)->value('id') ?? 1;

        return [
            // ===== EXISTING DEFINITIONS =====
            [
                'name' => 'Batas Administrasi',
                'geom_type' => 'Polygon',
                'color' => '#dc2626',
                'kategori_id' => $katId('Batas Administrasi'),
                'description' => 'Batas administrasi kelurahan Kota Sukabumi',
                'zip' => base_path('SHP/Batas Administrasi-20260722T022416Z-1-001.zip'),
                'shp_pattern' => '*_AR.shp',
            ],
            [
                'name' => 'Batas Administrasi Garis',
                'geom_type' => 'LineString',
                'color' => '#dc2626',
                'kategori_id' => $katId('Batas Administrasi'),
                'description' => 'Garis batas administrasi Kota Sukabumi',
                'zip' => base_path('SHP/Batas Administrasi-20260722T022416Z-1-001.zip'),
                'shp_pattern' => '*_LN.shp',
            ],
            [
                'name' => 'Histori Bencana',
                'geom_type' => 'Point',
                'color' => '#f59e0b',
                'kategori_id' => $katId('Infrastruktur'),
                'description' => 'Data histori bencana Kota Sukabumi 2016-2024',
                'zip' => base_path('SHP/SHP Histori Bencana 2016-2024-20260722T022432Z-1-001.zip'),
                'shp_pattern' => '*.shp',
            ],
            [
                'name' => 'IMB',
                'geom_type' => 'Point',
                'color' => '#3b82f6',
                'kategori_id' => $katId('Perizinan'),
                'description' => 'Izin Mendirikan Bangunan Kota Sukabumi',
                'zip' => base_path('SHP/SHP IMB-20260722T022505Z-1-001.zip'),
                'shp_pattern' => '*.shp',
            ],
            [
                'name' => 'Toponimi',
                'geom_type' => 'Point',
                'color' => '#8b5cf6',
                'kategori_id' => $katId('Toponimi'),
                'description' => 'Data toponimi (nama tempat) Kota Sukabumi',
                'zip' => base_path('SHP/SHP Toponimi-20260730T041836Z-1-001.zip'),
                'shp_pattern' => '*.shp',
            ],

            // ===== NEW DEFINITIONS =====
            [
                'name' => 'Kumuh',
                'geom_type' => 'Polygon',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Permukiman'),
                'description' => 'Data kawasan kumuh Kota Sukabumi',
                'zip' => base_path('SHP/SHP Kumuh-20260730T025414Z-1-001.zip'),
                'shp_pattern' => '*.shp',
            ],
            [
                'name' => 'Stunting',
                'geom_type' => 'Point',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Kesehatan'),
                'description' => 'Data stunting Kota Sukabumi',
                'zip' => base_path('SHP/SHP Stunting-20260730T025433Z-1-001.zip'),
                'shp_pattern' => '*.shp',
            ],
            [
                'name' => 'Trayek Angkot',
                'geom_type' => 'LineString',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Angkutan'),
                'description' => 'Peta trayek angkutan kota Sukabumi',
                'zip' => base_path('SHP/SHP Trayek Angkot-20260730T025437Z-1-001.zip'),
                'shp_pattern' => '*.shp',
            ],
            [
                'name' => 'Kemiskinan GP Cikole',
                'geom_type' => 'Point',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Sosial'),
                'description' => 'Data kemiskinan Kecamatan Gunung Puyuh dan Cikole',
                'zip' => base_path('SHP/SHP Kemiskinan-20260730T025410Z-1-001.zip'),
                'shp_pattern' => 'Data_Sigenko.shp',
            ],
            [
                'name' => 'Kemiskinan Warudoyong',
                'geom_type' => 'Point',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Sosial'),
                'description' => 'Data kemiskinan Kecamatan Warudoyong',
                'zip' => base_path('SHP/SHP Kemiskinan-20260730T025410Z-1-001.zip'),
                'shp_pattern' => 'Kel_*.shp',
                'merge' => true,
            ],

            // ===== RTRW: multi-SHP (13 layers from 1 zip) =====
            [
                'name' => 'Penetapan Kawasan Strategis',
                'geom_type' => 'Polygon',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Tata Ruang'),
                'description' => 'Penetapan kawasan strategis RTRW',
                'zip' => base_path('SHP/SHP RTRW Kota Sukabumi Tahun 2022-2042-20260730T025426Z-1-001.zip'),
                'shp_pattern' => 'Penetapan_Kawasan_Strategis.shp',
            ],
            [
                'name' => 'Rencana Pola Ruang',
                'geom_type' => 'Polygon',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Tata Ruang'),
                'description' => 'Rencana pola ruang RTRW',
                'zip' => base_path('SHP/SHP RTRW Kota Sukabumi Tahun 2022-2042-20260730T025426Z-1-001.zip'),
                'shp_pattern' => 'Rencana_Pola_Ruang.shp',
            ],
            [
                'name' => 'Rencana Sistem Jaringan Energi',
                'geom_type' => 'LineString',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Tata Ruang'),
                'description' => 'Rencana sistem jaringan energi',
                'zip' => base_path('SHP/SHP RTRW Kota Sukabumi Tahun 2022-2042-20260730T025426Z-1-001.zip'),
                'shp_pattern' => 'Rencana_Sistem_Jaringan_Energi.shp',
            ],
            [
                'name' => 'Sistem Infrastruktur Energi',
                'geom_type' => 'Point',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Tata Ruang'),
                'description' => 'Sistem infrastruktur energi',
                'zip' => base_path('SHP/SHP RTRW Kota Sukabumi Tahun 2022-2042-20260730T025426Z-1-001.zip'),
                'shp_pattern' => 'Sistem_Infrastruktur_Energi.shp',
            ],
            [
                'name' => 'Sistem Infrastruktur Perkotaan',
                'geom_type' => 'Point',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Tata Ruang'),
                'description' => 'Sistem infrastruktur perkotaan',
                'zip' => base_path('SHP/SHP RTRW Kota Sukabumi Tahun 2022-2042-20260730T025426Z-1-001.zip'),
                'shp_pattern' => 'Sistem_Infrastruktur_Perkotaan.shp',
            ],
            [
                'name' => 'Sistem Infrastruktur Sumber Daya Air',
                'geom_type' => 'Point',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Tata Ruang'),
                'description' => 'Sistem infrastruktur sumber daya air',
                'zip' => base_path('SHP/SHP RTRW Kota Sukabumi Tahun 2022-2042-20260730T025426Z-1-001.zip'),
                'shp_pattern' => 'Sistem_Infrastruktur_Sumber_Daya_Air.shp',
            ],
            [
                'name' => 'Sistem Infrastruktur Telekomunikasi',
                'geom_type' => 'Point',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Tata Ruang'),
                'description' => 'Sistem infrastruktur telekomunikasi',
                'zip' => base_path('SHP/SHP RTRW Kota Sukabumi Tahun 2022-2042-20260730T025426Z-1-001.zip'),
                'shp_pattern' => 'Sistem_Infrastruktur_Telekomunikasi.shp',
            ],
            [
                'name' => 'Sistem Infrastruktur Transportasi',
                'geom_type' => 'Point',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Tata Ruang'),
                'description' => 'Sistem infrastruktur transportasi',
                'zip' => base_path('SHP/SHP RTRW Kota Sukabumi Tahun 2022-2042-20260730T025426Z-1-001.zip'),
                'shp_pattern' => 'Sistem_Infrastruktur_Transportasi.shp',
            ],
            [
                'name' => 'Sistem Jaringan Infrastruktur Perkotaan',
                'geom_type' => 'LineString',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Tata Ruang'),
                'description' => 'Sistem jaringan infrastruktur perkotaan',
                'zip' => base_path('SHP/SHP RTRW Kota Sukabumi Tahun 2022-2042-20260730T025426Z-1-001.zip'),
                'shp_pattern' => 'Sistem_Jaringan_Infrastruktur_Perkotaan.shp',
            ],
            [
                'name' => 'Sistem Jaringan Sumber Daya Air',
                'geom_type' => 'LineString',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Tata Ruang'),
                'description' => 'Sistem jaringan sumber daya air',
                'zip' => base_path('SHP/SHP RTRW Kota Sukabumi Tahun 2022-2042-20260730T025426Z-1-001.zip'),
                'shp_pattern' => 'Sistem_Jaringan_Sumber_Daya_Air.shp',
            ],
            [
                'name' => 'Sistem Jaringan Telekomunikasi',
                'geom_type' => 'LineString',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Tata Ruang'),
                'description' => 'Sistem jaringan telekomunikasi',
                'zip' => base_path('SHP/SHP RTRW Kota Sukabumi Tahun 2022-2042-20260730T025426Z-1-001.zip'),
                'shp_pattern' => 'Sistem_Jaringan_Telekomunikasi.shp',
            ],
            [
                'name' => 'Sistem Jaringan Transportasi',
                'geom_type' => 'LineString',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Tata Ruang'),
                'description' => 'Sistem jaringan transportasi',
                'zip' => base_path('SHP/SHP RTRW Kota Sukabumi Tahun 2022-2042-20260730T025426Z-1-001.zip'),
                'shp_pattern' => 'Sistem_Jaringan_Transportasi.shp',
            ],
            [
                'name' => 'Sistem Pusat Pelayanan',
                'geom_type' => 'Point',
                'color' => $this->nextColor(),
                'kategori_id' => $katId('Tata Ruang'),
                'description' => 'Sistem pusat pelayanan',
                'zip' => base_path('SHP/SHP RTRW Kota Sukabumi Tahun 2022-2042-20260730T025426Z-1-001.zip'),
                'shp_pattern' => 'Sistem_Pusat_Pelayanan.shp',
            ],
        ];
    }

    private function processDefinition(array $def): void
    {
        $this->info("Processing: {$def['name']}...");

        $slug = Str::slug($def['name']);
        $existing = Layer::where('slug', $slug)->orWhere('nama', $def['name'])->first();

        if ($existing) {
            if ($existing->dataSpasial()->count() > 0) {
                $this->info("  Skipping: already has {$existing->dataSpasial()->count()} features.");
                return;
            }
            $layer = $existing;
        } else {
            $layer = Layer::create([
                'nama' => $def['name'],
                'slug' => $slug,
                'kategori_id' => $def['kategori_id'],
                'geom_type' => $def['geom_type'],
                'warna' => $def['color'],
                'opacity' => 0.8,
                'is_active' => true,
                'deskripsi' => $def['description'] ?? '',
                'order' => Layer::max('order') + 1,
                'created_by' => $this->adminId,
            ]);
        }

        $shpFiles = $this->extractAllShp($def['zip'], $def['shp_pattern']);

        if (empty($shpFiles)) {
            $this->error("  No .shp files found matching '{$def['shp_pattern']}'.");
            return;
        }

        if ($def['merge'] ?? false) {
            $total = 0;
            foreach ($shpFiles as $shp) {
                $total += $this->importShpToLayer($shp, $layer);
            }
            $this->info("  Imported {$total} features (merged).");
        } else {
            $count = $this->importShpToLayer($shpFiles[0], $layer);
            $this->info("  Imported {$count} features.");
        }
    }

    private function autoScan(array $processedZips): void
    {
        $shpDir = base_path('SHP');
        $zipFiles = glob($shpDir . '/*.zip');

        $foundNew = false;
        foreach ($zipFiles as $zipPath) {
            if (isset($processedZips[$zipPath])) continue;

            $foundNew = true;
            $this->line("\n[Auto-scan] Checking: " . basename($zipPath));

            // Try to open zip
            $zip = new \ZipArchive();
            if ($zip->open($zipPath) !== true) {
                $this->error("  Cannot open zip file.");
                continue;
            }
            $zip->close();

            // Extract and find .shp files
            $shpFiles = $this->extractAllShp($zipPath, '*.shp');
            if (empty($shpFiles)) {
                // Cleanup any extracted files
                foreach (glob($this->tempDir . '/*', GLOB_ONLYDIR) as $d) {
                    $this->rmdirRecursive($d);
                }
                $this->warn("  No .shp files found. Skipped.");
                continue;
            }

            foreach ($shpFiles as $shpPath) {
                $baseName = pathinfo($shpPath, PATHINFO_FILENAME);
                $layerName = $this->deriveLayerName($baseName);
                $geomType = $this->detectGeomType($shpPath);
                $slug = Str::slug($layerName);

                $existing = Layer::where('slug', $slug)->orWhere('nama', $layerName)->first();

                if ($existing && $existing->dataSpasial()->count() > 0) {
                    $this->info("  {$layerName}: already exists with {$existing->dataSpasial()->count()} features, skipped.");
                    continue;
                }

                if (!$geomType) {
                    $this->warn("  {$layerName}: cannot detect geometry type, skipped.");
                    continue;
                }

                $layer = $existing ?? Layer::create([
                    'nama' => $layerName,
                    'slug' => $slug,
                    'kategori_id' => 1,
                    'geom_type' => $geomType,
                    'warna' => $this->nextColor(),
                    'opacity' => 0.8,
                    'is_active' => true,
                    'deskripsi' => 'Auto-imported from ' . basename($zipPath),
                    'order' => Layer::max('order') + 1,
                    'created_by' => $this->adminId,
                ]);

                $count = $this->importShpToLayer($shpPath, $layer);
                $this->info("  ✅ {$layerName}: imported {$count} features.");
            }
        }

        if (!$foundNew) {
            $this->line("\n[Auto-scan] No new zip files found.");
        }
    }

    private function extractAllShp(string $zipPath, string $pattern): array
    {
        $extractDir = $this->tempDir . DIRECTORY_SEPARATOR . uniqid();
        mkdir($extractDir, 0755, true);

        $this->extractAllZips($zipPath, $extractDir);

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($extractDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $result = [];
        foreach ($files as $f) {
            if ($f->getExtension() !== 'shp') continue;
            if (fnmatch($pattern, $f->getFilename())) {
                $result[] = $f->getPathname();
            }
        }

        sort($result);
        return $result;
    }

    private function extractAllZips(string $zipPath, string $outputDir): void
    {
        $tempSub = $outputDir . DIRECTORY_SEPARATOR . pathinfo($zipPath, PATHINFO_FILENAME);
        @mkdir($tempSub, 0755, true);

        $zip = new \ZipArchive();
        if ($zip->open($zipPath) !== true) return;
        $zip->extractTo($tempSub);
        $zip->close();

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($tempSub, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($files as $f) {
            if ($f->getExtension() === 'zip') {
                $this->extractAllZips($f->getPathname(), $outputDir);
            }
        }
    }

    private function detectGeomType(string $shpPath): ?string
    {
        $cmd = sprintf(
            '"%s" -so -json "%s" 2>%s',
            $this->gdal,
            $shpPath,
            PHP_OS_FAMILY === 'Windows' ? 'nul' : '/dev/null'
        );
        $output = [];
        exec($cmd, $output, $exitCode);
        if ($exitCode !== 0 || empty($output)) return null;

        $json = implode('', $output);
        $data = json_decode($json, true);
        if (!$data || !isset($data['layers'][0]['geometryFields'][0]['type'])) return null;

        $rawType = $data['layers'][0]['geometryFields'][0]['type'];
        // Normalize "3D Polygon" -> "Polygon", etc.
        $clean = preg_replace('/^(3D|2D)\s*/', '', $rawType);
        $clean = str_replace('String', 'String', $clean);

        $validTypes = ['Point', 'LineString', 'Polygon', 'MultiPoint', 'MultiLineString', 'MultiPolygon'];
        $clean = str_replace('Line String', 'LineString', $clean);

        return in_array($clean, $validTypes) ? $clean : null;
    }

    private function importShpToLayer(string $shpPath, Layer $layer): int
    {
        $geojsonPath = $this->tempDir . DIRECTORY_SEPARATOR . uniqid() . '.geojson';

        $cmd = sprintf(
            '"%s" -f GeoJSON "%s" "%s" -t_srs EPSG:4326 -dim XY -lco COORDINATE_PRECISION=6 2>%s',
            $this->gdal,
            $geojsonPath,
            $shpPath,
            PHP_OS_FAMILY === 'Windows' ? 'nul' : '/dev/null'
        );
        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0 || !file_exists($geojsonPath)) {
            $this->warn("  GDAL convert failed for $shpPath, trying without reprojection...");
            $cmd = sprintf(
                '"%s" -f GeoJSON "%s" "%s" -dim XY -lco COORDINATE_PRECISION=6 2>%s',
                $this->gdal,
                $geojsonPath,
                $shpPath,
                PHP_OS_FAMILY === 'Windows' ? 'nul' : '/dev/null'
            );
            exec($cmd, $output, $exitCode);
        }

        if ($exitCode !== 0 || !file_exists($geojsonPath)) {
            $this->error("  GDAL convert failed.");
            return 0;
        }

        $geojson = json_decode(file_get_contents($geojsonPath), true);
        unlink($geojsonPath);

        if (!$geojson || !isset($geojson['features'])) return 0;

        $imported = 0;
        foreach ($geojson['features'] as $feature) {
            $geometry = $feature['geometry'] ?? null;
            $props = $feature['properties'] ?? [];

            if (!$geometry || !isset($geometry['type'])) continue;

            $geometry = $this->normalizeGeometry($geometry);
            $nama = $props['nama'] ?? $props['Kelurahan'] ?? $props['Nama_Subje'] ?? $props['Bencana'] ?? $props['Lokasi'] ?? ('Fitur ' . ($imported + 1));
            $deskripsi = $props['deskripsi'] ?? $props['Keterangan'] ?? $props['Alamat'] ?? null;

            try {
                $ds = DataSpasial::create([
                    'layer_id' => $layer->id,
                    'nama' => $nama,
                    'deskripsi' => $deskripsi,
                    'status' => 'aktif',
                    'created_by' => $this->adminId,
                ]);

                DB::statement("
                    UPDATE data_spasial SET geometry = ST_Force2D(ST_SetSRID(ST_GeomFromGeoJSON(?), 4326)) WHERE id = ?
                ", [json_encode($geometry), $ds->id]);

                $imported++;
            } catch (\Exception $e) {
                continue;
            }
        }

        return $imported;
    }

    private function deriveLayerName(string $baseName): string
    {
        $name = str_replace('_', ' ', $baseName);
        $name = preg_replace('/[_-]/', ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }

    private function normalizeGeometry(array $geometry): array
    {
        $typeMap = [
            'PointM' => 'Point', 'PointZM' => 'Point',
            'MultiPointM' => 'MultiPoint', 'MultiPointZM' => 'MultiPoint',
            'LineStringM' => 'LineString', 'LineStringZM' => 'LineString',
            'MultiLineStringM' => 'MultiLineString', 'MultiLineStringZM' => 'MultiLineString',
            'PolygonM' => 'Polygon', 'PolygonZM' => 'Polygon',
            'MultiPolygonM' => 'MultiPolygon', 'MultiPolygonZM' => 'MultiPolygon',
            'GeometryCollectionM' => 'GeometryCollection', 'GeometryCollectionZM' => 'GeometryCollection',
        ];

        if (isset($typeMap[$geometry['type']])) {
            $geometry['type'] = $typeMap[$geometry['type']];
            $geometry['coordinates'] = $this->stripMCoordinates($geometry['coordinates']);
        }

        return $geometry;
    }

    private function stripMCoordinates($coords)
    {
        if (!is_array($coords)) return $coords;

        $first = reset($coords);
        if (is_array($first) && is_numeric(reset($first))) {
            return array_map(function ($c) {
                return array_slice($c, 0, 3);
            }, $coords);
        }

        return array_map([$this, 'stripMCoordinates'], $coords);
    }

    private function nextColor(): string
    {
        $color = $this->colorPalette[$this->colorIndex % count($this->colorPalette)];
        $this->colorIndex++;
        return $color;
    }

    private function rmdirRecursive(string $dir): void
    {
        if (!is_dir($dir)) return;
        foreach (new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        ) as $file) {
            $file->isDir() ? @rmdir($file->getPathname()) : @unlink($file->getPathname());
        }
        @rmdir($dir);
    }
}
