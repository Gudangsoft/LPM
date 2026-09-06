<?php

namespace App\Console\Commands;

use App\Models\Akreditasi;
use App\Models\Auditor;
use App\Models\TemuanAmi;
use App\Models\TindakLanjut;
use App\Models\User;
use App\Notifications\AccreditationExpiringSoon;
use App\Notifications\AuditorCertExpiringSoon;
use App\Notifications\TemuanOverdue;
use App\Notifications\TindakLanjutPendingReview;
use Illuminate\Console\Command;

class CheckAmiDeadlines extends Command
{
    protected $signature = 'ami:check-deadlines';

    protected $description = 'Notify the responsible person about expiring accreditation, overdue findings, expiring auditor certificates, and pending tindak-lanjut reviews';

    public function handle(): int
    {
        // Keep the persisted status in sync with reality - without this, an
        // accreditation whose date has already passed can sit at status=aktif
        // indefinitely since nothing else ever writes to this column.
        Akreditasi::where('status', 'aktif')
            ->where('tanggal_kadaluarsa', '<', now())
            ->get()
            ->each->updateStatusBasedOnExpiration();

        $created = 0;

        foreach (Akreditasi::expiringSoon()->with('prodi.kaprodi')->get() as $akreditasi) {
            if ($kaprodi = $akreditasi->prodi?->kaprodi) {
                $created += $this->notifyOnce($kaprodi, AccreditationExpiringSoon::class, $akreditasi->id, fn () => new AccreditationExpiringSoon($akreditasi));
            }
        }

        foreach (TemuanAmi::overdue()->with('jadwalAmi.prodi.kaprodi')->get() as $temuan) {
            if ($kaprodi = $temuan->jadwalAmi?->prodi?->kaprodi) {
                $created += $this->notifyOnce($kaprodi, TemuanOverdue::class, $temuan->id, fn () => new TemuanOverdue($temuan));
            }
        }

        foreach (Auditor::certExpiringSoon()->with('user')->get() as $auditor) {
            if ($auditor->user) {
                $created += $this->notifyOnce($auditor->user, AuditorCertExpiringSoon::class, $auditor->id, fn () => new AuditorCertExpiringSoon($auditor));
            }
        }

        foreach (TindakLanjut::pendingReview()->with('temuanAmi.auditor.user')->get() as $tindakLanjut) {
            if ($reviewer = $tindakLanjut->temuanAmi?->auditor?->user) {
                $created += $this->notifyOnce($reviewer, TindakLanjutPendingReview::class, $tindakLanjut->id, fn () => new TindakLanjutPendingReview($tindakLanjut));
            }
        }

        $this->info("Created {$created} notification(s).");

        return self::SUCCESS;
    }

    /**
     * Notify $user unless an unread notification of the same $type+$subjectId
     * already exists - keeps repeated command runs from spamming duplicates,
     * while still re-notifying once a previous alert has been read/dismissed.
     */
    private function notifyOnce(User $user, string $type, int $subjectId, \Closure $make): int
    {
        $exists = $user->notifications()
            ->where('type', $type)
            ->whereNull('read_at')
            ->where('data->subject_id', $subjectId)
            ->exists();

        if ($exists) {
            return 0;
        }

        $user->notify($make());

        return 1;
    }
}
