<x-layouts.guest>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <a href="{{ url('/peta') }}" class="text-sm text-blue-600 hover:text-blue-800">&larr; Kembali ke Peta</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center gap-3 mb-4">
                <span class="inline-block w-4 h-4 rounded" style="background:{{ $layer->warna }}"></span>
                <h1 class="text-2xl font-bold text-gray-800">{{ $layer->nama }}</h1>
                <span class="px-2 py-0.5 text-xs font-medium rounded {{ $layer->geom_type === 'Point' ? 'bg-green-100 text-green-700' : ($layer->geom_type === 'LineString' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700') }}">
                    {{ $layer->geom_type }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Kategori:</span>
                    <span class="font-medium text-gray-800">{{ $layer->kategori->nama ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Tipe Geometri:</span>
                    <span class="font-medium text-gray-800">{{ $layer->geom_type }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Opacity:</span>
                    <span class="font-medium text-gray-800">{{ $layer->opacity }}</span>
                </div>
            </div>

            @if ($layer->deskripsi)
                <p class="mt-4 text-gray-600 text-sm">{{ $layer->deskripsi }}</p>
            @endif

            <div class="mt-6">
                <a href="{{ url('/peta?layer=' . $layer->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    Lihat di Peta
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Data Spasial — {{ $layer->nama }}</h2>

            @if ($dataList->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="text-left px-4 py-3 font-medium text-gray-600">No</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-600">Nama</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-600">Tahun</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataList as $i => $ds)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-500">{{ $dataList->firstItem() + $i }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $ds->nama }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 text-xs rounded {{ $ds->status === 'aktif' ? 'bg-green-100 text-green-700' : ($ds->status === 'nonaktif' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                            {{ $ds->status ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $ds->tahun ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ url('/data/' . $ds->id) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $dataList->links() }}
                </div>
            @else
                <p class="text-gray-400 text-sm py-8 text-center">Belum ada data spasial pada layer ini.</p>
            @endif
        </div>
    </div>
</x-layouts.guest>
