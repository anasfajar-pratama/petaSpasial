<x-layouts.guest>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ url('/') }}" class="text-sm text-blue-600 hover:text-blue-800 mb-6 inline-block">&larr; Kembali ke Beranda</a>

        <div class="bg-white rounded-lg shadow p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Pengaduan</h1>
            <p class="text-gray-600 mb-6">Silakan isi form di bawah untuk mengajukan pengaduan. Simpan nomor pengaduan Anda untuk mengecek status.</p>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                    @if (session('no_pengaduan'))
                        <div class="mt-3 p-4 bg-white border border-green-300 rounded-lg">
                            <p class="text-sm text-green-700 mb-1">Nomor Pengaduan Anda:</p>
                            <p class="text-2xl font-bold text-gray-900 tracking-wide">{{ session('no_pengaduan') }}</p>
                            <a href="{{ route('cek-pengaduan', ['no' => session('no_pengaduan')]) }}"
                                class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                Cek Status Pengaduan
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('pengaduan.kirim') }}" enctype="multipart/form-data" id="form-pengaduan">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK <span class="text-red-500">*</span></label>
                        <input type="text" name="nik" id="nik" value="{{ old('nik') }}" required maxlength="16" inputmode="numeric"
                            placeholder="16 digit NIK"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}" required maxlength="100"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required maxlength="100"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. HP <span class="text-red-500">*</span></label>
                        <input type="tel" name="no_hp" value="{{ old('no_hp') }}" required maxlength="20"
                            placeholder="08xxxxxxxxxx"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto (bukti pengaduan, opsional)</label>
                    <div class="flex flex-wrap items-center gap-3">
                        <button type="button" id="btn-galeri"
                            class="px-4 py-2 bg-blue-50 text-blue-700 text-sm font-medium rounded-lg border border-blue-200 hover:bg-blue-100 transition">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Ambil dari Galeri
                        </button>
                        <button type="button" id="btn-kamera"
                            class="px-4 py-2 bg-blue-50 text-blue-700 text-sm font-medium rounded-lg border border-blue-200 hover:bg-blue-100 transition">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0016.07 7H17a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Foto Langsung
                        </button>
                        <img id="foto-preview" class="hidden w-20 h-20 rounded-lg object-cover border-2 border-green-500" alt="Preview Foto">
                    </div>
                    <input type="file" name="foto" id="input-galeri" accept="image/*" class="hidden">
                    <input type="file" id="input-kamera" accept="image/*" capture="environment" class="hidden">
                    <p class="text-xs text-gray-500 mt-1">Maksimal 5MB. Format: JPG, PNG, GIF. Akan dikonversi ke WEBP.</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titik Koordinat (opsional)</label>
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        <button type="button" id="btn-lokasi"
                            class="px-4 py-2 bg-green-50 text-green-700 text-sm font-medium rounded-lg border border-green-200 hover:bg-green-100 transition">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Gunakan Lokasi Saat Ini
                        </button>
                        <span id="lokasi-teks" class="text-sm text-gray-600"></span>
                    </div>
                    <input type="text" name="maps_link" id="maps_link" value="{{ old('maps_link') }}" maxlength="1000"
                        placeholder="Atau tempel link Google Maps (https://maps.app.goo.gl/... atau @lat,lng)"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                    <p class="text-xs text-gray-500 mt-1">Koordinat akan terdeteksi otomatis dari link Google Maps.</p>
                </div>

                <button type="submit"
                    class="w-full sm:w-auto px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Kirim Pengaduan
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/pengaduan.js')
    @endpush
</x-layouts.guest>
