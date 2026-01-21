<?php

namespace App\Notifications;

use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class DraftClaimNotification extends Notification
{
    use Queueable;

    protected $claim;
    protected $summary;

    public function __construct(Claim $claim, array $summary = [])
    {
        $this->claim = $claim;
        $this->summary = $summary;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $approveUrl = URL::signedRoute('draft-claim.approve', ['claim' => $this->claim->id]);
        $denyUrl = URL::signedRoute('draft-claim.deny-form', ['claim' => $this->claim->id]);

        $message = (new MailMessage)
            ->subject('Nieuwe concept schademelding: ' . $this->claim->claim_number)
            ->greeting('Beste ' . $this->claim->company->name . ' medewerker,')
            ->line('Er is een nieuwe concept schademelding ingediend via het online formulier.')
            ->line('**Claim nummer:** ' . $this->claim->claim_number);

        if (!empty($this->summary)) {
            $message->line('### Samenvatting van de melding:');
            
            foreach ($this->summary as $label => $value) {
                $message->line('**' . $label . ':** ' . $value);
            }
        }

        if ($this->claim->draft_expires_at) {
            $message->line('Deze concept claim vervalt op: **' . $this->claim->draft_expires_at->format('d-m-Y H:i') . '**');
        }
        
        $message->line('U kunt de claim goedkeuren of afwijzen via onderstaande knoppen:')
            ->action('✓ Claim Goedkeuren', $approveUrl)
            ->line(' ')
            ->line('[✗ Claim Afwijzen](' . $denyUrl . ')')
            ->line('Na goedkeuring wordt de claim omgezet naar een actieve schademelding.')
            ->salutation('Met vriendelijke groet, AutoSchadePlan');

        return $message;
    }

    public function toArray($notifiable)
    {
        return [
            'claim_id' => $this->claim->id,
            'claim_number' => $this->claim->claim_number,
        ];
    }
}
