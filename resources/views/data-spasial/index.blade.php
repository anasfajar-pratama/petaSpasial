<x-layouts.admin>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Data Spasial</h1>
        <a href="{{ route('data-spasial.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">+ Tambah Data</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <form method="GET" class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Layer</label>
                <select name="layer_id" class="border rounded-lg px-3 py-2" onchange="this.form.submit()">
                    <option value="">Semua Layer</option>
                    @foreach ($layers as $l)
                        <option value="{{ $l->id }}" {{ request('layer_id') == $l->id ? 'selected' : '' }}>{{ $l->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" class="border rounded-lg px-3 py-2" placeholder="Nama...">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Filter</button>
            @if (request('layer_id') || request('search'))
                <a href="{{ route('data-spasial.index') }}" class="text-sm text-blue-600 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <form action="{{ route('data-spasial.bulk-destroy') }}" method="POST" onsubmit="return confirm('Hapus data terpilih?')">
            @csrf
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 w-10"><input type="checkbox" id="select-all" class="rounded border-gray-300"></th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Layer</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tahun</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($dataSpasial as $ds)
                    <tr>
                        <td class="px-4 py-4"><input type="checkbox" name="ids[]" value="{{ $ds->id }}" class="row-checkbox rounded border-gray-300"></td>
                        <td class="px-6 py-4 font-medium">{{ $ds->nama }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs rounded" style="background:{{ $ds->layer->warna ?? '#ccc' }}20; color:{{ $ds->layer->warna ?? '#333' }}">
                                {{ $ds->layer->nama ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm">{{ $ds->status }}</td>
                        <td class="px-6 py-4 text-center text-sm">{{ $ds->tahun ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('data-spasial.edit', $ds) }}" class="text-blue-600 hover:text-blue-800 mr-3">Edit</a>
                            <form action="{{ route('data-spasial.destroy', $ds) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada data spasial.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-3 border-t flex items-center justify-between">
                <button type="submit" class="px-3 py-1.5 text-sm bg-red-600 text-white rounded hover:bg-red-700 hidden" id="btn-bulk-delete">Hapus Terpilih</button>
                <div>{{ $dataSpasial->links() }}</div>
            </div>
        </form>
    </div>
</x-layouts.admin>

<script>
document.getElementById('select-all')?.addEventListener('change', function() {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
    toggleBulkDelete();
});
document.querySelectorAll('.row-checkbox').forEach(cb => cb.addEventListener('change', toggleBulkDelete));
function toggleBulkDelete() {
    const checked = document.querySelectorAll('.row-checkbox:checked').length;
    const btn = document.getElementById('btn-bulk-delete');
    if (btn) btn.classList.toggle('hidden', checked === 0);
}
</script>
