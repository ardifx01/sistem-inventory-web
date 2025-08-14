<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    public function handle(Logout $event): void
    {
        activity()
            ->causedBy($event->user)
            ->withProperties(['ip' => request()->ip()])
            ->log('Logout dari sistem');
    }
}
