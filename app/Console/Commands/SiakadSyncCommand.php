<?php

namespace App\Console\Commands;

use App\Services\Siakad\SiakadException;
use App\Services\Siakad\SiakadSync;
use Illuminate\Console\Command;

/** Tarik data Prodi & Dosen dari SIAKAD (bisa dijadwalkan lewat cron). */
class SiakadSyncCommand extends Command
{
    protected $signature = 'siakad:sync {what=all : prodi | dosen | all}';

    protected $description = 'Sinkron data Program Studi dan Dosen dari API SIAKAD';

    public function handle(SiakadSync $sync): int
    {
        $what = $this->argument('what');
        if (! in_array($what, ['prodi', 'dosen', 'all'], true)) {
            $this->error('Pilih: prodi, dosen, atau all.');

            return self::INVALID;
        }

        try {
            if (in_array($what, ['prodi', 'all'], true)) {
                $r = $sync->syncProdi();
                $this->info("Prodi: {$r['total']} dari SIAKAD -> {$r['created']} ditambahkan, {$r['updated']} diperbarui, ".count($r['skipped']).' dilewati.');
                foreach ($r['skipped'] as $s) {
                    $this->warn('  - '.$s);
                }
            }

            if (in_array($what, ['dosen', 'all'], true)) {
                $r = $sync->syncDosen();
                if ($r['status'] !== 'ok') {
                    $this->error('Dosen: '.$r['message']);
                    foreach (array_slice($r['errors'], 0, 20) as $e) {
                        $this->warn('  - '.$e);
                    }

                    return self::FAILURE;
                }
                $this->info("Dosen: {$r['total']} dari SIAKAD -> {$r['created']} ditambahkan, {$r['updated']} diperbarui, {$r['skipped']} dilewati.");
                foreach (array_slice($r['errors'], 0, 20) as $e) {
                    $this->warn('  - '.$e);
                }
            }
        } catch (SiakadException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
