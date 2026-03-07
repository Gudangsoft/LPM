<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturan';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label'
    ];

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $type = 'text', $group = 'general', $label = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'label' => $label ?: ucwords(str_replace('_', ' ', $key))
            ]
        );
    }

    public static function getGroup($group)
    {
        return static::where('group', $group)->get()->pluck('value', 'key');
    }

    public static function getAll()
    {
        return static::all()->pluck('value', 'key');
    }
}
