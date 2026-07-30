<x-layouts.guest>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ url('/#informasi') }}" class="text-sm text-blue-600 hover:text-blue-800 mb-6 inline-block">&larr; Kembali ke Beranda</a>

        <div class="bg-white rounded-lg shadow p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">FAQ</h1>
            <p class="text-gray-600 mb-6">Pertanyaan yang sering diajukan.</p>

            <div class="space-y-3" x-data="{ active: null }">
                @php
                    $faqs = [
                        ['q' => 'Apa itu petaSpasial?', 'a' => 'petaSpasial adalah Sistem Informasi Geografis (SIG) berbasis web yang menyajikan data spasial dan statistik untuk wilayah Kota Sukabumi.'],
                        ['q' => 'Apakah petaSpasial gratis digunakan?', 'a' => 'Ya, peta publik dapat diakses secara gratis oleh siapa saja. Fitur administrasi dan pengelolaan data tersedia untuk pengguna yang memiliki akses.'],
                        ['q' => 'Bagaimana cara mengakses peta interaktif?', 'a' => 'Kunjungi menu Peta di navigasi utama. Anda dapat memperbesar/memperkecil peta, mengaktifkan/menonaktifkan layer, dan melihat detail data spasial.'],
                        ['q' => 'Bagaimana cara mengunduh data spasial?', 'a' => 'Data spasial dapat diunduh melalui menu Export di halaman admin. Format yang tersedia: SHP, GeoJSON, KML, dan CSV.'],
                        ['q' => 'Apa yang harus dilakukan jika data tidak muncul?', 'a' => 'Pastikan layer yang diinginkan sudah diaktifkan di panel layer. Jika masih bermasalah, hubungi administrator.'],
                        ['q' => 'Bagaimana cara memberikan kritik dan saran?', 'a' => 'Silakan gunakan form Kritik & Saran yang tersedia di halaman ini atau klik tombol floating 💬 di pojok kanan bawah.'],
                        ['q' => 'Apakah data yang ditampilkan selalu terbaru?', 'a' => 'Data diperbarui secara berkala oleh operator. Untuk informasi lebih lanjut mengenai jadwal pembaruan, hubungi admin.'],
                    ];
                @endphp

                @foreach ($faqs as $i => $faq)
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button
                            @click="active = active === {{ $i }} ? null : {{ $i }}"
                            class="w-full flex items-center justify-between px-5 py-4 text-left text-gray-800 font-medium hover:bg-gray-50 transition"
                        >
                            <span>{{ $faq['q'] }}</span>
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                                :class="active === {{ $i }} ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="active === {{ $i }}" x-collapse class="px-5 pb-4 text-gray-600 text-sm">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.guest>
