<?php

namespace App\Models;

use App\Models\Concerns\RecordsAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuditButir extends Model
{
    use RecordsAuditTrail;

    protected $table = 'audit_butir';

    protected $fillable = [
        'jadwal_ami_id',
        'butir_instrumen_id',
        'nilai_mandiri',
        'deskripsi_capaian',
        'nilai_auditor',
        'catatan_auditor',
        'status_verifikasi',
        'diisi_auditee_at',
        'diisi_auditor_at',
    ];

    protected $casts = [
        'nilai_mandiri' => 'decimal:2',
        'nilai_auditor' => 'decimal:2',
        'diisi_auditee_at' => 'datetime',
        'diisi_auditor_at' => 'datetime',
    ];

    public function jadwalAmi(): BelongsTo
    {
        return $this->belongsTo(JadwalAmi::class);
    }

    public function butir(): BelongsTo
    {
        return $this->belongsTo(ButirInstrumen::class, 'butir_instrumen_id');
    }

    public function bukti(): HasMany
    {
        return $this->hasMany(BuktiAudit::class)->orderByDesc('versi')->orderByDesc('id');
    }

    public function getStatusVerifikasiColorAttribute(): string
    {
        return match ($this->status_verifikasi) {
            'sesuai' => 'success',
            'perlu_perbaikan' => 'warning',
            'tidak_sesuai' => 'danger',
            default => 'secondary',
        };
    }
}
