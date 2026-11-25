<?php

namespace App\Certificates\Contracts;

use App\Models\Certificate;

interface CertificateCategoryModule
{
    public function key(): string;

    public function fieldDefinitions(): array;

    public function validationRules(string $context = 'store', ?Certificate $certificate = null): array;

    public function normalizeInput(array $input): array;

    public function persist(Certificate $certificate, array $normalizedInput): void;

    public function buildDossierSections(Certificate $certificate): array;

    public function renewalValidationRules(Certificate $certificate): array;

    public function notificationTags(Certificate $certificate): array;
}