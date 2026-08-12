<x-layouts.admin>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Informasi</h1>
        <a href="{{ route('admin.informasi.create', ['tipe' => $tipe]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">+ Tambah</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">{{ session('error') }}</div>
    @endif

    <div class="mb-4 border-b border-gray-200 flex gap-1">
        @foreach (['berita' => 'Berita', 'infografis' => 'Infografis', 'panduan' => 'Panduan Teknis', 'riset' => 'Riset & Publikasi'] as $key => $label)
            <a href="{{ route('admin.informasi.index', ['tipe' => $key]) }}"
               class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px {{ $tipe === $key ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300' }}">
                {{ $label }}
                @if (($counts[$key] ?? 0) > 0)
                    <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full {{ $tipe === $key ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-500' }}">{{ $counts[$key] }}</span>
                @endif
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if ($items->count() === 0)
            <p class="text-gray-400 text-sm text-center py-12">Belum ada data {{ $tipe }}. Klik "+ Tambah" untuk membuat.</p>
        @else
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gambar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                    @if ($tipe === 'riset')
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun</th>
                    @elseif ($tipe === 'berita')
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    @endif
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Urutan</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($items as $item)
                <tr>
                    <td class="px-6 py-4">
                        @if ($item->gambar)
                            <img src="{{ asset('storage/' . $item->gambar) }}" alt="" class="w-14 h-10 object-cover rounded border">
                        @else
                            <span class="inline-block w-14 h-10 rounded border bg-gray-100"></span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-medium">{{ $item->judul }}</td>
                    @if ($tipe === 'riset')
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item->jenis ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item->tahun ?? '-' }}</td>
                    @elseif ($tipe === 'berita')
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item->tanggal?->format('d M Y') ?? '-' }}</td>
                    @endif
                    <td class="px-6 py-4 text-center text-sm">{{ $item->urutan }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $item->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <a href="{{ route('admin.informasi.edit', $item) }}" class="text-blue-600 hover:text-blue-800 mr-3">Edit</a>
                        <form action="{{ route('admin.informasi.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus informasi ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</x-layouts.admin>