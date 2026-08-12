<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Informasi extends Model
{
    protected $table = 'informasis';

    protected $fillable = [
        'tipe', 'judul', 'isi', 'gambar', 'jenis',
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
}
