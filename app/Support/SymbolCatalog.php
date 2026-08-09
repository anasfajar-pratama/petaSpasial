<?php

namespace App\Support;

class SymbolCatalog
{
    public static function read(): array
    {
        $path = public_path('icons/esri/catalog.json');
        if (!file_exists($path)) {
            return ['markers' => [], 'lines' => [], 'fills' => []];
        }

        $data = json_decode(file_get_contents($path), true);

        return [
            'markers' => $data['markers'] ?? [],
            'lines' => $data['lines'] ?? [],
            'fills' => $data['fills'] ?? [],
        ];
    }
}