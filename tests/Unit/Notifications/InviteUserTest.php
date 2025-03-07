<?php

namespace Tests\Unit\Notifications;

use Tests\TestCase;
use voku\helper\ASCII;
use App\Models\Invitation;
use App\Notifications\InviteUser;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InviteUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_sent_to_correct_email()
    {
        $invitation = Invitation::factory()->create();
        Notification::fake();
        $invitation->notify(new InviteUser());

        Notification::assertSentTo(
            $invitation,
            InviteUser::class, function (InviteUser $notification, $channels, $notifiable) use ($invitation) {
                return $notifiable->email === $invitation->email;
        });
    }
}
