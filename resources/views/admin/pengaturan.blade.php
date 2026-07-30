<x-layouts.admin>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pengaturan</h1>
    </div>

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

    <form method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Sitasi</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Site</label>
                        <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'petaSpasial') }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" maxlength="100" required>
                        <p class="text-xs text-gray-500 mt-1">Nama aplikasi yang tampil di navbar, title, dan sidebar.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Hero</label>
                        <input type="text" name="hero_title" value="{{ old('hero_title', $settings['hero_title'] ?? 'petaSpasial') }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" maxlength="200" required>
                        <p class="text-xs text-gray-500 mt-1">Judul besar di halaman utama (hero section).</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subjudul Hero</label>
                        <textarea name="hero_subtitle" rows="2"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" maxlength="500" required>{{ old('hero_subtitle', $settings['hero_subtitle'] ?? '') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Subjudul di bawah judul hero.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
                        <input type="text" name="hero_tagline" value="{{ old('hero_tagline', $settings['hero_tagline'] ?? 'Jelajahi Data Spasial Wilayah') }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" maxlength="500" required>
                        <p class="text-xs text-gray-500 mt-1">Tagline yang muncul di bagian eksplorasi halaman utama.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Ikon & Favicon</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ikon Site</label>
                        <input type="file" name="site_icon" accept="image/png,image/jpeg,image/x-icon"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, atau ICO. Maks 1MB. Akan muncul di browser tab.</p>
                        @if (!empty($settings['site_icon']))
                            <div class="mt-2 flex items-center gap-2">
                                <img src="{{ asset('storage/' . $settings['site_icon']) }}" class="w-10 h-10 rounded border">
                                <span class="text-xs text-gray-500">Ikon saat ini</span>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                        <input type="file" name="site_favicon" accept="image/png,image/jpeg,image/x-icon"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, atau ICO. Maks 512KB.</p>
                        @if (!empty($settings['site_favicon']))
                            <div class="mt-2 flex items-center gap-2">
                                <img src="{{ asset('storage/' . $settings['site_favicon']) }}" class="w-6 h-6 rounded border">
                                <span class="text-xs text-gray-500">Favicon saat ini</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
                Simpan Pengaturan
            </button>
        </div>
    </form>
</x-layouts.admin>
