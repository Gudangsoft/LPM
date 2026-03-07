<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Halaman extends Model
{
    use HasFactory;

    protected $table = 'halaman';

    protected $fillable = [
        'judul',
        'slug',
        'konten',
        'konten_translations',
        'template',
        'meta_title',
        'meta_description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'konten_translations' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->judul);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getLocalizedKonten($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        if ($this->konten_translations && isset($this->konten_translations[$locale])) {
            return $this->konten_translations[$locale];
        }
        
        return $this->konten;
    }
}
