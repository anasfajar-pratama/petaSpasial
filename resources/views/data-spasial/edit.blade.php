<x-layouts.admin>
    <div class="mb-6">
        <a href="{{ route('data-spasial.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Edit Data Spasial</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-lg">
        <form action="{{ route('data-spasial.update', $dataSpasial) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Layer</label>
                <select name="layer_id" class="w-full border rounded-lg px-3 py-2" required>
                    @foreach ($layers as $l)
                        <option value="{{ $l->id }}" {{ $dataSpasial->layer_id == $l->id ? 'selected' : '' }}>{{ $l->nama }} ({{ $l->geom_type }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="nama" value="{{ old('nama', $dataSpasial->nama) }}" class="w-full border rounded-lg px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="w-full border rounded-lg px-3 py-2">{{ old('deskripsi', $dataSpasial->deskripsi) }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="kategori_id" class="w-full border rounded-lg px-3 py-2">
                    <option value="">-- Pilih --</option>
                    @foreach ($kategoriList as $k)
                        <option value="{{ $k->id }}" {{ $dataSpasial->kategori_id == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2">
                    <option value="aktif" {{ $dataSpasial->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ $dataSpasial->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    <option value="draft" {{ $dataSpasial->status == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <input type="number" name="tahun" value="{{ old('tahun', $dataSpasial->tahun) }}" min="1900" max="2099" class="w-full border rounded-lg px-3 py-2">
            </div>
            <div class="border-t pt-4 mt-4">
                <p class="text-sm font-medium text-gray-700 mb-2">Koordinat</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Latitude</label>
                        <input type="number" name="latitude" step="any" value="{{ old('latitude', $lat) }}" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Longitude</label>
                        <input type="number" name="longitude" step="any" value="{{ old('longitude', $lng) }}" class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>
            </div>
            <button type="submit" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
        </form>
    </div>
</x-layouts.admin>
