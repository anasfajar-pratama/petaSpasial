<x-layouts.guest>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ url('/#informasi') }}" class="text-sm text-blue-600 hover:text-blue-800 mb-6 inline-block">&larr; Kembali ke Beranda</a>

        <div class="bg-white rounded-lg shadow p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Kritik &amp; Saran</h1>
            <p class="text-gray-600 mb-6">Silakan isi form di bawah untuk memberikan kritik, saran, atau masukan.</p>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
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

            <form method="POST" action="{{ route('kritik-saran.kirim') }}" enctype="multipart/form-data" id="kritik-saran-form">
                @csrf

                <div id="form-fields">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama" value="{{ old('nama') }}" required maxlength="100"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required maxlength="100"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subjek</label>
                        <input type="text" name="subjek" value="{{ old('subjek') }}" maxlength="150"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pesan / Saran <span class="text-red-500">*</span></label>
                        <textarea name="pesan" rows="5" required minlength="10"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('pesan') }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Pendukung (opsional)</label>
                        <input type="file" name="gambar" accept="image/jpeg,image/png,image/gif"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-1">Maksimal 2MB. Format: JPG, PNG, GIF. Akan dikonversi ke WEBP.</p>
                    </div>
                </div>

                <div id="face-capture-section" class="mb-6 hidden">
                    <div id="verifikasi-message" class="bg-yellow-50 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mb-4 hidden">
                        <strong>Verifikasi Wajah Anda</strong>
                        <p class="text-sm mt-1">Ambil foto wajah Anda untuk verifikasi sebelum mengirim.</p>
                    </div>

                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Wajah <span class="text-red-500">*</span></label>
                    <div class="relative bg-black rounded-lg overflow-hidden" style="max-width: 480px; aspect-ratio: 4/3;">
                        <video id="face-video" class="w-full h-full object-cover" autoplay muted playsinline></video>
                        <canvas id="face-overlay" class="absolute inset-0 w-full h-full"></canvas>
                    </div>
                    <div class="mt-3 flex items-center gap-4">
                        <button type="button" id="btn-capture"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-blue-700 transition opacity-50 cursor-not-allowed"
                            disabled>
                            Ambil Foto
                        </button>
                        <img id="face-preview" class="hidden w-16 h-16 rounded-full object-cover border-2 border-green-500" alt="Preview">
                    </div>
                    <input type="hidden" name="foto_wajah" id="foto_wajah_input" value="">
                    <p class="text-xs text-gray-500 mt-1">Posisikan wajah di dalam bingkai oval hingga tombol aktif, lalu ambil foto.</p>
                </div>

                <button type="button" id="btn-submit"
                    class="w-full sm:w-auto px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Kirim
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/kritik-saran.js')
    @endpush
</x-layouts.guest>
