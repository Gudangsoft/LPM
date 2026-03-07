<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistik extends Model
{
    use HasFactory;

    protected $table = 'statistik';

    protected $fillable = [
        'kategori',
        'tahun',
        'usulan',
        'didanai',
        'dana_usulan',
        'dana_disetujui',
        'keterangan',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'usulan' => 'integer',
        'didanai' => 'integer',
        'dana_usulan' => 'decimal:2',
        'dana_disetujui' => 'decimal:2',
    ];

    /**
     * Available categories
     */
    public const KATEGORI_PENELITIAN = 'penelitian';
    public const KATEGORI_PENGABDIAN = 'pengabdian';
    public const KATEGORI_PUBLIKASI = 'publikasi';
    public const KATEGORI_HKI = 'hki';
    public const KATEGORI_KERJASAMA = 'kerjasama';

    /**
     * Get all categories
     */
    public static function getKategoriOptions(): array
    {
        return [
            self::KATEGORI_PENELITIAN => 'Penelitian',
            self::KATEGORI_PENGABDIAN => 'Pengabdian',
            self::KATEGORI_PUBLIKASI => 'Publikasi',
            self::KATEGORI_HKI => 'HKI/Paten',
            self::KATEGORI_KERJASAMA => 'Kerjasama',
        ];
    }

    /**
     * Get label for kategori
     */
    public function getKategoriLabelAttribute(): string
    {
        return self::getKategoriOptions()[$this->kategori] ?? $this->kategori;
    }

    /**
     * Scope for specific category
     */
    public function scopeKategori($query, string $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope for year range
     */
    public function scopeTahunRange($query, int $from, int $to)
    {
        return $query->whereBetween('tahun', [$from, $to]);
    }

    /**
     * Get statistics for chart - by category
     */
    public static function getChartData(string $kategori, int $fromYear = null, int $toYear = null): array
    {
        $fromYear = $fromYear ?? now()->year - 10;
        $toYear = $toYear ?? now()->year;

        $data = self::where('kategori', $kategori)
            ->whereBetween('tahun', [$fromYear, $toYear])
            ->orderBy('tahun')
            ->get();

        $years = [];
        $usulan = [];
        $didanai = [];

        // Fill all years including empty ones
        for ($year = $fromYear; $year <= $toYear; $year++) {
            $years[] = $year;
            $record = $data->firstWhere('tahun', $year);
            $usulan[] = $record ? $record->usulan : 0;
            $didanai[] = $record ? $record->didanai : 0;
        }

        return [
            'labels' => $years,
            'datasets' => [
                [
                    'label' => 'Usulan',
                    'data' => $usulan,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Didanai',
                    'data' => $didanai,
                    'borderColor' => '#c084fc',
                    'backgroundColor' => 'rgba(192, 132, 252, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    /**
     * Get all categories chart data
     */
    public static function getAllCategoriesChartData(int $fromYear = null, int $toYear = null): array
    {
        $result = [];
        foreach (self::getKategoriOptions() as $key => $label) {
            $result[$key] = self::getChartData($key, $fromYear, $toYear);
        }
        return $result;
    }

    /**
     * Get summary statistics
     */
    public static function getSummary(string $kategori = null): array
    {
        $query = self::query();
        
        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        return [
            'total_usulan' => $query->sum('usulan'),
            'total_didanai' => $query->sum('didanai'),
            'total_dana_usulan' => $query->sum('dana_usulan'),
            'total_dana_disetujui' => $query->sum('dana_disetujui'),
            'persentase_didanai' => $query->sum('usulan') > 0 
                ? round(($query->sum('didanai') / $query->sum('usulan')) * 100, 2) 
                : 0,
        ];
    }

    /**
     * Get yearly summary
     */
    public static function getYearlySummary(int $year): array
    {
        $data = self::where('tahun', $year)->get();
        
        $summary = [];
        foreach (self::getKategoriOptions() as $key => $label) {
            $record = $data->firstWhere('kategori', $key);
            $summary[$key] = [
                'label' => $label,
                'usulan' => $record ? $record->usulan : 0,
                'didanai' => $record ? $record->didanai : 0,
                'dana_usulan' => $record ? $record->dana_usulan : 0,
                'dana_disetujui' => $record ? $record->dana_disetujui : 0,
            ];
        }

        return $summary;
    }
}
