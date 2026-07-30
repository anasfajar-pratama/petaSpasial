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

    @if (session('success'))
    <div id="flash-success" class="fixed top-24 left-1/2 -translate-x-1/2 z-[3000] bg-green-50 border border-green-200 text-green-800 rounded-xl px-6 py-3 shadow-lg text-sm flex items-center gap-3 max-w-lg">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-green-500 hover:text-green-700">&times;</button>
    </div>
    @endif
    @if (session('error'))
    <div id="flash-error" class="fixed top-24 left-1/2 -translate-x-1/2 z-[3000] bg-red-50 border border-red-200 text-red-800 rounded-xl px-6 py-3 shadow-lg text-sm flex items-center gap-3 max-w-lg">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-red-500 hover:text-red-700">&times;</button>
    </div>
    @endif
    @if ($errors->any())
    <div id="flash-validation" class="fixed top-24 left-1/2 -translate-x-1/2 z-[3000] bg-red-50 border border-red-200 text-red-800 rounded-xl px-6 py-3 shadow-lg text-sm flex items-center gap-3 max-w-lg">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ $errors->first() }}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-red-500 hover:text-red-700">&times;</button>
    </div>
    @endif

    <div id="top-nav" class="fixed top-5 left-5 right-5 z-[1000] bg-white rounded-[40px] shadow-[0_4px_15px_rgba(0,0,0,0.15)] h-[85px] flex items-center justify-between px-8">
        <a href="{{ url('/') }}" class="flex items-center gap-3 flex-shrink-0">
            <div class="w-9 h-9 rounded-lg bg-[#0C3F8A] flex items-center justify-center text-white font-bold text-sm">PS</div>
            <span class="font-bold text-[#1E1E1E] text-lg">petaSpasial</span>
        </a>
        <div class="flex items-center gap-8">
            <a href="{{ route('dashboard') }}" class="text-[#1E1E1E] font-medium text-sm hover:text-[#0C3F8A] transition-colors">Dashboard</a>
            <a href="{{ url('/map') }}" class="text-[#0C3F8A] font-medium text-sm border-b-2 border-[#0C3F8A] pb-1">Peta</a>
            <button onclick="document.getElementById('import-modal').classList.remove('hidden'); document.getElementById('import-modal').classList.add('flex')" class="text-[#1E1E1E] font-medium text-sm hover:text-[#0C3F8A] transition-colors cursor-pointer bg-transparent border-none">Import SHP</button>
        </div>
        <div class="w-24"></div>
    </div>

    <button id="btn-toggle-sidebar" class="fixed z-[1000] w-[60px] h-[60px] bg-[#0C3F8A] rounded-xl shadow-[0_4px_15px_rgba(0,0,0,0.15)] flex items-center justify-center text-white hover:scale-105 transition-transform" style="top:145px; left:30px;">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>

    <div id="sidebar" class="fixed z-[999] bg-white rounded-[18px] shadow-[0_4px_15px_rgba(0,0,0,0.15)] flex flex-col overflow-hidden transition-all duration-300" style="width:400px; height:620px; left:30px; top:220px; display:none;">
        <div class="flex-shrink-0 px-6 pt-6 pb-3">
            <button id="katalog-header" class="w-full h-14 bg-[#0C3F8A] rounded-xl text-white font-semibold text-sm">Katalog Layer</button>
        </div>

        <div class="flex-shrink-0 px-6 pb-3 space-y-2">
            <div class="relative">
                <input type="text" id="search-input" placeholder="Cari nama lokasi..." class="w-full h-10 bg-white border border-[#EAEAEA] rounded-lg pl-10 pr-8 text-sm text-[#1E1E1E] placeholder-[#A5A5A5] focus:outline-none focus:ring-2 focus:ring-[#0C3F8A]/20">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-[#A5A5A5] w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="10" r="7"/><path d="M21 21l-6-6"/></svg>
                <span id="search-spinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                    <svg class="animate-spin text-[#0C3F8A] w-4 h-4" viewBox="0 0 16 16" fill="none"><circle class="opacity-25" cx="8" cy="8" r="7" stroke="currentColor" stroke-width="2"/><path class="opacity-75" fill="currentColor" d="M8 0a8 8 0 018 8h-2a6 6 0 00-6-6V0z"/></svg>
                </span>
            </div>
            <div class="flex gap-2">
                <select id="filter-kategori" class="flex-1 h-9 bg-white border border-[#EAEAEA] rounded-lg px-2 text-xs text-[#1E1E1E] focus:outline-none focus:ring-2 focus:ring-[#0C3F8A]/20">
                    <option value="">Kategori</option>
                </select>
                <select id="filter-status" class="flex-1 h-9 bg-white border border-[#EAEAEA] rounded-lg px-2 text-xs text-[#1E1E1E] focus:outline-none focus:ring-2 focus:ring-[#0C3F8A]/20">
                    <option value="">Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                    <option value="proses">Proses</option>
                </select>
            </div>
            <div class="flex gap-2">
                <select id="filter-district" class="flex-1 h-9 bg-white border border-[#EAEAEA] rounded-lg px-2 text-xs text-[#1E1E1E] focus:outline-none focus:ring-2 focus:ring-[#0C3F8A]/20">
                    <option value="">Kecamatan</option>
                </select>
                <select id="filter-village" class="flex-1 h-9 bg-white border border-[#EAEAEA] rounded-lg px-2 text-xs text-[#1E1E1E] focus:outline-none focus:ring-2 focus:ring-[#0C3F8A]/20" disabled>
                    <option value="">Kelurahan</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button id="btn-radius" class="flex-1 h-9 bg-white border border-[#EAEAEA] rounded-lg text-xs text-[#1E1E1E] font-medium hover:bg-gray-50 transition">🔘 Radius</button>
                <button id="btn-clear" class="flex-1 h-9 bg-white border border-[#EAEAEA] rounded-lg text-xs text-[#1E1E1E] font-medium hover:bg-gray-50 transition">✕ Reset</button>
            </div>
        </div>

        @hasanyrole('Administrator|Operator')
        <div class="flex-shrink-0 px-6 pb-3">
            <div class="border border-[#EAEAEA] rounded-xl p-3 bg-gray-50">
                <select id="draw-layer-select" class="w-full h-9 bg-white border border-[#EAEAEA] rounded-lg px-2 text-xs text-[#1E1E1E] focus:outline-none focus:ring-2 focus:ring-[#0C3F8A]/20 mb-2">
                    <option value="">Pilih layer tujuan...</option>
                </select>
                <div class="flex gap-1.5">
                    <button id="btn-draw-point" class="flex-1 h-8 bg-[#0C3F8A] text-white text-xs rounded-lg hover:bg-[#0a3575] disabled:opacity-50 disabled:cursor-not-allowed transition" disabled>Point</button>
                    <button id="btn-draw-line" class="flex-1 h-8 bg-[#0C3F8A] text-white text-xs rounded-lg hover:bg-[#0a3575] disabled:opacity-50 disabled:cursor-not-allowed transition" disabled>Line</button>
                    <button id="btn-draw-polygon" class="flex-1 h-8 bg-[#0C3F8A] text-white text-xs rounded-lg hover:bg-[#0a3575] disabled:opacity-50 disabled:cursor-not-allowed transition" disabled>Polygon</button>
                    <button id="btn-draw-cancel" class="h-8 px-3 bg-white border border-[#FF2020] text-[#FF2020] text-xs rounded-lg hover:bg-red-50 transition hidden">Batal</button>
                </div>
            </div>
        </div>
        @endhasanyrole

        <div id="search-info" class="hidden flex-shrink-0 mx-6 px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-700 flex items-center justify-between">
            <span id="search-count"></span>
            <button id="btn-clear-results" class="text-blue-500 hover:text-blue-700 font-medium">Tutup</button>
        </div>

        <div id="layer-list" class="flex-1 overflow-y-auto px-6 py-2 space-y-0.5 custom-scrollbar">
            <p class="text-xs text-[#A5A5A5] text-center py-8">Memuat layer...</p>
        </div>

        <div class="flex-shrink-0 px-6 pb-6 pt-3">
            <button id="btn-hapus-semua" class="w-full h-14 bg-[#FF2020] rounded-xl text-white font-semibold text-sm">Hapus Semua</button>
        </div>
    </div>

    <div id="map-toolbar" class="fixed z-[1000] flex flex-row gap-2" style="top:145px; right:20px;">
        <button data-toolbar="home" class="toolbar-btn" title="Home">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l-2 0l9-9l9 9l-2 0"/><path d="M9 21v-6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v6"/></svg>
        </button>
        <button data-toolbar="fullscreen" class="toolbar-btn" title="Fullscreen">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3h-3a2 2 0 0 0-2 2v3"/><path d="M21 8v-3a2 2 0 0 0-2-2h-3"/><path d="M16 21h3a2 2 0 0 0 2-2v-3"/><path d="M3 16v3a2 2 0 0 0 2 2h3"/></svg>
        </button>
        <button data-toolbar="zoom-extent" class="toolbar-btn" title="Zoom Extent">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 4h-2a2 2 0 0 0-2 2v2"/><path d="M20 8v-2a2 2 0 0 0-2-2h-2"/><path d="M16 20h2a2 2 0 0 0 2-2v-2"/><path d="M4 16v2a2 2 0 0 0 2 2h2"/></svg>
        </button>
        <button data-toolbar="measure" class="toolbar-btn" title="Measure">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20l4-4"/><path d="M14 4l-4 4"/><path d="M6 18l4-4"/><path d="M12 12l-4 4"/><path d="M16 16l-4 4"/><path d="M20 20l-4 4"/></svg>
        </button>
        <button data-toolbar="gps" class="toolbar-btn" title="GPS">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0-6 0"/><path d="M12 2l0 4"/><path d="M12 18l0 4"/><path d="M2 12l4 0"/><path d="M18 12l4 0"/></svg>
        </button>
        <button data-toolbar="radius" class="toolbar-btn" title="Radius">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
        <button data-toolbar="basemap" class="toolbar-btn" title="Ganti Basemap">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        </button>
    </div>

    <div id="coord-widget" class="fixed z-[1000] bg-white rounded-xl shadow-[0_4px_15px_rgba(0,0,0,0.15)] p-4" style="top:255px; right:20px; width:380px;">
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <span class="text-[11px] text-[#A5A5A5]">Latitude</span>
                <div class="text-sm text-[#1E1E1E] font-mono" id="mouse-coord">-6.921410</div>
            </div>
            <div class="flex-1">
                <span class="text-[11px] text-[#A5A5A5]">Longitude</span>
                <div class="text-sm text-[#1E1E1E] font-mono" id="lng-display">106.925700</div>
            </div>
        </div>
    </div>

    <div id="radius-modal" class="fixed inset-0 z-[2000] hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 mx-4">
            <h3 class="text-lg font-semibold mb-4 text-[#1E1E1E]">Cari dalam Radius</h3>
            <p class="text-sm text-gray-500 mb-3">Klik titik pusat di peta, lalu masukkan radius.</p>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Radius (meter)</label>
                <input type="number" id="radius-value" value="1000" min="1" max="50000" class="w-full border border-[#EAEAEA] rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" id="radius-batal" class="px-4 py-2 text-sm border border-[#EAEAEA] rounded-lg hover:bg-gray-50 text-[#1E1E1E]">Batal</button>
                <button type="button" id="radius-cari" class="px-4 py-2 text-sm bg-[#0C3F8A] text-white rounded-lg hover:bg-[#0a3575]">Cari</button>
            </div>
        </div>
    </div>

    <div id="digitasi-modal" class="fixed inset-0 z-[2000] hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 mx-4">
            <h3 class="text-lg font-semibold mb-4 text-[#1E1E1E]" id="dig-title">Simpan Data Spasial</h3>
            <form id="digitasi-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="dig-method" value="POST">
                <input type="hidden" name="layer_id" id="dig-layer-id">
                <input type="hidden" name="geometry" id="dig-geometry">
                <input type="hidden" name="data_id" id="dig-data-id" value="">

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" name="nama" id="dig-nama" class="w-full border border-[#EAEAEA] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C3F8A]/20" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="dig-deskripsi" rows="2" class="w-full border border-[#EAEAEA] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C3F8A]/20"></textarea>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="kategori_id" id="dig-kategori" class="w-full border border-[#EAEAEA] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C3F8A]/20">
                        <option value="">-- Pilih --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                    <input type="file" name="foto" id="dig-foto" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#0C3F8A]/10 file:text-[#0C3F8A] hover:file:bg-[#0C3F8A]/20">
                </div>
                <div class="flex gap-2 justify-end mt-4">
                    <button type="button" id="dig-batal" class="px-4 py-2 text-sm border border-[#EAEAEA] rounded-lg hover:bg-gray-50 text-[#1E1E1E]">Batal</button>
                    <button type="submit" id="dig-submit" class="px-4 py-2 text-sm bg-[#0C3F8A] text-white rounded-lg hover:bg-[#0a3575]">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="import-modal" class="fixed inset-0 z-[2000] hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#EAEAEA]">
                <h3 class="text-lg font-semibold text-[#1E1E1E]">Import Data</h3>
                <button onclick="document.getElementById('import-modal').classList.add('hidden'); document.getElementById('import-modal').classList.remove('flex')" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border border-[#EAEAEA] rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-[#1E1E1E] mb-3">Import GeoJSON</h4>
                    <form action="{{ route('import.geojson') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <select name="layer_id" class="w-full h-9 bg-white border border-[#EAEAEA] rounded-lg px-2 text-xs text-[#1E1E1E] focus:outline-none focus:ring-2 focus:ring-[#0C3F8A]/20" required>
                                <option value="">Pilih layer...</option>
                                @foreach ($layers as $l)
                                    <option value="{{ $l->id }}">{{ $l->nama }} ({{ $l->geom_type }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="file" name="file" accept=".json,.geojson" class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-[#0C3F8A]/10 file:text-[#0C3F8A] hover:file:bg-[#0C3F8A]/20" required>
                        </div>
                        <button type="submit" class="w-full h-8 bg-[#0C3F8A] text-white text-xs rounded-lg hover:bg-[#0a3575] transition font-medium">Import</button>
                    </form>
                </div>
                <div class="border border-[#EAEAEA] rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-[#1E1E1E] mb-3">Import CSV</h4>
                    <p class="text-[10px] text-gray-400 mb-2">CSV harus punya kolom <strong>latitude</strong> &amp; <strong>longitude</strong>.</p>
                    <form action="{{ route('import.csv') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <select name="layer_id" class="w-full h-9 bg-white border border-[#EAEAEA] rounded-lg px-2 text-xs text-[#1E1E1E] focus:outline-none focus:ring-2 focus:ring-[#0C3F8A]/20" required>
                                <option value="">Pilih layer Point...</option>
                                @foreach ($layers->where('geom_type', 'Point') as $l)
                                    <option value="{{ $l->id }}">{{ $l->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="file" name="file" accept=".csv" class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-[#0C3F8A]/10 file:text-[#0C3F8A] hover:file:bg-[#0C3F8A]/20" required>
                        </div>
                        <button type="submit" class="w-full h-8 bg-[#0C3F8A] text-white text-xs rounded-lg hover:bg-[#0a3575] transition font-medium">Import</button>
                    </form>
                </div>
                <div class="border border-[#EAEAEA] rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-[#1E1E1E] mb-3">Import SHP</h4>
                    <p class="text-[10px] text-gray-400 mb-2">Upload .zip berisi .shp + .shx + .dbf.</p>
                    <form action="{{ route('import.shp') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <select name="layer_id" class="w-full h-9 bg-white border border-[#EAEAEA] rounded-lg px-2 text-xs text-[#1E1E1E] focus:outline-none focus:ring-2 focus:ring-[#0C3F8A]/20" required>
                                <option value="">Pilih layer...</option>
                                @foreach ($layers as $l)
                                    <option value="{{ $l->id }}">{{ $l->nama }} ({{ $l->geom_type }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="file" name="file" accept=".zip" class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-[#0C3F8A]/10 file:text-[#0C3F8A] hover:file:bg-[#0C3F8A]/20" required>
                        </div>
                        <button type="submit" class="w-full h-8 bg-[#FF2020] text-white text-xs rounded-lg hover:bg-[#d91b1b] transition font-medium">Import SHP</button>
                    </form>
                </div>
                <div class="border border-[#EAEAEA] rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-[#1E1E1E] mb-3">Import KML</h4>
                    <p class="text-[10px] text-gray-400 mb-2">Upload file .kml atau .kmz.</p>
                    <form action="{{ route('import.kml') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <select name="layer_id" class="w-full h-9 bg-white border border-[#EAEAEA] rounded-lg px-2 text-xs text-[#1E1E1E] focus:outline-none focus:ring-2 focus:ring-[#0C3F8A]/20" required>
                                <option value="">Pilih layer...</option>
                                @foreach ($layers as $l)
                                    <option value="{{ $l->id }}">{{ $l->nama }} ({{ $l->geom_type }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="file" name="file" accept=".kml,.kmz" class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-[#0C3F8A]/10 file:text-[#0C3F8A] hover:file:bg-[#0C3F8A]/20" required>
                        </div>
                        <button type="submit" class="w-full h-8 bg-[#16a34a] text-white text-xs rounded-lg hover:bg-[#15803d] transition font-medium">Import KML</button>
                    </form>
                </div>
            </div>
            <div class="px-6 pb-6">
                <div class="border border-[#EAEAEA] rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-[#1E1E1E] mb-2">Data Contoh</h4>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('import.contoh', 'contoh-titik.geojson') }}" class="text-xs text-blue-600 hover:underline">Titik (GeoJSON)</a>
                        <a href="{{ route('import.contoh', 'contoh-garis.geojson') }}" class="text-xs text-blue-600 hover:underline">Garis (GeoJSON)</a>
                        <a href="{{ route('import.contoh', 'contoh-poligon.geojson') }}" class="text-xs text-blue-600 hover:underline">Polygon (GeoJSON)</a>
                        <a href="{{ route('import.contoh', 'contoh-titik.csv') }}" class="text-xs text-blue-600 hover:underline">Titik (CSV)</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
document.getElementById('btn-toggle-sidebar').addEventListener('click', function () {
    const sidebar = document.getElementById('sidebar');
    const isHidden = sidebar.style.display === 'none';
    sidebar.style.display = isHidden ? 'flex' : 'none';
});

window._REFERENSI = {
    kategori: @json($kategori),
    districts: @json($districts),
};
</script>
<style>
.toolbar-btn {
    width: 44px; height: 44px;
    display: inline-flex; align-items: center; justify-content: center;
    border: none; border-radius: 10px;
    background: #fff;
    color: #1E1E1E;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    transition: all 0.15s ease;
}
.toolbar-btn:hover {
    transform: scale(1.05);
    background: #f8f9fa;
}
.toolbar-btn.active {
    color: #fff;
    background: #0C3F8A;
}
.toolbar-btn.active:hover {
    background: #0a3575;
}
.custom-scrollbar::-webkit-scrollbar { width: 8px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #0C3F8A; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #EAEAEA; border-radius: 4px; }

.leaflet-control-zoom {
    border: none !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15) !important;
}
.leaflet-control-zoom a {
    background: #fff !important;
    color: #1E1E1E !important;
    width: 44px !important;
    height: 44px !important;
    line-height: 44px !important;
    font-size: 22px !important;
}
.leaflet-control-zoom a:first-child {
    border-radius: 10px 10px 0 0 !important;
    border-bottom: 1px solid #EAEAEA !important;
}
.leaflet-control-zoom a:last-child {
    border-radius: 0 0 10px 10px !important;
}
</style>
</body>
</html>
