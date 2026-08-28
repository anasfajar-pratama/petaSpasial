<x-layouts.guest>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ url('/') }}" class="text-sm text-blue-600 hover:text-blue-800 mb-6 inline-block">&larr; Kembali ke Beranda</a>

        <div class="bg-white rounded-lg shadow p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Cek Pengaduan</h1>
            <p class="text-gray-600 mb-6">Masukkan nomor pengaduan Anda untuk melihat status dan perkembangannya.</p>

            <form method="GET" action="{{ route('cek-pengaduan') }}" class="mb-8">
                <div class="flex gap-3">
                    <input type="text" name="no" value="{{ request('no') }}" required placeholder="Contoh: PDG-20260828-AB12C"
                        class="flex-1 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                        Cek
                    </button>
                </div>
            </form>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (request()->filled('no') && !$pengaduan)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    Pengaduan dengan nomor <strong>{{ request('no') }}</strong> tidak ditemukan. Periksa kembali nomor Anda.
                </div>
            @endif

            @if ($pengaduan)
                @php
                    $statusColors = [
                        'baru' => 'bg-yellow-100 text-yellow-800',
                        'proses' => 'bg-blue-100 text-blue-800',
                        'tolak' => 'bg-red-100 text-red-800',
                        'selesai' => 'bg-green-100 text-green-800',
                    ];
                    $badge = $statusColors[$pengaduan->status] ?? 'bg-gray-100 text-gray-700';
                @endphp

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Nomor Pengaduan</p>
                                <p class="text-xl font-bold text-gray-900">{{ $pengaduan->no_pengaduan }}</p>
                            </div>
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $badge }}">
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
                            <p class="mt-1 text-sm text-gray-700">
                                {{ $pengaduan->latitude }}, {{ $pengaduan->longitude }}
                            </p>
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

                        @if ($pengaduan->catatan_progress)
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Progress</p>
                            <div class="mt-1 p-4 bg-blue-50 border border-blue-100 rounded-lg text-gray-700 whitespace-pre-wrap">
                                {{ $pengaduan->catatan_progress }}
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Diperbarui: {{ $pengaduan->updated_at->format('d M Y H:i') }}</p>
                        </div>
                        @endif

                        @if ($pengaduan->feedback)
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Feedback / Tanggapan Admin</p>
                            <div class="mt-1 p-4 bg-green-50 border border-green-200 rounded-lg text-gray-700 whitespace-pre-wrap">
                                {{ $pengaduan->feedback }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.guest>
