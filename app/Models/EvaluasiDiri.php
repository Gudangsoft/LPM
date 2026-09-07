<?php

namespace App\Models;

use App\Models\Concerns\RecordsAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluasiDiri extends Model
{
    use RecordsAuditTrail;

    protected $table = 'evaluasi_diri';

    protected $fillable = [
        'jadwal_ami_id',
        'status',
        'catatan',
        'submitted_at',
        'submitted_by',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function jadwalAmi(): BelongsTo
    {
        return $this->belongsTo(JadwalAmi::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status === 'submitted' ? 'success' : 'secondary';
    }
}
