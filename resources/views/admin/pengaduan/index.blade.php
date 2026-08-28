<x-layouts.admin>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">Kelola Pengaduan</h1>
                <span class="text-sm text-gray-500">Total: {{ $pengaduans->total() }}</span>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form method="GET" class="mb-4 flex flex-wrap gap-3">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nomor pengaduan, NIK, nama..."
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <div class="w-40">
                    <select name="status" onchange="this.form.submit()"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Semua Status</option>
                        @foreach (\App\Models\Pengaduan::STATUS as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
                    Cari
                </button>
                @if (request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.pengaduan.index') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition">
                        Reset
                    </a>
                @endif
            </form>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Pengaduan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIK</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $statusBadges = [
                                'baru' => 'bg-yellow-100 text-yellow-800',
                                'proses' => 'bg-blue-100 text-blue-800',
                                'tolak' => 'bg-red-100 text-red-800',
                                'selesai' => 'bg-green-100 text-green-800',
                            ];
                        @endphp
                        @forelse ($pengaduans as $pengaduan)
                            <tr class="{{ $pengaduan->status === 'baru' ? 'bg-blue-50' : '' }}">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration + ($pengaduans->currentPage() - 1) * $pengaduans->perPage() }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $pengaduan->no_pengaduan }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $pengaduan->nama }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $pengaduan->nik }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusBadges[$pengaduan->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ \App\Models\Pengaduan::STATUS[$pengaduan->status] ?? $pengaduan->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $pengaduan->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('admin.pengaduan.show', $pengaduan) }}"
                                        class="text-blue-600 hover:text-blue-800 transition" title="Lihat">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada pengaduan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $pengaduans->links() }}
            </div>
        </div>
    </div>
</x-layouts.admin>
