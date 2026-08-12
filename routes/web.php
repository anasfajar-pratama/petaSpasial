<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['track.visitor'])->group(function () {
    Route::get('/', [App\Http\Controllers\GuestController::class, 'beranda']);
    Route::get('/peta', [App\Http\Controllers\GuestController::class, 'peta']);
    Route::get('/statistik-publik', [App\Http\Controllers\GuestController::class, 'statistik']);
    Route::get('/layer/{slug}', [App\Http\Controllers\GuestController::class, 'layer']);
    Route::get('/data/{id}', [App\Http\Controllers\GuestController::class, 'detailData']);

    Route::prefix('informasi')->name('informasi.')->group(function () {
        Route::get('/berita', [App\Http\Controllers\GuestController::class, 'berita'])->name('berita');
        Route::get('/infografis', [App\Http\Controllers\GuestController::class, 'infografis'])->name('infografis');
        Route::get('/panduan-teknis', [App\Http\Controllers\GuestController::class, 'panduanTeknis'])->name('panduan-teknis');
        Route::get('/riset-publikasi', [App\Http\Controllers\GuestController::class, 'risetPublikasi'])->name('riset-publikasi');
    });

    Route::get('/kritik-saran', [App\Http\Controllers\GuestController::class, 'kritikSaran'])->name('kritik-saran');
    Route::post('/kritik-saran', [App\Http\Controllers\GuestController::class, 'kirimKritikSaran'])->name('kritik-saran.kirim');
    Route::get('/faq', [App\Http\Controllers\GuestController::class, 'faq'])->name('faq');
});

Route::get('/api/villages', function (Illuminate\Http\Request $r) {
    $villages = App\Models\Village::where('district_id', $r->district_id)->orderBy('nama')->get(['id', 'nama']);
    return response()->json($villages);
});
Route::get('/api/publik/statistik-ringkas', [App\Http\Controllers\GuestController::class, 'statistikRingkas']);
Route::get('/api/publik/layers', [App\Http\Controllers\MapPublicController::class, 'layers']);
Route::get('/api/publik/{layer}/data', [App\Http\Controllers\MapPublicController::class, 'data']);
Route::get('/api/publik/data', [App\Http\Controllers\MapPublicController::class, 'bbox']);
Route::get('/api/publik/search', [App\Http\Controllers\MapPublicController::class, 'search']);
Route::post('/api/publik/buffer', [App\Http\Controllers\MapPublicController::class, 'buffer']);
Route::get('/api/publik/data-single', [App\Http\Controllers\MapPublicController::class, 'single']);

Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:Administrator'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('/users', App\Http\Controllers\Admin\UserController::class)->except(['show']);
    Route::resource('/roles', App\Http\Controllers\Admin\RoleController::class)->except(['show']);
    Route::resource('/kategori', App\Http\Controllers\Admin\KategoriController::class)->except(['show']);

    Route::get('/wilayah', [App\Http\Controllers\Admin\WilayahController::class, 'index'])->name('wilayah');
    Route::post('/wilayah/district', [App\Http\Controllers\Admin\WilayahController::class, 'storeDistrict'])->name('wilayah.district.store');
    Route::get('/wilayah/district/{district}/edit', [App\Http\Controllers\Admin\WilayahController::class, 'editDistrict'])->name('wilayah.district.edit');
    Route::put('/wilayah/district/{district}', [App\Http\Controllers\Admin\WilayahController::class, 'updateDistrict'])->name('wilayah.district.update');
    Route::delete('/wilayah/district/{district}', [App\Http\Controllers\Admin\WilayahController::class, 'destroyDistrict'])->name('wilayah.district.destroy');

    Route::get('/wilayah/district/{district}/villages', [App\Http\Controllers\Admin\WilayahController::class, 'villages'])->name('wilayah.villages');
    Route::post('/wilayah/district/{district}/village', [App\Http\Controllers\Admin\WilayahController::class, 'storeVillage'])->name('wilayah.village.store');
    Route::get('/wilayah/village/{village}/edit', [App\Http\Controllers\Admin\WilayahController::class, 'editVillage'])->name('wilayah.village.edit');
    Route::put('/wilayah/village/{village}', [App\Http\Controllers\Admin\WilayahController::class, 'updateVillage'])->name('wilayah.village.update');
    Route::delete('/wilayah/village/{village}', [App\Http\Controllers\Admin\WilayahController::class, 'destroyVillage'])->name('wilayah.village.destroy');

    Route::resource('/layers', App\Http\Controllers\Admin\LayerController::class)->except(['show']);
    Route::patch('/layers/{layer}/toggle', [App\Http\Controllers\Admin\LayerController::class, 'toggle'])->name('layers.toggle');
    Route::get('/laporan', [App\Http\Controllers\Admin\LaporanController::class, 'index'])->name('laporan');
    Route::get('/simbol', [App\Http\Controllers\Admin\SimbolController::class, 'index'])->name('simbol');
    Route::resource('/informasi', App\Http\Controllers\Admin\InformasiController::class)->except(['show']);
    Route::get('/laporan/pdf', [App\Http\Controllers\Admin\LaporanController::class, 'cetakPdf'])->name('laporan.pdf');
    Route::get('/laporan/excel', [App\Http\Controllers\Admin\LaporanController::class, 'cetakExcel'])->name('laporan.excel');
    Route::get('/pengaturan', [App\Http\Controllers\Admin\PengaturanController::class, 'index'])->name('pengaturan');
    Route::put('/pengaturan', [App\Http\Controllers\Admin\PengaturanController::class, 'update'])->name('pengaturan.update');
    Route::get('/kritik-saran', [App\Http\Controllers\Admin\FeedbackController::class, 'index'])->name('kritik-saran.index');
    Route::get('/kritik-saran/{feedback}', [App\Http\Controllers\Admin\FeedbackController::class, 'show'])->name('kritik-saran.show');
    Route::patch('/kritik-saran/{feedback}/read', [App\Http\Controllers\Admin\FeedbackController::class, 'markAsRead'])->name('kritik-saran.read');
});

