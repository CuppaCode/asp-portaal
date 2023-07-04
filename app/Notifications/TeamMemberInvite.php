<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;

class TeamMemberInvite extends Notification
{
    use Queueable;

    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return $this->getMessage();
    }

    public function getMessage()
    {
        return (new MailMessage)
            ->subject(config('app.name') . ': Gebruiker aangemaakt ')
            ->greeting('Hallo,')
            ->line('Er is zojuist een account aangemaakt voor AutosSchadePlan Portaal')
            ->line('Klik hier om jouw account te activeren')
            ->action('Registreren', $this->url)
            ->salutation(new HtmlString('Bedankt, <br> AutoSchadePlan'));
    }
}
