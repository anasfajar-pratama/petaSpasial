<?php

namespace App\Console\Commands;

use App\Models\DataSpasial;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixNullGeometry extends Command
{
    protected $signature = 'fix:geometry {--layer= : ID layer}';
    protected $description = 'Fix NULL geometry records by re-importing from source or cleaning up';

    public function handle()
    {
        $layerId = $this->option('layer');

        $query = DB::table('data_spasial')
            ->leftJoin('layers', 'data_spasial.layer_id', '=', 'layers.id')
            ->whereNull('data_spasial.geometry')
            ->select('data_spasial.id', 'data_spasial.layer_id', 'layers.nama as layer_nama');

        if ($layerId) {
            $query->where('data_spasial.layer_id', $layerId);
        }

        $records = $query->get();

        if ($records->isEmpty()) {
            $this->info('No NULL geometry records found.');
            return;
        }

        $this->info("Found {$records->count()} records with NULL geometry.");
        $grouped = $records->groupBy('layer_nama');

        foreach ($grouped as $layerName => $items) {
            $this->warn("  {$layerName}: {$items->count()} records");
        }

        if ($this->confirm('Delete these records?')) {
            $ids = $records->pluck('id');
            DB::table('data_spasial')->whereIn('id', $ids)->delete();
            $this->info("Deleted {$ids->count()} records with NULL geometry.");
        }
    }
}
