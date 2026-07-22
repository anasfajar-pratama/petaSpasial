<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    protected $fillable = [
        'user_id', 'layer_id', 'format', 'filename',
        'total_imported', 'total_duplicates', 'total_errors', 'errors',
    ];
}
