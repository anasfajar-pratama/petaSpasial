<x-layouts.admin>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Kategori</h1>
        <a href="{{ route('admin.kategori.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">+ Tambah Kategori</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Warna</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Layer</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($kategori as $item)
                <tr>
                    <td class="px-6 py-4 font-medium">{{ $item->nama }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $item->deskripsi ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-block w-6 h-6 rounded border" style="background-color: {{ $item->warna_default }}"></span>
                        <span class="text-xs text-gray-500 ml-1">{{ $item->warna_default }}</span>
                    </td>
                    <td class="px-6 py-4">{{ $item->layers_count ?? $item->layers()->count() }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.kategori.edit', $item) }}" class="text-blue-600 hover:text-blue-800 mr-3">Edit</a>
                        <form action="{{ route('admin.kategori.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">
            {{ $kategori->links() }}
        </div>
    </div>
</x-layouts.admin>
