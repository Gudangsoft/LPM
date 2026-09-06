<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TindakLanjut extends Model
{
    use HasFactory;

    protected $table = 'tindak_lanjut';

    protected $fillable = [
        'temuan_ami_id',
        'user_id',
        'deskripsi',
        'file_bukti',
        'tanggal_submit',
        'status',
        'catatan_reviewer',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'tanggal_submit' => 'date',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get temuan
     */
    public function temuanAmi(): BelongsTo
    {
        return $this->belongsTo(TemuanAmi::class);
    }

    /**
     * Get user (PIC)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get reviewer
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'submitted' => 'info',
            'reviewed' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for pending review
     */
    public function scopePendingReview($query)
    {
        return $query->where('status', 'submitted');
    }

    /**
     * Scope to only records belonging to a prodi the given user heads (kaprodi_id).
     */
    public function scopeOwnedByKaprodi($query, User $user)
    {
        return $query->whereHas('temuanAmi.jadwalAmi.prodi', fn ($q) => $q->where('kaprodi_id', $user->id));
    }

    /**
     * Approve the follow-up
     */
    public function approve(User $reviewer, string $catatan = null): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'catatan_reviewer' => $catatan,
        ]);

        // Update temuan status to closed
        $this->temuanAmi->update(['status' => 'closed']);
    }

    /**
     * Reject the follow-up
     */
    public function reject(User $reviewer, string $catatan): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'catatan_reviewer' => $catatan,
        ]);
    }

    /**
     * Mark as reviewed
     */
    public function markAsReviewed(User $reviewer): void
    {
        $this->update([
            'status' => 'reviewed',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);
    }
}
