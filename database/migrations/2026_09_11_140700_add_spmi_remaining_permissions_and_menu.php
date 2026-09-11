<?php

use App\Models\Permission;
use App\Models\Role;
use Database\Seeders\AmiMenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Fungsikan 8 menu SPMI yang masih coming-soon: Sasaran Mutu, Pengendalian
     * Dokumen, Rencana Peningkatan Mutu, Benchmarking, Survey Kepuasan, Monev,
     * Evaluasi Pembelajaran, Capaian Pembelajaran. Pengendalian Dokumen dan
     * Rencana Peningkatan Mutu memakai ulang permission dokumen.view/rtm.view
     * yang sudah ada - hanya 6 fitur di bawah yang perlu permission baru.
     */
    public function up(): void
    {
        $perms = [
            'sasaran-mutu.view' => 'Lihat Sasaran Mutu',
            'sasaran-mutu.manage' => 'Kelola Sasaran Mutu',
            'benchmarking.view' => 'Lihat Benchmarking',
            'benchmarking.manage' => 'Kelola Benchmarking',
            'survey.view' => 'Lihat Survey Kepuasan',
            'survey.manage' => 'Kelola Survey Kepuasan',
            'monev.view' => 'Lihat Monev',
            'monev.manage' => 'Kelola Monev',
            'evaluasi-pembelajaran.view' => 'Lihat Evaluasi Pembelajaran',
            'evaluasi-pembelajaran.manage' => 'Kelola Evaluasi Pembelajaran',
            'capaian-pembelajaran.view' => 'Lihat Capaian Pembelajaran',
            'capaian-pembelajaran.manage' => 'Kelola Capaian Pembelajaran',
        ];

        foreach ($perms as $slug => $name) {
            Permission::firstOrCreate(['slug' => $slug], ['name' => $name, 'module' => 'ami']);
        }

        $ids = fn (array $slugs) => Permission::whereIn('slug', $slugs)->pluck('id');

        if ($admin = Role::where('slug', 'admin')->first()) {
            $admin->permissions()->syncWithoutDetaching($ids(array_keys($perms)));
        }

        // Kaprodi manages the prodi-scoped ones (Sasaran Mutu, Monev, Evaluasi
        // Pembelajaran, Capaian Pembelajaran), but only views the institutional
        // ones (Benchmarking, Survey Kepuasan) - those are LPM/admin-filled.
        if ($kaprodi = Role::where('slug', 'kaprodi')->first()) {
            $kaprodi->permissions()->syncWithoutDetaching($ids([
                'sasaran-mutu.view', 'sasaran-mutu.manage',
                'monev.view', 'monev.manage',
                'evaluasi-pembelajaran.view', 'evaluasi-pembelajaran.manage',
                'capaian-pembelajaran.view', 'capaian-pembelajaran.manage',
                'benchmarking.view',
                'survey.view',
            ]));
        }

        foreach (['auditor', 'viewer'] as $slug) {
            if ($role = Role::where('slug', $slug)->first()) {
                $role->permissions()->syncWithoutDetaching($ids([
                    'sasaran-mutu.view', 'benchmarking.view', 'survey.view',
                    'monev.view', 'evaluasi-pembelajaran.view', 'capaian-pembelajaran.view',
                ]));
            }
        }

        AmiMenuSeeder::rebuild();
    }

    public function down(): void
    {
        // Menu & permission data - reversal via re-running the seeders.
    }
};
