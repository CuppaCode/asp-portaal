<?php

namespace App\Observers;

use App\Models\Certificate;
use App\Models\Driver;
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
     * Handle the Certificate "saved" event (created or updated).
     *
     * - A changed date makes the four-eyes check of the driver card invalid.
     * - The end of the Code 95 cycle follows the "geldig tot" date of the certificate.
     */
    public function saved(Certificate $certificate): void
    {
        $datesChanged = $certificate->wasRecentlyCreated
            || $certificate->wasChanged(['expiry_date', 'issue_date', 'not_applicable']);

        if ($datesChanged && $certificate->driver_id) {
            Driver::withTrashed()
                ->whereKey($certificate->driver_id)
                ->whereNotNull('checked_at')
                ->update(['checked_by_user_id' => null, 'checked_at' => null]);
        }

        if ($certificate->wasChanged('expiry_date') && $certificate->category?->isCode95()) {
            $expiry = $certificate->expiryCarbon();

            $certificate->upsertMetaValue(
                'code95_cycle_end',
                'date',
                $expiry ? $expiry->format('Y-m-d') : null,
                'internal'
            );
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
