<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

/**
 * Builds the "PENJAMINAN MUTU" section of the admin sidebar as a PPEPP tree:
 *
 *   SPMI
 *   ├─ P-1 Penetapan   → Standar Mutu, Program Studi, Buku Panduan
 *   ├─ P-2 Pelaksanaan → DKPS
 *   ├─ E-Evaluasi      → Audit Mutu Internal (Periode/Auditor/Jadwal/Penugasan/Temuan),
 *   │                    Akreditasi (Data, Dashboard), Laporan Evaluasi
 *   ├─ P-3 Pengendalian→ Tindak Lanjut
 *   └─ P-4 Peningkatan  (empty until a feature exists)
 */
class AmiMenuSeeder extends Seeder
{
    /** Leaf routes that belong to this section (used to clean up the old flat menu). */
    private const LEAF_ROUTES = [
        'admin.prodi.index',
        'admin.akreditasi.index',
        'admin.akreditasi.dashboard',
        'admin.ami.periode.index',
        'admin.ami.auditor.index',
        'admin.ami.jadwal.index',
        'admin.ami.penugasan.saya',
        'admin.ami.temuan.index',
        'admin.ami.tindak-lanjut.index',
        'admin.ami.standar-mutu.index',
        'admin.laporan.index',
        'admin.panduan.index',
        'admin.dkps.index',
    ];

    public function run(): void
    {
        self::rebuild();
    }

    /**
     * Remove the previous SPMI menu (flat or PPEPP) and rebuild it. Idempotent.
     */
    public static function rebuild(): void
    {
        // Old section headers + any previous SPMI root (cascades to its children).
        Menu::where('lokasi', 'admin')
            ->whereNull('parent_id')
            ->whereIn('nama', ['SPMI', 'PENJAMINAN MUTU', 'AUDIT MUTU INTERNAL'])
            ->get()
            ->each->delete();

        // Any leftover flat leaf rows.
        Menu::where('lokasi', 'admin')->whereIn('route', self::LEAF_ROUTES)->delete();

        $base = (int) Menu::where('lokasi', 'admin')->whereNull('parent_id')->max('urutan');

        Menu::create([
            'nama' => 'PENJAMINAN MUTU',
            'tipe' => 'section',
            'lokasi' => 'admin',
            'urutan' => $base + 1,
            'is_active' => true,
        ]);

        $spmi = self::node(null, $base + 2, [
            'nama' => 'SPMI',
            'icon' => 'bi-diagram-3',
        ]);

        // -- P-1 Penetapan -------------------------------------------------
        $p1 = self::node($spmi, 1, ['nama' => 'P-1 Penetapan', 'icon' => 'bi-1-circle']);
        self::node($p1, 1, ['nama' => 'Standar Mutu', 'route' => 'admin.ami.standar-mutu.index', 'route_pattern' => 'admin.ami.standar-mutu.*', 'icon' => 'bi-list-check', 'permission' => 'standar-mutu.view']);
        self::node($p1, 2, ['nama' => 'Program Studi', 'route' => 'admin.prodi.index', 'route_pattern' => 'admin.prodi.*', 'icon' => 'bi-mortarboard', 'permission' => 'prodi.view']);
        self::node($p1, 3, ['nama' => 'Buku Panduan', 'route' => 'admin.panduan.index', 'route_pattern' => 'admin.panduan.*', 'icon' => 'bi-journal-bookmark', 'permission' => 'panduan.view']);

        // -- P-2 Pelaksanaan --------------------------------------------------
        $p2 = self::node($spmi, 2, ['nama' => 'P-2 Pelaksanaan', 'icon' => 'bi-2-circle']);
        self::node($p2, 1, ['nama' => 'DKPS', 'route' => 'admin.dkps.index', 'route_pattern' => 'admin.dkps.*', 'icon' => 'bi-clipboard-data', 'permission' => 'dkps.view']);

        // -- E-Evaluasi -----------------------------------------------------
        $ev = self::node($spmi, 3, ['nama' => 'E-Evaluasi', 'icon' => 'bi-clipboard-check']);

        $ami = self::node($ev, 1, ['nama' => 'Audit Mutu Internal', 'icon' => 'bi-search']);
        self::node($ami, 1, ['nama' => 'Periode AMI', 'route' => 'admin.ami.periode.index', 'route_pattern' => 'admin.ami.periode.*', 'icon' => 'bi-calendar3', 'permission' => 'periode-ami.view']);
        self::node($ami, 2, ['nama' => 'Auditor', 'route' => 'admin.ami.auditor.index', 'route_pattern' => 'admin.ami.auditor.*', 'icon' => 'bi-person-badge', 'permission' => 'auditor.view']);
        self::node($ami, 3, ['nama' => 'Jadwal Audit', 'route' => 'admin.ami.jadwal.index', 'route_pattern' => 'admin.ami.jadwal.*', 'icon' => 'bi-calendar-check', 'permission' => 'jadwal-ami.view']);
        self::node($ami, 4, ['nama' => 'Penugasan Saya', 'route' => 'admin.ami.penugasan.saya', 'route_pattern' => 'admin.ami.penugasan.*', 'icon' => 'bi-inbox', 'permission' => 'penugasan.respond']);
        self::node($ami, 5, ['nama' => 'Temuan', 'route' => 'admin.ami.temuan.index', 'route_pattern' => 'admin.ami.temuan.*', 'icon' => 'bi-exclamation-diamond', 'permission' => 'temuan.view']);

        $akr = self::node($ev, 2, ['nama' => 'Akreditasi', 'icon' => 'bi-award']);
        self::node($akr, 1, ['nama' => 'Data Akreditasi', 'route' => 'admin.akreditasi.index', 'route_pattern' => 'admin.akreditasi.*', 'icon' => 'bi-award', 'permission' => 'akreditasi.view']);
        self::node($akr, 2, ['nama' => 'Dashboard Akreditasi', 'route' => 'admin.akreditasi.dashboard', 'icon' => 'bi-speedometer2', 'permission' => 'akreditasi.view']);

        self::node($ev, 3, ['nama' => 'Laporan Evaluasi', 'route' => 'admin.laporan.index', 'route_pattern' => 'admin.laporan.*', 'icon' => 'bi-file-earmark-bar-graph', 'permission' => 'laporan-ami.view']);

        // -- P-3 Pengendalian ----------------------------------------------
        $p3 = self::node($spmi, 4, ['nama' => 'P-3 Pengendalian', 'icon' => 'bi-3-circle']);
        self::node($p3, 1, [
            'nama' => 'Tindak Lanjut',
            'route' => 'admin.ami.tindak-lanjut.index',
            'route_pattern' => 'admin.ami.tindak-lanjut.*',
            'icon' => 'bi-clipboard-check',
            'permission' => 'tindak-lanjut.view',
            'badge_model' => 'App\\Models\\TindakLanjut',
            'badge_method' => 'pendingReview',
            'badge_class' => 'bg-warning',
        ]);

        // -- P-4 Peningkatan ---------------------------------------------------
        self::node($spmi, 5, ['nama' => 'P-4 Peningkatan', 'icon' => 'bi-4-circle']);

        Menu::clearCache();
    }

    private static function node(?int $parentId, int $urutan, array $attr): int
    {
        return Menu::create(array_merge([
            'tipe' => 'link',
            'lokasi' => 'admin',
            'parent_id' => $parentId,
            'urutan' => $urutan,
            'is_active' => true,
            'badge_class' => 'bg-danger',
        ], $attr))->id;
    }
}
