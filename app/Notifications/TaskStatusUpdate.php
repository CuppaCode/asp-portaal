<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskStatusUpdate extends Notification
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

        $status = Task::STATUS_SELECT[$this->task->status];
        $url = url('/admin/claims/'.$this->claim->id);

        return (new MailMessage)
            ->subject(config('app.name') . ' - Status van taak is gewijzigd naar '. $status)
            ->line("De status van de taak is gewijzigd naar ". $status. ". Betreffende onderstaande taak: ")
            ->line($this->task->description)
            ->action('Bekijk taak in claim', $url)
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
