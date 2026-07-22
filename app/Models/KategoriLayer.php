<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriLayer extends Model
{
    protected $table = 'kategori_layers';

    protected $fillable = [
        'nama',
        'deskripsi',
        'icon',
        'warna_default',
    ];

    public function layers()
    {
        return $this->hasMany(Layer::class, 'kategori_id');
    }
}
