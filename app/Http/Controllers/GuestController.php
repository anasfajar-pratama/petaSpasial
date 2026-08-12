<?php

namespace App\Http\Controllers;

use App\Models\KategoriLayer;
use App\Models\District;
use App\Models\Informasi;
use App\Models\Layer;
use App\Models\DataSpasial;
use App\Models\Feedback;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class GuestController extends Controller
{
    public function beranda()
    {
        $totalLayers = DB::table('layers')->whereNull('deleted_at')->count();
        $totalDistricts = DB::table('districts')->where('regency_id', env('ACTIVE_REGENCY_ID', 1))->count();
        $totalCategories = DB::table('kategori_layers')->count();

        $totalObjects = DB::select("
            SELECT COUNT(*) AS c FROM data_spasial ds WHERE ds.geometry IS NOT NULL
        ")[0]->c;

        $objectsByType = DB::select("
            SELECT CASE
                WHEN ST_GeometryType(ds.geometry) IN ('ST_Point', 'ST_MultiPoint') THEN 'Point'
                WHEN ST_GeometryType(ds.geometry) IN ('ST_LineString', 'ST_MultiLineString') THEN 'Line'
                WHEN ST_GeometryType(ds.geometry) IN ('ST_Polygon', 'ST_MultiPolygon') THEN 'Polygon'
                ELSE 'Lainnya'
            END AS tipe, COUNT(*) AS c
            FROM data_spasial ds
            WHERE ds.geometry IS NOT NULL
            GROUP BY tipe ORDER BY c DESC
        ");

        $settings = Setting::pluck('value', 'key')->toArray();

        return view('guest.beranda', compact(
            'totalLayers', 'totalDistricts', 'totalCategories',
            'totalObjects', 'objectsByType', 'settings'
        ));
    }

    public function peta()
    {
        $kategori = KategoriLayer::orderBy('nama')->get(['id', 'nama']);
        $districts = District::where('regency_id', env('ACTIVE_REGENCY_ID', 1))->orderBy('nama')->get(['id', 'nama']);

        return view('guest.peta', compact('kategori', 'districts'));
    }

    public function statistik(Request $request)
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
        $totalDistricts = DB::table('districts')->where('regency_id', env('ACTIVE_REGENCY_ID', 1))->count();
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

        $districts = DB::table('districts')->where('regency_id', env('ACTIVE_REGENCY_ID', 1))->orderBy('nama')->get(['id', 'nama']);
        $tahuns = DB::select("SELECT DISTINCT tahun FROM data_spasial WHERE tahun IS NOT NULL ORDER BY tahun DESC");

        return view('guest.statistik', compact(
            'totalLayers', 'totalObjects', 'totalDistricts', 'totalCategories',
            'objectsByType', 'objectsByLayer', 'objectsByCategory',
            'objectsByDistrict', 'objectsByYear',
            'districts', 'tahuns', 'tahunFilter', 'districtFilter'
        ));
    }

    public function layer($slug)
    {
        $layer = Layer::where('slug', $slug)->firstOrFail();

        $dataList = DataSpasial::where('layer_id', $layer->id)
            ->orderBy('nama')
            ->paginate(20);

        return view('guest.layer', compact('layer', 'dataList'));
    }

    public function detailData($id)
    {
        $data = DataSpasial::with('layer')->findOrFail($id);

        $geometry = null;
        if ($data->geometry) {
            $geo = DB::select("SELECT ST_AsGeoJSON(geometry) AS g FROM data_spasial WHERE id = ?", [$data->id]);
            $geometry = $geo[0]->g ?? null;
        }

        return view('guest.detail-data', compact('data', 'geometry'));
    }

    public function berita()
    {
        $items = Informasi::tipe('berita')->where('is_active', true)
            ->orderBy('urutan')->orderByDesc('tanggal')->get();

        return view('guest.informasi.berita', compact('items'));
    }

    public function infografis()
    {
        $items = Informasi::tipe('infografis')->where('is_active', true)
            ->orderBy('urutan')->get();

        return view('guest.informasi.infografis', compact('items'));
    }

    public function panduanTeknis()
    {
        $items = Informasi::tipe('panduan')->where('is_active', true)
            ->orderBy('urutan')->get();

        return view('guest.informasi.panduan-teknis', compact('items'));
    }

    public function risetPublikasi()
    {
        $items = Informasi::tipe('riset')->where('is_active', true)
            ->orderBy('urutan')->get();

        return view('guest.informasi.riset-publikasi', compact('items'));
    }

    public function kritikSaran()
    {
        return view('guest.kritik-saran');
    }

    public function kirimKritikSaran(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'subjek' => 'nullable|string|max:150',
            'pesan' => 'required|string|min:10',
            'foto_wajah' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        Storage::disk('public')->makeDirectory('feedback');

        $foto_wajah = null;
        if ($request->filled('foto_wajah')) {
            $data = $request->input('foto_wajah');
            $data = str_replace('data:image/webp;base64,', '', $data);
            $data = str_replace(' ', '+', $data);
            $decoded = base64_decode($data);
            $filename = 'wajah_' . time() . '_' . uniqid() . '.webp';
            Storage::disk('public')->put('feedback/' . $filename, $decoded);
            $foto_wajah = 'feedback/' . $filename;
        }

        $gambar_path = null;
        if ($request->hasFile('gambar')) {
            $img = Image::read($request->file('gambar')->getRealPath());
            $encoded = $img->toWebp(80);
            $filename = 'gambar_' . time() . '_' . uniqid() . '.webp';
            $encoded->save(storage_path('app/public/feedback/' . $filename));
            $gambar_path = 'feedback/' . $filename;
        }

        Feedback::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'subjek' => $validated['subjek'],
            'pesan' => $validated['pesan'],
            'foto_wajah' => $foto_wajah,
            'gambar' => $gambar_path,
        ]);

        return redirect()->route('kritik-saran')->with('success', 'Terima kasih! Kritik dan saran Anda telah kami terima.');
    }

    public function faq()
    {
        return view('guest.faq');
    }

    public function statistikRingkas()
    {
        $totalLayers = DB::table('layers')->whereNull('deleted_at')->count();
        $totalObjects = DB::select("SELECT COUNT(*) AS c FROM data_spasial ds WHERE ds.geometry IS NOT NULL")[0]->c;
        $totalDistricts = DB::table('districts')->where('regency_id', env('ACTIVE_REGENCY_ID', 1))->count();

        return response()->json([
            'total_layers' => $totalLayers,
            'total_objects' => $totalObjects,
            'total_districts' => $totalDistricts,
        ]);
    }
}
