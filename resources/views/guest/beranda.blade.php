<x-layouts.guest>
    <div class="bg-gradient-to-br from-blue-600 to-blue-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <h1 class="text-4xl sm:text-5xl font-bold mb-4">{{ config('app.name', 'petaSpasial') }}</h1>
            <p class="text-lg sm:text-xl text-blue-100 max-w-2xl mx-auto">
                Sistem Informasi Geospasial — Visualisasi, kelola, dan analisis data spasial wilayah secara interaktif.
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
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Jelajahi Data Spasial Wilayah</h2>
        <p class="text-gray-500 max-w-xl mx-auto">
            Akses informasi geospasial secara interaktif. Lihat peta, cari lokasi, dan dapatkan informasi data spasial terkini.
        </p>
    </div>
</x-layouts.guest>
