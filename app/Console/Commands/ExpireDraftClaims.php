<?php

namespace App\Console\Commands;

use App\Models\Claim;
use Illuminate\Console\Command;

class ExpireDraftClaims extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'claims:expire-drafts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire draft claims that have passed their expiry date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredClaims = Claim::where('status', 'draft')
            ->where('draft_expires_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expiredClaims as $claim) {
            $expiryDays = $claim->company->draft_expiry_days ?? 30;
            
            $claim->update([
                'status' => 'draft_denied',
                'denied_reason' => "Automatisch verlopen na {$expiryDays} dagen zonder goedkeuring.",
            ]);
            
            $count++;
        }

        $this->info("Expired {$count} draft claims.");

        return 0;
    }
}
