<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VisitorLog extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    public static function getCounts(): array
    {
        $today = DB::selectOne("SELECT COUNT(*) AS c FROM visitor_logs WHERE visited_at >= CURRENT_DATE AND visited_at < CURRENT_DATE + INTERVAL '1 day'")->c;
        $week = DB::selectOne("SELECT COUNT(*) AS c FROM visitor_logs WHERE visited_at >= DATE_TRUNC('week', CURRENT_DATE) AND visited_at < DATE_TRUNC('week', CURRENT_DATE) + INTERVAL '7 days'")->c;
        $month = DB::selectOne("SELECT COUNT(*) AS c FROM visitor_logs WHERE visited_at >= DATE_TRUNC('month', CURRENT_DATE) AND visited_at < DATE_TRUNC('month', CURRENT_DATE) + INTERVAL '1 month'")->c;
        $total = DB::selectOne("SELECT COUNT(*) AS c FROM visitor_logs")->c;

        return [
            'hari_ini' => (int) $today,
            'minggu_ini' => (int) $week,
            'bulan_ini' => (int) $month,
            'total' => (int) $total,
        ];
    }
}
