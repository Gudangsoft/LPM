<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsTik extends Model
{
    protected $table = 'dkps_tik';

    protected $fillable = [
        'dkps_submission_id',
        'nama_infrastruktur',
        'deskripsi',
        'jumlah',
        'terintegrasi',
        'mutahir',
        'ada_panduan',
        'kepemilikan',
        'kondisi',
        'urutan',
    ];

    protected $casts = [
        'ada_panduan' => 'boolean',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }
}
