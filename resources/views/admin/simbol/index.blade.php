<x-layouts.admin>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Galeri Simbolisasi</h1>
        <p class="text-sm text-gray-500 mt-1">402 simbol hasil konversi <code>Simbolisasi.style</code> (ArcGIS) &mdash; Marker {{ $markers->count() }}, Line {{ $lines->count() }}, Fill {{ $fills->count() }}.</p>
        <p class="text-xs text-gray-400 mt-1">Simbol bertanda "font" masih berupa perkiraan (font ESRI belum tersedia); akan diganti saat font diterima.</p>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="border-b border-gray-200 flex">
            <button type="button" data-tab="marker" class="tab-btn flex-1 px-4 py-3 text-sm font-medium border-b-2 active:text-[#0C3F8A] active:border-[#0C3F8A] text-gray-500 border-transparent hover:text-gray-700">Marker ({{ $markers->count() }})</button>
            <button type="button" data-tab="line" class="tab-btn flex-1 px-4 py-3 text-sm font-medium border-b-2 text-gray-500 border-transparent hover:text-gray-700">Line ({{ $lines->count() }})</button>
            <button type="button" data-tab="fill" class="tab-btn flex-1 px-4 py-3 text-sm font-medium border-b-2 text-gray-500 border-transparent hover:text-gray-700">Fill ({{ $fills->count() }})</button>
        </div>

        <div class="p-4">
            <input type="text" id="galeri-cari" placeholder="Cari nama simbol..." class="w-full sm:w-72 border rounded-lg px-3 py-2 mb-4 text-sm">

            <div id="panel-marker">
                @forelse ($markers->groupBy('category') as $kategori => $items)
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2 mt-5">{{ $kategori ?: 'Tanpa Kategori' }}</h3>
                    <div class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-8 lg:grid-cols-10 gap-3 simbol-item">
                        @foreach ($items as $m)
                            <div class="simbol-card border border-gray-200 rounded-lg p-2 text-center hover:shadow" data-nama="{{ strtolower($m['name']) }}">
                                <img src="{{ $m['icon'] }}" alt="{{ $m['name'] }}" class="w-10 h-10 mx-auto object-contain" loading="lazy">
                                <p class="text-[11px] leading-tight mt-1 text-gray-600">{{ $m['name'] }}</p>
                                @if (!empty($m['is_font_based']))
                                    <span class="text-[10px] text-amber-600">font-based</span>
                                @else
                                    <span class="text-[10px] text-gray-400">{{ $m['color'] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Belum ada simbol marker.</p>
                @endforelse
            </div>

            <div id="panel-line" class="hidden space-y-2">
                @forelse ($lines as $l)
                    <div class="simbol-item border border-gray-200 rounded-lg px-4 py-3 hover:border-blue-300 flex items-center justify-between gap-4" data-nama="{{ strtolower($l['name']) }}">
                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            <svg class="w-14 h-3 flex-shrink-0" viewBox="0 0 56 4">
                                <line x1="0" y1="2" x2="56" y2="2"
                                      stroke="{{ $l['color'] ?? '#333' }}"
                                      stroke-width="{{ isset($l['weight']) ? min(3, max(1, $l['weight'])) : 2 }}"
                                      stroke-dasharray="{{ !empty($l['dash']) ? implode(' ', array_map(fn($d) => $d * 3, $l['dash'])) : '' }}"/>
                            </svg>
                            <p class="text-sm text-gray-700 truncate">{{ $l['name'] }}</p>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0 text-xs text-gray-400">
                            <span>{{ $l['color'] ?? 'n/a' }}</span>
                            @if (isset($l['weight'])) <span>{{ $l['weight'] }}px</span> @endif
                            @if (!empty($l['dash'])) <span>dash</span> @endif
                            @if (!empty($l['is_font_based'])) <span class="text-amber-600">font-based</span> @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Belum ada simbol garis.</p>
                @endforelse
            </div>

            <div id="panel-fill" class="hidden space-y-2">
                @forelse ($fills as $f)
                    <div class="simbol-item border border-gray-200 rounded-lg px-3 py-3 flex items-center gap-3 hover:border-blue-300" data-nama="{{ strtolower($f['name']) }}">
                        <span class="inline-block w-10 h-8 rounded border flex-shrink-0"
                              style="background-color: {{ $f['color'] ?? 'transparent' }}; {{ $f['pattern'] === 'hatch' && !empty($f['color']) ? 'background-image: repeating-linear-gradient(45deg,' . $f['color'] . ' 0 2px, transparent 2px 6px);' : '' }} border-color: {{ $f['outline'] ?? '#333' }}"></span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-700 truncate">{{ $f['name'] }}</p>
                            <p class="text-xs text-gray-400">fill {{ $f['color'] ?? 'n/a' }}@if($f['outline']) &middot; outline {{ $f['outline'] }}@endif &middot; {{ $f['pattern'] }}</p>
                        </div>
                        @if (!empty($f['pattern']) && $f['pattern'] === 'hatch')
                            <span class="text-xs text-amber-600 flex-shrink-0">pola</span>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Belum ada simbol poligon.</p>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            const tabs = document.querySelectorAll('.tab-btn');
            const panels = { marker: 'panel-marker', line: 'panel-line', fill: 'panel-fill' };
            let activeTab = 'marker';

            function setActive(tab) {
                activeTab = tab;
                tabs.forEach(b => {
                    const on = b.dataset.tab === tab;
                    b.classList.toggle('text-[#0C3F8A]', on);
                    b.classList.toggle('border-[#0C3F8A]', on);
                    b.classList.toggle('text-gray-500', !on);
                    b.classList.toggle('border-transparent', !on);
                });
                Object.entries(panels).forEach(([k, id]) => {
                    document.getElementById(id).classList.toggle('hidden', k !== tab);
                });
            }

            tabs.forEach(t => t.addEventListener('click', () => renderActive(t.dataset.tab)));

            const input = document.getElementById('galeri-cari');
            input.addEventListener('input', () => {
                const q = input.value.toLowerCase().trim();
                const container = document.getElementById(panels[activeTab]);
                [...container.querySelectorAll('.simbol-item')].forEach(item => {
                    const nama = (item.dataset.nama || '').toLowerCase();
                    const group = item.closest('.grid');
                    item.classList.toggle('hidden', q !== '' && !nama.includes(q));
                    if (group && group.querySelectorAll('.simbol-item:not(.hidden)').length === 0) {
                        const heading = group.previousElementSibling;
                        if (heading && heading.tagName === 'H3') heading.classList.toggle('hidden', q !== '');
                    }
                });
            });

            renderActive('marker');
        })();
    </script>
    @endpush
</x-layouts.admin>