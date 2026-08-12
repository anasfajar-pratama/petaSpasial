<x-layouts.guest>
    @php
        $meta = [
            'berita' => [
                'label' => 'Berita',
                'route' => 'informasi.berita',
                'color' => 'text-blue-600',
                'bg' => 'bg-blue-100',
                'hover' => 'hover:bg-blue-50',
                'border' => 'border-blue-600',
                'badge' => 'bg-blue-50 text-blue-700 ring-blue-200',
                'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
            ],
            'infografis' => [
                'label' => 'Infografis',
                'route' => 'informasi.infografis',
                'color' => 'text-green-600',
                'bg' => 'bg-green-100',
                'hover' => 'hover:bg-green-50',
                'border' => 'border-green-600',
                'badge' => 'bg-green-50 text-green-700 ring-green-200',
                'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
            ],
            'panduan' => [
                'label' => 'Panduan Teknis',
                'route' => 'informasi.panduan-teknis',
                'color' => 'text-orange-600',
                'bg' => 'bg-orange-100',
                'hover' => 'hover:bg-orange-50',
                'border' => 'border-orange-600',
                'badge' => 'bg-orange-50 text-orange-700 ring-orange-200',
                'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            ],
        ];
        $meta['riset'] = [
            'label' => 'Riset & Publikasi',
            'route' => 'informasi.riset-publikasi',
            'color' => 'text-purple-600',
            'bg' => 'bg-purple-100',
            'hover' => 'hover:bg-purple-50',
            'border' => 'border-purple-600',
            'badge' => 'bg-purple-50 text-purple-700 ring-purple-200',
            'icon' => $meta['panduan']['icon'],
        ];
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ url('/') }}#informasi" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Beranda
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6 sm:p-8">
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $meta[$tipe]['badge'] }} px-2.5 py-1 rounded-full ring-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $meta[$tipe]['icon'] }}"/></svg>
                        {{ $meta[$tipe]['label'] }}
                    </span>

                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mt-4">{{ $informasi->judul }}</h1>

                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-gray-500 mt-4">
                        @if ($informasi->tanggal)
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $informasi->tanggal->format('d F Y') }}
                            </span>
                        @endif
                        @if ($informasi->jenis)
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                {{ $informasi->jenis }}
                            </span>
                        @endif
                        @if ($informasi->penulis)
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $informasi->penulis }}
                            </span>
                        @endif
                        @if ($informasi->tahun)
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $informasi->tahun }}
                            </span>
                        @endif
                    </div>

                    @if ($informasi->gambar)
                        <img src="{{ asset('storage/' . $informasi->gambar) }}" alt="{{ $informasi->judul }}" class="w-full object-cover rounded-lg mt-6 shadow-sm">
                    @endif

                    @if ($informasi->videoId())
                        <div class="relative w-full aspect-video bg-black rounded-lg overflow-hidden shadow-sm mt-6">
                            <iframe class="absolute inset-0 w-full h-full" src="https://www.youtube.com/embed/{{ $informasi->videoId() }}" title="{{ $informasi->judul }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    @endif

                    <div class="mt-6 text-gray-700 text-base leading-relaxed">
                        {!! nl2br(e($informasi->isi)) !!}
                    </div>
                </div>
            </div>

            <aside class="lg:col-span-1 lg:sticky lg:top-6 space-y-6">
                @foreach (\App\Models\Informasi::TIPES as $t)
                    @php $m = $meta[$t]; @endphp
                    <div class="bg-white rounded-lg shadow p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 {{ $m['bg'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 {{ $m['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $m['icon'] }}"/></svg>
                                </div>
                                <a href="{{ route($m['route']) }}" class="text-sm font-semibold text-gray-800 {{ $m['hover'] }} transition">{{ $m['label'] }}</a>
                            </div>
                            <a href="{{ route($m['route']) }}" class="text-xs {{ $m['color'] }} hover:underline flex-shrink-0">Semua</a>
                        </div>
                        <ul class="space-y-2">
                            @foreach ($sidebars[$t] as $item)
                                <li>
                                    <a href="{{ route('informasi.detail', ['tipe' => $item->tipe, 'informasi' => $item->id]) }}"
                                        class="block text-sm py-1.5 px-3 rounded-lg border-l-2 transition
                                            {{ $item->id === $informasi->id && $t === $tipe
                                                ? $m['border'] . ' bg-gray-50 font-semibold'
                                                : 'border-gray-200 hover:bg-gray-50' }}">
                                        <span class="{{ $item->id === $informasi->id && $t === $tipe ? $m['color'] : 'text-gray-600' }}">{{ $item->judul }}</span>
                                    </a>
                                </li>
                            @endforeach
                            @if ($sidebars[$t]->isEmpty())
                                <li class="text-xs text-gray-400 py-1.5 px-3">Belum ada {{ strtolower($m['label']) }}.</li>
                            @endif
                        </ul>
                    </div>
                @endforeach
            </aside>
        </div>
    </div>
</x-layouts.guest>