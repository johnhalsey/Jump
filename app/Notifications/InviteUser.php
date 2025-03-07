<?php

namespace App\Notifications;

use App\Models\Project;
use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InviteUser extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(Invitation $invitation): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Invitation $invitation): MailMessage
    {
        $project = Project::find($invitation->project_id);

        $signedUrl = URL::temporarySignedRoute(
            'project.invitations.accept', now()->addHours(2), [
                'project' => $project->id,
                'email'   => $invitation->email,
                'token'   => $invitation->token,
            ]
        );

        return (new MailMessage)
            ->subject('You have been invited to join a project on ' . config('app.name'))
            ->line('Hello,')
            ->line('You have been invited to join the ' . $project->name . ' project on ' . config('app.name'))
            ->action('Accept', url($signedUrl))
            ->line('Jump in to project management');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
