<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'petaSpasial') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-800">
    <div class="min-h-screen flex flex-col">
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <a href="{{ url('/') }}" class="flex items-center space-x-2">
                        <x-application-logo class="w-8 h-8 fill-current text-blue-600" />
                        <span class="text-lg font-bold text-gray-800">{{ config('app.name', 'petaSpasial') }}</span>
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
                        <span class="text-sm font-medium text-gray-300">{{ config('app.name', 'petaSpasial') }}</span>
                    </div>
                    <p class="text-sm">&copy; {{ date('Y') }} {{ config('app.name', 'petaSpasial') }}. Hak cipta dilindungi.</p>
                </div>
            </div>
        </footer>
    </div>

    <script>
        document.getElementById('hamburger')?.addEventListener('click', function () {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>

    @stack('scripts')
</body>
</html>
