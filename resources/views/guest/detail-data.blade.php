<x-layouts.guest>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <a href="{{ url('/peta') }}" class="text-sm text-blue-600 hover:text-blue-800">&larr; Kembali ke Peta</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">{{ $data->nama }}</h1>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <table class="w-full">
                        <tr>
                            <td class="text-gray-500 pr-4 py-1">Layer</td>
                            <td class="font-medium">
                                @if ($data->layer)
                                    <a href="{{ url('/layer/' . $data->layer->slug) }}" class="text-blue-600 hover:text-blue-800">{{ $data->layer->nama }}</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-gray-500 pr-4 py-1">Status</td>
                            <td class="font-medium">
                                <span class="px-2 py-0.5 text-xs rounded {{ $data->status === 'aktif' ? 'bg-green-100 text-green-700' : ($data->status === 'nonaktif' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ $data->status ?? '-' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-gray-500 pr-4 py-1">Tahun</td>
                            <td class="font-medium">{{ $data->tahun ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-gray-500 pr-4 py-1">Luas</td>
                            <td class="font-medium">{{ $data->luas ? number_format($data->luas, 2) . ' m²' : '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <table class="w-full">
                        <tr>
                            <td class="text-gray-500 pr-4 py-1">Kecamatan</td>
                            <td class="font-medium">{{ $data->district->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-gray-500 pr-4 py-1">Desa</td>
                            <td class="font-medium">{{ $data->village->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-gray-500 pr-4 py-1">Dibuat</td>
                            <td class="font-medium">{{ $data->created_at ? $data->created_at->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-gray-500 pr-4 py-1">Diupdate</td>
                            <td class="font-medium">{{ $data->updated_at ? $data->updated_at->format('d/m/Y') : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if ($data->deskripsi)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi</h3>
                    <p class="text-gray-600 text-sm">{{ $data->deskripsi }}</p>
                </div>
            @endif

            @if ($data->foto)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Foto</h3>
                    <img src="{{ asset('storage/' . $data->foto) }}" alt="{{ $data->nama }}" class="max-w-full rounded-lg shadow max-h-96">
                </div>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Lokasi</h2>
            <div id="mini-map" class="w-full h-[300px] rounded-lg border"></div>
        </div>
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const map = L.map('mini-map', {
            center: [-6.9217, 106.9273],
            zoom: 13,
            zoomControl: true,
            scrollWheelZoom: false,
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap',
        }).addTo(map);

        @if ($geometry)
            const geojson = {!! $geometry !!};
            const layer = L.geoJSON(geojson, {
                style: { color: '#3b82f6', weight: 3, fillColor: '#3b82f6', fillOpacity: 0.2 },
                pointToLayer: function (f, latlng) {
                    return L.circleMarker(latlng, {
                        radius: 10, fillColor: '#3b82f6', color: '#fff', weight: 2, fillOpacity: 0.9,
                    });
                },
            }).addTo(map);
            const bounds = layer.getBounds();
            if (bounds.isValid()) map.fitBounds(bounds, { padding: [30, 30] });
        @endif
    });
    </script>
    @endpush
</x-layouts.guest>
