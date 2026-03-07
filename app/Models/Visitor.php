<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $table = 'visitors';

    protected $fillable = [
        'ip_address',
        'user_agent',
        'page_visited',
        'referrer',
        'country',
        'city',
        'visit_date'
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    public static function recordVisit($request, $page = null)
    {
        return static::create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'page_visited' => $page ?: $request->path(),
            'referrer' => $request->header('referer'),
            'visit_date' => now()->toDateString(),
        ]);
    }

    public static function getTodayCount()
    {
        return static::where('visit_date', now()->toDateString())->count();
    }

    public static function getMonthCount()
    {
        return static::whereMonth('visit_date', now()->month)
            ->whereYear('visit_date', now()->year)
            ->count();
    }

    public static function getTotalCount()
    {
        return static::count();
    }

    public static function getUniqueVisitors($period = 'today')
    {
        $query = static::query();
        
        if ($period === 'today') {
            $query->where('visit_date', now()->toDateString());
        } elseif ($period === 'month') {
            $query->whereMonth('visit_date', now()->month)
                ->whereYear('visit_date', now()->year);
        }
        
        return $query->distinct('ip_address')->count('ip_address');
    }
}
