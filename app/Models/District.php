<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = [
        'regency_id',
        'kode',
        'nama',
    ];

    public function regency()
    {
        return $this->belongsTo(Regency::class, 'regency_id');
    }

    public function villages()
    {
        return $this->hasMany(Village::class, 'district_id');
    }
}
