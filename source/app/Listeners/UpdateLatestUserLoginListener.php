<?php

namespace App\Listeners;

use App\Events\UserLoginSuccessEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateLatestUserLoginListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(UserLoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        $user->update([
            'log_login' => json_encode($event->getAgentData())
        ]);
    }
}
