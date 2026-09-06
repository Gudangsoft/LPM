<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsPrasarana extends Model
{
    protected $table = 'dkps_prasarana';

    protected $fillable = [
        'dkps_submission_id',
        'nama_prasarana',
        'fungsi',
        'jumlah_unit',
        'total_luas_m2',
        'kualitas',
        'kepemilikan',
        'kondisi',
        'urutan',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }
}
