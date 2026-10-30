<?php

use App\Models\Panduan;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Melengkapi 5 slot "[SS]" terakhir yang sebelumnya dilewati karena
     * butuh state modal/tab/data yang tidak praktis diambil dalam batch
     * pertama - sekarang sudah ada data RTM contoh (lihat migration
     * seed_dummy_rtm) dan screenshot modal/tab sudah diambil.
     */
    public function up(): void
    {
        $old = fn (string $desc) => '<div class="my-3 p-3 rounded" style="border:2px dashed #94a3b8;background:#f8fafc;">'
            . '<p class="mb-0 text-muted"><strong>[SS]</strong> — <em>' . $desc . '</em></p></div>';

        $img = fn (string $desc, string $file) => '<figure class="my-3">'
            . '<img src="/storage/panduan/' . $file . '" alt="' . $desc . '">'
            . '<figcaption class="text-muted small mt-1">' . $desc . '</figcaption></figure>';

        $map = [
            'Profil & Keamanan Akun Saya' => [
                ['Halaman Profil - tab Sesi Aktif dengan daftar perangkat dan tombol akhiri sesi', 'profile-sesi.png'],
            ],
            'Rapat Tinjauan Manajemen (RTM)' => [
                ['Halaman detail RTM - daftar agenda dan tabel keputusan/rekomendasi', 'rtm-show.png'],
            ],
            'Evaluasi Pembelajaran' => [
                ['Jendela evaluasi pembelajaran - pilihan kesesuaian RPS, kendala, rekomendasi', 'evaluasi-pembelajaran-modal.png'],
            ],
            'Review RPS' => [
                ['Jendela review RPS - pilihan status dan catatan reviewer', 'review-rps-modal.png'],
            ],
            'Editor Menu Website Publik' => [
                ['Panel quick-edit menu - field nama, tujuan/link, dan toggle buka tab baru', 'menu-web-quickedit.png'],
            ],
        ];

        foreach ($map as $judul => $entries) {
            $panduan = Panduan::where('judul', $judul)->first();
            if (! $panduan) {
                continue;
            }

            $konten = $panduan->konten;
            foreach ($entries as [$desc, $file]) {
                $konten = str_replace($old($desc), $img($desc, $file), $konten);
            }

            if ($konten !== $panduan->konten) {
                $panduan->update(['konten' => $konten]);
            }
        }
    }

    public function down(): void
    {
        // Konten panduan - tidak ada rollback otomatis.
    }
};
