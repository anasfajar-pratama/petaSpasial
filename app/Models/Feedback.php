<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = ['nama', 'email', 'subjek', 'pesan', 'foto_wajah', 'gambar'];

    protected $casts = [
        'is_read' => 'boolean',
    ];
}
