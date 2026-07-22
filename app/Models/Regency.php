<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regency extends Model
{
    protected $fillable = [
        'kode',
        'nama',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function districts()
    {
        return $this->hasMany(District::class, 'regency_id');
    }
}
