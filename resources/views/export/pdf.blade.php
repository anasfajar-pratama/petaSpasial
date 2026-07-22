<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Spasial - {{ config('app.name') }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        h1 { font-size: 16px; margin-bottom: 5px; }
        h2 { font-size: 12px; color: #555; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #2563eb; color: white; padding: 5px; text-align: left; font-size: 9px; }
        td { padding: 4px 5px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) td { background: #f9fafb; }
        .footer { margin-top: 20px; font-size: 8px; color: #999; text-align: center; }
        .layer-header { margin-top: 5px; margin-bottom: 2px; color: #2563eb; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Laporan Data Spasial</h1>
    <h2>{{ config('app.name') }} — Dicetak: {{ now()->isoFormat('DD MMMM YYYY HH:mm') }}</h2>

    @php $grouped = $data->groupBy(fn($d) => $d->layer?->nama ?? 'Tanpa Layer'); @endphp

    @foreach ($grouped as $layerName => $items)
        <div class="layer-header">{{ $layerName }} ({{ $items->count() }} data)</div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Tahun</th>
                    <th>Luas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->nama }}</td>
                        <td>{{ Str::limit($item->deskripsi, 50) }}</td>
                        <td>{{ $item->kategori?->nama ?? '-' }}</td>
                        <td>{{ $item->status }}</td>
                        <td>{{ $item->tahun ?? '-' }}</td>
                        <td>{{ $item->luas ? $item->luas . ' m²' : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="footer">Dicetak dari {{ config('app.url') }} &mdash; {{ now()->format('Y-m-d H:i:s') }}</div>
</body>
</html>
