<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Certificate;
use App\Services\MailTriggerService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CheckExpiringCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificates:check-expiring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expiring certificates and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expiring certificates...');

        $today = Carbon::today();
        $processedCount = 0;
        $errorCount = 0;
        $maxPerRun = 100; // Rate limiting

        // Query certificates that need notification
        $certificates = Certificate::whereHas('category', function($q) {
                $q->where('enable_notifications', true);
            })
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', $today)
            ->whereNull('original_expiry_date') // Not yet renewed
            ->where(function($q) use ($today) {
                // First notification OR time for reminder
                $q->whereNull('last_notification_sent_at')
                  ->orWhereRaw('DATE_ADD(last_notification_sent_at, INTERVAL ? DAY) <= ?', [7, $today]);
            })
            ->with(['category', 'driver.company'])
            ->take($maxPerRun)
            ->get();

        foreach ($certificates as $certificate) {
            try {
                // Check if we're within the notification window
                if (!$certificate->notify_date || Carbon::parse($certificate->notify_date)->isFuture()) {
                    continue;
                }

                // Check reminder frequency
                if ($certificate->last_notification_sent_at) {
                    $daysSinceLastNotification = Carbon::parse($certificate->last_notification_sent_at)->diffInDays($today);
                    $reminderFrequency = $certificate->category->reminder_frequency_days ?? 7;
                    
                    if ($daysSinceLastNotification < $reminderFrequency) {
                        continue;
                    }
                }

                // Generate renewal token
                $certificate->renewal_token = Str::random(64);
                $certificate->renewal_token_expires_at = Carbon::now()->addDays(30);
                $certificate->save();

                // Build recipients list
                $recipients = [];
                
                // Add driver email
                if ($certificate->driver && $certificate->driver->email) {
                    $recipients[] = $certificate->driver->email;
                }
                
                // Add category admin emails
                if ($certificate->category->notification_recipients) {
                    $recipients = array_merge($recipients, $certificate->category->notification_recipients);
                }

                // Remove duplicates
                $recipients = array_unique($recipients);

                if (empty($recipients)) {
                    $this->warn("No recipients for certificate ID {$certificate->id}, skipping...");
                    continue;
                }

                // Dispatch notification
                $mailService = new MailTriggerService();
                $mailService->dispatch('CERTIFICATE_EXPIRING', $certificate, [
                    'recipients' => $recipients
                ]);

                // Update last notification timestamp
                $certificate->last_notification_sent_at = Carbon::now();
                $certificate->save();

                $processedCount++;
                $this->info("Notification sent for certificate ID {$certificate->id} ({$certificate->name})");

            } catch (\Exception $e) {
                $errorCount++;
                
                // Log error
                Log::error('Certificate notification failed', [
                    'certificate_id' => $certificate->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                // Notify super admin
                try {
                    $errorData = new \stdClass();
                    $errorData->error_message = $e->getMessage();
                    $errorData->id = $certificate->id;
                    $errorData->name = $certificate->name;
                    
                    $mailService = new MailTriggerService();
                    $mailService->dispatch('CERTIFICATE_NOTIFICATION_ERROR', $errorData, [
                        'recipients' => ['jeffrey@cuppacode.nl']
                    ]);
                } catch (\Exception $notifyError) {
                    Log::error('Failed to notify super admin about certificate error', [
                        'original_error' => $e->getMessage(),
                        'notification_error' => $notifyError->getMessage()
                    ]);
                }

                $this->error("Failed to send notification for certificate ID {$certificate->id}: {$e->getMessage()}");
            }
        }

        $this->info("Processed {$processedCount} certificates, {$errorCount} errors");
        
        return Command::SUCCESS;
    }
}

