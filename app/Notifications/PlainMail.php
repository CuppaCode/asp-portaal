<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;

class PlainMail extends Notification
{
    use Queueable;

    protected $subject;
    protected $message;
    protected $attachments;
    protected $cc;
    protected $bcc;

    /**
     * Create a new notification instance.
     */
    public function __construct($subject, $message, $attachments = null, $cc = [], $bcc = [])
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->attachments = $attachments;
        $this->cc = $cc;
        $this->bcc = $bcc;
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

        $mailMessage = (new MailMessage)
            ->view('emails.plain-email', ['body' => $this->message])
            ->subject($this->subject);

        // Add CC recipients if provided
        if(!empty($this->cc)) {
            $mailMessage->cc($this->cc);
        }

        if (!empty($this->bcc)) {
            $mailMessage->bcc($this->bcc);
        }
      
        if($this->attachments) {

            foreach($this->attachments as $index => $file) {
                
                $mailMessage->attach($file, [
                    'as' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType()
                ]);

            }

        }

        return $mailMessage;
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
