<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Models\Setting::getValue('site_name', config('app.name', 'petaSpasial')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-800">
    <div class="min-h-screen flex flex-col">
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <a href="{{ url('/') }}" class="flex items-center space-x-2">
                        <x-application-logo class="w-8 h-8 fill-current text-blue-600" />
                        <span class="text-lg font-bold text-gray-800">{{ \App\Models\Setting::getValue('site_name', config('app.name', 'petaSpasial')) }}</span>
                    </a>
                    <div class="hidden sm:flex items-center space-x-6">
                        <a href="{{ url('/') }}" class="text-sm font-medium {{ request()->is('/') ? 'text-blue-600' : 'text-gray-600 hover:text-gray-800' }}">Beranda</a>
                        <a href="{{ url('/peta') }}" class="text-sm font-medium {{ request()->is('peta') ? 'text-blue-600' : 'text-gray-600 hover:text-gray-800' }}">Peta</a>
                        <a href="{{ url('/statistik-publik') }}" class="text-sm font-medium {{ request()->is('statistik-publik') ? 'text-blue-600' : 'text-gray-600 hover:text-gray-800' }}">Statistik</a>
                    </div>
                    <button id="hamburger" class="sm:hidden text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
            <div id="mobile-menu" class="hidden sm:hidden border-t px-4 py-3 space-y-2">
                <a href="{{ url('/') }}" class="block text-sm font-medium {{ request()->is('/') ? 'text-blue-600' : 'text-gray-600' }}">Beranda</a>
                <a href="{{ url('/peta') }}" class="block text-sm font-medium {{ request()->is('peta') ? 'text-blue-600' : 'text-gray-600' }}">Peta</a>
                <a href="{{ url('/statistik-publik') }}" class="block text-sm font-medium {{ request()->is('statistik-publik') ? 'text-blue-600' : 'text-gray-600' }}">Statistik</a>
            </div>
        </nav>

        <main class="flex-1">
            {{ $slot }}
        </main>

        <footer class="bg-gray-900 text-gray-400 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center space-x-2">
                        <x-application-logo class="w-6 h-6 fill-current text-gray-400" />
                        <span class="text-sm font-medium text-gray-300">{{ \App\Models\Setting::getValue('site_name', config('app.name', 'petaSpasial')) }}</span>
                    </div>
                    <p class="text-sm">&copy; {{ date('Y') }} {{ \App\Models\Setting::getValue('site_name', config('app.name', 'petaSpasial')) }}. Hak cipta dilindungi.</p>
                </div>
            </div>
        </footer>
    </div>

    <script>
        document.getElementById('hamburger')?.addEventListener('click', function () {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>

    <div x-data="{
        open: false,
        tab: 'pengunjung',
        counts: {{ Js::from($visitorCounts ?? ['hari_ini' => 0, 'minggu_ini' => 0, 'bulan_ini' => 0, 'total' => 0]) }},
        animated: { hari_ini: 0, minggu_ini: 0, bulan_ini: 0, total: 0 },
        maxTotal: {{ max($visitorCounts['total'] ?? 1, 1) }},
        animating: false,
        messages: [
            { id: 0, role: 'bot', text: 'Halo! Ada yang bisa saya bantu tentang {{ \App\Models\Setting::getValue('site_name', 'petaSpasial') }}?' }
        ],
        inputText: '',
        showOptions: true,
        msgId: 1,
        botResponses: {
            'apa itu petaspasial': '**petaSpasial** adalah Sistem Informasi Geospasial (SIG) berbasis web yang menyediakan visualisasi, pengelolaan, dan analisis data spasial wilayah secara interaktif. Anda bisa menjelajahi peta, melihat statistik, dan mengakses berbagai informasi geografis.',
            'cara melihat peta': 'Untuk melihat peta, klik tombol **Lihat Peta** di halaman utama atau pilih menu **Peta** di navigasi atas. Di halaman peta, Anda bisa memperbesar/memperkecil, mencari lokasi, dan mengklik objek untuk melihat detailnya.',
            'apa itu data spasial': 'Data spasial adalah data yang memiliki referensi geografis (koordinat) pada permukaan bumi. Contohnya: batas wilayah, jalan, sungai, persebaran fasilitas umum. Data ini bisa berupa titik (point), garis (line), atau area (polygon).',
            'cara menghubungi admin': 'Anda bisa menghubungi admin melalui form **Kritik & Saran** yang tersedia di menu navigasi atau melalui widget ini. Isi pesan Anda lengkap dengan kontak, dan admin akan merespon secepatnya.',
            'apa itu digitasi': 'Digitasi adalah proses mengonversi data spasial dari format analog (peta cetak, gambar) menjadi format digital berupa titik, garis, atau area. Di petaSpasial, pengguna dengan akses dapat melakukan digitasi langsung pada peta interaktif.',
            'kritik dan saran': 'Silakan sampaikan kritik dan saran melalui halaman **Kritik & Saran** yang bisa diakses dari menu navigasi. Setiap masukan sangat berarti untuk pengembangan petaSpasial.',
            'fitur apa saja': 'petaSpasial memiliki fitur: **Peta Interaktif** (zoom, search, layer), **Statistik** (grafik data spasial), **Digitasi** (menggambar di peta), **Informasi** (berita, infografis, panduan, publikasi), dan **Kritik & Saran**.',
        },
        animateCounter() {
            if (this.animating) return;
            this.animating = true;
            const targets = this.counts;
            const steps = 20;
            let step = 0;
            const interval = setInterval(() => {
                step++;
                this.animated = {
                    hari_ini: Math.min(Math.round((targets.hari_ini / steps) * step), targets.hari_ini),
                    minggu_ini: Math.min(Math.round((targets.minggu_ini / steps) * step), targets.minggu_ini),
                    bulan_ini: Math.min(Math.round((targets.bulan_ini / steps) * step), targets.bulan_ini),
                    total: Math.min(Math.round((targets.total / steps) * step), targets.total),
                };
                if (step >= steps) {
                    clearInterval(interval);
                    this.animated = { ...targets };
                    this.animating = false;
                }
            }, 30);
        },
        sendMessage() {
            const text = this.inputText.trim();
            if (!text) return;
            this.messages.push({ id: this.msgId++, role: 'user', text: text });
            this.inputText = '';
            this.showQuickReplies = false;
            this.$nextTick(() => this.scrollChat());
            setTimeout(() => {
                const lower = text.toLowerCase();
                let reply = null;
                for (const [key, answer] of Object.entries(this.botResponses)) {
                    if (lower.includes(key)) {
                        reply = answer;
                        break;
                    }
                }
                if (!reply) {
                    reply = 'Maaf, saya belum bisa menjawab pertanyaan tersebut. Silakan hubungi admin melalui form **Kritik & Saran** atau coba pertanyaan lain.';
                }
                this.messages.push({ id: this.msgId++, role: 'bot', text: reply });
                this.showOptions = false;
                this.$nextTick(() => this.scrollChat());
            }, 600);
        },
        quickReply(text) {
            this.inputText = text;
            this.sendMessage();
        },
        scrollChat() {
            const box = this.$refs.chatBox;
            if (box) box.scrollTop = box.scrollHeight;
        }
    }" class="fixed bottom-6 right-6 z-50"
    x-init="$watch('open', val => { if (val) animateCounter() })">

        <button @click="open = !open"
            class="floating-btn w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-xl hover:shadow-blue-500/30"
            :class="{ 'rotate-45 ring-4 ring-blue-300': open }">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
        </button>

        <div x-show="open" @click.away="open = false" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-90 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-4"
            class="absolute bottom-16 right-0 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">

            <div class="flex border-b border-gray-100">
                <button @click="tab = 'pengunjung'"
                    class="flex-1 flex items-center justify-center gap-1.5 px-3 py-3 text-xs font-medium transition"
                    :class="tab === 'pengunjung' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50/50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Pengunjung
                </button>
                <button @click="tab = 'chat'"
                    class="flex-1 flex items-center justify-center gap-1.5 px-3 py-3 text-xs font-medium transition"
                    :class="tab === 'chat' ? 'text-green-600 border-b-2 border-green-600 bg-green-50/50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Chat
                </button>
            </div>

            <div x-show="tab === 'pengunjung'">
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-800">Pengunjung</span>
                    </div>

                    <div class="space-y-3">
                        <div class="group">
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-gray-600 flex items-center gap-2">
                                    <span class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </span>
                                    Hari Ini
                                </span>
                                <span class="font-bold text-gray-800" x-text="animated.hari_ini.toLocaleString()">0</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-green-400 to-green-500 rounded-full transition-all duration-500"
                                    :style="'width: ' + Math.min((animated.hari_ini / maxTotal) * 100, 100) + '%'"></div>
                            </div>
                        </div>

                        <div class="group">
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-gray-600 flex items-center gap-2">
                                    <span class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </span>
                                    Minggu Ini
                                </span>
                                <span class="font-bold text-gray-800" x-text="animated.minggu_ini.toLocaleString()">0</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-400 to-blue-500 rounded-full transition-all duration-500"
                                    :style="'width: ' + Math.min((animated.minggu_ini / maxTotal) * 100, 100) + '%'"></div>
                            </div>
                        </div>

                        <div class="group">
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-gray-600 flex items-center gap-2">
                                    <span class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    </span>
                                    Bulan Ini
                                </span>
                                <span class="font-bold text-gray-800" x-text="animated.bulan_ini.toLocaleString()">0</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-purple-400 to-purple-500 rounded-full transition-all duration-500"
                                    :style="'width: ' + Math.min((animated.bulan_ini / maxTotal) * 100, 100) + '%'"></div>
                            </div>
                        </div>

                        <div class="group">
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-gray-600 flex items-center gap-2">
                                    <span class="w-6 h-6 bg-amber-100 rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                        </svg>
                                    </span>
                                    Total
                                </span>
                                <span class="font-bold text-gray-800" x-text="animated.total.toLocaleString()">0</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-amber-400 to-amber-500 rounded-full transition-all duration-500"
                                    :style="'width: ' + Math.min((animated.total / maxTotal) * 100, 100) + '%'"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 grid grid-cols-2 divide-x divide-gray-100">
                    <a href="{{ route('kritik-saran') }}"
                        class="flex flex-col items-center gap-1.5 px-3 py-4 text-sm text-gray-700 hover:bg-blue-50 transition group">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 group-hover:scale-110 transition-all">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-blue-700">Kritik &amp; Saran</span>
                    </a>
                    <a href="{{ route('faq') }}"
                        class="flex flex-col items-center gap-1.5 px-3 py-4 text-sm text-gray-700 hover:bg-orange-50 transition group">
                        <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center group-hover:bg-orange-200 group-hover:scale-110 transition-all">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-orange-700">FAQ</span>
                    </a>
                </div>
            </div>

            <div x-show="tab === 'chat'" class="flex flex-col h-96">
                <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50" x-ref="chatBox">
                    <template x-for="msg in messages" :key="msg.id">
                        <div>
                            <div x-show="msg.role === 'bot'"
                                class="flex items-start gap-2">
                                <div class="w-7 h-7 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                </div>
                                <div class="bg-white rounded-lg rounded-tl-none px-3 py-2 text-sm text-gray-700 shadow-sm max-w-[85%]" x-html="msg.text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>')">
                                </div>
                            </div>
                            <div x-show="msg.role === 'user'"
                                class="flex justify-end">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg rounded-tr-none px-3 py-2 text-sm text-white max-w-[80%] shadow-sm">
                                    <span x-text="msg.text"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="showOptions" class="flex flex-wrap gap-2 mt-3">
                        <template x-for="(answer, question) in botResponses" :key="question">
                            <button @click="quickReply(question)"
                                class="text-xs px-3 py-1.5 bg-white border border-gray-200 rounded-full text-gray-600 hover:bg-green-50 hover:border-green-300 hover:text-green-700 transition whitespace-nowrap">
                                <span x-text="question.charAt(0).toUpperCase() + question.slice(1)"></span>
                            </button>
                        </template>
                    </div>
                    <div x-show="!showOptions && messages[messages.length - 1].role === 'bot'" class="mt-3 text-center">
                        <button @click="showOptions = true"
                            class="text-xs px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full transition">
                            ↻ Pertanyaan Lainnya
                        </button>
                    </div>
                </div>

                <div class="border-t border-gray-100 p-3 bg-white">
                    <div class="flex gap-2">
                        <input type="text" x-model="inputText" @keydown.enter="sendMessage"
                            placeholder="Ketik pesan..."
                            class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent">
                        <button @click="sendMessage"
                            class="w-9 h-9 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg flex items-center justify-center hover:from-green-600 hover:to-green-700 transition flex-shrink-0"
                            :disabled="!inputText.trim()"
                            :class="{ 'opacity-50 cursor-not-allowed': !inputText.trim() }">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        @keyframes float {
            0%, 100% { box-shadow: 0 8px 24px rgba(37, 99, 235, 0.3); }
            50% { box-shadow: 0 8px 32px rgba(37, 99, 235, 0.6); }
        }
        .floating-btn { animation: float 3s ease-in-out infinite; }
    </style>

    @stack('scripts')
</body>
</html>
