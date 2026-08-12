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
    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar (opsional, dikonversi ke WebP)</label>
    <input type="file" name="gambar" accept="image/*" class="w-full border rounded-lg px-3 py-2">
    @if ($item && $item->gambar)
        <div class="mt-2">
            <img src="{{ asset('storage/' . $item->gambar) }}" alt="gambar" class="w-40 h-28 object-cover rounded border">
        </div>
    @endif
    @error('gambar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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