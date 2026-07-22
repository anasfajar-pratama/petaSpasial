<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layer;
use App\Models\KategoriLayer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LayerController extends Controller
{
    public function index(Request $request)
    {
        $query = Layer::with('kategori', 'creator');

        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        $layers = $query->orderBy('order')->paginate(15);
        $kategoriList = KategoriLayer::all();

        return view('admin.layers.index', compact('layers', 'kategoriList'));
    }

    public function create()
    {
        $kategoriList = KategoriLayer::all();
        $geomTypes = ['Point', 'LineString', 'Polygon', 'MultiPolygon'];
        return view('admin.layers.create', compact('kategoriList', 'geomTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_layers,id',
            'geom_type' => 'required|in:Point,LineString,Polygon,MultiPolygon',
            'warna' => 'required|string|max:9',
            'icon_marker' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'opacity' => 'required|numeric|min:0|max:1',
            'order' => 'required|integer|min:0',
        ]);

        $data['slug'] = Str::slug($data['nama']) . '-' . Str::random(4);
        $data['is_active'] = $request->boolean('is_active');
        $data['created_by'] = auth()->id();

        Layer::create($data);

        return to_route('admin.layers.index')->with('success', 'Layer berhasil ditambahkan.');
    }

    public function edit(Layer $layer)
    {
        $kategoriList = KategoriLayer::all();
        $geomTypes = ['Point', 'LineString', 'Polygon', 'MultiPolygon'];
        return view('admin.layers.edit', compact('layer', 'kategoriList', 'geomTypes'));
    }

    public function update(Request $request, Layer $layer)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_layers,id',
            'geom_type' => 'required|in:Point,LineString,Polygon,MultiPolygon',
            'warna' => 'required|string|max:9',
            'icon_marker' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'opacity' => 'required|numeric|min:0|max:1',
            'order' => 'required|integer|min:0',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $layer->update($data);

        return to_route('admin.layers.index')->with('success', 'Layer berhasil diperbarui.');
    }

    public function destroy(Layer $layer)
    {
        if ($layer->dataSpasial()->count() > 0) {
            return back()->with('error', 'Layer masih memiliki data spasial. Tidak bisa dihapus.');
        }

        $layer->delete();
        return to_route('admin.layers.index')->with('success', 'Layer berhasil dihapus.');
    }

    public function toggle(Layer $layer)
    {
        $layer->update(['is_active' => !$layer->is_active]);
        return back()->with('success', 'Status layer berhasil diubah.');
    }
}
