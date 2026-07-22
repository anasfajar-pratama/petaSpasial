<x-layouts.admin>
    <div class="mb-6">
        <a href="{{ route('admin.layers.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Edit Layer</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-lg">
        <form action="{{ route('admin.layers.update', $layer) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Layer</label>
                <input type="text" name="nama" value="{{ old('nama', $layer->nama) }}" class="w-full border rounded-lg px-3 py-2" required>
                @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="kategori_id" class="w-full border rounded-lg px-3 py-2" required>
                    @foreach ($kategoriList as $kat)
                        <option value="{{ $kat->id }}" {{ $layer->kategori_id == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Geometri</label>
                <select name="geom_type" class="w-full border rounded-lg px-3 py-2" required>
                    @foreach ($geomTypes as $type)
                        <option value="{{ $type }}" {{ $layer->geom_type == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
                <div class="flex items-center gap-2">
                    <input type="color" name="warna" value="{{ old('warna', $layer->warna) }}" class="h-10 w-20 border rounded cursor-pointer">
                    <input type="text" name="warna_hex" value="{{ old('warna', $layer->warna) }}" class="border rounded-lg px-3 py-2 flex-1" maxlength="9">
                </div>
                @error('warna') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ikon Marker</label>
                <input type="text" name="icon_marker" value="{{ old('icon_marker', $layer->icon_marker) }}" class="w-full border rounded-lg px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="w-full border rounded-lg px-3 py-2">{{ old('deskripsi', $layer->deskripsi) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Opacity (0-1)</label>
                    <input type="number" name="opacity" value="{{ old('opacity', $layer->opacity) }}" step="0.05" min="0" max="1" class="w-full border rounded-lg px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                    <input type="number" name="order" value="{{ old('order', $layer->order) }}" min="0" class="w-full border rounded-lg px-3 py-2" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ $layer->is_active ? 'checked' : '' }} class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                </label>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
        </form>
    </div>
</x-layouts.admin>
