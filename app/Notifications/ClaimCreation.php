<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;

class ClaimCreation extends Notification
{
    use Queueable;

    protected $url;

    /**
     * Create a new notification instance.
     */
    public function __construct($claim, $user)
    {
        $this->claim = $claim;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/admin/claims/'.$this->claim->id);

        $assign_self = $this->claim->assign_self;
        $claim_number = $this->claim->claim_number;
        
        // dd($this->claim);

        return (new MailMessage)
            ->subject(config('app.name') . ': Er is een nieuwe claim aangemaakt ')
            ->greeting("Hi Patrick,")
            ->line("Er staat een nieuwe claim klaar")
            ->lineIf($assign_self == 1, "LET OP CLAIM WORDT DOOR KLANT ZELF OPGEPAKT.")
            ->line("Schadedossier nummer: {$claim_number}")
            ->action('Bekijk schadedossier', $url)
            ->salutation(new HtmlString("Bedankt, <br>Autoschadeplan"));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            // 'description'   => $this->task->description,
            // 'claim_number'  => $this->task->claim_number,
        ];
    }
}
