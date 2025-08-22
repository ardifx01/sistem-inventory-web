<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ResetPasswordRequest extends Notification
{
    use Queueable;

    public $requester;

    public function __construct($requester)
    {
        $this->requester = $requester;
    }

    public function via($notifiable)
    {
        return ['database']; // hanya notifikasi internal
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->requester->username . ' meminta reset password.',
            'user_id' => $this->requester->id,
        ];
    }
}