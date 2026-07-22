<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Peta Interaktif - {{ config('app.name', 'petaSpasial') }}</title>
    @vite(['resources/css/app.css', 'resources/js/map.js'])
</head>
<body class="m-0 p-0 overflow-hidden font-sans">
    <div id="map" class="w-screen h-screen"></div>

    <div id="floating-panel" class="fixed top-4 left-4 z-[1000] w-80 bg-gray-900 bg-opacity-90 text-white rounded-lg shadow-lg max-h-[calc(100vh-2rem)] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 bg-gray-800 border-b border-gray-700">
            <h2 class="text-sm font-semibold">Peta Interaktif</h2>
            <div class="flex gap-1">
                <button data-basemap="Street" class="px-2 py-1 text-xs bg-gray-700 hover:bg-gray-600 rounded">Street</button>
                <button data-basemap="Satellite" class="px-2 py-1 text-xs bg-gray-700 hover:bg-gray-600 rounded">Satellite</button>
                <button data-basemap="Dark" class="px-2 py-1 text-xs bg-gray-700 hover:bg-gray-600 rounded">Dark</button>
            </div>
        </div>

        <div class="px-3 py-2 bg-gray-800 border-b border-gray-700 space-y-1.5">
            <div class="relative">
                <input type="text" id="search-input" placeholder="Cari nama lokasi..." class="w-full text-xs bg-gray-700 text-white border border-gray-600 rounded px-2.5 py-1.5 pr-8 placeholder-gray-400 focus:outline-none focus:border-blue-500">
                <span id="search-spinner" class="hidden absolute right-2 top-1/2 -translate-y-1/2">
                    <svg class="animate-spin text-blue-400 w-3.5 h-3.5" viewBox="0 0 16 16" fill="none"><circle class="opacity-25" cx="8" cy="8" r="7" stroke="currentColor" stroke-width="2"/><path class="opacity-75" fill="currentColor" d="M8 0a8 8 0 018 8h-2a6 6 0 00-6-6V0z"/></svg>
                </span>
            </div>
            <div class="flex gap-1.5">
                <select id="filter-kategori" class="flex-1 text-xs bg-gray-700 text-white border border-gray-600 rounded px-1.5 py-1.5">
                    <option value="">Kategori</option>
                </select>
                <select id="filter-status" class="flex-1 text-xs bg-gray-700 text-white border border-gray-600 rounded px-1.5 py-1.5">
                    <option value="">Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                    <option value="proses">Proses</option>
                </select>
            </div>
            <div class="flex gap-1.5">
                <select id="filter-district" class="flex-1 text-xs bg-gray-700 text-white border border-gray-600 rounded px-1.5 py-1.5">
                    <option value="">Kecamatan</option>
                </select>
                <select id="filter-village" class="flex-1 text-xs bg-gray-700 text-white border border-gray-600 rounded px-1.5 py-1.5" disabled>
                    <option value="">Desa</option>
                </select>
            </div>
            <div class="flex gap-1.5">
                <button id="btn-radius" class="flex-1 px-2 py-1.5 text-xs bg-gray-700 hover:bg-gray-600 border border-gray-600 rounded">🔘 Radius</button>
                <button id="btn-clear" class="flex-1 px-2 py-1.5 text-xs bg-gray-700 hover:bg-gray-600 border border-gray-600 rounded">✕ Reset</button>
            </div>
        </div>

        @hasanyrole('Administrator|Operator')
        <div class="px-3 py-2 bg-gray-800 border-b border-gray-700">
            <div class="flex items-center gap-2">
                <select id="draw-layer-select" class="flex-1 text-xs bg-gray-700 text-white border border-gray-600 rounded px-2 py-1.5">
                    <option value="">Pilih layer tujuan...</option>
                </select>
            </div>
            <div class="flex gap-1 mt-2">
                <button id="btn-draw-point" class="flex-1 px-2 py-1 text-xs bg-blue-600 hover:bg-blue-700 rounded disabled:opacity-50 disabled:cursor-not-allowed" disabled>Point</button>
                <button id="btn-draw-line" class="flex-1 px-2 py-1 text-xs bg-blue-600 hover:bg-blue-700 rounded disabled:opacity-50 disabled:cursor-not-allowed" disabled>Line</button>
                <button id="btn-draw-polygon" class="flex-1 px-2 py-1 text-xs bg-blue-600 hover:bg-blue-700 rounded disabled:opacity-50 disabled:cursor-not-allowed" disabled>Polygon</button>
                <button id="btn-draw-cancel" class="px-2 py-1 text-xs bg-red-600 hover:bg-red-700 rounded hidden">Batal</button>
            </div>
        </div>
        @endhasanyrole

        <div id="search-info" class="hidden px-3 py-1.5 bg-blue-900 bg-opacity-60 border-b border-blue-800 text-xs text-blue-200 flex items-center justify-between">
            <span id="search-count"></span>
            <button id="btn-clear-results" class="text-blue-300 hover:text-white">Tutup</button>
        </div>

        <div id="layer-list" class="flex-1 overflow-y-auto p-3 space-y-1">
            <p class="text-xs text-gray-400 text-center py-4">Memuat layer...</p>
        </div>
    </div>

    <div id="bottom-bar" class="fixed bottom-0 left-0 right-0 z-[1000] bg-gray-900 bg-opacity-80 px-4 py-1.5 flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="text-xs text-blue-400 hover:text-blue-300 mr-auto">&larr; Dashboard</a>
    </div>

    <a href="{{ route('dashboard') }}" class="fixed top-4 right-4 z-[1000] px-3 py-1.5 bg-gray-900 bg-opacity-80 text-white text-xs rounded hover:bg-opacity-100 transition">
        &larr; Dashboard
    </a>

    <div id="radius-modal" class="fixed inset-0 z-[2000] hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-sm p-6 mx-4">
            <h3 class="text-lg font-semibold mb-4">Cari dalam Radius</h3>
            <p class="text-sm text-gray-500 mb-3">Klik titik pusat di peta, lalu masukkan radius.</p>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Radius (meter)</label>
                <input type="number" id="radius-value" value="1000" min="1" max="50000" class="w-full border rounded px-3 py-2 text-sm">
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" id="radius-batal" class="px-4 py-2 text-sm border rounded hover:bg-gray-100">Batal</button>
                <button type="button" id="radius-cari" class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Cari</button>
            </div>
        </div>
    </div>

    <div id="digitasi-modal" class="fixed inset-0 z-[2000] hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 mx-4">
            <h3 class="text-lg font-semibold mb-4" id="dig-title">Simpan Data Spasial</h3>
            <form id="digitasi-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="dig-method" value="POST">
                <input type="hidden" name="layer_id" id="dig-layer-id">
                <input type="hidden" name="geometry" id="dig-geometry">
                <input type="hidden" name="data_id" id="dig-data-id" value="">

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" name="nama" id="dig-nama" class="w-full border rounded px-3 py-2 text-sm" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="dig-deskripsi" rows="2" class="w-full border rounded px-3 py-2 text-sm"></textarea>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="kategori_id" id="dig-kategori" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">-- Pilih --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                    <input type="file" name="foto" id="dig-foto" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div class="flex gap-2 justify-end mt-4">
                    <button type="button" id="dig-batal" class="px-4 py-2 text-sm border rounded hover:bg-gray-100">Batal</button>
                    <button type="submit" id="dig-submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

<script>
window._REFERENSI = {
    kategori: @json($kategori),
    districts: @json($districts),
};
</script>
</body>
</html>
