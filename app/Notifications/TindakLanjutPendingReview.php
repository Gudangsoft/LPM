<?php

namespace App\Notifications;

use App\Models\TindakLanjut;
use Illuminate\Notifications\Notification;

class TindakLanjutPendingReview extends Notification
{
    public function __construct(protected TindakLanjut $tindakLanjut)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $prodiNama = $this->tindakLanjut->temuanAmi->jadwalAmi->prodi->nama ?? '-';

        return [
            'subject_type' => 'tindak_lanjut',
            'subject_id' => $this->tindakLanjut->id,
            'message' => "Tindak lanjut dari {$prodiNama} menunggu review Anda",
            'url' => route('admin.ami.tindak-lanjut.show', $this->tindakLanjut),
        ];
    }
}
