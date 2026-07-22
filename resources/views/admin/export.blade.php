<x-layouts.admin>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Export Data</h1>
        <p class="text-gray-500 text-sm">Download data spasial ke berbagai format.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Export Semua Data</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('export.all', 'geojson') }}" class="inline-flex items-center px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">GeoJSON</a>
            <a href="{{ route('export.all', 'csv') }}" class="inline-flex items-center px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">CSV</a>
            <a href="{{ route('export.all', 'excel') }}" class="inline-flex items-center px-4 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Excel</a>
            <a href="{{ route('export.all', 'pdf') }}" class="inline-flex items-center px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700">PDF</a>
            <a href="{{ route('export.all', 'shp') }}" class="inline-flex items-center px-4 py-2 text-sm bg-orange-600 text-white rounded-lg hover:bg-orange-700">SHP</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold">Export Per Layer</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-gray-600">
                        <th class="px-6 py-3 font-medium">Layer</th>
                        <th class="px-6 py-3 font-medium">Tipe</th>
                        <th class="px-6 py-3 font-medium">Jumlah Data</th>
                        <th class="px-6 py-3 font-medium">Export</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($layers as $layer)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <span class="inline-block w-3 h-3 rounded-sm mr-2" style="background:{{ $layer->warna }}"></span>
                                {{ $layer->nama }}
                            </td>
                            <td class="px-6 py-3 text-gray-500">{{ $layer->geom_type }}</td>
                            <td class="px-6 py-3">{{ $layer->data_spasial_count }}</td>
                            <td class="px-6 py-3">
                                <div class="flex flex-wrap gap-1.5">
                                    <a href="{{ route('export.layer', [$layer->id, 'geojson']) }}" class="px-2.5 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600">GeoJSON</a>
                                    <a href="{{ route('export.layer', [$layer->id, 'csv']) }}" class="px-2.5 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">CSV</a>
                                    <a href="{{ route('export.layer', [$layer->id, 'excel']) }}" class="px-2.5 py-1 text-xs bg-emerald-500 text-white rounded hover:bg-emerald-600">Excel</a>
                                    <a href="{{ route('export.layer', [$layer->id, 'pdf']) }}" class="px-2.5 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600">PDF</a>
                                    <a href="{{ route('export.layer', [$layer->id, 'shp']) }}" class="px-2.5 py-1 text-xs bg-orange-500 text-white rounded hover:bg-orange-600">SHP</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">Belum ada layer.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
