<?php

namespace App\Services\Academy;

use App\Models\Certificate;

/**
 * Follow-up of a renewed certificate (new expiry date), whoever renewed it.
 */
class CertificateRenewalHandler
{
    public function __construct(
        protected Code95Calculator $code95,
        protected AcademyChecklistService $checklist
    ) {
    }

    public function afterRenewal(Certificate $certificate): void
    {
        $certificate->loadMissing('category');

        if (!$certificate->category?->isCode95()) {
            return;
        }

        // The hours of the previous cycle no longer count.
        $this->code95->startNewCycle($certificate);

        // Stap 7: send M12 and the yearly planning.
        $this->checklist->createRenewalItem($certificate);
    }
}
