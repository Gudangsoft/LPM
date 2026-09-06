<?php

namespace App\Notifications;

use App\Models\Auditor;
use Illuminate\Notifications\Notification;

class AuditorCertExpiringSoon extends Notification
{
    public function __construct(protected Auditor $auditor)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'subject_type' => 'auditor',
            'subject_id' => $this->auditor->id,
            'message' => "Sertifikat auditor Anda akan kadaluarsa pada {$this->auditor->masa_berlaku->translatedFormat('d F Y')}",
            'url' => route('admin.ami.auditor.show', $this->auditor),
        ];
    }
}
