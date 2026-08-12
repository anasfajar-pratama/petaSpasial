<x-layouts.guest>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ url('/') }}#informasi" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Beranda
        </a>
        <div class="bg-white rounded-lg shadow p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="p-3 rounded-full bg-blue-100">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Berita</h1>
                    <p class="text-sm text-gray-500">Informasi dan berita terkini seputar data spasial Kota Sukabumi</p>
                </div>
            </div>
            <hr class="mb-6">
            <div class="space-y-6">
                @forelse ($items as $item)
                    <a href="{{ route('informasi.detail', ['tipe' => $item->tipe, 'informasi' => $item->id]) }}" class="block group border border-gray-200 rounded-lg p-5 flex gap-4 hover:border-blue-300 hover:shadow-md transition">
                        @if ($item->gambar)
                            <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->judul }}" class="w-32 h-24 object-cover rounded-lg flex-shrink-0">
                        @elseif ($item->videoId())
                            <img src="https://img.youtube.com/vi/{{ $item->videoId() }}/hqdefault.jpg" alt="{{ $item->judul }}" class="w-32 h-24 object-cover rounded-lg flex-shrink-0">
                        @endif
                        <div class="flex-1">
                            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide">{{ $item->tanggal?->format('d F Y') ?? '' }}</p>
                            <h3 class="text-lg font-semibold text-gray-800 mt-1 group-hover:text-blue-600">{{ $item->judul }}</h3>
                            <p class="text-sm text-gray-600 mt-2">{{ $item->isi }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-400 text-sm text-center py-8">Belum ada berita.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.guest>