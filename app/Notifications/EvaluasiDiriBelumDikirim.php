<?php

namespace App\Notifications;

use App\Models\JadwalAmi;
use Illuminate\Notifications\Notification;

class EvaluasiDiriBelumDikirim extends Notification
{
    public function __construct(protected JadwalAmi $jadwal)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $prodi = $this->jadwal->prodi->nama ?? '-';
        $tanggal = optional($this->jadwal->tanggal_audit)->format('d M Y');

        return [
            'subject_type' => 'jadwal_ami',
            'subject_id' => $this->jadwal->id,
            'message' => "Evaluasi diri {$prodi} belum dikirim — audit dijadwalkan {$tanggal}",
            'url' => route('admin.ami.evaluasi-diri.show', $this->jadwal),
        ];
    }
}
