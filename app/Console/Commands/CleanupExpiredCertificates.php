<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Certificate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanupExpiredCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificates:cleanup-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup certificates expired for more than 1 year';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up expired certificates...');

        $oneYearAgo = Carbon::now()->subYear();
        
        // Find certificates expired more than 1 year ago
        $certificates = Certificate::where('expiry_date', '<', $oneYearAgo)
            ->whereNull('deleted_at')
            ->get();

        $count = $certificates->count();

        if ($count === 0) {
            $this->info('No certificates to cleanup.');
            return Command::SUCCESS;
        }

        $this->info("Found {$count} certificates to cleanup...");

        foreach ($certificates as $certificate) {
            try {
                $certificate->delete(); // Soft delete
                $this->info("Deleted certificate ID {$certificate->id} ({$certificate->name})");
            } catch (\Exception $e) {
                $this->error("Failed to delete certificate ID {$certificate->id}: {$e->getMessage()}");
                Log::error('Certificate cleanup failed', [
                    'certificate_id' => $certificate->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info("Cleaned up {$count} expired certificates");
        $this->info("Successfully cleaned up {$count} certificates.");

        return Command::SUCCESS;
    }
}

