<x-layouts.admin>
    <div class="mb-6">
        <a href="{{ route('admin.wilayah.villages', $village->district) }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Edit Desa/Kelurahan</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-lg">
        <form action="{{ route('admin.wilayah.village.update', $village) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode</label>
                <input type="text" name="kode" value="{{ old('kode', $village->kode) }}" class="w-full border rounded-lg px-3 py-2" required>
                @error('kode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Desa/Kelurahan</label>
                <input type="text" name="nama" value="{{ old('nama', $village->nama) }}" class="w-full border rounded-lg px-3 py-2" required>
                @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
        </form>
    </div>
</x-layouts.admin>
