<?php

namespace App\Listeners;

use App\Notifications\InviteUser;
use App\Events\UserInvitedToProject;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendInvitationEmail implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(UserInvitedToProject $event): void
    {
        $event->invitation->notify(new InviteUser());
    }
}
