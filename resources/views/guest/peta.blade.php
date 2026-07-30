<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Peta Interaktif - {{ config('app.name', 'petaSpasial') }}</title>
    @vite(['resources/css/app.css', 'resources/js/map-public.js'])
</head>
<body class="m-0 p-0 overflow-hidden font-sans">
    <div id="map" class="w-screen h-screen"></div>

    <div id="top-nav" class="fixed top-5 left-5 right-5 z-[1000] bg-white rounded-[40px] shadow-[0_4px_15px_rgba(0,0,0,0.15)] h-[85px] flex items-center justify-between px-8">
        <a href="{{ url('/') }}" class="flex items-center gap-3 flex-shrink-0">
            <div class="w-9 h-9 rounded-lg bg-[#0C3F8A] flex items-center justify-center text-white font-bold text-sm">PS</div>
            <span class="font-bold text-[#1E1E1E] text-lg">petaSpasial</span>
        </a>
        <div class="flex items-center gap-8">
            <a href="{{ url('/') }}" class="text-[#1E1E1E] font-medium text-sm hover:text-[#0C3F8A] transition-colors">Beranda</a>
            <a href="{{ url('/peta') }}" class="text-[#0C3F8A] font-medium text-sm border-b-2 border-[#0C3F8A] pb-1">Peta</a>
            <a href="{{ url('/statistik-publik') }}" class="text-[#1E1E1E] font-medium text-sm hover:text-[#0C3F8A] transition-colors">Statistik</a>
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

    <div id="search-container" class="fixed z-[1000]" style="top:197px; right:20px; width:380px; height:50px;">
        <div class="relative w-full h-full">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-[#A5A5A5] w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="10" r="7"/><path d="M21 21l-6-6"/></svg>
            <input type="text" id="search-input" placeholder="Cari alamat atau koordinat (lat, lon)..." class="w-full h-full bg-white rounded-xl shadow-[0_4px_15px_rgba(0,0,0,0.15)] pl-12 pr-12 text-sm text-[#1E1E1E] placeholder-[#A5A5A5] focus:outline-none focus:ring-2 focus:ring-[#0C3F8A]/20">
            <span id="search-spinner" class="hidden absolute right-4 top-1/2 -translate-y-1/2">
                <svg class="animate-spin text-[#0C3F8A] w-5 h-5" viewBox="0 0 16 16" fill="none"><circle class="opacity-25" cx="8" cy="8" r="7" stroke="currentColor" stroke-width="2"/><path class="opacity-75" fill="currentColor" d="M8 0a8 8 0 018 8h-2a6 6 0 00-6-6V0z"/></svg>
            </span>
        </div>
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

<script>
document.getElementById('btn-toggle-sidebar').addEventListener('click', function () {
    const sidebar = document.getElementById('sidebar');
    const isHidden = sidebar.style.display === 'none';
    sidebar.style.display = isHidden ? 'flex' : 'none';
});
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
