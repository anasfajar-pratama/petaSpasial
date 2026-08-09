@php
    $layer ??= null;
    $iconVal = old('icon_marker', $layer->icon_marker ?? null);
    $styleRaw = old('style_json', $layer->style_json ?? null);
    $styleVal = is_array($styleRaw) ? json_encode($styleRaw) : $styleRaw;
@endphp

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Simbolisasi <span class="text-gray-400 text-xs">(otomatis mengikuti Tipe Geometri)</span></label>

    <div id="simb-hint" class="text-xs text-gray-500 mb-2">Pilih dari galeri simbol, atau isi manual di kolom bawah.</div>

    <input type="hidden" name="icon_marker" id="simb-icon-marker" value="{{ $iconVal }}">
    <input type="hidden" name="style_json" id="simb-style-json" value="{{ is_string($styleVal) ? $styleVal : '' }}">

    <div id="simb-panel-point" class="hidden">
        <div class="border border-gray-200 rounded-lg p-3 mb-2">
            <input type="text" id="simb-cari-marker" placeholder="Cari ikon..." class="w-full sm:w-60 border rounded-lg px-3 py-1.5 text-sm mb-2">
            <div id="simb-grid-marker" class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2 max-h-64 overflow-y-auto p-1">
                @foreach ($simbMarker as $m)
                    <button type="button" data-pilih-icon="{{ $m['icon'] }}"
                            class="simb-item group border rounded-lg p-1.5 text-center hover:border-blue-400 focus:outline-none"
                            title="{{ $m['name'] }}">
                        <img src="{{ $m['icon'] }}" alt="{{ $m['name'] }}" class="w-8 h-8 mx-auto object-contain" loading="lazy">
                        <span class="block text-[10px] text-gray-500 truncate">{{ $m['name'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div id="simb-panel-line" class="hidden">
        <div class="border border-gray-200 rounded-lg p-3 mb-2 max-h-64 overflow-y-auto space-y-1.5">
            @foreach ($simbLine as $l)
                @php $styleData = ['name' => $l['name'], 'weight' => $l['weight'] ?? null, 'dash' => $l['dash'] ?? [], 'color' => $l['color'] ?? null]; @endphp
                <button type="button" data-pilih-style="{{ json_encode($styleData) }}" data-warna="{{ $l['color'] ?? '' }}"
                        class="simb-item w-full flex items-center gap-3 border rounded-lg px-2 py-1.5 text-left hover:border-blue-400 focus:outline-none">
                    <svg class="w-14 h-3 flex-shrink-0" viewBox="0 0 56 4">
                        <line x1="0" y1="2" x2="56" y2="2"
                              stroke="{{ $l['color'] ?? '#333' }}"
                              stroke-width="{{ isset($l['weight']) ? min(3, max(1, $l['weight'])) : 2 }}"
                              stroke-dasharray="{{ !empty($l['dash']) ? implode(' ', array_map(fn($d) => $d * 3, $l['dash'])) : '' }}"/>
                    </svg>
                    <span class="text-xs text-gray-600 truncate">{{ $l['name'] }}</span>
                    @if (!empty($l['is_font_based'])) <span class="text-[10px] text-amber-600 ml-auto">font</span> @endif
                </button>
            @endforeach
        </div>
    </div>

    <div id="simb-panel-fill" class="hidden">
        <div class="border border-gray-200 rounded-lg p-3 mb-2 max-h-64 overflow-y-auto space-y-1.5">
            @foreach ($simbFill as $f)
                @php $fill = ['name' => $f['name'], 'color' => $f['color'] ?? null, 'outline' => $f['outline'] ?? null, 'pattern' => $f['pattern'] ?? 'solid']; @endphp
                <button type="button" data-pilih-style="{{ json_encode($fill) }}" data-warna="{{ $f['outline'] ?? ($f['color'] ?? '') }}"
                        class="simb-item w-full flex items-center gap-3 border rounded-lg px-2 py-1.5 text-left hover:border-blue-400 focus:outline-none">
                    <span class="inline-block w-9 h-6 rounded border flex-shrink-0"
                          style="background-color: {{ $f['color'] ?? 'transparent' }}; {{ $f['pattern'] === 'hatch' && !empty($f['color']) ? 'background-image: repeating-linear-gradient(45deg,' . $f['color'] . ' 0 2px, transparent 2px 6px);' : '' }} border-color: {{ $f['outline'] ?? '#333' }}"></span>
                    <span class="text-xs text-gray-600 truncate">{{ $f['name'] }}</span>
                    @if ($f['pattern'] === 'hatch') <span class="text-[10px] text-amber-600 ml-auto">pola</span> @endif
                </button>
            @endforeach
        </div>
    </div>

    <div class="flex items-center gap-2 mt-2">
        <input type="text" id="simb-icon-manual" value="{{ $iconVal }}"
               placeholder="ISI manual path ikon (opsional)" class="w-full sm:w-72 border rounded-lg px-3 py-1.5 text-sm">
        <button type="button" id="simb-clear" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg text-gray-500 hover:text-red-600 hover:border-red-300">Reset</button>
        <span id="simb-info" class="text-xs text-gray-400 truncate">{{ $iconVal }}</span>
    </div>
    @error('icon_marker') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const panels = {
            Point: 'simb-panel-point',
            LineString: 'simb-panel-line',
            Polygon: 'simb-panel-fill',
            MultiPolygon: 'simb-panel-fill',
        };
        const selGeom = document.querySelector('select[name="geom_type"]');
        const styleInput = document.getElementById('simb-style-json');
        const iconInput = document.getElementById('simb-icon-marker');
        const manual = document.getElementById('simb-icon-manual');
        const info = document.getElementById('simb-info');

        function setPanel(geom) {
            Object.entries(panels).forEach(([k, id]) => {
                const el = document.getElementById(id);
                if (el) el.classList.toggle('hidden', k !== geom);
            });
        }

        function setHighlight(btn, on) {
            btn.classList.toggle('ring-2', on);
            btn.classList.toggle('ring-blue-500', on);
            btn.classList.toggle('bg-blue-50', on);
        }

        function clearHighlight(scope) {
            (scope || document).querySelectorAll('.simb-item').forEach(it => setHighlight(it, false));
        }

        function selectStyle(btn) {
            clearHighlight(btn.closest('[id]'));
            setHighlight(btn, true);
            if (styleInput) styleInput.value = btn.getAttribute('data-pilih-style') || '';
            const warna = btn.getAttribute('data-warna');
            if (warna) {
                const c = document.querySelector('input[name="warna"]');
                const cx = document.querySelector('input[name="warna_hex"]');
                if (c) c.value = warna;
                if (cx) cx.value = warna;
            }
        }

        function restoreIconHighlight() {
            const v = iconInput.value;
            if (!v) return;
            document.querySelectorAll('#simb-grid-marker [data-pilih-icon]').forEach(b => {
                setHighlight(b, b.getAttribute('data-pilih-icon') === v);
            });
        }

        function restoreStyleHighlight() {
            let want = null;
            try { want = JSON.parse(styleInput.value); } catch (_) { return; }
            if (!want || !want.name) return;
            ['simb-panel-line', 'simb-panel-fill'].forEach(id => {
                document.querySelectorAll('#' + id + ' [data-pilih-style]').forEach(b => {
                    let data = null;
                    try { data = JSON.parse(b.getAttribute('data-pilih-style')); } catch (_) { return; }
                    setHighlight(b, data.name === want.name);
                });
            });
        }

        document.getElementById('simb-grid-marker')?.addEventListener('click', e => {
            const btn = e.target.closest('[data-pilih-icon]');
            if (!btn) return;
            clearHighlight(document.getElementById('simb-grid-marker'));
            setHighlight(btn, true);
            iconInput.value = btn.getAttribute('data-pilih-icon');
            if (manual) manual.value = iconInput.value;
            if (info) info.textContent = iconInput.value;
            styleInput.value = '';
        });

        ['simb-panel-line', 'simb-panel-fill'].forEach(id => {
            document.getElementById(id)?.addEventListener('click', e => {
                const btn = e.target.closest('[data-pilih-style]');
                if (btn) {
                    clearHighlight(document.getElementById(id));
                    setHighlight(btn, true);
                    styleInput.value = btn.getAttribute('data-pilih-style') || '';
                    iconInput.value = '';
                    if (manual) manual.value = '';
                    if (info) info.textContent = '';
                    const warna = btn.getAttribute('data-warna');
                    if (warna) {
                        const c = document.querySelector('input[name="warna"]');
                        const cx = document.querySelector('input[name="warna_hex"]');
                        if (c) c.value = warna;
                        if (cx) cx.value = warna;
                    }
                }
            });
        });

        document.getElementById('simb-cari-marker')?.addEventListener('input', e => {
            const q = e.target.value.toLowerCase().trim();
            document.querySelectorAll('#simb-grid-marker .simb-item').forEach(it => {
                it.classList.toggle('hidden', q !== '' && !(it.getAttribute('title') || '').toLowerCase().includes(q));
            });
        });

        manual?.addEventListener('input', () => {
            iconInput.value = manual.value;
            styleInput.value = '';
            if (info) info.textContent = manual.value;
        });

        document.getElementById('simb-clear')?.addEventListener('click', () => {
            iconInput.value = '';
            styleInput.value = '';
            if (manual) manual.value = '';
            if (info) info.textContent = '';
            clearHighlight(document);
        });

        if (selGeom) selGeom.addEventListener('change', () => setPanel(selGeom.value));
        if (selGeom) setPanel(selGeom.value);
        restoreIconHighlight();
        restoreStyleHighlight();
    });
</script>
@endpush
