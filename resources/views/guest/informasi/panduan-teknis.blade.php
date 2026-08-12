<x-layouts.guest>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ url('/') }}#informasi" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Beranda
        </a>
        <div class="bg-white rounded-lg shadow p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="p-3 rounded-full bg-orange-100">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Panduan Teknis</h1>
                    <p class="text-sm text-gray-500">Dokumentasi dan panduan penggunaan sistem informasi geospasial</p>
                </div>
            </div>
            <hr class="mb-6">
            <div class="space-y-4">
                @forelse ($items as $i => $item)
                    <div class="border border-gray-200 rounded-lg p-5 flex items-start gap-4">
                        <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold">{{ $i + 1 }}</span>
                        <div>
                            <h3 class="text-base font-semibold text-gray-800">{{ $item->judul }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $item->isi }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm text-center py-8">Belum ada panduan teknis.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.guest>