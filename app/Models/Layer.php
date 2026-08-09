<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Layer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama',
        'slug',
        'kategori_id',
        'geom_type',
        'warna',
        'style_json',
        'icon_marker',
        'deskripsi',
        'is_active',
        'opacity',
        'order',
        'tampil',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'tampil' => 'boolean',
            'opacity' => 'decimal:2',
            'style_json' => 'array',
        ];
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriLayer::class, 'kategori_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function dataSpasial()
    {
        return $this->hasMany(DataSpasial::class, 'layer_id');
    }
}
