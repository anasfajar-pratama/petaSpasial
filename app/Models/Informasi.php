<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Informasi extends Model
{
    protected $table = 'informasis';

    protected $fillable = [
        'tipe', 'judul', 'isi', 'gambar', 'video_url', 'jenis',
        'penulis', 'tahun', 'tanggal', 'urutan', 'is_active',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'tanggal' => 'date',
        'is_active' => 'boolean',
    ];

    public const TIPES = ['berita', 'infografis', 'panduan', 'riset'];

    public function scopeTipe(Builder $q, string $tipe): Builder
    {
        return $q->where('tipe', $tipe);
    }

    public function videoId(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        preg_match('/(?:youtube\.com\/(?:watch\?.*v=|embed\/|shorts\/|live\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/', $this->video_url, $m);

        return $m[1] ?? null;
    }
}
