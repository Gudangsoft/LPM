<?php

namespace Database\Seeders;

use App\Models\Dokumen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Heals document records whose physical file is missing on this environment
 * (e.g. rows imported from a dump or created by DummyDataSeeder without the
 * actual files). Safe to run repeatedly on any environment.
 *
 *   php artisan db:seed --class=DokumenFileSeeder --force
 */
class DokumenFileSeeder extends Seeder
{
    public function run(): void
    {
        $disk = Storage::disk('public');
        $created = 0;
        $fixed = 0;

        foreach (Dokumen::all() as $dokumen) {
            $path = $dokumen->file_path;

            if (blank($path)) {
                $path = 'dokumen/' . $dokumen->slug . '.pdf';
                $dokumen->file_path = $path;
            }

            if (! $this->isUsablePdf($disk, $path)) {
                $disk->put($path, $this->placeholderPdf($dokumen->judul));
                $created++;
            }

            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION) ?: 'pdf');

            $dokumen->fill([
                'file_path' => $path,
                'file_name' => $dokumen->file_name ?: Str::slug($dokumen->judul) . '.' . $extension,
                'file_size' => $disk->exists($path) ? $disk->size($path) : $dokumen->file_size,
                // Normalise MIME types ("application/pdf") down to a plain extension.
                'file_type' => str_contains((string) $dokumen->file_type, '/')
                    ? $extension
                    : ($dokumen->file_type ?: $extension),
            ]);

            if ($dokumen->isDirty()) {
                $dokumen->save();
                $fixed++;
            }
        }

        $this->command?->info("DokumenFileSeeder: {$created} placeholder file(s) created, {$fixed} record(s) updated.");
    }

    /**
     * A stored path counts as usable when the file exists and, for PDFs, actually
     * looks like a PDF (guards against 0-byte / plain-text placeholder stubs).
     */
    private function isUsablePdf($disk, string $path): bool
    {
        if (! $disk->exists($path)) {
            return false;
        }

        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'pdf') {
            return true;
        }

        return str_starts_with((string) $disk->get($path), '%PDF-') && $disk->size($path) > 200;
    }

    /**
     * Build a minimal but valid single-page PDF used as a placeholder document.
     */
    private function placeholderPdf(string $title): string
    {
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $title);
        $stream = "BT /F1 18 Tf 56 760 Td ($text) Tj ET";

        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            3 => '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] '
                . '/Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            5 => '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $num => $body) {
            $offsets[$num] = strlen($pdf);
            $pdf .= $num . " 0 obj\n" . $body . "\nendobj\n";
        }

        $xref = strlen($pdf);
        $size = count($objects) + 1;
        $pdf .= "xref\n0 {$size}\n0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        $pdf .= "trailer\n<< /Size {$size} /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";

        return $pdf;
    }
}
