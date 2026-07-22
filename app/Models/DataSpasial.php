<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSpasial extends Model
{
    protected $table = 'data_spasial';

    protected $fillable = [
        'layer_id',
        'nama',
        'deskripsi',
        'kategori_id',
        'district_id',
        'village_id',
        'status',
        'tahun',
        'luas',
        'foto',
        'dokumen',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'luas' => 'decimal:4',
        ];
    }

    public function layer()
    {
        return $this->belongsTo(Layer::class, 'layer_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriLayer::class, 'kategori_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
