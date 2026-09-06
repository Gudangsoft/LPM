<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsPenelitianPkmRingkasan extends Model
{
    protected $table = 'dkps_penelitian_pkm_ringkasan';

    protected $fillable = [
        'dkps_submission_id',
        'jenis',
        'sumber_pembiayaan',
        'jumlah_ts2',
        'jumlah_ts1',
        'jumlah_ts',
    ];

    const SUMBER_PEMBIAYAAN = ['pt_mandiri', 'lembaga_dalam_negeri', 'lembaga_luar_negeri'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }

    public function scopeJenis($query, string $jenis)
    {
        return $query->where('jenis', $jenis);
    }
}
