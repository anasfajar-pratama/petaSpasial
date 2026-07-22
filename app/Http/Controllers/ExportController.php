<?php

namespace App\Http\Controllers;

use App\Models\DataSpasial;
use App\Models\Layer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use League\Csv\Writer;
use Shapefile\Shapefile;
use Shapefile\ShapefileWriter;
use Shapefile\Geometry\Point;
use Shapefile\Geometry\Linestring;
use Shapefile\Geometry\Polygon;

class ExportController extends Controller
{
    public function index()
    {
        $layers = Layer::withCount('dataSpasial')->orderBy('order')->get();
        $gdalEnabled = config('gdal.enabled');
        return view('admin.export', compact('layers', 'gdalEnabled'));
    }

    public function exportData(Request $request, $layerId = null, $format = 'geojson')
    {
        if ($layerId === 'all') {
            $data = DataSpasial::with('layer', 'kategori')->get();
            $filename = 'semua-data';
        } else {
            $layer = Layer::findOrFail($layerId);
            $data = DataSpasial::with('layer', 'kategori')
                ->where('layer_id', $layerId)->get();
            $filename = Str::slug($layer->nama);
        }

        return match ($format) {
            'geojson' => $this->exportGeoJson($data, $filename),
            'csv' => $this->exportCsv($data, $filename),
            'excel' => $this->exportExcel($data, $filename),
            'pdf' => $this->exportPdf($data, $filename),
            'shp' => $this->exportShp($data, $filename),
            default => abort(404),
        };
    }

    public function exportLayer(Request $request, $layerId, $format)
    {
        return $this->exportData($request, $layerId, $format);
    }

    public function exportAll(Request $request, $format)
    {
        if ($format === 'shp') {
            $layers = Layer::with('dataSpasial.kategori')->whereHas('dataSpasial')->orderBy('order')->get();
            $tempDir = storage_path('app/temp/shp-all-' . uniqid());
            mkdir($tempDir, 0755, true);

            foreach ($layers as $layer) {
                $this->writeShapefile($layer->dataSpasial, Str::slug($layer->nama), $tempDir);
            }

            $zipPath = $tempDir . '.zip';
            $this->createZipFromDir($tempDir, $zipPath);

            $response = response()->download($zipPath, 'semua-data-shp.zip')->deleteFileAfterSend(true);
            $this->rmdirRecursive($tempDir);
            return $response;
        }

        return $this->exportData($request, 'all', $format);
    }

