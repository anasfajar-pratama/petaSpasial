<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $table = 'pengaduans';

    public const STATUS = [
        'baru' => 'Baru',
        'proses' => 'Proses',
        'tolak' => 'Ditolak',
        'selesai' => 'Selesai',
    ];

    protected $fillable = [
        'no_pengaduan', 'nik', 'nama', 'email', 'no_hp', 'foto',
        'latitude', 'longitude', 'maps_link',
        'status', 'catatan_progress', 'feedback',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];
}
