<x-layouts.admin>
    <div class="mb-6">
        <a href="{{ route('data-spasial.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Tambah Data Spasial (Manual)</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-lg">
        <form action="{{ route('data-spasial.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Layer</label>
                <select name="layer_id" id="layer-select" class="w-full border rounded-lg px-3 py-2" required onchange="updateGeomTypeHint()">
                    @foreach ($layers as $l)
                        <option value="{{ $l->id }}" data-geom="{{ $l->geom_type }}" {{ old('layer_id') == $l->id ? 'selected' : '' }}>{{ $l->nama }} ({{ $l->geom_type }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="nama" value="{{ old('nama') }}" class="w-full border rounded-lg px-3 py-2" required>
                @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="w-full border rounded-lg px-3 py-2">{{ old('deskripsi') }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="kategori_id" class="w-full border rounded-lg px-3 py-2">
                    <option value="">-- Pilih --</option>
                    @foreach ($kategoriList as $k)
                        <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <input type="number" name="tahun" value="{{ old('tahun', date('Y')) }}" min="1900" max="2099" class="w-full border rounded-lg px-3 py-2">
            </div>

            <div id="manual-coords" class="border-t pt-4 mt-4">
                <p class="text-sm font-medium text-gray-700 mb-2">Koordinat <span id="geom-hint" class="text-gray-400 text-xs">(Point)</span></p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Latitude</label>
                        <input type="number" name="latitude" step="any" value="{{ old('latitude') }}" class="w-full border rounded-lg px-3 py-2" placeholder="-6.9217">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Longitude</label>
                        <input type="number" name="longitude" step="any" value="{{ old('longitude') }}" class="w-full border rounded-lg px-3 py-2" placeholder="106.9273">
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-1">Input koordinat untuk tipe Point. Untuk Line/Polygon gunakan digitasi di peta.</p>
            </div>

            <button type="submit" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
        </form>
    </div>
</x-layouts.admin>
