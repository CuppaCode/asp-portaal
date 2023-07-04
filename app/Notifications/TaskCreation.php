<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;

class TaskCreation extends Notification
{
    use Queueable;

    protected $url;

    /**
     * Create a new notification instance.
     */
    public function __construct($task, $claim, $user)
    {
        $this->task = $task;
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
        $url = url('/admin/tasks/'.$this->task->id);
        
        // dd($this->claim);

        if (isset($this->claim[0]->claim_number)){
            $claim_number = $this->claim[0]->claim_number;
        } else {
            $claim_number = null;
        }

        return (new MailMessage)
            ->subject(config('app.name') . ': Er is een nieuwe taak aangemaakt ')
            ->greeting("Hallo {$this->user['name']},")
            ->line("Er staat een nieuwe taak voor je klaar")
            ->lineIf($claim_number != null, "Betreffende schadedossier: {$claim_number}")
            ->line("Beschrijving taak: {$this->task['description']}")
            ->line("Deadline: {$this->task['deadline_at']}  ")
            ->action('Bekijk taak', $url)
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
