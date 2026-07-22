<x-layouts.admin>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Wilayah</h1>
        @if ($regency)
        <p class="text-gray-500">Kabupaten/Kota aktif: <strong>{{ $regency->nama }}</strong></p>
        @endif
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Tambah Kecamatan</h2>
        <form action="{{ route('admin.wilayah.district.store') }}" method="POST" class="flex gap-4 items-end">
            @csrf
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode</label>
                <input type="text" name="kode" value="{{ old('kode') }}" class="w-full border rounded-lg px-3 py-2" placeholder="32.02.xx" required>
            </div>
            <div class="flex-[2]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kecamatan</label>
                <input type="text" name="nama" value="{{ old('nama') }}" class="w-full border rounded-lg px-3 py-2" required>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Tambah</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kecamatan</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah Desa</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($districts as $district)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $district->kode }}</td>
                    <td class="px-6 py-4 font-medium">{{ $district->nama }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.wilayah.villages', $district) }}" class="text-blue-600 hover:text-blue-800">
                            {{ $district->villages_count }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.wilayah.villages', $district) }}" class="text-green-600 hover:text-green-800 mr-3">Desa</a>
                        <a href="{{ route('admin.wilayah.district.edit', $district) }}" class="text-blue-600 hover:text-blue-800 mr-3">Edit</a>
                        <form action="{{ route('admin.wilayah.district.destroy', $district) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kecamatan ini? Semua desa di dalamnya juga akan terhapus.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">
            {{ $districts->links() }}
        </div>
    </div>
</x-layouts.admin>
