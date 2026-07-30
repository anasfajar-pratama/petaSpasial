<x-layouts.guest>
    <div class="bg-gradient-to-br from-blue-600 to-blue-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <h1 class="text-4xl sm:text-5xl font-bold mb-4">{{ $settings['hero_title'] ?? config('app.name', 'petaSpasial') }}</h1>
            <p class="text-lg sm:text-xl text-blue-100 max-w-2xl mx-auto">
                {{ $settings['hero_subtitle'] ?? 'Sistem Informasi Geospasial — Visualisasi, kelola, dan analisis data spasial wilayah secara interaktif.' }}
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ url('/peta') }}" class="inline-flex items-center px-6 py-3 bg-white text-blue-700 font-semibold rounded-lg shadow hover:bg-blue-50 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    Lihat Peta
                </a>
                <a href="{{ url('/statistik-publik') }}" class="inline-flex items-center px-6 py-3 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-400 transition border border-blue-400">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Lihat Statistik
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Objek Spasial</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalObjects) }}</p>
                        <div class="flex gap-3 mt-1 text-xs text-gray-500">
                            @foreach ($objectsByType as $t)
                                <span>{{ $t->tipe }}: {{ number_format($t->c) }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="p-3 rounded-full bg-green-100">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Layer</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalLayers) }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Kecamatan</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalDistricts) }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-purple-100">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ $settings['hero_tagline'] ?? 'Jelajahi Data Spasial Wilayah' }}</h2>
        <p class="text-gray-500 max-w-xl mx-auto">
            Akses informasi geospasial secara interaktif. Lihat peta, cari lokasi, dan dapatkan informasi data spasial terkini.
        </p>
    </div>

    <div id="informasi" class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl font-bold text-gray-800">Informasi</h2>
                <p class="text-gray-500 mt-2">Berita, infografis, panduan, dan publikasi data spasial Kota Sukabumi</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="{{ route('informasi.berita') }}" class="group bg-white rounded-xl shadow p-6 hover:shadow-lg transition text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-800 mt-4 group-hover:text-blue-600 transition">Berita</h3>
                    <p class="text-sm text-gray-500 mt-2">Informasi dan berita terkini seputar pengelolaan data spasial</p>
                </a>
                <a href="{{ route('informasi.infografis') }}" class="group bg-white rounded-xl shadow p-6 hover:shadow-lg transition text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-800 mt-4 group-hover:text-green-600 transition">Infografis</h3>
                    <p class="text-sm text-gray-500 mt-2">Visualisasi data spasial dalam bentuk infografis informatif</p>
                </a>
                <a href="{{ route('informasi.panduan-teknis') }}" class="group bg-white rounded-xl shadow p-6 hover:shadow-lg transition text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-orange-100 flex items-center justify-center group-hover:bg-orange-200 transition">
                        <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-800 mt-4 group-hover:text-orange-600 transition">Panduan Teknis</h3>
                    <p class="text-sm text-gray-500 mt-2">Dokumentasi dan panduan penggunaan sistem geospasial</p>
                </a>
                <a href="{{ route('informasi.riset-publikasi') }}" class="group bg-white rounded-xl shadow p-6 hover:shadow-lg transition text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-800 mt-4 group-hover:text-purple-600 transition">Riset & Publikasi</h3>
                    <p class="text-sm text-gray-500 mt-2">Publikasi ilmiah dan hasil riset berbasis data spasial</p>
                </a>
            </div>
        </div>
    </div>
</x-layouts.guest>
