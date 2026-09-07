<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RtmKeputusan extends Model
{
    protected $table = 'rtm_keputusan';

    protected $fillable = [
        'rtm_id', 'keputusan', 'rekomendasi', 'pic', 'target_tanggal', 'status', 'tindak_lanjut', 'urutan',
    ];

    protected $casts = [
        'target_tanggal' => 'date',
    ];

    public function rtm(): BelongsTo
    {
        return $this->belongsTo(Rtm::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'selesai' => 'success',
            'proses' => 'info',
            default => 'secondary',
        };
    }
}
