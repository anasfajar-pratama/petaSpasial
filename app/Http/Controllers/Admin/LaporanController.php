<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataSpasial;
use App\Models\District;
use App\Models\Layer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $layerId = $request->get('layer_id');
        $districtId = $request->get('district_id');
        $tahunFrom = $request->get('tahun_from');
        $tahunTo = $request->get('tahun_to');

        $where = "WHERE ds.geometry IS NOT NULL ";
        $bindings = [];

        if ($layerId) {
            $where .= " AND ds.layer_id = ?";
            $bindings[] = $layerId;
        }
        if ($districtId) {
            $where .= " AND ds.district_id = ?";
            $bindings[] = $districtId;
        }
        if ($tahunFrom) {
            $where .= " AND ds.tahun >= ?";
            $bindings[] = $tahunFrom;
        }
        if ($tahunTo) {
            $where .= " AND ds.tahun <= ?";
            $bindings[] = $tahunTo;
        }

        $rekapLayer = DB::select("
            SELECT l.id, l.nama, l.warna, l.geom_type, COUNT(ds.id) AS total
            FROM data_spasial ds
            JOIN layers l ON l.id = ds.layer_id
            $where
            GROUP BY l.id, l.nama, l.warna, l.geom_type
            ORDER BY total DESC
        ", $bindings);

        $rekapWilayah = DB::select("
            SELECT d.id, d.nama, COUNT(ds.id) AS total
            FROM data_spasial ds
            JOIN districts d ON d.id = ds.district_id
            WHERE ds.geometry IS NOT NULL  AND ds.district_id IS NOT NULL
            GROUP BY d.id, d.nama
            ORDER BY total DESC
        ");

        $rekapKategori = DB::select("
            SELECT kl.id, kl.nama, kl.warna_default, COUNT(ds.id) AS total
            FROM data_spasial ds
            JOIN layers l ON l.id = ds.layer_id
            JOIN kategori_layers kl ON kl.id = l.kategori_id
            $where
            GROUP BY kl.id, kl.nama, kl.warna_default
            ORDER BY total DESC
        ", $bindings);

        $layers = Layer::whereNull('deleted_at')->orderBy('nama')->get(['id', 'nama']);
        $districts = District::where('regency_id', env('ACTIVE_REGENCY_ID', 1))->orderBy('nama')->get(['id', 'nama']);
        $tahuns = DB::select("SELECT DISTINCT tahun FROM data_spasial WHERE tahun IS NOT NULL ORDER BY tahun DESC");

        return view('admin.laporan.index', compact(
            'rekapLayer', 'rekapWilayah', 'rekapKategori',
            'layers', 'districts', 'tahuns',
            'layerId', 'districtId', 'tahunFrom', 'tahunTo'
        ));
    }

    public function cetakPdf(Request $request)
    {
        $layerId = $request->get('layer_id');
        $districtId = $request->get('district_id');
        $tahunFrom = $request->get('tahun_from');
        $tahunTo = $request->get('tahun_to');

        $where = "WHERE ds.geometry IS NOT NULL ";
        $bindings = [];

        if ($layerId) {
            $where .= " AND ds.layer_id = ?";
            $bindings[] = $layerId;
        }
        if ($districtId) {
            $where .= " AND ds.district_id = ?";
            $bindings[] = $districtId;
        }
        if ($tahunFrom) {
            $where .= " AND ds.tahun >= ?";
            $bindings[] = $tahunFrom;
        }
        if ($tahunTo) {
            $where .= " AND ds.tahun <= ?";
            $bindings[] = $tahunTo;
        }

        $rekapLayer = DB::select("
            SELECT l.id, l.nama, l.warna, l.geom_type, COUNT(ds.id) AS total
            FROM data_spasial ds
            JOIN layers l ON l.id = ds.layer_id
            $where
            GROUP BY l.id, l.nama, l.warna, l.geom_type
            ORDER BY total DESC
        ", $bindings);

        $rekapWilayah = DB::select("
            SELECT d.id, d.nama, COUNT(ds.id) AS total
            FROM data_spasial ds
            JOIN districts d ON d.id = ds.district_id
            WHERE ds.geometry IS NOT NULL  AND ds.district_id IS NOT NULL
            GROUP BY d.id, d.nama
            ORDER BY total DESC
        ");

        $rekapKategori = DB::select("
            SELECT kl.id, kl.nama, kl.warna_default, COUNT(ds.id) AS total
            FROM data_spasial ds
            JOIN layers l ON l.id = ds.layer_id
            JOIN kategori_layers kl ON kl.id = l.kategori_id
            $where
            GROUP BY kl.id, kl.nama, kl.warna_default
            ORDER BY total DESC
        ", $bindings);

        $totalSemua = array_sum(array_column($rekapLayer, 'total'));

        $pdf = Pdf::loadView('admin.laporan.pdf', compact(
            'rekapLayer', 'rekapWilayah', 'rekapKategori', 'totalSemua',
            'layerId', 'districtId', 'tahunFrom', 'tahunTo'
        ));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('laporan-data-spasial.pdf');
    }

    public function cetakExcel(Request $request)
    {
        $layerId = $request->get('layer_id');
        $districtId = $request->get('district_id');
        $tahunFrom = $request->get('tahun_from');
        $tahunTo = $request->get('tahun_to');

        $where = "WHERE ds.geometry IS NOT NULL ";
        $bindings = [];

        if ($layerId) {
            $where .= " AND ds.layer_id = ?";
            $bindings[] = $layerId;
        }
        if ($districtId) {
            $where .= " AND ds.district_id = ?";
            $bindings[] = $districtId;
        }
        if ($tahunFrom) {
            $where .= " AND ds.tahun >= ?";
            $bindings[] = $tahunFrom;
        }
        if ($tahunTo) {
            $where .= " AND ds.tahun <= ?";
            $bindings[] = $tahunTo;
        }

        $rekapLayer = DB::select("
            SELECT l.nama AS layer, l.geom_type, COUNT(ds.id) AS total
            FROM data_spasial ds
            JOIN layers l ON l.id = ds.layer_id
            $where
            GROUP BY l.nama, l.geom_type
            ORDER BY total DESC
        ", $bindings);

        $rows = [];
        $rows[] = ['LAPORAN DATA SPASIAL'];
        $rows[] = [config('app.name')];
        $rows[] = [];
        $rows[] = ['Rekap Per Layer'];
        $rows[] = ['No', 'Layer', 'Tipe Geometri', 'Jumlah Data'];
        foreach ($rekapLayer as $i => $r) {
            $rows[] = [$i + 1, $r->layer, $r->geom_type, $r->total];
        }
        $rows[] = [];
        $rows[] = ['Total', '', '', array_sum(array_column($rekapLayer, 'total'))];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"';
        $xml .= ' xmlns:o="urn:schemas-microsoft-com:office:office"';
        $xml .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"';
        $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        $xml .= ' <Worksheet ss:Name="Laporan">' . "\n";
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
        }, 'laporan-data-spasial.xls', ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);
    }
}
