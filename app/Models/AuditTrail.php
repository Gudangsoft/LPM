<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditTrail extends Model
{
    /** Only created_at is tracked. */
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'description',
        'changes',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Record an entry. Called by the RecordsAuditTrail trait and by workflow
     * methods for named events (verified, approved, closed, ...).
     */
    public static function log(Model $model, string $event, ?string $description = null, ?array $changes = null): void
    {
        static::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->getKey(),
            'description' => $description,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }

    public function getEventLabelAttribute(): string
    {
        return match ($this->event) {
            'created' => 'Dibuat',
            'updated' => 'Diubah',
            'deleted' => 'Dihapus',
            'verified' => 'Diverifikasi',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'closed' => 'Ditutup',
            'reopened' => 'Dibuka Kembali',
            'accepted' => 'Diterima',
            'activated' => 'Diaktifkan',
            'completed' => 'Diselesaikan',
            default => ucfirst($this->event),
        };
    }

    public function getEventColorAttribute(): string
    {
        return match ($this->event) {
            'created', 'accepted', 'activated' => 'success',
            'updated' => 'info',
            'deleted', 'rejected' => 'danger',
            'verified', 'approved', 'closed', 'completed' => 'primary',
            'reopened' => 'warning',
            default => 'secondary',
        };
    }

    public function getModelLabelAttribute(): string
    {
        return class_basename($this->auditable_type);
    }
}
