<?php

namespace App\Notifications;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComplaintNotification extends Notification
{
    use Queueable;

    protected $company;
    protected $summary;

    /**
     * Create a new notification instance.
     */
    public function __construct(Company $company, array $summary = [])
    {
        $this->company = $company;
        $this->summary = $summary;
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
        $message = (new MailMessage)
            ->subject('Nieuwe klacht ingediend - ' . $this->company->name)
            ->greeting('Beste ' . $this->company->name . ' medewerker,')
            ->line('Er is een nieuwe klacht ingediend via het online formulier.');

        if (!empty($this->summary)) {
            $message->line('### Samenvatting van de klacht:');
            
            foreach ($this->summary as $label => $value) {
                $message->line('**' . $label . ':** ' . $value);
            }
        }

        $message->line('Deze klacht is ter informatie en creëert geen schademelding in het systeem.')
            ->salutation('Met vriendelijke groet, AutoSchadePlan');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'company_id' => $this->company->id,
            'type' => 'complaint',
        ];
    }
}
