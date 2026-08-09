<?php

namespace App\Console\Commands;

use App\Models\Layer;
use App\Support\SymbolCatalog;
use Illuminate\Console\Command;

class ApplyLayerSymbols extends Command
{
    protected $signature = 'layers:apply-symbols {--force : Timpa simbol layer yang sudah terisi}';
    protected $description = 'Isi icon_marker/style_json layer lama secara idempoten berdasarkan katalog simbol';

    private array $markers = [];
    private array $lines = [];
    private array $fills = [];

    private array $markerOverrides = [
        'sistem infrastruktur energi' => '/icons/esri/gardu-listrik.png',
        'sistem infrastruktur sumber daya air' => '/icons/esri/bangunan-sumber-daya-air.png',
        'sistem infrastruktur telekomunikasi' => '/icons/esri/menara-base-transceiver-station-bts.png',
        'sistem infrastruktur transportasi' => '/icons/esri/halte.png',
        'sistem pusat pelayanan' => '/icons/esri/pusat-pelayanan-kota.png',
    ];

    public function handle(): int
    {
        $catalog = SymbolCatalog::read();
        $this->markers = $catalog['markers'];
        $this->lines = $catalog['lines'];
        $this->fills = $catalog['fills'];

        $force = $this->option('force');
        $layers = Layer::all();

        $updated = 0;
        $skipped = 0;
        $unmatched = 0;

        foreach ($layers as $layer) {
            $needIcon = $this->needsIcon($layer);
            $needStyle = $this->needsStyle($layer);

            if (!$force && !$needIcon && !$needStyle) {
                $skipped++;
                continue;
            }

            $wasUpdated = $this->apply($layer, $this->needsIcon($layer), $this->needsStyle($layer));
            if ($wasUpdated) {
                $updated++;
                $this->line("  [ok] {$layer->nama}");
            } else {
                $unmatched++;
                $this->warn("  [unmatched] {$layer->nama}");
            }
        }

        $this->info("Selesai: {$updated} layer disimbolkan, {$skipped} sudah lengkap (dilewati), {$unmatched} tidak ada kecocokan.");

        return self::SUCCESS;
    }

    private function needsIcon(Layer $layer): bool
    {
        return $layer->geom_type === 'Point' && !$layer->icon_marker;
    }

    private function needsStyle(Layer $layer): bool
    {
        return in_array($layer->geom_type, ['LineString', 'Polygon', 'MultiPolygon']) && !is_array($layer->style_json);
    }

    private function apply(Layer $layer, bool $needIcon, bool $needStyle): bool
    {
        $norm = $this->normalize($layer->nama);
        $changed = false;

        if ($needIcon && $layer->geom_type === 'Point') {
            $icon = $this->findMarker($norm);
            if ($icon) {
                $layer->icon_marker = $icon;
                $changed = true;
            }
        }

        if ($needStyle && in_array($layer->geom_type, ['LineString', 'Polygon', 'MultiPolygon'])) {
            $style = $layer->geom_type === 'LineString'
                ? $this->findLine($norm)
                : $this->findFill($norm);
            if ($style) {
                $layer->style_json = $style;
                $changed = true;
            }
        }

        if ($changed) $layer->save();

        return $changed;
    }

    private function findMarker(string $norm): ?string
    {
        if (isset($this->markerOverrides[$norm])) {
            return $this->markerOverrides[$norm];
        }

        foreach ($this->markers as $m) {
            if ($this->normalize($m['name']) === $norm) {
                return $m['icon'];
            }
        }

        return null;
    }

    private function findLine(string $norm): ?array
    {
        $match = $this->searchByName($this->lines, $norm);
        if (!$match) return null;

        return [
            'name' => $match['name'],
            'weight' => $match['weight'] ?? 2,
            'dash' => $match['dash'] ?? [],
            'color' => $match['color'] ?? null,
        ];
    }

    private function findFill(string $norm): ?array
    {
        $match = $this->searchByName($this->fills, $norm);
        if (!$match) return null;

        return [
            'name' => $match['name'],
            'color' => $match['color'] ?? null,
            'outline' => $match['outline'] ?? null,
            'pattern' => $match['pattern'] ?? 'solid',
        ];
    }

    private function searchByName(array $catalogItems, string $norm): ?array
    {
        foreach ($catalogItems as $item) {
            if ($this->normalize($item['name']) === $norm) {
                return $item;
            }
        }

        return null;
    }

    private function normalize(string $value): string
    {
        $value = strtolower(trim($value));

        $map = [
            'ä' => 'a', 'ö' => 'o', 'ü' => 'u', 'ß' => 'ss',
            'ñ' => 'n', 'á' => 'a', 'é' => 'e', 'í' => 'i',
            'ó' => 'o', 'ú' => 'u', 'ç' => 'c',
        ];
        $value = strtr($value, $map);

        return preg_replace('/[^a-z0-9]+/', ' ', $value) ?? $value;
    }
}