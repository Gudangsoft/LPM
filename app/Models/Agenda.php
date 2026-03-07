<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agenda';

    protected $fillable = [
        'judul',
        'slug',
        'deskripsi',
        'lokasi',
        'tanggal_mulai',
        'tanggal_selesai',
        'waktu_mulai',
        'waktu_selesai',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
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

    public function scopeUpcoming($query)
    {
        return $query->where('tanggal_mulai', '>=', now()->toDateString());
    }

    public function scopePast($query)
    {
        return $query->where('tanggal_mulai', '<', now()->toDateString());
    }

    public function getFormattedDateAttribute()
    {
        if ($this->tanggal_selesai && $this->tanggal_mulai != $this->tanggal_selesai) {
            return $this->tanggal_mulai->format('d M Y') . ' - ' . $this->tanggal_selesai->format('d M Y');
        }
        return $this->tanggal_mulai->format('d M Y');
    }
}
