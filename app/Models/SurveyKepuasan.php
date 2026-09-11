<?php

namespace App\Models;

use App\Models\Concerns\RecordsAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SurveyKepuasan extends Model
{
    use RecordsAuditTrail;

    protected $table = 'survey_kepuasan';

    protected $fillable = [
        'prodi_id',
        'jenis_responden',
        'judul_survei',
        'tahun_akademik',
        'semester',
        'jumlah_responden',
        'rata_rata_skor',
        'skala_maksimal',
        'ringkasan_hasil',
        'file_path',
        'file_name',
    ];

    protected static function booted(): void
    {
        static::deleting(function (self $survey) {
            if ($survey->file_path) {
                Storage::disk('public')->delete($survey->file_path);
            }
        });
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function getJenisRespondenLabelAttribute(): string
    {
        return match ($this->jenis_responden) {
            'mahasiswa' => 'Mahasiswa',
            'dosen' => 'Dosen',
            'tendik' => 'Tenaga Kependidikan',
            'alumni' => 'Alumni',
            'pengguna_lulusan' => 'Pengguna Lulusan',
            default => ucfirst($this->jenis_responden),
        };
    }

    public function getPersentaseSkorAttribute(): ?float
    {
        if ($this->rata_rata_skor === null || ! $this->skala_maksimal) {
            return null;
        }

        return round(($this->rata_rata_skor / $this->skala_maksimal) * 100, 1);
    }
}
