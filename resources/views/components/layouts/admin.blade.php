<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Models\Setting::getValue('site_name', config('app.name', 'petaSpasial')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="flex h-screen overflow-hidden bg-gray-100">
        <div x-data="{ sidebarOpen: true }" class="flex w-full">
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 bg-gradient-to-b from-gray-900 to-gray-800 text-gray-300 transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-auto flex flex-col sidebar-scroll">
                <div class="flex items-center justify-between h-16 px-5 bg-gray-800/80 border-b border-gray-700/50">
                    <a href="{{ route('dashboard') }}" class="text-lg font-bold text-white tracking-tight">{{ \App\Models\Setting::getValue('site_name', 'petaSpasial') }}</a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <nav class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-5 space-y-0.5 sidebar-scroll">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-blue-600/20 text-blue-400 border-l-2 border-blue-500' : 'text-gray-400 hover:text-white hover:bg-gray-700/50 border-l-2 border-transparent' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </a>

                    @role('Administrator')
                    <div class="pt-5 pb-1.5">
                        <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500">Master Data</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.users*') ? 'bg-gray-700/80 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                        <span>User</span>
                    </a>
                    <a href="{{ route('admin.roles.index') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.roles*') ? 'bg-gray-700/80 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Role</span>
                    </a>
                    <a href="{{ route('admin.layers.index') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.layers*') ? 'bg-gray-700/80 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Layer</span>
                    </a>
                    <a href="{{ route('admin.kategori.index') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.kategori*') ? 'bg-gray-700/80 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <span>Kategori</span>
                    </a>
                    <a href="{{ route('admin.simbol') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.simbol') ? 'bg-gray-700/80 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a1 1 0 011-1h4a1 1 0 011 1v12a4 4 0 01-4 4zM14 3h6a1 1 0 011 1v3a1 1 0 01-1 1h-6a1 1 0 01-1-1V4a1 1 0 011-1zM14 11h6a1 1 0 011 1v3a1 1 0 01-1 1h-6a1 1 0 01-1-1v-3a1 1 0 011-1z"/></svg>
                        <span>Galeri Simbol</span>
                    </a>
                    <a href="{{ route('admin.wilayah') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.wilayah*') ? 'bg-gray-700/80 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        <span>Wilayah</span>
                    </a>
                    <a href="{{ route('admin.kritik-saran.index') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.kritik-saran*') ? 'bg-gray-700/80 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span>Kritik &amp; Saran</span>
                    </a>
                    @endrole

                    <div class="pt-5 pb-1.5">
                        <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500">Data Spasial</p>
                    </div>
                    @hasanyrole('Administrator|Operator')
                    <a href="{{ route('data-spasial.index') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('data-spasial*') ? 'bg-gray-700/80 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Data Spasial</span>
                    </a>
                    <a href="{{ route('import.index') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('import*') ? 'bg-gray-700/80 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span>Import</span>
                    </a>
                    <a href="{{ route('admin.export') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.export') || request()->routeIs('export.*') ? 'bg-gray-700/80 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Export</span>
                    </a>
                    @endhasanyrole

                    <div class="pt-5 pb-1.5">
                        <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500">Peta & Laporan</p>
                    </div>
                    <a href="{{ route('map') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('map') ? 'bg-gray-700/80 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        <span>Peta Interaktif</span>
                    </a>
                    <a href="{{ route('statistik') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('statistik') ? 'bg-gray-700/80 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Statistik</span>
                    </a>
                    @role('Administrator')
                    <a href="{{ route('admin.laporan') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.laporan') ? 'bg-gray-700/80 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Laporan</span>
                    </a>
                    <a href="{{ route('admin.pengaturan') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.pengaturan') ? 'bg-gray-700/80 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Pengaturan</span>
                    </a>
                    @endrole
                </nav>

                <div class="p-4 border-t border-gray-700/50 bg-gray-800/40">
                    <div class="flex items-center">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-sm font-bold text-white shadow-lg">{{ substr(Auth::user()->name, 0, 1) }}</div>
                        <div class="ml-3 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ Auth::user()->roles->first()?->name ?? 'No Role' }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="flex-1 flex flex-col min-w-0">
                <header class="bg-white shadow-sm h-16 flex items-center px-6">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 lg:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="ml-auto flex items-center space-x-4">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">Logout</button>
                        </form>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>
    <style>
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
    </style>
    @stack('scripts')
</body>
</html>
