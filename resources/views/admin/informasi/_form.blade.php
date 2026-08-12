@php
    $item ??= null;
    $tipe = old('tipe', $tipe ?? $item?->tipe ?? 'berita');

    $judul = old('judul', $item?->judul ?? '');
    $isi = old('isi', $item?->isi ?? '');
    $jenis = old('jenis', $item?->jenis ?? '');
    $penulis = old('penulis', $item?->penulis ?? '');
    $tahun = old('tahun', $item?->tahun ?? now()->year);
    $tanggal = old('tanggal', $item?->tanggal?->format('Y-m-d') ?? now()->format('Y-m-d'));
    $urutan = old('urutan', $item?->urutan ?? 0);
    $aktif = old('is_active', $item?->is_active ?? 1);
    $mediaType = old('media_type', $item?->video_url ? 'video' : 'gambar');
@endphp

<input type="hidden" name="tipe" value="{{ $tipe }}">

<div class="mb-2">
    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
    <p class="text-sm text-gray-500 font-medium">
        @php $label = ['berita' => 'Berita', 'infografis' => 'Infografis', 'panduan' => 'Panduan Teknis', 'riset' => 'Riset & Publikasi'][$tipe]; @endphp
        {{ $label }}
    </p>
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
    <input type="text" name="judul" value="{{ $judul }}" class="w-full border rounded-lg px-3 py-2" required>
    @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

@if ($tipe === 'riset')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
        <select name="jenis" class="w-full border rounded-lg px-3 py-2">
            @foreach (['Jurnal', 'Laporan Teknis', 'Prosiding', 'Buku'] as $opt)
                <option value="{{ $opt }}" {{ $jenis === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Penulis</label>
        <input type="text" name="penulis" value="{{ $penulis }}" class="w-full border rounded-lg px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
        <input type="number" name="tahun" value="{{ $tahun }}" min="1900" max="2100" class="w-full border rounded-lg px-3 py-2">
    </div>
</div>
@elseif ($tipe === 'berita')
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
    <input type="date" name="tanggal" value="{{ $tanggal }}" class="w-full sm:w-64 border rounded-lg px-3 py-2">
    @error('tanggal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>
@endif

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tipe === 'panduan' ? 'Isi / Langkah' : 'Isi' }}</label>
    <textarea name="isi" rows="6" class="w-full border rounded-lg px-3 py-2" required>{{ $isi }}</textarea>
    @error('isi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-2">Media (Pilih salah satu)</label>

    <div class="flex gap-5 mb-3">
        <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
            <input type="radio" name="media_type" value="gambar" {{ $mediaType === 'gambar' ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            Upload Gambar
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
            <input type="radio" name="media_type" value="video" {{ $mediaType === 'video' ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            URL Video (YouTube)
        </label>
    </div>

    <div id="panel-gambar" class="{{ $mediaType === 'gambar' ? '' : 'hidden' }}">
        <input type="file" name="gambar" id="gambar-input" accept="image/*" class="w-full border rounded-lg px-3 py-2">
        <p class="text-xs text-gray-400 mt-1">Dikonversi ke WebP saat disimpan. Media baru akan menggantikan media lama.</p>
        <div id="gambar-preview" class="mt-3 {{ $item && $item->gambar ? '' : 'hidden' }}">
            <div class="relative w-full max-w-xl aspect-video bg-gray-100 rounded-lg overflow-hidden border">
                @if ($item && $item->gambar)
                    <img id="gambar-preview-img" src="{{ asset('storage/' . $item->gambar) }}" alt="Preview" class="w-full h-full object-cover">
                @else
                    <img id="gambar-preview-img" src="" alt="Preview" class="w-full h-full object-cover hidden">
                @endif
            </div>
            <p class="text-xs text-gray-400 mt-1">Preview gambar (rasio 16:9).</p>
        </div>
    </div>

    <div id="panel-video" class="{{ $mediaType === 'video' ? '' : 'hidden' }}">
        <input type="url" name="video_url" id="video-url-input" value="{{ old('video_url', $item?->video_url ?? '') }}" placeholder="https://www.youtube.com/watch?v=..." class="w-full border rounded-lg px-3 py-2">
        <p class="text-xs text-gray-400 mt-1">Tempel URL video YouTube (watch, youtu.be, shorts, atau embed).</p>
        <div id="video-preview" class="mt-3 {{ $item && $item->videoId() ? '' : 'hidden' }}">
            <div class="relative w-full max-w-xl aspect-video bg-gray-100 rounded-lg overflow-hidden border">
                <img id="video-preview-img" src="{{ $item && $item->videoId() ? 'https://img.youtube.com/vi/' . $item->videoId() . '/hqdefault.jpg' : '' }}" alt="Preview video" class="w-full h-full object-cover {{ $item && $item->videoId() ? '' : 'hidden' }}">
            </div>
            <p class="text-xs text-gray-400 mt-1">Preview thumbnail video YouTube (16:9).</p>
        </div>
    </div>

    @error('gambar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    @error('video_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
        <input type="number" name="urutan" value="{{ $urutan }}" min="0" class="w-full border rounded-lg px-3 py-2">
        <p class="text-xs text-gray-400 mt-1">Semakin kecil, tampil lebih dulu.</p>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select name="is_active" class="w-full border rounded-lg px-3 py-2">
            <option value="1" {{ $aktif ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ !$aktif ? 'selected' : '' }}>Nonaktif</option>
        </select>
    </div>
</div>

<script>
(function () {
    const mediaGambar = document.querySelector('input[name="media_type"][value="gambar"]');
    const mediaVideo = document.querySelector('input[name="media_type"][value="video"]');
    const panelGambar = document.getElementById('panel-gambar');
    const panelVideo = document.getElementById('panel-video');
    const gambarInput = document.getElementById('gambar-input');
    const gambarPreview = document.getElementById('gambar-preview');
    const gambarPreviewImg = document.getElementById('gambar-preview-img');
    const videoUrlInput = document.getElementById('video-url-input');
    const videoPreview = document.getElementById('video-preview');
    const videoPreviewImg = document.getElementById('video-preview-img');

    function extractYoutubeId(url) {
        const m = (url || '').match(/(?:youtube\.com\/(?:watch\?.*v=|embed\/|shorts\/|live\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/);
        return m ? m[1] : null;
    }

    function togglePanels() {
        const isVideo = mediaVideo.checked;
        panelGambar.classList.toggle('hidden', isVideo);
        panelVideo.classList.toggle('hidden', !isVideo);
        updateVideoPreview();
    }

    function updateVideoPreview() {
        const id = extractYoutubeId(videoUrlInput.value);
        if (id) {
            videoPreviewImg.src = 'https://img.youtube.com/vi/' + id + '/hqdefault.jpg';
            videoPreviewImg.classList.remove('hidden');
            videoPreview.classList.remove('hidden');
        } else {
            videoPreview.classList.add('hidden');
        }
    }

    mediaGambar.addEventListener('change', togglePanels);
    mediaVideo.addEventListener('change', togglePanels);
    gambarInput.addEventListener('change', function () {
        const file = gambarInput.files && gambarInput.files[0];
        if (file) {
            gambarPreviewImg.src = URL.createObjectURL(file);
            gambarPreviewImg.classList.remove('hidden');
            gambarPreview.classList.remove('hidden');
        }
    });
    videoUrlInput.addEventListener('input', updateVideoPreview);
    togglePanels();
})();
</script>