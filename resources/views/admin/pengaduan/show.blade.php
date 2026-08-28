<x-layouts.admin>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('admin.pengaduan.index') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </a>
                <span class="text-gray-300">|</span>
                <h1 class="text-xl font-bold text-gray-800">Detail Pengaduan</h1>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $statusBadges = [
                    'baru' => 'bg-yellow-100 text-yellow-800',
                    'proses' => 'bg-blue-100 text-blue-800',
                    'tolak' => 'bg-red-100 text-red-800',
                    'selesai' => 'bg-green-100 text-green-800',
                ];
            @endphp

            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-start justify-between flex-wrap gap-3">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Nomor Pengaduan</p>
                            <p class="text-xl font-bold text-gray-900">{{ $pengaduan->no_pengaduan }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $statusBadges[$pengaduan->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ \App\Models\Pengaduan::STATUS[$pengaduan->status] ?? $pengaduan->status }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-3">Diajukan pada {{ $pengaduan->created_at->format('d M Y H:i') }}</p>
                </div>

                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">NIK</p>
                            <p class="mt-1 text-gray-800">{{ $pengaduan->nik }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nama</p>
                            <p class="mt-1 text-gray-800">{{ $pengaduan->nama }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Email</p>
                            <p class="mt-1 text-gray-800">{{ $pengaduan->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">No. HP</p>
                            <p class="mt-1 text-gray-800">{{ $pengaduan->no_hp }}</p>
                        </div>
                    </div>

                    @if ($pengaduan->foto)
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Foto</p>
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $pengaduan->foto) }}" target="_blank">
                                <img src="{{ asset('storage/' . $pengaduan->foto) }}" alt="Foto Pengaduan"
                                    class="max-w-sm rounded-lg border border-gray-200 hover:opacity-90 transition cursor-pointer">
                            </a>
                        </div>
                    </div>
                    @endif

                    @if ($pengaduan->latitude && $pengaduan->longitude)
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Lokasi</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $pengaduan->latitude }}, {{ $pengaduan->longitude }}</p>
                        <a href="https://www.google.com/maps?q={{ $pengaduan->latitude }},{{ $pengaduan->longitude }}" target="_blank"
                            class="inline-block mt-2 text-sm text-blue-600 hover:text-blue-800">
                            Buka di Google Maps &rarr;
                        </a>
                        <div class="mt-3">
                            <iframe
                                src="https://maps.google.com/maps?q={{ $pengaduan->latitude }},{{ $pengaduan->longitude }}&z=15&output=embed"
                                class="w-full h-64 rounded-lg border border-gray-200" loading="lazy"></iframe>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden mt-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Update Status &amp; Feedback</h2>
                    <p class="text-sm text-gray-500 mt-1">Ubah status pengaduan dan berikan progress / feedback yang akan tampil di halaman cek pengaduan.</p>
                </div>
                <form method="POST" action="{{ route('admin.pengaduan.update', $pengaduan) }}" class="p-6 space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @foreach (\App\Models\Pengaduan::STATUS as $value => $label)
                                <option value="{{ $value }}" {{ $pengaduan->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Progress</label>
                        <textarea name="catatan_progress" rows="3" maxlength="2000"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Contoh: Pengaduan diterima, sedang diverifikasi oleh petugas...">{{ old('catatan_progress', $pengaduan->catatan_progress) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Feedback / Tanggapan</label>
                        <textarea name="feedback" rows="3" maxlength="2000"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Tanggapan untuk pelapor...">{{ old('feedback', $pengaduan->feedback) }}</textarea>
                    </div>

                    <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
