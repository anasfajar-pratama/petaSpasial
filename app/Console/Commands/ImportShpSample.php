<?php

namespace App\Console\Commands;

use App\Models\Layer;
use App\Models\DataSpasial;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportShpSample extends Command
{
    protected $signature = 'import:shp-sample';
    protected $description = 'Pre-import SHP sample data ke database';

    private string $gdal = 'C:/Program Files/PostgreSQL/17/bin/ogr2ogr';
    private string $tempDir;

    public function handle()
    {
        $this->tempDir = storage_path('app/temp/preimport-' . uniqid());
        mkdir($this->tempDir, 0755, true);

        $definitions = [
            [
                'name' => 'Batas Administrasi',
                'geom_type' => 'Polygon',
                'color' => '#dc2626',
                'kategori_id' => 7,
                'description' => 'Batas administrasi kelurahan Kota Sukabumi',
                'zip' => 'F:\petaSpasial\SHP\Batas Administrasi-20260722T022416Z-1-001.zip',
                'shp_pattern' => '*_AR.shp',
            ],
            [
                'name' => 'Batas Administrasi Garis',
                'geom_type' => 'LineString',
                'color' => '#dc2626',
                'kategori_id' => 7,
                'description' => 'Garis batas administrasi Kota Sukabumi',
                'zip' => 'F:\petaSpasial\SHP\Batas Administrasi-20260722T022416Z-1-001.zip',
                'shp_pattern' => '*_LN.shp',
            ],
            [
                'name' => 'Histori Bencana',
                'geom_type' => 'Point',
                'color' => '#f59e0b',
                'kategori_id' => 1,
                'description' => 'Data histori bencana Kota Sukabumi 2016-2024',
                'zip' => 'F:\petaSpasial\SHP\SHP Histori Bencana 2016-2024-20260722T022432Z-1-001.zip',
                'shp_pattern' => '*.shp',
            ],
            [
                'name' => 'IMB',
                'geom_type' => 'Point',
                'color' => '#3b82f6',
                'kategori_id' => 6,
                'description' => 'Izin Mendirikan Bangunan Kota Sukabumi',
                'zip' => 'F:\petaSpasial\SHP\SHP IMB-20260722T022505Z-1-001.zip',
                'shp_pattern' => '*.shp',
            ],
            [
                'name' => 'Toponimi',
                'geom_type' => 'Point',
                'color' => '#8b5cf6',
                'kategori_id' => 5,
                'description' => 'Data toponimi (nama tempat) Kota Sukabumi',
                'zip' => 'F:\petaSpasial\SHP\SHP Toponimi-20260722T031649Z-1-001.zip',
                'shp_pattern' => '*.shp',
            ],
        ];

        foreach ($definitions as $def) {
            $this->info("Processing: {$def['name']}...");

            $slug = Str::slug($def['name']);

            $existing = Layer::where('slug', $slug)->orWhere('nama', $def['name'])->first();

            if ($existing) {
                $this->info("  Layer '{$def['name']}' already exists, skipping.");
                if ($existing->dataSpasial()->count() > 0) {
                    $this->info("  Layer already has {$existing->dataSpasial()->count()} data, skipping import.");
                    continue;
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
                    'deskripsi' => $def['description'],
                    'order' => Layer::max('order') + 1,
                    'created_by' => 1,
                ]);
                $this->info("  Layer '{$def['name']}' created.");
            }

            if ($layer->wasRecentlyCreated) {
                $this->info("  Layer '{$def['name']}' created.");
            }

            $shpFile = $this->extractShp($def['zip'], $def['shp_pattern']);
            if (!$shpFile) {
                $this->error("  SHP not found in {$def['zip']}");
                continue;
            }

            $count = $this->importShpToLayer($shpFile, $layer);
            $this->info("  Imported {$count} features to '{$def['name']}'.");
        }

        $this->rmdirRecursive($this->tempDir);
        $this->info('Done.');
    }

    private function extractShp(string $zipPath, string $pattern): ?string
    {
        $extractDir = $this->tempDir . DIRECTORY_SEPARATOR . uniqid();
        mkdir($extractDir, 0755, true);

        // Extract all levels of nesting
        $this->extractAllZips($zipPath, $extractDir);

        // Find matching .shp file recursively
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($extractDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($files as $f) {
            if ($f->getExtension() !== 'shp') continue;
            if (fnmatch($pattern, $f->getFilename())) {
                return $f->getPathname();
            }
        }

        return null;
    }

    private function extractAllZips(string $zipPath, string $outputDir): void
    {
        $tempSub = $outputDir . DIRECTORY_SEPARATOR . pathinfo($zipPath, PATHINFO_FILENAME);
        mkdir($tempSub, 0755, true);

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

    private function importShpToLayer(string $shpPath, Layer $layer): int
    {
        $geojsonPath = $this->tempDir . DIRECTORY_SEPARATOR . uniqid() . '.geojson';

        $cmd = sprintf(
            '"%s" -f GeoJSON "%s" "%s" -t_srs EPSG:4326 -lco COORDINATE_PRECISION=6 2>nul',
            $this->gdal . '.exe',
            $geojsonPath,
            $shpPath
        );
        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0 || !file_exists($geojsonPath)) {
            $this->warn("  GDAL convert failed for $shpPath, trying without reprojection...");
            $cmd = sprintf(
                '"%s" -f GeoJSON "%s" "%s" -lco COORDINATE_PRECISION=6 2>nul',
                $this->gdal . '.exe',
                $geojsonPath,
                $shpPath
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

            $nama = $props['nama'] ?? $props['Kelurahan'] ?? $props['Nama_Subje'] ?? $props['Bencana'] ?? ('Fitur ' . ($imported + 1));
            $deskripsi = $props['deskripsi'] ?? $props['Keterangan'] ?? $props['Alamat'] ?? null;

            try {
                $ds = DataSpasial::create([
                    'layer_id' => $layer->id,
                    'nama' => $nama,
                    'deskripsi' => $deskripsi,
                    'status' => 'aktif',
                    'created_by' => 1,
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

    private function rmdirRecursive(string $dir): void
    {
        if (!is_dir($dir)) return;
        foreach (new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        ) as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($dir);
    }
}
