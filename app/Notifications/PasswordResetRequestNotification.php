<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PasswordResetRequestNotification extends Notification
{
    use Queueable;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "User {$this->user->username} ({$this->user->email}) meminta reset password.",
            'user_id' => $this->user->id,
        ];
    }
}
