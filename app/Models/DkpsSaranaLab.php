<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsSaranaLab extends Model
{
    protected $table = 'dkps_sarana_lab';

    protected $fillable = [
        'dkps_submission_id',
        'nama_lab_ruang',
        'nama_alat_peraga',
        'kualitas',
        'jumlah',
        'kepemilikan',
        'kondisi',
        'rata_rata_jam_minggu',
        'urutan',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }
}
