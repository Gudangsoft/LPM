<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsKerjasama extends Model
{
    protected $table = 'dkps_kerjasama';

    protected $fillable = [
        'dkps_submission_id',
        'bidang',
        'lembaga_mitra',
        'tingkat',
        'judul_kegiatan',
        'manfaat',
        'tanggal_awal',
        'tanggal_akhir',
        'bukti_file',
        'urutan',
    ];

    protected $casts = [
        'tanggal_awal' => 'date',
        'tanggal_akhir' => 'date',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }

    public function scopeBidang($query, string $bidang)
    {
        return $query->where('bidang', $bidang);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan');
    }
}
