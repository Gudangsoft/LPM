<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';

    protected $fillable = [
        'judul',
        'slug',
        'deskripsi',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'kategori',
        'jenis_dokumen_id',
        'standar_mutu_id',
        'download_count',
        'is_active',
        'status',
        'uploaded_by',
        'reviewed_by',
        'reviewed_at',
        'catatan_reviewer',
        'current_version',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->judul);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Visible to public visitors - active AND has cleared the approval workflow.
     */
    public function scopePublished($query)
    {
        return $query->where('is_active', true)->where('status', 'approved');
    }

    /**
     * Get jenis dokumen
     */
    public function jenisDokumen()
    {
        return $this->belongsTo(JenisDokumen::class, 'jenis_dokumen_id');
    }

    public function standarMutu()
    {
        return $this->belongsTo(StandarMutu::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function versions()
    {
        return $this->hasMany(DokumenVersion::class)->orderByDesc('version_number');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'submitted' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    public function submit(): void
    {
        $this->update(['status' => 'submitted']);
    }

    public function approve(User $reviewer, ?string $catatan = null): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'catatan_reviewer' => $catatan,
        ]);
    }

    public function reject(User $reviewer, string $catatan): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'catatan_reviewer' => $catatan,
        ]);
    }

    public function incrementDownload()
    {
        $this->increment('download_count');
    }

    /**
     * Whether the browser can render this file inline (used for the preview modal).
     */
    public function getIsPreviewableAttribute(): bool
    {
        return in_array(strtolower((string) $this->file_type), [
            'pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'txt',
        ], true);
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }
}
