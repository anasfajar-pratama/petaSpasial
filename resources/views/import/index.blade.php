<x-layouts.admin>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Import Data</h1>
        <p class="text-gray-500 text-sm">Upload GeoJSON, CSV, atau SHP (zip) untuk ditambahkan ke layer.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Import GeoJSON</h2>
            <form action="{{ route('import.geojson') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Layer Tujuan</label>
                    <select name="layer_id" class="w-full border rounded-lg px-3 py-2" required>
                        @foreach ($layers as $l)
                            <option value="{{ $l->id }}">{{ $l->nama }} ({{ $l->geom_type }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">File (.geojson, .json)</label>
                    <input type="file" name="file" accept=".json,.geojson" class="w-full border rounded-lg px-3 py-2" required>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Import</button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Import CSV</h2>
            <p class="text-xs text-gray-400 mb-3">CSV harus memiliki kolom <strong>latitude</strong> dan <strong>longitude</strong>. Opsional: <strong>nama</strong>.</p>
            <form action="{{ route('import.csv') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Layer Tujuan (harus Point)</label>
                    <select name="layer_id" class="w-full border rounded-lg px-3 py-2" required>
                        @foreach ($layers->where('geom_type', 'Point') as $l)
                            <option value="{{ $l->id }}">{{ $l->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">File (.csv)</label>
                    <input type="file" name="file" accept=".csv" class="w-full border rounded-lg px-3 py-2" required>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Import</button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Import SHP</h2>
            <p class="text-xs text-gray-400 mb-3">Upload file <strong>.zip</strong> berisi file .shp + .shx + .dbf. Mendukung nested zip dan reproject otomatis ke WGS84.</p>
            <form action="{{ route('import.shp') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Layer Tujuan</label>
                    <select name="layer_id" class="w-full border rounded-lg px-3 py-2" required>
                        @foreach ($layers as $l)
                            <option value="{{ $l->id }}">{{ $l->nama }} ({{ $l->geom_type }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">File (.zip berisi SHP)</label>
                    <input type="file" name="file" accept=".zip" class="w-full border rounded-lg px-3 py-2" required>
                </div>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">Import SHP</button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Import KML</h2>
            <p class="text-xs text-gray-400 mb-3">Upload file <strong>.kml</strong> atau <strong>.kmz</strong>. Dikonversi via GDAL ke GeoJSON.</p>
            <form action="{{ route('import.kml') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Layer Tujuan</label>
                    <select name="layer_id" class="w-full border rounded-lg px-3 py-2" required>
                        @foreach ($layers as $l)
                            <option value="{{ $l->id }}">{{ $l->nama }} ({{ $l->geom_type }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">File (.kml, .kmz)</label>
                    <input type="file" name="file" accept=".kml,.kmz" class="w-full border rounded-lg px-3 py-2" required>
                </div>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Import KML</button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h2 class="text-lg font-semibold mb-2">Data Contoh</h2>
        <p class="text-sm text-gray-500 mb-3">Download file contoh untuk testing:</p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('import.contoh', 'contoh-titik.geojson') }}" class="text-sm text-blue-600 hover:underline">🗺️ Titik (GeoJSON)</a>
            <a href="{{ route('import.contoh', 'contoh-garis.geojson') }}" class="text-sm text-blue-600 hover:underline">📏 Garis (GeoJSON)</a>
            <a href="{{ route('import.contoh', 'contoh-poligon.geojson') }}" class="text-sm text-blue-600 hover:underline">⬡ Polygon (GeoJSON)</a>
            <a href="{{ route('import.contoh', 'contoh-titik.csv') }}" class="text-sm text-blue-600 hover:underline">📍 Titik (CSV)</a>
        </div>
    </div>
</x-layouts.admin>
