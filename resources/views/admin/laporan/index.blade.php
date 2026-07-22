<x-layouts.admin>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Laporan Data Spasial</h1>
        <p class="text-gray-500 text-sm">Rekapitulasi data spasial berdasarkan layer, wilayah, dan kategori.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Layer</label>
                <select name="layer_id" class="border rounded-lg px-3 py-2 text-sm w-48">
                    <option value="">Semua Layer</option>
                    @foreach ($layers as $l)
                        <option value="{{ $l->id }}" {{ $layerId == $l->id ? 'selected' : '' }}>{{ $l->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Kecamatan</label>
                <select name="district_id" class="border rounded-lg px-3 py-2 text-sm w-48">
                    <option value="">Semua Kecamatan</option>
                    @foreach ($districts as $d)
                        <option value="{{ $d->id }}" {{ $districtId == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tahun Dari</label>
                <select name="tahun_from" class="border rounded-lg px-3 py-2 text-sm w-32">
                    <option value="">-</option>
                    @foreach ($tahuns as $t)
                        <option value="{{ $t->tahun }}" {{ $tahunFrom == $t->tahun ? 'selected' : '' }}>{{ $t->tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tahun Sampai</label>
                <select name="tahun_to" class="border rounded-lg px-3 py-2 text-sm w-32">
                    <option value="">-</option>
                    @foreach ($tahuns as $t)
                        <option value="{{ $t->tahun }}" {{ $tahunTo == $t->tahun ? 'selected' : '' }}>{{ $t->tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Filter</button>
                <a href="{{ route('admin.laporan') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300">Reset</a>
            </div>
        </form>
    </div>

    <div class="flex gap-3 mb-4">
        <a href="{{ route('admin.laporan.pdf', request()->query()) }}" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Cetak PDF
        </a>
        <a href="{{ route('admin.laporan.excel', request()->query()) }}" class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export Excel
        </a>
    </div>

    <div x-data="{ tab: 'layer' }">
        <div class="flex border-b mb-4">
            <button @click="tab = 'layer'" :class="tab === 'layer' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'" class="px-4 py-2 text-sm font-medium hover:text-blue-600">Per Layer</button>
            <button @click="tab = 'wilayah'" :class="tab === 'wilayah' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'" class="px-4 py-2 text-sm font-medium hover:text-blue-600">Per Wilayah</button>
            <button @click="tab = 'kategori'" :class="tab === 'kategori' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'" class="px-4 py-2 text-sm font-medium hover:text-blue-600">Per Kategori</button>
        </div>

        <div x-show="tab === 'layer'">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-gray-600">
                            <th class="px-6 py-3 font-medium w-12">No</th>
                            <th class="px-6 py-3 font-medium">Layer</th>
                            <th class="px-6 py-3 font-medium">Tipe Geometri</th>
                            <th class="px-6 py-3 font-medium text-right">Jumlah Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rekapLayer as $i => $r)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-6 py-3 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-block w-3 h-3 rounded-sm mr-2" style="background:{{ $r->warna }}"></span>
                                    {{ $r->nama }}
                                </td>
                                <td class="px-6 py-3 text-gray-500">{{ $r->geom_type }}</td>
                                <td class="px-6 py-3 text-right font-medium">{{ number_format($r->total) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                    @if (count($rekapLayer) > 0)
                        <tfoot>
                            <tr class="border-t bg-gray-50 font-semibold">
                                <td colspan="3" class="px-6 py-3 text-right">Total</td>
                                <td class="px-6 py-3 text-right">{{ number_format(array_sum(array_column($rekapLayer, 'total'))) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <div x-show="tab === 'wilayah'" x-cloak>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-gray-600">
                            <th class="px-6 py-3 font-medium w-12">No</th>
                            <th class="px-6 py-3 font-medium">Kecamatan</th>
                            <th class="px-6 py-3 font-medium text-right">Jumlah Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rekapWilayah as $i => $r)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-6 py-3 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-6 py-3">{{ $r->nama }}</td>
                                <td class="px-6 py-3 text-right font-medium">{{ number_format($r->total) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                    @if (count($rekapWilayah) > 0)
                        <tfoot>
                            <tr class="border-t bg-gray-50 font-semibold">
                                <td colspan="2" class="px-6 py-3 text-right">Total</td>
                                <td class="px-6 py-3 text-right">{{ number_format(array_sum(array_column($rekapWilayah, 'total'))) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <div x-show="tab === 'kategori'" x-cloak>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-gray-600">
                            <th class="px-6 py-3 font-medium w-12">No</th>
                            <th class="px-6 py-3 font-medium">Kategori</th>
                            <th class="px-6 py-3 font-medium text-right">Jumlah Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rekapKategori as $i => $r)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-6 py-3 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-block w-3 h-3 rounded-sm mr-2" style="background:{{ $r->warna_default }}"></span>
                                    {{ $r->nama }}
                                </td>
                                <td class="px-6 py-3 text-right font-medium">{{ number_format($r->total) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                    @if (count($rekapKategori) > 0)
                        <tfoot>
                            <tr class="border-t bg-gray-50 font-semibold">
                                <td colspan="2" class="px-6 py-3 text-right">Total</td>
                                <td class="px-6 py-3 text-right">{{ number_format(array_sum(array_column($rekapKategori, 'total'))) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</x-layouts.admin>
