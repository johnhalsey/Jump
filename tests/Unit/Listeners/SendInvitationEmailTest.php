<?php

namespace Tests\Unit\Listeners;

use Tests\TestCase;
use App\Models\Invitation;
use App\Notifications\InviteUser;
use App\Events\UserInvitedToProject;
use Illuminate\Support\Facades\Queue;
use App\Listeners\SendInvitationEmail;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SendInvitationEmailTest extends TestCase
{
   use RefreshDatabase;

   public function test_notification_is_triggered_when_listener_fires()
   {
       $invitation = Invitation::factory()->create();
       $event = new UserInvitedToProject($invitation);
       $listener = new SendInvitationEmail();
       Notification::fake();
       $listener->handle($event);
       Notification::assertSentTo(
           $invitation,
           InviteUser::class
       );
   }

   public function test_assert_listener_queued_when_event_fires()
   {
       Queue::fake();

       $invitation = Invitation::factory()->create();
       event(new UserInvitedToProject($invitation));

       Queue::assertPushed(CallQueuedListener::class, function ($job) {
           return $job->class == SendInvitationEmail::class;
       });
   }
}
