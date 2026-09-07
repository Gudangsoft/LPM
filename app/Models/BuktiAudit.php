<?php

namespace App\Models;

use App\Models\Concerns\RecordsAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BuktiAudit extends Model
{
    use RecordsAuditTrail;

    protected $table = 'bukti_audit';

    protected $fillable = [
        'audit_butir_id',
        'judul',
        'file_path',
        'tautan',
        'versi',
        'keterangan',
        'status_validasi',
        'catatan_validasi',
        'diunggah_oleh',
    ];

    protected static function booted(): void
    {
        static::deleting(function (self $bukti) {
            if ($bukti->file_path) {
                Storage::disk('public')->delete($bukti->file_path);
            }
        });
    }

    public function auditButir(): BelongsTo
    {
        return $this->belongsTo(AuditButir::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }

    public function getUrlAttribute(): ?string
    {
        if ($this->file_path) {
            return Storage::disk('public')->url($this->file_path);
        }

        return $this->tautan;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status_validasi) {
            'valid' => 'success',
            'tidak_valid' => 'danger',
            default => 'secondary',
        };
    }
}
