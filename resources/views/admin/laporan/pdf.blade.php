<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Spasial - {{ config('app.name') }}</title>
    <style>
        @page { margin: 20mm 15mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #333; }
        .kop { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 10px; margin-bottom: 15px; }
        .kop h1 { font-size: 16px; margin: 0; color: #1e3a5f; text-transform: uppercase; }
        .kop h2 { font-size: 11px; margin: 3px 0; color: #555; font-weight: normal; }
        .kop p { font-size: 8px; margin: 2px 0; color: #777; }
        .title { text-align: center; font-size: 13px; font-weight: bold; margin-bottom: 15px; text-decoration: underline; }
        .subtitle { text-align: center; font-size: 9px; color: #666; margin-bottom: 15px; }
        .section { margin-bottom: 18px; }
        .section h3 { font-size: 10px; color: #1e40af; border-bottom: 1px solid #ddd; padding-bottom: 3px; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        th { background: #1e40af; color: white; padding: 4px 5px; text-align: left; font-size: 8px; }
        td { padding: 3px 5px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) td { background: #f8fafc; }
        .total-row td { font-weight: bold; border-top: 2px solid #333; background: #f1f5f9; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 7px; color: #999; border-top: 1px solid #ddd; padding-top: 4px; }
        .page-number:before { content: "Halaman " counter(page); }
    </style>
</head>
<body>
    <div class="kop">
        <h1>{{ config('app.name') }}</h1>
        <h2>Sistem Informasi Geografis Data Spasial</h2>
        <p>Kabupaten Sukabumi, Provinsi Jawa Barat</p>
    </div>

    <div class="title">LAPORAN DATA SPASIAL</div>
    <div class="subtitle">
        Dicetak: {{ now()->isoFormat('DD MMMM YYYY HH:mm') }}
        @if ($layerId || $districtId || $tahunFrom || $tahunTo)
            | Filter aktif
        @endif
    </div>

    <div class="section">
        <h3>A. Rekap Per Layer</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:8%">No</th>
                    <th>Layer</th>
                    <th style="width:20%">Tipe Geometri</th>
                    <th style="width:15%; text-align:right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rekapLayer as $i => $r)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $r->nama }}</td>
                        <td>{{ $r->geom_type }}</td>
                        <td style="text-align:right">{{ number_format($r->total) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center; color:#999">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
            @if (count($rekapLayer) > 0)
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3" style="text-align:right">Total</td>
                        <td style="text-align:right">{{ number_format($totalSemua) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    <div class="section">
        <h3>B. Rekap Per Wilayah (Kecamatan)</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:8%">No</th>
                    <th>Kecamatan</th>
                    <th style="width:15%; text-align:right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rekapWilayah as $i => $r)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $r->nama }}</td>
                        <td style="text-align:right">{{ number_format($r->total) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center; color:#999">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
            @if (count($rekapWilayah) > 0)
                <tfoot>
                    <tr class="total-row">
                        <td colspan="2" style="text-align:right">Total</td>
                        <td style="text-align:right">{{ number_format(array_sum(array_column($rekapWilayah, 'total'))) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    <div class="section">
        <h3>C. Rekap Per Kategori</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:8%">No</th>
                    <th>Kategori</th>
                    <th style="width:15%; text-align:right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rekapKategori as $i => $r)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $r->nama }}</td>
                        <td style="text-align:right">{{ number_format($r->total) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center; color:#999">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
            @if (count($rekapKategori) > 0)
                <tfoot>
                    <tr class="total-row">
                        <td colspan="2" style="text-align:right">Total</td>
                        <td style="text-align:right">{{ number_format(array_sum(array_column($rekapKategori, 'total'))) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    <div style="margin-top: 30px; font-size: 8px;">
        <table style="width:100%; border:none;">
            <tr>
                <td style="width:50%; border:none; text-align:center;">
                    <p>Mengetahui,</p>
                    <br><br>
                    <p style="font-weight:bold; text-decoration:underline;">Kepala Dinas</p>
                    <p style="font-size:7px; color:#666;">NIP. ................................</p>
                </td>
                <td style="width:50%; border:none; text-align:center;">
                    <p>Sukabumi, {{ now()->isoFormat('DD MMMM YYYY') }}</p>
                    <br><br>
                    <p style="font-weight:bold; text-decoration:underline;">Petugas</p>
                    <p style="font-size:7px; color:#666;">NIP. ................................</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <span class="page-number"></span> &mdash; Dicetak dari {{ config('app.url') }}
    </div>
</body>
</html>
