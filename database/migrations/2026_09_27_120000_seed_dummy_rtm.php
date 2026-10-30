<?php

use App\Models\PeriodeAmi;
use App\Models\Rtm;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Rtm belum pernah diisi data contoh - membuat halaman "Rencana
     * Peningkatan Mutu" (yang merangkum rtm_keputusan lintas RTM) dan bab
     * panduan terkait tampak kosong. Seed satu RTM contoh lengkap dengan
     * agenda dan keputusan, supaya representatif untuk didokumentasikan.
     */
    public function up(): void
    {
        if (Rtm::count() > 0) {
            return;
        }

        $periodeId = PeriodeAmi::value('id');
        if (! $periodeId) {
            return;
        }

        $rtm = Rtm::create([
            'periode_ami_id' => $periodeId,
            'judul' => 'RTM Semester Genap 2025/2026',
            'tanggal' => now()->subDays(10)->toDateString(),
            'tempat' => 'Ruang Rapat Pimpinan',
            'pemimpin' => 'Ketua LPM',
            'notulen' => 'Sekretaris LPM',
            'status' => 'selesai',
            'ringkasan' => 'Rapat membahas capaian AMI periode berjalan: penyelesaian temuan, tindak lanjut, dan rencana peningkatan mutu untuk semester berikutnya.',
        ]);

        foreach ([
            ['urutan' => 1, 'topik' => 'Pembukaan dan penyampaian ringkasan hasil AMI periode berjalan', 'pembahasan' => 'Ketua LPM menyampaikan capaian audit mutu internal semester ini secara umum.'],
            ['urutan' => 2, 'topik' => 'Pembahasan temuan yang belum tuntas ditindaklanjuti', 'pembahasan' => 'Diskusi temuan yang masih berstatus open/in_progress dan kendala penyelesaiannya.'],
            ['urutan' => 3, 'topik' => 'Evaluasi capaian sasaran mutu dan rekomendasi peningkatan', 'pembahasan' => 'Peninjauan sasaran mutu yang belum tercapai dan usulan program peningkatan.'],
        ] as $agenda) {
            $rtm->agenda()->create($agenda);
        }

        foreach ([
            ['keputusan' => 'Menambah tenaga dosen tetap bidang Rekayasa Perangkat Lunak', 'rekomendasi' => 'Prodi mengajukan kebutuhan formasi ke bagian kepegawaian', 'pic' => 'Kaprodi Teknik Informatika', 'target_tanggal' => now()->addMonths(3)->toDateString(), 'status' => 'proses', 'tindak_lanjut' => 'Draf pengajuan formasi sudah disusun.', 'urutan' => 1],
            ['keputusan' => 'Perbaikan sarana laboratorium yang rusak', 'rekomendasi' => 'Koordinasi dengan bagian umum untuk perbaikan sebelum semester baru', 'pic' => 'Kepala Sarana Prasarana', 'target_tanggal' => now()->addMonth()->toDateString(), 'status' => 'belum', 'tindak_lanjut' => null, 'urutan' => 2],
            ['keputusan' => 'Optimalisasi kerja sama dengan mitra industri', 'rekomendasi' => 'Menjajaki minimal 2 MoU baru pada tahun akademik berjalan', 'pic' => 'Wakil Ketua Bidang Kerja Sama', 'target_tanggal' => now()->subDays(2)->toDateString(), 'status' => 'selesai', 'tindak_lanjut' => 'MoU dengan 2 mitra industri lokal telah ditandatangani.', 'urutan' => 3],
        ] as $keputusan) {
            $rtm->keputusan()->create($keputusan);
        }
    }

    public function down(): void
    {
        // Data contoh - tidak perlu rollback otomatis.
    }
};
