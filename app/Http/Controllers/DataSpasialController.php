<?php

namespace App\Http\Controllers;

use App\Models\DataSpasial;
use App\Models\Layer;
use App\Models\KategoriLayer;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataSpasialController extends Controller
{
    public function index(Request $request)
    {
        $query = DataSpasial::with('layer', 'kategori', 'creator');

        if ($request->filled('layer_id')) {
            $query->where('layer_id', $request->layer_id);
        }

        if ($request->filled('search')) {
            $query->where('nama', 'ILIKE', '%' . $request->search . '%');
        }

        $dataSpasial = $query->latest()->paginate(15);
        $layers = Layer::orderBy('order')->get();

        return view('data-spasial.index', compact('dataSpasial', 'layers'));
    }

    public function create()
    {
        $layers = Layer::where('is_active', true)->orderBy('order')->get();
        $kategoriList = KategoriLayer::all();

        return view('data-spasial.create', compact('layers', 'kategoriList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'layer_id' => 'required|exists:layers,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori_id' => 'nullable|exists:kategori_layers,id',
            'status' => 'required|string|max:50',
            'tahun' => 'nullable|integer|min:1900|max:2099',
            'latitude' => 'required_if:geom_type,Point|nullable|numeric|between:-90,90',
            'longitude' => 'required_if:geom_type,Point|nullable|numeric|between:-180,180',
        ]);

        $layer = Layer::findOrFail($data['layer_id']);

        $ds = DataSpasial::create([
            'layer_id' => $data['layer_id'],
            'nama' => $data['nama'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'kategori_id' => $data['kategori_id'] ?? null,
            'status' => $data['status'],
            'tahun' => $data['tahun'] ?? null,
            'created_by' => auth()->id(),
        ]);

        if ($layer->geom_type === 'Point' && $data['latitude'] && $data['longitude']) {
            DB::statement("
                UPDATE data_spasial
                SET geometry = ST_SetSRID(ST_MakePoint(?, ?), 4326)
                WHERE id = ?
            ", [$data['longitude'], $data['latitude'], $ds->id]);
        }

        return to_route('data-spasial.index')->with('success', 'Data spasial berhasil ditambahkan.');
    }

    public function edit(DataSpasial $dataSpasial)
    {
        $layers = Layer::where('is_active', true)->orderBy('order')->get();
        $kategoriList = KategoriLayer::all();

        $coords = DB::select("SELECT ST_X(geometry) AS lng, ST_Y(geometry) AS lat FROM data_spasial WHERE id = ?", [$dataSpasial->id]);
        $lat = $coords[0]->lat ?? null;
        $lng = $coords[0]->lng ?? null;

        return view('data-spasial.edit', compact('dataSpasial', 'layers', 'kategoriList', 'lat', 'lng'));
    }

    public function update(Request $request, DataSpasial $dataSpasial)
    {
        $data = $request->validate([
            'layer_id' => 'required|exists:layers,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori_id' => 'nullable|exists:kategori_layers,id',
            'status' => 'required|string|max:50',
            'tahun' => 'nullable|integer|min:1900|max:2099',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $dataSpasial->update([
            'layer_id' => $data['layer_id'],
            'nama' => $data['nama'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'kategori_id' => $data['kategori_id'] ?? null,
            'status' => $data['status'],
            'tahun' => $data['tahun'] ?? null,
        ]);

        if ($data['latitude'] && $data['longitude']) {
            DB::statement("
                UPDATE data_spasial
                SET geometry = ST_SetSRID(ST_MakePoint(?, ?), 4326)
                WHERE id = ?
            ", [$data['longitude'], $data['latitude'], $dataSpasial->id]);
        }

        return to_route('data-spasial.index')->with('success', 'Data spasial berhasil diperbarui.');
    }

    public function destroy(DataSpasial $dataSpasial)
    {
        $dataSpasial->delete();
        return to_route('data-spasial.index')->with('success', 'Data spasial berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:data_spasial,id']);
        DataSpasial::whereIn('id', $request->ids)->delete();
        return back()->with('success', count($request->ids) . ' data berhasil dihapus.');
    }
}
