<?php

namespace App\Notifications;

use App\Models\TemuanAmi;
use Illuminate\Notifications\Notification;

class TemuanOverdue extends Notification
{
    public function __construct(protected TemuanAmi $temuan)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $prodiNama = $this->temuan->jadwalAmi->prodi->nama ?? '-';

        return [
            'subject_type' => 'temuan_ami',
            'subject_id' => $this->temuan->id,
            'message' => "Temuan \"{$this->temuan->standar}\" untuk {$prodiNama} sudah melewati batas waktu tindak lanjut",
            'url' => route('admin.ami.temuan.show', $this->temuan),
        ];
    }
}
