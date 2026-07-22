<x-layouts.admin>
    <div class="mb-6">
        <a href="{{ route('admin.kategori.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Tambah Kategori</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-lg">
        <form action="{{ route('admin.kategori.store') }}" method="POST">
            @csrf
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Ikon</label>
                <input type="text" name="icon" value="{{ old('icon') }}" class="w-full border rounded-lg px-3 py-2" placeholder="road, building, water, dll">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Warna Default</label>
                <div class="flex items-center gap-2">
                    <input type="color" name="warna_default" value="{{ old('warna_default', '#3388ff') }}" class="h-10 w-20 border rounded cursor-pointer">
                    <input type="text" name="warna_default_hex" value="{{ old('warna_default', '#3388ff') }}" class="border rounded-lg px-3 py-2 flex-1" maxlength="9">
                </div>
                @error('warna_default') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
        </form>
    </div>
</x-layouts.admin>
