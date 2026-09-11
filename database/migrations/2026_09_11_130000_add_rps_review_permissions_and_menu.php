<?php

use App\Models\Permission;
use App\Models\Role;
use Database\Seeders\AmiMenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Review RPS: daftarkan permission rps.* dan bangun ulang menu sidebar SPMI
     * sehingga item "Review RPS" mengarah ke fitur nyata, bukan coming-soon.
     */
    public function up(): void
    {
        $perms = [
            'rps.view' => 'Lihat Review RPS',
            'rps.manage' => 'Kelola Review RPS',
        ];

        foreach ($perms as $slug => $name) {
            Permission::firstOrCreate(['slug' => $slug], ['name' => $name, 'module' => 'ami']);
        }

        $viewId = Permission::where('slug', 'rps.view')->value('id');
        $manageId = Permission::where('slug', 'rps.manage')->value('id');

        if ($admin = Role::where('slug', 'admin')->first()) {
            $admin->permissions()->syncWithoutDetaching([$viewId, $manageId]);
        }
        if ($kaprodi = Role::where('slug', 'kaprodi')->first()) {
            $kaprodi->permissions()->syncWithoutDetaching([$viewId, $manageId]);
        }
        foreach (['auditor', 'viewer'] as $slug) {
            if ($role = Role::where('slug', $slug)->first()) {
                $role->permissions()->syncWithoutDetaching([$viewId]);
            }
        }

        AmiMenuSeeder::rebuild();
    }

    public function down(): void
    {
        // Menu & permission data - reversal via re-running the seeders.
    }
};
