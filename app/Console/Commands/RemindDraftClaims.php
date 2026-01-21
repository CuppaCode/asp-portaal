<?php

namespace App\Console\Commands;

use App\Models\Claim;
use App\Notifications\DraftClaimReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class RemindDraftClaims extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'claims:remind-drafts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications for pending draft claims';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $claims = Claim::where('status', 'draft')
            ->with('company.claimFormNotifications')
            ->get();

        $count = 0;
        foreach ($claims as $claim) {
            $company = $claim->company;
            
            // Get reminder settings
            $reminderDays = $company->draft_reminder_days ?? 7;
            $reminderFrequency = $company->draft_reminder_frequency_days ?? 7;
            
            // Calculate when first reminder should be sent
            $firstReminderDate = $claim->created_at->copy()->addDays($reminderDays);
            
            // Check if we should send a reminder
            $shouldSendReminder = false;
            
            if (now()->gte($firstReminderDate)) {
                if (is_null($claim->last_reminder_sent_at)) {
                    // First reminder
                    $shouldSendReminder = true;
                } else {
                    // Subsequent reminders based on frequency
                    $nextReminderDate = $claim->last_reminder_sent_at->copy()->addDays($reminderFrequency);
                    if (now()->gte($nextReminderDate)) {
                        $shouldSendReminder = true;
                    }
                }
            }
            
            if ($shouldSendReminder) {
                $recipients = $company->claimFormNotifications;
                
                if ($recipients->isNotEmpty()) {
                    foreach ($recipients as $recipient) {
                        Notification::route('mail', $recipient->email)
                            ->notify(new DraftClaimReminder($claim));
                    }
                    
                    $claim->update(['last_reminder_sent_at' => now()]);
                    $count++;
                }
            }
        }

        $this->info("Sent {$count} reminder notifications.");

        return 0;
    }
}
