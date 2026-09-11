<?php

namespace App\Models;

use App\Models\Concerns\RecordsAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class RpsReview extends Model
{
    use RecordsAuditTrail;

    protected $table = 'rps_review';

    protected $fillable = [
        'prodi_id',
        'kode_mk',
        'mata_kuliah',
        'dosen_pengampu',
        'sks',
        'semester',
        'tahun_akademik',
        'file_path',
        'file_name',
        'status',
        'catatan_reviewer',
        'reviewed_by',
        'reviewed_at',
        'submitted_by',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function (self $rps) {
            if ($rps->file_path) {
                Storage::disk('public')->delete($rps->file_path);
            }
        });
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function scopeOwnedByKaprodi($query, User $user)
    {
        return $query->whereHas('prodi', fn ($q) => $q->where('kaprodi_id', $user->id));
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'sesuai' => 'success',
            'perlu_revisi' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'sesuai' => 'Sesuai',
            'perlu_revisi' => 'Perlu Revisi',
            default => 'Belum Direview',
        };
    }
}
