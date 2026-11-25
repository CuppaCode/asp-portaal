<?php

namespace App\Certificates\Modules;

use App\Certificates\Contracts\CertificateCategoryModule;
use App\Models\Certificate;

class DefaultCertificateCategoryModule implements CertificateCategoryModule
{
    public function key(): string
    {
        return 'default';
    }

    public function fieldDefinitions(): array
    {
        return [];
    }

    public function validationRules(string $context = 'store', ?Certificate $certificate = null): array
    {
        return [];
    }

    public function normalizeInput(array $input): array
    {
        return [];
    }

    public function persist(Certificate $certificate, array $normalizedInput): void
    {
        // Default module has no extra persistence logic.
    }

    public function buildDossierSections(Certificate $certificate): array
    {
        return [];
    }

    public function renewalValidationRules(Certificate $certificate): array
    {
        return [];
    }

    public function notificationTags(Certificate $certificate): array
    {
        return [];
    }
}