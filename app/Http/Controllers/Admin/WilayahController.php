<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function index()
    {
        $activeRegencyId = (int) env('ACTIVE_REGENCY_ID', 1);
        $regency = Regency::find($activeRegencyId);
        $districts = District::where('regency_id', $activeRegencyId)->withCount('villages')->paginate(20);

        return view('admin.wilayah.index', compact('regency', 'districts'));
    }

    public function storeDistrict(Request $request)
    {
        $data = $request->validate([
            'kode' => 'required|string|max:10|unique:districts,kode',
            'nama' => 'required|string|max:255',
        ]);

        District::create([
            'regency_id' => (int) env('ACTIVE_REGENCY_ID', 1),
            'kode' => $data['kode'],
            'nama' => $data['nama'],
        ]);

        return to_route('admin.wilayah')->with('success', 'Kecamatan berhasil ditambahkan.');
    }

    public function editDistrict(District $district)
    {
        return view('admin.wilayah.edit-district', compact('district'));
    }

    public function updateDistrict(Request $request, District $district)
    {
        $data = $request->validate([
            'kode' => 'required|string|max:10|unique:districts,kode,' . $district->id,
            'nama' => 'required|string|max:255',
        ]);

        $district->update($data);

        return to_route('admin.wilayah')->with('success', 'Kecamatan berhasil diperbarui.');
    }

    public function destroyDistrict(District $district)
    {
        $district->delete();
        return to_route('admin.wilayah')->with('success', 'Kecamatan berhasil dihapus.');
    }

    public function villages(District $district)
    {
        $villages = $district->villages()->paginate(20);
        return view('admin.wilayah.villages', compact('district', 'villages'));
    }

    public function storeVillage(Request $request, District $district)
    {
        $data = $request->validate([
            'kode' => 'required|string|max:15|unique:villages,kode',
            'nama' => 'required|string|max:255',
        ]);

        Village::create([
            'district_id' => $district->id,
            'kode' => $data['kode'],
            'nama' => $data['nama'],
        ]);

        return to_route('admin.wilayah.villages', $district)->with('success', 'Desa berhasil ditambahkan.');
    }

    public function editVillage(Village $village)
    {
        return view('admin.wilayah.edit-village', compact('village'));
    }

    public function updateVillage(Request $request, Village $village)
    {
        $data = $request->validate([
            'kode' => 'required|string|max:15|unique:villages,kode,' . $village->id,
            'nama' => 'required|string|max:255',
        ]);

        $village->update($data);

        return to_route('admin.wilayah.villages', $village->district)->with('success', 'Desa berhasil diperbarui.');
    }

    public function destroyVillage(Village $village)
    {
        $district = $village->district;
        $village->delete();
        return to_route('admin.wilayah.villages', $district)->with('success', 'Desa berhasil dihapus.');
    }
}
