<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriLayer;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = KategoriLayer::paginate(15);
        return view('admin.kategori.index', compact('kategori'));
    }

    public function create()
    {
        return view('admin.kategori.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'warna_default' => 'required|string|max:9',
        ]);

        KategoriLayer::create($data);

        return to_route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(KategoriLayer $kategori)
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, KategoriLayer $kategori)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'warna_default' => 'required|string|max:9',
        ]);

        $kategori->update($data);

        return to_route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(KategoriLayer $kategori)
    {
        if ($kategori->layers()->count() > 0) {
            return back()->with('error', 'Kategori masih memiliki layer. Tidak bisa dihapus.');
        }

        $kategori->delete();
        return to_route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
