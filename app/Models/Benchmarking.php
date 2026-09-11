<?php

namespace App\Models;

use App\Models\Concerns\RecordsAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Benchmarking extends Model
{
    use RecordsAuditTrail;

    protected $table = 'benchmarking';

    protected $fillable = [
        'standar_mutu_id',
        'prodi_id',
        'tahun_akademik',
        'aspek',
        'institusi_pembanding',
        'nilai_sendiri',
        'nilai_pembanding',
        'kesimpulan',
        'rekomendasi',
    ];

    public function standarMutu(): BelongsTo
    {
        return $this->belongsTo(StandarMutu::class);
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }
}