Route::middleware(['auth', 'role:Administrator|Operator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/export', [App\Http\Controllers\ExportController::class, 'index'])->name('export');
});

Route::middleware(['auth', 'role:Administrator|Operator'])->group(function () {
    Route::get('/export/all/{format}', [App\Http\Controllers\ExportController::class, 'exportAll'])->name('export.all');
    Route::get('/export/layer/{layerId}/{format}', [App\Http\Controllers\ExportController::class, 'exportLayer'])->name('export.layer');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/map', function () {
        $kategori = App\Models\KategoriLayer::orderBy('nama')->get(['id', 'nama']);
        $districts = App\Models\District::where('regency_id', env('ACTIVE_REGENCY_ID', 1))->orderBy('nama')->get(['id', 'nama']);
        $layers = App\Models\Layer::where('is_active', true)->orderBy('order')->get(['id', 'nama', 'geom_type']);
        return view('map.index', compact('kategori', 'districts', 'layers'));
    })->name('map');
    Route::view('/statistik', 'statistik.index')->name('statistik');

    Route::get('/api/map/layers', [App\Http\Controllers\MapController::class, 'layers']);
    Route::get('/api/map/{layer}/data', [App\Http\Controllers\MapController::class, 'data']);
    Route::get('/api/map/data', [App\Http\Controllers\MapController::class, 'bbox']);
    Route::get('/api/map/search', [App\Http\Controllers\MapController::class, 'search']);
    Route::post('/api/map/buffer', [App\Http\Controllers\MapController::class, 'buffer']);
    Route::get('/api/map/data-single', [App\Http\Controllers\MapController::class, 'single']);

    Route::post('/api/map/digitasi', [App\Http\Controllers\DigitasiController::class, 'store']);
    Route::put('/api/map/digitasi/{dataSpasial}', [App\Http\Controllers\DigitasiController::class, 'update']);
    Route::delete('/api/map/digitasi/{dataSpasial}', [App\Http\Controllers\DigitasiController::class, 'destroy']);

    Route::get('/data-spasial', [App\Http\Controllers\DataSpasialController::class, 'index'])->name('data-spasial.index');
    Route::get('/data-spasial/create', [App\Http\Controllers\DataSpasialController::class, 'create'])->name('data-spasial.create');
    Route::post('/data-spasial', [App\Http\Controllers\DataSpasialController::class, 'store'])->name('data-spasial.store');
    Route::get('/data-spasial/{dataSpasial}/edit', [App\Http\Controllers\DataSpasialController::class, 'edit'])->name('data-spasial.edit');
    Route::put('/data-spasial/{dataSpasial}', [App\Http\Controllers\DataSpasialController::class, 'update'])->name('data-spasial.update');
    Route::delete('/data-spasial/{dataSpasial}', [App\Http\Controllers\DataSpasialController::class, 'destroy'])->name('data-spasial.destroy');
    Route::post('/data-spasial/bulk-delete', [App\Http\Controllers\DataSpasialController::class, 'bulkDestroy'])->name('data-spasial.bulk-destroy');

    Route::get('/import', [App\Http\Controllers\ImportController::class, 'index'])->name('import.index');
    Route::post('/import/geojson', [App\Http\Controllers\ImportController::class, 'importGeoJson'])->name('import.geojson');
    Route::post('/import/csv', [App\Http\Controllers\ImportController::class, 'importCsv'])->name('import.csv');
    Route::post('/import/shp', [App\Http\Controllers\ImportController::class, 'importShp'])->name('import.shp');
    Route::post('/import/kml', [App\Http\Controllers\ImportController::class, 'importKml'])->name('import.kml');
    Route::get('/import/contoh/{file}', function ($file) {
        $path = storage_path('app/contoh/' . basename($file));
        if (!file_exists($path)) abort(404);
        return response()->download($path);
    })->name('import.contoh');
});

require __DIR__.'/auth.php';
