<?php

namespace App\Observers;

use App\Models\Certificate;
use Carbon\Carbon;

class CertificateObserver
{
    /**
     * Handle the Certificate "saving" event.
     * Automatically calculate notify_date based on category settings
     */
    public function saving(Certificate $certificate): void
    {
        // Only auto-calculate if we have necessary data
        if ($certificate->expiry_date && $certificate->category_id) {
            $category = $certificate->category;
            
            if ($category && $category->notify_days_before) {
                $expiryDate = Carbon::parse($certificate->expiry_date);
                $certificate->notify_date = $expiryDate->copy()->subDays($category->notify_days_before)->format(config('panel.date_format'));
            }
        }
    }

    /**
     * Handle the Certificate "created" event.
     */
    public function created(Certificate $certificate): void
    {
        //
    }

    /**
     * Handle the Certificate "updated" event.
     */
    public function updated(Certificate $certificate): void
    {
        //
    }

    /**
     * Handle the Certificate "deleted" event.
     */
    public function deleted(Certificate $certificate): void
    {
        //
    }

    /**
     * Handle the Certificate "restored" event.
     */
    public function restored(Certificate $certificate): void
    {
        //
    }

    /**
     * Handle the Certificate "force deleted" event.
     */
    public function forceDeleted(Certificate $certificate): void
    {
        //
    }
}
