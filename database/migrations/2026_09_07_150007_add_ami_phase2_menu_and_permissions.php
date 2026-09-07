<?php

use App\Models\Permission;
use App\Models\Role;
use Database\Seeders\AmiMenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * AMI Fase 2: Evaluasi Diri, Lembar Kerja Audit, Dokumen/Bukti.
     * Daftarkan permission baru dan bangun ulang menu sidebar SPMI.
     */
    public function up(): void
    {
        $perms = [
            'evaluasi-diri.view' => 'Lihat Evaluasi Diri',
            'evaluasi-diri.fill' => 'Isi Evaluasi Diri',
            'lembar-audit.view' => 'Lihat Lembar Kerja Audit',
            'lembar-audit.fill' => 'Isi Lembar Kerja Audit',
            'bukti.view' => 'Lihat Dokumen/Bukti Audit',
        ];

        $admin = Role::where('slug', 'admin')->first();
        $auditor = Role::where('slug', 'auditor')->first();
        $kaprodi = Role::where('slug', 'kaprodi')->first();
        $viewer = Role::where('slug', 'viewer')->first();

        $grant = function (?Role $role, array $slugs) {
            if (! $role) {
                return;
            }
            $ids = Permission::whereIn('slug', $slugs)->pluck('id');
            $role->permissions()->syncWithoutDetaching($ids);
        };

        foreach ($perms as $slug => $name) {
            Permission::firstOrCreate(['slug' => $slug], ['name' => $name, 'module' => 'ami']);
        }

        $grant($admin, array_keys($perms));
        $grant($auditor, ['evaluasi-diri.view', 'lembar-audit.view', 'lembar-audit.fill', 'bukti.view']);
        $grant($kaprodi, ['evaluasi-diri.view', 'evaluasi-diri.fill', 'lembar-audit.view', 'bukti.view']);
        $grant($viewer, ['evaluasi-diri.view', 'lembar-audit.view', 'bukti.view']);

        AmiMenuSeeder::rebuild();
    }

    public function down(): void
    {
        // Menu & permission data - reversal via re-running the seeders.
    }
};
