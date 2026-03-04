<?php

namespace App\Notifications;

use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class DraftClaimReminder extends Notification
{
    use Queueable;

    protected $claim;

    public function __construct(Claim $claim)
    {
        $this->claim = $claim;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $approveUrl = URL::signedRoute('draft-claim.approve', ['claim' => $this->claim->id]);
        $denyUrl = URL::signedRoute('draft-claim.deny-form', ['claim' => $this->claim->id]);

        $daysRemaining = now()->diffInDays($this->claim->draft_expires_at, false);
        $daysRemainingText = $daysRemaining > 0 
            ? $daysRemaining . ' dagen' 
            : 'minder dan 1 dag';

        return (new MailMessage)
            ->subject('Herinnering: Concept claim ' . $this->claim->claim_number . ' wacht op goedkeuring')
            ->greeting('Beste ' . $this->claim->company->name . ' medewerker,')
            ->line('Dit is een herinnering dat de volgende concept schademelding nog steeds wacht op goedkeuring:')
            ->line('**Claim nummer:** ' . $this->claim->claim_number)
            ->line('**Onderwerp:** ' . $this->claim->subject)
            ->line('**Ingediend op:** ' . $this->claim->created_at->format('d-m-Y H:i'))
            ->line('**Verloopt over:** ' . $daysRemainingText)
            ->line('Als deze claim niet voor ' . $this->claim->draft_expires_at->format('d-m-Y H:i') . ' wordt goedgekeurd, wordt deze automatisch afgewezen.')
            ->action('✓ Claim Goedkeuren', $approveUrl)
            ->line(' ')
            ->line('[✗ Claim Afwijzen](' . $denyUrl . ')')
            ->salutation('Met vriendelijke groet, AutoSchadePlan');
    }

    public function toArray($notifiable)
    {
        return [
            'claim_id' => $this->claim->id,
            'claim_number' => $this->claim->claim_number,
        ];
    }
}
