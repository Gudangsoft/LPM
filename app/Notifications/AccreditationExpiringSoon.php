<?php

namespace App\Notifications;

use App\Models\Akreditasi;
use Illuminate\Notifications\Notification;

class AccreditationExpiringSoon extends Notification
{
    public function __construct(protected Akreditasi $akreditasi)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'subject_type' => 'akreditasi',
            'subject_id' => $this->akreditasi->id,
            'message' => "Akreditasi {$this->akreditasi->prodi->nama} akan kadaluarsa pada {$this->akreditasi->tanggal_kadaluarsa->translatedFormat('d F Y')}",
            'url' => route('admin.akreditasi.show', $this->akreditasi),
        ];
    }
}
