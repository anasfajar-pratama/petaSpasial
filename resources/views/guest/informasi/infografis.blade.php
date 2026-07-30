<x-layouts.guest>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ url('/') }}#informasi" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Beranda
        </a>
        <div class="bg-white rounded-lg shadow p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="p-3 rounded-full bg-green-100">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Infografis</h1>
                    <p class="text-sm text-gray-500">Visualisasi data spasial dalam bentuk infografis informatif</p>
                </div>
            </div>
            <hr class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border border-gray-200 rounded-lg p-5 text-center">
                    <div class="bg-gray-100 rounded-lg h-48 flex items-center justify-center mb-3">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-800">Distribusi Objek Spasial per Kategori</h3>
                    <p class="text-xs text-gray-500 mt-1">Grafik batang distribusi objek spasial berdasarkan kategori layer</p>
                </div>
                <div class="border border-gray-200 rounded-lg p-5 text-center">
                    <div class="bg-gray-100 rounded-lg h-48 flex items-center justify-center mb-3">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-800">Sebaran Data per Kecamatan</h3>
                    <p class="text-xs text-gray-500 mt-1">Visualisasi sebaran data spasial di 7 kecamatan Kota Sukabumi</p>
                </div>
                <div class="border border-gray-200 rounded-lg p-5 text-center">
                    <div class="bg-gray-100 rounded-lg h-48 flex items-center justify-center mb-3">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-800">Komposisi Jenis Geometri</h3>
                    <p class="text-xs text-gray-500 mt-1">Proporsi data Point, Line, dan Polygon dalam sistem</p>
                </div>
                <div class="border border-gray-200 rounded-lg p-5 text-center">
                    <div class="bg-gray-100 rounded-lg h-48 flex items-center justify-center mb-3">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21h18M3 10h18M3 7l9-4 9 4M3 14l9-4 9 4"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-800">Cakupan Data per Tahun</h3>
                    <p class="text-xs text-gray-500 mt-1">Perkembangan pengumpulan data spasial dari tahun ke tahun</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>