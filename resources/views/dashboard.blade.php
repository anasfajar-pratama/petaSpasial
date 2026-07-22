<x-layouts.admin>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <div class="text-sm text-gray-500">
            {{ now()->format('l, d F Y') }}
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Filter Tahun</label>
                <select name="tahun" class="border rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Tahun</option>
                    @foreach ($tahuns as $t)
                        <option value="{{ $t->tahun }}" {{ $tahunFilter == $t->tahun ? 'selected' : '' }}>{{ $t->tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Filter Kecamatan</label>
                <select name="district" class="border rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Kecamatan</option>
                    @foreach ($districts as $d)
                        <option value="{{ $d->id }}" {{ $districtFilter == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Terapkan</button>
            @if ($tahunFilter || $districtFilter)
                <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-100">Reset</a>
            @endif
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Layer</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalLayers }}</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Objek Spasial</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalObjects) }}</p>
                    <div class="flex gap-3 mt-1 text-xs text-gray-500">
                        @foreach ($objectsByType as $t)
                            <span>{{ $t->tipe }}: {{ number_format($t->c) }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="p-3 rounded-full bg-green-100">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Kecamatan</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalDistricts) }}</p>
                </div>
                <div class="p-3 rounded-full bg-purple-100">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Kategori</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalCategories }}</p>
                </div>
                <div class="p-3 rounded-full bg-yellow-100">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-8">
        <h2 class="text-sm font-semibold text-gray-800 mb-2">Peta Ringkasan</h2>
        <div id="mini-map" class="w-full h-[300px] rounded-lg cursor-pointer border" title="Klik untuk buka peta interaktif"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Objek per Layer</h2>
            <canvas id="chartByLayer" height="200"></canvas>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Distribusi per Kategori</h2>
            <canvas id="chartByCategory" height="200"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Objek per Kecamatan</h2>
            @if (count($objectsByDistrict) > 0)
                <canvas id="chartByDistrict" height="200"></canvas>
            @else
                <p class="text-gray-400 text-sm py-8 text-center">Belum ada data objek yang terhubung ke kecamatan.</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Tren per Tahun</h2>
            @if (count($objectsByYear) > 0)
                <canvas id="chartByYear" height="200"></canvas>
            @else
                <p class="text-gray-400 text-sm py-8 text-center">Belum ada data tahun pada objek spasial.</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Selamat datang, {{ Auth::user()->name }}!</h2>
        <p class="text-gray-500">Anda login sebagai <strong>{{ Auth::user()->roles->first()?->name ?? 'User' }}</strong>.</p>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartByLayer = new Chart(document.getElementById('chartByLayer'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($objectsByLayer, 'nama')) !!},
                datasets: [{
                    label: 'Jumlah Objek',
                    data: {!! json_encode(array_column($objectsByLayer, 'c')) !!},
                    backgroundColor: {!! json_encode(array_map(fn($l) => $l->warna ?: '#3b82f6', $objectsByLayer)) !!},
                    borderWidth: 0,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { maxRotation: 45, font: { size: 11 } } }
                }
            }
        });

        new Chart(document.getElementById('chartByCategory'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_column($objectsByCategory, 'nama')) !!},
                datasets: [{
                    data: {!! json_encode(array_column($objectsByCategory, 'c')) !!},
                    backgroundColor: {!! json_encode(array_map(fn($c) => $c->warna_default ?: '#6b7280', $objectsByCategory)) !!},
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } }
                }
            }
        });

        @if (count($objectsByDistrict) > 0)
        new Chart(document.getElementById('chartByDistrict'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($objectsByDistrict, 'nama')) !!},
                datasets: [{
                    label: 'Jumlah Objek',
                    data: {!! json_encode(array_column($objectsByDistrict, 'c')) !!},
                    backgroundColor: '#8b5cf6',
                    borderWidth: 0,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { maxRotation: 45, font: { size: 11 } } }
                }
            }
        });
        @endif

        @if (count($objectsByYear) > 0)
        new Chart(document.getElementById('chartByYear'), {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($objectsByYear, 'tahun')) !!},
                datasets: [{
                    label: 'Jumlah Objek',
                    data: {!! json_encode(array_column($objectsByYear, 'c')) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: '#3b82f6',
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
        @endif
    });
    </script>
    @endpush

    @vite('resources/js/dashboard.js')
</x-layouts.admin>
