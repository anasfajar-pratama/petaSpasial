<x-layouts.admin>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Layer</h1>
        <a href="{{ route('admin.layers.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">+ Tambah Layer</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <form method="GET" class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Kategori</label>
                <select name="kategori" class="border rounded-lg px-3 py-2" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoriList as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
            @if (request('kategori'))
                <a href="{{ route('admin.layers.index') }}" class="text-sm text-blue-600 hover:underline self-center">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Layer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tipe</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Warna</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Urutan</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($layers as $layer)
                <tr>
                    <td class="px-6 py-4">
                        <p class="font-medium">{{ $layer->nama }}</p>
                        <p class="text-xs text-gray-500">{{ $layer->slug }}</p>
                    </td>
                    <td class="px-6 py-4">{{ $layer->kategori->nama }}</td>
                    <td class="px-6 py-4 text-center text-sm">{{ $layer->geom_type }}</td>
                    <td class="px-6 py-4 text-center">
                        @if ($layer->icon_marker)
                            <img src="{{ $layer->icon_marker }}" alt="ikon" class="inline-block w-6 h-6 object-contain" title="{{ $layer->icon_marker }}">
                        @else
                            <span class="inline-block w-6 h-6 rounded border" style="background-color: {{ $layer->warna }}; opacity: {{ $layer->opacity }}"></span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center text-sm">{{ $layer->order }}</td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('admin.layers.toggle', $layer) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $layer->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $layer->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.layers.edit', $layer) }}" class="text-blue-600 hover:text-blue-800 mr-3">Edit</a>
                        <form action="{{ route('admin.layers.destroy', $layer) }}" method="POST" class="inline" onsubmit="return confirm('Hapus layer ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada layer. <a href="{{ route('admin.layers.create') }}" class="text-blue-600 hover:underline">Tambah layer pertama</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">
            {{ $layers->links() }}
        </div>
    </div>
</x-layouts.admin>
