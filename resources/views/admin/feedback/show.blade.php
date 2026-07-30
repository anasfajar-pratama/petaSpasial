<x-layouts.admin>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('admin.kritik-saran.index') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </a>
                <span class="text-gray-300">|</span>
                <h1 class="text-xl font-bold text-gray-800">Detail Kritik &amp; Saran</h1>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-4">
                            @if ($feedback->foto_wajah)
                                <img src="{{ asset('storage/' . $feedback->foto_wajah) }}"
                                    class="w-14 h-14 rounded-full object-cover border-2 border-blue-200">
                            @else
                                <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl font-bold">
                                    {{ strtoupper(substr($feedback->nama, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800">{{ $feedback->nama }}</h2>
                                <p class="text-sm text-gray-500">{{ $feedback->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $feedback->is_read ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $feedback->is_read ? 'Terbaca' : 'Baru' }}
                            </span>
                            <form action="{{ route('admin.kritik-saran.read', $feedback) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                                    {{ $feedback->is_read ? 'Tandai Belum Terbaca' : 'Tandai Terbaca' }}
                                </button>
                            </form>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-3">{{ $feedback->created_at->format('d M Y H:i') }}</p>
                </div>

                <div class="p-6 space-y-6">
                    @if ($feedback->subjek)
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Subjek</label>
                        <p class="mt-1 text-gray-800 font-medium">{{ $feedback->subjek }}</p>
                    </div>
                    @endif

                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pesan</label>
                        <div class="mt-1 p-4 bg-gray-50 rounded-lg text-gray-700 whitespace-pre-wrap border border-gray-100">{{ $feedback->pesan }}</div>
                    </div>

                    @if ($feedback->gambar)
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Gambar Pendukung</label>
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $feedback->gambar) }}" target="_blank">
                                <img src="{{ asset('storage/' . $feedback->gambar) }}" alt="Gambar Pendukung"
                                    class="max-w-md rounded-lg border border-gray-200 hover:opacity-90 transition cursor-pointer">
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
