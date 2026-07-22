<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahunFilter = $request->get('tahun');
        $districtFilter = $request->get('district');

        $whereGeometry = "WHERE ds.geometry IS NOT NULL";
        $bindings = [];

        if ($tahunFilter) {
            $whereGeometry .= " AND ds.tahun = ?";
            $bindings[] = $tahunFilter;
        }
        if ($districtFilter) {
            $whereGeometry .= " AND ds.district_id = ?";
            $bindings[] = $districtFilter;
        }

        $totalLayers = DB::table('layers')->whereNull('deleted_at')->count();
        $totalDistricts = DB::table('districts')->where('regency_id', 1)->count();
        $totalCategories = DB::table('kategori_layers')->count();

        $totalObjects = DB::select("SELECT COUNT(*) AS c FROM data_spasial ds $whereGeometry", $bindings)[0]->c;

        $objectsByType = DB::select("
            SELECT CASE
                WHEN ST_GeometryType(ds.geometry) IN ('ST_Point', 'ST_MultiPoint') THEN 'Point'
                WHEN ST_GeometryType(ds.geometry) IN ('ST_LineString', 'ST_MultiLineString') THEN 'Line'
                WHEN ST_GeometryType(ds.geometry) IN ('ST_Polygon', 'ST_MultiPolygon') THEN 'Polygon'
                ELSE 'Lainnya'
            END AS tipe, COUNT(*) AS c
            FROM data_spasial ds $whereGeometry
            GROUP BY tipe ORDER BY c DESC
        ", $bindings);

        $objectsByLayer = DB::select("
            SELECT l.nama, l.warna, COUNT(ds.id) AS c
            FROM data_spasial ds
            JOIN layers l ON l.id = ds.layer_id
            $whereGeometry
            GROUP BY l.id, l.nama, l.warna
            ORDER BY c DESC
            LIMIT 10
        ", $bindings);

        $objectsByCategory = DB::select("
            SELECT kl.nama, kl.warna_default, COUNT(ds.id) AS c
            FROM data_spasial ds
            JOIN layers l ON l.id = ds.layer_id
            JOIN kategori_layers kl ON kl.id = l.kategori_id
            $whereGeometry
            GROUP BY kl.id, kl.nama, kl.warna_default
            ORDER BY c DESC
        ", $bindings);

        $objectsByDistrict = DB::select("
            SELECT d.nama, COUNT(ds.id) AS c
            FROM data_spasial ds
            JOIN districts d ON d.id = ds.district_id
            WHERE ds.geometry IS NOT NULL AND ds.district_id IS NOT NULL
            GROUP BY d.id, d.nama
            ORDER BY c DESC
            LIMIT 10
        ");

        $objectsByYear = DB::select("
            SELECT tahun, COUNT(*) AS c
            FROM data_spasial
            WHERE tahun IS NOT NULL AND geometry IS NOT NULL
            GROUP BY tahun ORDER BY tahun
        ");

        $districts = DB::table('districts')->where('regency_id', 1)->orderBy('nama')->get(['id', 'nama']);
        $tahuns = DB::select("SELECT DISTINCT tahun FROM data_spasial WHERE tahun IS NOT NULL ORDER BY tahun DESC");

        return view('dashboard', compact(
            'totalLayers', 'totalObjects', 'totalDistricts', 'totalCategories',
            'objectsByType', 'objectsByLayer', 'objectsByCategory',
            'objectsByDistrict', 'objectsByYear',
            'districts', 'tahuns', 'tahunFilter', 'districtFilter'
        ));
    }
}
