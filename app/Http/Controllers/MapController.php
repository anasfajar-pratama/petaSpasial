<?php

namespace App\Http\Controllers;

use App\Models\Layer;
use App\Models\DataSpasial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function layers()
    {
        $layers = Layer::with('kategori')
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(fn ($l) => [
                'id' => $l->id,
                'nama' => $l->nama,
                'slug' => $l->slug,
                'geom_type' => $l->geom_type,
                'warna' => $l->warna,
                'icon_marker' => $l->icon_marker,
                'style' => $l->style_json,
                'opacity' => (float) $l->opacity,
                'kategori' => $l->kategori->nama ?? '-',
            ]);

        return response()->json($layers);
    }

    public function data(Layer $layer)
    {
        $geojson = DB::select("
            SELECT jsonb_build_object(
                'type', 'FeatureCollection',
                'features', COALESCE(jsonb_agg(
                    jsonb_build_object(
                        'type', 'Feature',
                        'geometry', ST_AsGeoJSON(ds.geometry)::jsonb,
                        'properties', jsonb_build_object(
                            'id', ds.id,
                            'nama', ds.nama,
                            'deskripsi', ds.deskripsi,
                            'status', ds.status,
                            'tahun', ds.tahun,
                            'luas', ds.luas,
                            'layer_id', ds.layer_id
                        )
                    )
                ), '[]'::jsonb)
            ) AS geojson
            FROM data_spasial ds
            WHERE ds.layer_id = ?
        ", [$layer->id]);

        return response()->json(json_decode($geojson[0]->geojson, true));
    }

    public function bbox(Request $request)
    {
        $request->validate([
            'sw_lat' => 'required|numeric',
            'sw_lng' => 'required|numeric',
            'ne_lat' => 'required|numeric',
            'ne_lng' => 'required|numeric',
            'layer_id' => 'nullable|exists:layers,id',
        ]);

        $query = DataSpasial::query()
            ->whereRaw("geometry && ST_MakeEnvelope(?, ?, ?, ?, 4326)", [
                $request->sw_lng, $request->sw_lat, $request->ne_lng, $request->ne_lat
            ]);

        if ($request->filled('layer_id')) {
            $query->where('layer_id', $request->layer_id);
        }

        $features = $query->get()->map(fn ($ds) => [
            'type' => 'Feature',
            'geometry' => json_decode(DB::select("SELECT ST_AsGeoJSON(geometry) AS g FROM data_spasial WHERE id = ?", [$ds->id])[0]->g, true),
            'properties' => [
                'id' => $ds->id,
                'nama' => $ds->nama,
                'layer_id' => $ds->layer_id,
            ],
        ]);

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:255',
            'layer_id' => 'nullable|exists:layers,id',
            'kategori_id' => 'nullable|exists:kategori_layers,id',
            'status' => 'nullable|string|max:50',
            'district_id' => 'nullable|exists:districts,id',
            'village_id' => 'nullable|exists:villages,id',
            'tahun_from' => 'nullable|integer|min:1900|max:2100',
            'tahun_to' => 'nullable|integer|min:1900|max:2100',
            'limit' => 'nullable|integer|min:1|max:500',
        ]);

        $query = DataSpasial::whereNotNull('geometry')->with('layer');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('nama', 'ILIKE', "%{$q}%")
                    ->orWhere('deskripsi', 'ILIKE', "%{$q}%");
            });
        }

        if ($request->filled('layer_id')) {
            $query->where('layer_id', $request->layer_id);
        }

        if ($request->filled('kategori_id')) {
            $query->whereHas('layer', fn ($q) => $q->where('kategori_id', $request->kategori_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->filled('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        if ($request->filled('tahun_from')) {
            $query->where('tahun', '>=', $request->tahun_from);
        }

        if ($request->filled('tahun_to')) {
            $query->where('tahun', '<=', $request->tahun_to);
        }

        $total = $query->count();

        $features = $query->limit($request->limit ?: 200)->get()->map(fn ($ds) => [
            'type' => 'Feature',
            'geometry' => json_decode(DB::select("SELECT ST_AsGeoJSON(geometry) AS g FROM data_spasial WHERE id = ?", [$ds->id])[0]->g, true),
            'properties' => [
                'id' => $ds->id,
                'nama' => $ds->nama,
                'deskripsi' => $ds->deskripsi,
                'status' => $ds->status,
                'tahun' => $ds->tahun,
                'layer_id' => $ds->layer_id,
                'layer_nama' => $ds->layer?->nama,
                'layer_warna' => $ds->layer?->warna,
            ],
        ]);

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
            'total' => $total,
        ]);
    }

    public function buffer(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'required|numeric|min:1|max:50000',
            'layer_id' => 'nullable|exists:layers,id',
        ]);

        $query = DataSpasial::query()
            ->whereRaw("ST_DWithin(geometry::geography, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography, ?)", [
                $request->lng, $request->lat, $request->radius,
            ]);

        if ($request->filled('layer_id')) {
            $query->where('layer_id', $request->layer_id);
        }

        $total = $query->count();

        $features = $query->limit(500)->get()->map(fn ($ds) => [
            'type' => 'Feature',
            'geometry' => json_decode(DB::select("SELECT ST_AsGeoJSON(geometry) AS g FROM data_spasial WHERE id = ?", [$ds->id])[0]->g, true),
            'properties' => [
                'id' => $ds->id,
                'nama' => $ds->nama,
                'distance' => round(DB::select("SELECT ST_Distance(geometry::geography, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography) AS d FROM data_spasial WHERE id = ?", [$request->lng, $request->lat, $ds->id])[0]->d, 1),
                'layer_id' => $ds->layer_id,
            ],
        ]);

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
            'total' => $total,
            'center' => ['lat' => $request->lat, 'lng' => $request->lng],
            'radius' => $request->radius,
        ]);
    }

    public function single(Request $request)
    {
        $ds = DataSpasial::findOrFail($request->id);
        $geometry = json_decode(DB::select("SELECT ST_AsGeoJSON(geometry) AS g FROM data_spasial WHERE id = ?", [$ds->id])[0]->g);

        return response()->json([
            'id' => $ds->id,
            'nama' => $ds->nama,
            'deskripsi' => $ds->deskripsi,
            'kategori_id' => $ds->kategori_id,
            'geometry' => $geometry,
        ]);
    }
}
