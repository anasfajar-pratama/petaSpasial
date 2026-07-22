<?php

namespace App\Http\Controllers;

use App\Models\DataSpasial;
use App\Models\Layer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DigitasiController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'layer_id' => 'required|exists:layers,id',
            'geometry' => 'required|json',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori_id' => 'nullable|exists:kategori_layers,id',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $layer = Layer::findOrFail($data['layer_id']);
        $geomType = $this->detectGeoJsonType(json_decode($data['geometry'], true));

        if ($geomType !== $layer->geom_type) {
            return response()->json([
                'message' => "Tipe geometri tidak sesuai. Layer menerima {$layer->geom_type}, tapi data yang dikirim adalah $geomType.",
            ], 422);
        }

        $foto = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('foto', 'public');
        }

        $dataSpasial = DataSpasial::create([
            'layer_id' => $data['layer_id'],
            'nama' => $data['nama'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'kategori_id' => $data['kategori_id'] ?? null,
            'foto' => $foto,
            'status' => 'aktif',
            'created_by' => auth()->id(),
        ]);

        DB::statement("
            UPDATE data_spasial
            SET geometry = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326)
            WHERE id = ?
        ", [$data['geometry'], $dataSpasial->id]);

        $dataSpasial->refresh();

        return response()->json([
            'message' => 'Data berhasil disimpan.',
            'id' => $dataSpasial->id,
        ], 201);
    }

    public function update(Request $request, DataSpasial $dataSpasial)
    {
        $data = $request->validate([
            'geometry' => 'nullable|json',
            'nama' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori_id' => 'nullable|exists:kategori_layers,id',
        ]);

        if (isset($data['nama'])) $dataSpasial->nama = $data['nama'];
        if (isset($data['deskripsi'])) $dataSpasial->deskripsi = $data['deskripsi'];
        if (array_key_exists('kategori_id', $data)) $dataSpasial->kategori_id = $data['kategori_id'];
        $dataSpasial->save();

        if (!empty($data['geometry'])) {
            $layer = $dataSpasial->layer;
            $geomType = $this->detectGeoJsonType(json_decode($data['geometry'], true));
            if ($geomType !== $layer->geom_type) {
                return response()->json(['message' => "Tipe geometri tidak sesuai dengan layer."], 422);
            }
            DB::statement("
                UPDATE data_spasial
                SET geometry = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326)
                WHERE id = ?
            ", [$data['geometry'], $dataSpasial->id]);
        }

        return response()->json(['message' => 'Data berhasil diperbarui.']);
    }

    public function destroy(DataSpasial $dataSpasial)
    {
        $dataSpasial->delete();
        return response()->json(['message' => 'Data berhasil dihapus.']);
    }

    private function detectGeoJsonType(array $geometry): string
    {
        $map = [
            'Point' => 'Point',
            'MultiPoint' => 'Point',
            'LineString' => 'LineString',
            'MultiLineString' => 'LineString',
            'Polygon' => 'Polygon',
            'MultiPolygon' => 'MultiPolygon',
        ];
        return $map[$geometry['type'] ?? ''] ?? 'Point';
    }
}