    private function exportGeoJson($data, $filename)
    {
        $features = $data->map(function ($item) {
            $geom = DB::select("SELECT ST_AsGeoJSON(geometry) AS geojson FROM data_spasial WHERE id = ?", [$item->id]);
            $geometry = $geom ? json_decode($geom[0]->geojson, true) : null;

            return [
                'type' => 'Feature',
                'geometry' => $geometry,
                'properties' => [
                    'id' => $item->id,
                    'layer' => $item->layer?->nama,
                    'nama' => $item->nama,
                    'deskripsi' => $item->deskripsi,
                    'kategori' => $item->kategori?->nama,
                    'status' => $item->status,
                    'tahun' => $item->tahun,
                    'luas' => $item->luas,
                    'created_at' => $item->created_at?->toISOString(),
                ],
            ];
        });

        $geojson = [
            'type' => 'FeatureCollection',
            'features' => $features->toArray(),
        ];

        return response()->streamDownload(function () use ($geojson) {
            echo json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename . '.geojson', ['Content-Type' => 'application/geo+json']);
    }

    private function exportCsv($data, $filename)
    {
        $writer = Writer::createFromString('');

        $headers = ['id', 'layer', 'nama', 'deskripsi', 'kategori', 'status', 'tahun', 'luas', 'latitude', 'longitude', 'created_at'];
        $writer->insertOne($headers);

        $data->each(function ($item) use ($writer) {
            $geom = DB::select("SELECT ST_X(ST_Centroid(geometry)) AS lng, ST_Y(ST_Centroid(geometry)) AS lat FROM data_spasial WHERE id = ?", [$item->id]);
            $lat = $geom[0]->lat ?? null;
            $lng = $geom[0]->lng ?? null;

            $writer->insertOne([
                $item->id,
                $item->layer?->nama,
                $item->nama,
                $item->deskripsi,
                $item->kategori?->nama,
                $item->status,
                $item->tahun,
                $item->luas,
                $lat,
                $lng,
                $item->created_at?->toDateTimeString(),
            ]);
        });

        return response()->streamDownload(function () use ($writer) {
            echo $writer->toString();
        }, $filename . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function exportExcel($data, $filename)
    {
        $rows = [];
        $rows[] = ['id', 'layer', 'nama', 'deskripsi', 'kategori', 'status', 'tahun', 'luas', 'latitude', 'longitude', 'created_at'];

        $data->each(function ($item) use (&$rows) {
            $geom = DB::select("SELECT ST_X(ST_Centroid(geometry)) AS lng, ST_Y(ST_Centroid(geometry)) AS lat FROM data_spasial WHERE id = ?", [$item->id]);

            $rows[] = [
                $item->id,
                $item->layer?->nama,
                $item->nama,
                $item->deskripsi,
                $item->kategori?->nama,
                $item->status,
                $item->tahun,
                $item->luas,
                $geom[0]->lat ?? '',
                $geom[0]->lng ?? '',
                $item->created_at?->toDateTimeString(),
            ];
        });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"';
        $xml .= ' xmlns:o="urn:schemas-microsoft-com:office:office"';
        $xml .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"';
        $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        $xml .= ' <Worksheet ss:Name="Data Spasial">' . "\n";
        $xml .= '  <Table>' . "\n";

        foreach ($rows as $row) {
            $xml .= '   <Row>' . "\n";
            foreach ($row as $cell) {
                $xml .= '    <Cell><Data ss:Type="String">' . htmlspecialchars((string) $cell) . '</Data></Cell>' . "\n";
            }
            $xml .= '   </Row>' . "\n";
        }

        $xml .= '  </Table>' . "\n";
        $xml .= ' </Worksheet>' . "\n";
        $xml .= '</Workbook>';

        return response()->streamDownload(function () use ($xml) {
            echo $xml;
        }, $filename . '.xls', ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);
    }

    private function exportPdf($data, $filename)
    {
        $pdf = Pdf::loadView('export.pdf', compact('data'));
        return $pdf->download($filename . '.pdf');
    }

    private function exportShp($data, $filename)
    {
        $tempDir = storage_path('app/temp/shp-' . uniqid());
        mkdir($tempDir, 0755, true);

        $this->writeShapefile($data, $filename, $tempDir);

        $zipPath = $tempDir . '.zip';
        $this->createZipFromDir($tempDir, $zipPath);

        $response = response()->download($zipPath, $filename . '-shp.zip')->deleteFileAfterSend(true);
        $this->rmdirRecursive($tempDir);
        return $response;
    }

    private function writeShapefile($data, $filename, string $outputDir): void
    {
        $gdalBin = config('gdal.bin');
        $useGdal = config('gdal.enabled') && file_exists($gdalBin . '.exe');

        if ($useGdal) {
            $this->writeShapefileViaGdal($data, $filename, $outputDir, $gdalBin);
        } else {
            $this->writeShapefileViaLib($data, $filename, $outputDir);
        }
    }

    private function writeShapefileViaGdal($data, string $filename, string $outputDir, string $gdalBin): void
    {
        $geojsonPath = $outputDir . DIRECTORY_SEPARATOR . $filename . '.geojson';
        $shpPath = $outputDir . DIRECTORY_SEPARATOR . $filename . '.shp';

        $features = $data->map(function ($item) use (&$dbfFields) {
            $geom = DB::select("SELECT ST_AsGeoJSON(geometry) AS geojson FROM data_spasial WHERE id = ?", [$item->id]);
            return [
                'type' => 'Feature',
                'geometry' => $geom ? json_decode($geom[0]->geojson, true) : null,
                'properties' => [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'deskripsi' => $item->deskripsi,
                    'kategori' => $item->kategori?->nama,
                    'status' => $item->status,
                    'tahun' => $item->tahun,
                    'luas' => $item->luas,
                ],
            ];
        });

        file_put_contents($geojsonPath, json_encode([
            'type' => 'FeatureCollection',
            'features' => $features->toArray(),
        ], JSON_UNESCAPED_UNICODE));

        $cmd = sprintf(
            '"%s" -f "ESRI Shapefile" "%s" "%s" -lco ENCODING=UTF-8 2>nul',
            $gdalBin . '.exe',
            $shpPath,
            $geojsonPath
        );
        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            throw new \RuntimeException('GDAL error: ' . implode("\n", $output));
        }

        unlink($geojsonPath);
    }

    private function writeShapefileViaLib($data, string $filename, string $outputDir): void
    {
        $shpPath = $outputDir . DIRECTORY_SEPARATOR . $filename;

        $geomType = $data->first()?->layer?->geom_type ?? 'Point';
        $shapeType = match ($geomType) {
            'Point' => Shapefile::SHAPE_TYPE_POINT,
            'LineString' => Shapefile::SHAPE_TYPE_POLYLINE,
            'Polygon', 'MultiPolygon' => Shapefile::SHAPE_TYPE_POLYGON,
            default => Shapefile::SHAPE_TYPE_POINT,
        };

        $fields = [
            ['name' => 'id', 'type' => 'N', 'size' => 10, 'decimals' => 0],
            ['name' => 'nama', 'type' => 'C', 'size' => 100, 'decimals' => 0],
            ['name' => 'deskripsi', 'type' => 'C', 'size' => 255, 'decimals' => 0],
            ['name' => 'kategori', 'type' => 'C', 'size' => 50, 'decimals' => 0],
            ['name' => 'status', 'type' => 'C', 'size' => 20, 'decimals' => 0],
        ];

        $shp = ShapefileWriter::init($shpPath, $shapeType, $fields);

        $data->each(function ($item) use ($shp) {
            $geom = DB::select("SELECT ST_AsGeoJSON(geometry) AS geojson FROM data_spasial WHERE id = ?", [$item->id]);
            if (!$geom) return;

            $g = json_decode($geom[0]->geojson, true);
            if (!$g || !isset($g['type'])) return;

            $geometry = $this->geomFromGeoJson($g);

            if ($geometry) {
                $shp->writeRecord([
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'deskripsi' => mb_substr($item->deskripsi ?? '', 0, 255),
                    'kategori' => $item->kategori?->nama ?? '',
                    'status' => $item->status ?? '',
                ], $geometry);
            }
        });

        $shp->close();
    }

    private function geomFromGeoJson(array $g): ?\Shapefile\Geometry\Geometry
    {
        try {
            return match ($g['type']) {
                'Point' => new Point($g['coordinates'][0], $g['coordinates'][1]),
                'LineString' => new Linestring(array_map(fn($c) => [$c[0], $c[1]], $g['coordinates'])),
                'Polygon' => $this->polygonFromCoords($g['coordinates']),
                'MultiPolygon' => new Polygon(...array_map(fn($ring) => $this->polygonFromCoords($ring), $g['coordinates'])),
                default => null,
            };
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function polygonFromCoords(array $coords): Polygon
    {
        $pgon = new Polygon();
        foreach ($coords as $ring) {
            $linestring = new Linestring(array_map(fn($c) => [$c[0], $c[1]], $ring));
            $pgon->addRing($linestring);
        }
        return $pgon;
    }

    private function createZipFromDir(string $dir, string $zipPath): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('Cannot create zip file');
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($files as $file) {
            $relativePath = substr($file->getPathname(), strlen($dir) + 1);
            $zip->addFile($file->getPathname(), $relativePath);
        }

        $zip->close();
    }

    private function rmdirRecursive(string $dir): void
    {
        if (!is_dir($dir)) return;
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($dir);
    }
}
