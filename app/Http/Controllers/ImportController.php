<?php

namespace App\Http\Controllers;

use App\Models\DataSpasial;
use App\Models\Layer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function index()
    {
        $layers = Layer::where('is_active', true)->orderBy('order')->get();
        return view('import.index', compact('layers'));
    }

    public function importGeoJson(Request $request)
    {
        $data = $request->validate([
            'layer_id' => 'required|exists:layers,id',
            'file' => 'required|file|mimes:json,geojson,txt|max:51200',
        ]);

        $layer = Layer::findOrFail($data['layer_id']);
        $contents = file_get_contents($request->file('file')->getRealPath());
        $geojson = json_decode($contents, true);

        if (!$geojson || !isset($geojson['type'])) {
            return back()->with('error', 'Format GeoJSON tidak valid.');
        }

        $features = $geojson['type'] === 'FeatureCollection'
            ? ($geojson['features'] ?? [])
            : ($geojson['type'] === 'Feature' ? [$geojson] : []);

        if (empty($features)) {
            return back()->with('error', 'Tidak ada fitur dalam file.');
        }

        $imported = 0;
        $errors = [];

        foreach ($features as $i => $feature) {
            $geometry = $feature['geometry'] ?? null;
            $props = $feature['properties'] ?? [];

            if (!$geometry || !isset($geometry['type'])) {
                $errors[] = "Fitur ke-" . ($i + 1) . ": geometry tidak valid";
                continue;
            }

            $geomType = $this->detectType($geometry['type']);
            if ($geomType !== $layer->geom_type) {
                $errors[] = "Fitur ke-" . ($i + 1) . ": {$geometry['type']} tidak sesuai layer {$layer->geom_type}";
                continue;
            }

            try {
                $ds = DataSpasial::create([
                    'layer_id' => $layer->id,
                    'nama' => $props['nama'] ?? $props['name'] ?? $props['Nama'] ?? ('Fitur ' . ($i + 1)),
                    'deskripsi' => $props['deskripsi'] ?? $props['description'] ?? null,
                    'status' => 'aktif',
                    'created_by' => auth()->id(),
                ]);

                DB::statement("
                    UPDATE data_spasial SET geometry = ST_Force2D(ST_SetSRID(ST_GeomFromGeoJSON(?), 4326)) WHERE id = ?
                ", [json_encode($geometry), $ds->id]);

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Fitur ke-" . ($i + 1) . ": " . $e->getMessage();
            }
        }

        $message = "Berhasil mengimport $imported data.";
        if (!empty($errors)) {
            $message .= " Gagal: " . implode(', ', array_slice($errors, 0, 5));
        }

        $this->logImport($layer->id, 'geojson', $imported, $errors);

        return back()->with('success', $message);
    }

    public function importCsv(Request $request)
    {
        $data = $request->validate([
            'layer_id' => 'required|exists:layers,id',
            'file' => 'required|file|mimes:csv,txt|max:51200',
        ]);

        $layer = Layer::findOrFail($data['layer_id']);
        if ($layer->geom_type !== 'Point') {
            return back()->with('error', 'CSV hanya untuk layer Point.');
        }

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $headers = fgetcsv($handle);
        $latIdx = array_search('latitude', array_map('strtolower', $headers));
        $lngIdx = array_search('longitude', array_map('strtolower', $headers));
        $namaIdx = array_search('nama', array_map('strtolower', $headers));

        if ($latIdx === false || $lngIdx === false) {
            fclose($handle);
            return back()->with('error', 'CSV harus memiliki kolom latitude dan longitude.');
        }

        $imported = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $lat = (float) ($row[$latIdx] ?? 0);
            $lng = (float) ($row[$lngIdx] ?? 0);

            if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) continue;

            $nama = $namaIdx !== false ? ($row[$namaIdx] ?? 'Titik ' . ($imported + 1)) : 'Titik ' . ($imported + 1);

            try {
                $ds = DataSpasial::create([
                    'layer_id' => $layer->id,
                    'nama' => $nama,
                    'status' => 'aktif',
                    'created_by' => auth()->id(),
                ]);

                DB::statement("
                    UPDATE data_spasial SET geometry = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE id = ?
                ", [$lng, $lat, $ds->id]);

                $imported++;
            } catch (\Exception $e) {
                continue;
            }
        }

        fclose($handle);
        $this->logImport($layer->id, 'csv', $imported);
        return back()->with('success', "Berhasil mengimport $imported titik dari CSV.");
    }

    public function importKml(Request $request)
    {
        $data = $request->validate([
            'layer_id' => 'required|exists:layers,id',
            'file' => 'required|file|mimes:kml,kmz|max:51200',
        ]);

        $layer = Layer::findOrFail($data['layer_id']);
        $tempDir = storage_path('app/temp/kml-import-' . uniqid());
        mkdir($tempDir, 0755, true);

        try {
            $kmlPath = $request->file('file')->getRealPath();
            $geojsonPath = $tempDir . DIRECTORY_SEPARATOR . 'output.geojson';

            $gdalBin = config('gdal.bin');
            $useGdal = config('gdal.enabled');

            if ($useGdal) {
                $cmd = sprintf(
                    '"%s" -f GeoJSON "%s" "%s" -t_srs EPSG:4326 -lco COORDINATE_PRECISION=6 2>nul',
                    $gdalBin . '.exe',
                    $geojsonPath,
                    $kmlPath
                );
                exec($cmd, $output, $exitCode);

                if ($exitCode !== 0 || !file_exists($geojsonPath)) {
                    $this->rmdirRecursive($tempDir);
                    return back()->with('error', 'Gagal mengkonversi KML. Pastikan GDAL terinstall dan file KML valid.');
                }
            } else {
                $this->rmdirRecursive($tempDir);
                return back()->with('error', 'GDAL diperlukan untuk import KML.');
            }

            $geojson = json_decode(file_get_contents($geojsonPath), true);
            $this->rmdirRecursive($tempDir);

            if (!$geojson || !isset($geojson['features'])) {
                return back()->with('error', 'Tidak ada fitur dalam file KML.');
            }

            $totalImported = $this->importFeatures($geojson['features'], $layer);
            $this->logImport($layer->id, 'kml', $totalImported);
            return back()->with('success', "Berhasil mengimport $totalImported data dari KML ke layer '{$layer->nama}'.");
        } catch (\Exception $e) {
            $this->rmdirRecursive($tempDir);
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function importShp(Request $request)
    {
        $data = $request->validate([
            'layer_id' => 'required|exists:layers,id',
            'file' => 'required|file|mimes:zip|max:102400',
        ]);

        $layer = Layer::findOrFail($data['layer_id']);
        $tempDir = storage_path('app/temp/shp-import-' . uniqid());
        mkdir($tempDir, 0755, true);

        try {
            $zipPath = $request->file('file')->getRealPath();
            $this->extractAllZips($zipPath, $tempDir);
            $shpFiles = $this->findShpFiles($tempDir);

            if (empty($shpFiles)) {
                return back()->with('error', 'Tidak ada file .shp dalam zip.');
            }

            $totalImported = 0;
            $gdalBin = config('gdal.bin');
            $useGdal = config('gdal.enabled');

            foreach ($shpFiles as $shpPath) {
                $geojsonPath = $tempDir . DIRECTORY_SEPARATOR . uniqid() . '.geojson';
                $errFile = $tempDir . DIRECTORY_SEPARATOR . uniqid() . '.err';

                if ($useGdal) {
                    $cmd = sprintf(
                        '"%s" -f GeoJSON "%s" "%s" -t_srs EPSG:4326 -lco COORDINATE_PRECISION=6 2>"%s"',
                        $gdalBin . '.exe',
                        $geojsonPath,
                        $shpPath,
                        $errFile
                    );
                    exec($cmd, $output, $exitCode);

                    if ($exitCode !== 0 || !file_exists($geojsonPath)) {
                        $errMsg = file_exists($errFile) ? file_get_contents($errFile) : 'unknown error';
                        $cmd = sprintf(
                            '"%s" -f GeoJSON "%s" "%s" -lco COORDINATE_PRECISION=6 2>"%s"',
                            $gdalBin . '.exe',
                            $geojsonPath,
                            $shpPath,
                            $errFile
                        );
                        exec($cmd, $output, $exitCode);
                        if ($exitCode !== 0 || !file_exists($geojsonPath)) {
                            $errMsg2 = file_exists($errFile) ? file_get_contents($errFile) : 'unknown error';
                            return back()->with('error', "GDAL convert gagal: $errMsg2")->withInput();
                        }
                    }
                } else {
                    $this->convertShpViaLib($shpPath, $geojsonPath);
                }

                @unlink($errFile);
                if (!file_exists($geojsonPath)) continue;

                $geojson = json_decode(file_get_contents($geojsonPath), true);
                unlink($geojsonPath);

                if (!$geojson || !isset($geojson['features'])) continue;

                $imported = $this->importFeatures($geojson['features'], $layer);
                $totalImported += $imported;
            }

            $this->rmdirRecursive($tempDir);
            $this->logImport($layer->id, 'shp', $totalImported);
            return back()->with('success', "Berhasil mengimport $totalImported data dari SHP ke layer '{$layer->nama}'.");
        } catch (\Exception $e) {
            $this->rmdirRecursive($tempDir);
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
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

    private function findShpFiles(string $dir): array
    {
        $shpFiles = [];
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($files as $f) {
            if ($f->getExtension() === 'shp') {
                $shpFiles[] = $f->getPathname();
            }
        }
        return $shpFiles;
    }

    private function importFeatures(array $features, Layer $layer): int
    {
        $imported = 0;
        foreach ($features as $feature) {
            $geometry = $feature['geometry'] ?? null;
            $props = $feature['properties'] ?? [];

            if (!$geometry || !isset($geometry['type'])) continue;

            $nama = $props['nama'] ?? $props['Nama'] ?? $props['name'] ?? $props['Kelurahan'] ?? $props['Nama_Subje'] ?? $props['Bencana'] ?? ('Fitur ' . ($imported + 1));
            $deskripsi = $props['deskripsi'] ?? $props['Deskripsi'] ?? $props['Keterangan'] ?? $props['Alamat'] ?? null;

            $exists = DB::table('data_spasial')
                ->where('layer_id', $layer->id)
                ->where('nama', $nama)
                ->whereRaw("ST_Equals(geometry, ST_SetSRID(ST_GeomFromGeoJSON(?), 4326))", [json_encode($geometry)])
                ->exists();

            if ($exists) continue;

            try {
                $ds = DataSpasial::create([
                    'layer_id' => $layer->id,
                    'nama' => $nama,
                    'deskripsi' => $deskripsi,
                    'status' => 'aktif',
                    'created_by' => auth()->id(),
                ]);

                DB::statement("
                    UPDATE data_spasial SET geometry = ST_Force2D(ST_SetSRID(ST_GeomFromGeoJSON(?), 4326)) WHERE id = ?
                ", [json_encode($geometry), $ds->id]);

                $check = DB::select("SELECT geometry IS NOT NULL AS ok FROM data_spasial WHERE id = ?", [$ds->id]);
                if (!empty($check) && $check[0]->ok) {
                    $imported++;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        return $imported;
    }

    private function convertShpViaLib(string $shpPath, string $geojsonPath): void
    {
        $reader = new \Shapefile\ShapefileReader($shpPath);
        $features = [];

        while ($record = $reader->fetchRecord()) {
            $shapeData = $record->getGeoJSON();
            if (!$shapeData) continue;

            $geom = json_decode($shapeData, true);
            $dbfData = $record->getDataArray();

            if ($geom) {
                $features[] = [
                    'type' => 'Feature',
                    'geometry' => $geom,
                    'properties' => $dbfData,
                ];
            }
        }

        $reader->close();

        file_put_contents($geojsonPath, json_encode([
            'type' => 'FeatureCollection',
            'features' => $features,
        ], JSON_UNESCAPED_UNICODE));
    }

    private function rmdirRecursive(string $dir): void
    {
        if (!is_dir($dir)) return;
        foreach (new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        ) as $f) {
            $f->isDir() ? rmdir($f->getPathname()) : unlink($f->getPathname());
        }
        rmdir($dir);
    }

    private function detectType(string $type): string
    {
        return match ($type) {
            'Point', 'MultiPoint' => 'Point',
            'LineString', 'MultiLineString' => 'LineString',
            'Polygon' => 'Polygon',
            'MultiPolygon' => 'MultiPolygon',
            default => 'Point',
        };
    }

    private function logImport(int $layerId, string $format, int $imported, array $errors = []): void
    {
        try {
            \App\Models\ImportLog::create([
                'user_id' => auth()->id(),
                'layer_id' => $layerId,
                'format' => $format,
                'filename' => request()->file('file')->getClientOriginalName(),
                'total_imported' => $imported,
                'total_duplicates' => 0,
                'total_errors' => count($errors),
                'errors' => !empty($errors) ? implode('; ', array_slice($errors, 0, 20)) : null,
            ]);
        } catch (\Exception $e) {
            // silent
        }
    }
}
