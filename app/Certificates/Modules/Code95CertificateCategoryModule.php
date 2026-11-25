<?php

namespace App\Certificates\Modules;

use App\Certificates\Contracts\CertificateCategoryModule;
use App\Models\Certificate;
use App\Services\Academy\Code95Calculator;

/**
 * Code 95: 35 hours per 5 years, of which at least 7 hours practice (28 theory).
 *
 * - The end of the cycle is the "geldig tot" date of the certificate (rijbewijs kolom 12).
 * - The hours entered on the card are the hours already achieved according to Mijn CBR (opening balance).
 * - Trainings that are confirmed in the CBR register are added to the opening balance.
 */
class Code95CertificateCategoryModule implements CertificateCategoryModule
{
    public function key(): string
    {
        return 'code_95';
    }

    public function fieldDefinitions(): array
    {
        return [
            [
                'key' => 'code95_cycle_start',
                'label' => 'Begin huidige cyclus',
                'type' => 'date',
                'required' => false,
            ],
            [
                'key' => 'code95_hours_completed',
                'label' => 'Behaalde uren volgens Mijn CBR',
                'type' => 'number',
                'required' => false,
                'min' => 0,
                'step' => 1,
            ],
            [
                'key' => 'code95_hours_practice_completed',
                'label' => 'Waarvan praktijkuren',
                'type' => 'number',
                'required' => false,
                'min' => 0,
                'step' => 1,
            ],
            [
                'key' => 'code95_remaining_note',
                'label' => 'Toelichting resterende uren',
                'type' => 'textarea',
                'required' => false,
            ],
        ];
    }

    public function validationRules(string $context = 'store', ?Certificate $certificate = null): array
    {
        return [
            'module_fields.code95_cycle_start' => 'nullable|date',
            'module_fields.code95_hours_completed' => 'nullable|integer|min:0|max:1000',
            'module_fields.code95_hours_practice_completed' => 'nullable|integer|min:0|max:1000|lte:module_fields.code95_hours_completed',
            'module_fields.code95_remaining_note' => 'nullable|string|max:2000',
        ];
    }

    public function normalizeInput(array $input): array
    {
        $moduleFields = $input['module_fields'] ?? [];

        return [
            'code95_cycle_start' => $this->nullIfEmpty($moduleFields['code95_cycle_start'] ?? null),
            'code95_hours_completed' => $this->nullIfEmpty($moduleFields['code95_hours_completed'] ?? null) ?? 0,
            'code95_hours_practice_completed' => $this->nullIfEmpty($moduleFields['code95_hours_practice_completed'] ?? null) ?? 0,
            'code95_remaining_note' => $this->nullIfEmpty($moduleFields['code95_remaining_note'] ?? null),
        ];
    }

    public function persist(Certificate $certificate, array $normalizedInput): void
    {
        $certificate->upsertMetaValue('code95_cycle_start', 'date', $this->nullIfEmpty($normalizedInput['code95_cycle_start'] ?? null), 'internal');
        $certificate->upsertMetaValue('code95_hours_completed', 'number', $this->nullIfEmpty($normalizedInput['code95_hours_completed'] ?? null), 'internal');
        $certificate->upsertMetaValue('code95_hours_practice_completed', 'number', $this->nullIfEmpty($normalizedInput['code95_hours_practice_completed'] ?? null), 'internal');
        $certificate->upsertMetaValue('code95_remaining_note', 'string', $this->nullIfEmpty($normalizedInput['code95_remaining_note'] ?? null), 'internal');
    }

    public function buildDossierSections(Certificate $certificate): array
    {
        $progress = $this->calculator()->progress($certificate);
        $note = $certificate->getMetaValue('code95_remaining_note');

        $cycleStart = $progress['cycleStart'];
        $cycleEnd = $progress['cycleEnd'];

        return [
            [
                'title' => 'Code 95 voortgang',
                'rows' => [
                    [
                        'label' => 'Cyclus',
                        'value' => ($cycleStart ? $cycleStart->format('d-m-Y') : '-') . ' t/m ' . ($cycleEnd ? $cycleEnd->format('d-m-Y') : '-'),
                    ],
                    [
                        'label' => 'Behaald',
                        'value' => $progress['completed'] . ' van ' . $progress['required'] . ' uur',
                    ],
                    [
                        'label' => 'Praktijk',
                        'value' => $progress['practiceCompleted'] . ' van minimaal ' . $progress['practiceRequired'] . ' uur',
                    ],
                    [
                        'label' => 'Resterend',
                        'value' => $progress['remaining'] . ' uur totaal, waarvan ' . $progress['practiceRemaining'] . ' uur praktijk',
                    ],
                ],
            ],
            [
                'title' => 'Code 95 toelichting',
                'content' => $note ?: 'Geen toelichting vastgelegd.',
            ],
        ];
    }

    public function buildSummary(Certificate $certificate): array
    {
        $progress = $this->calculator()->progress($certificate);

        $required = max(1, $progress['required']);
        $completed = $progress['completed'];
        $practiceRequired = $progress['practiceRequired'];
        $practiceCompleted = $progress['practiceCompleted'];
        $remaining = $progress['remaining'];
        $practiceRemaining = $progress['practiceRemaining'];
        $cycleStart = $progress['cycleStart'];
        $cycleEnd = $progress['cycleEnd'];
        $daysRemaining = $progress['daysRemaining'];
        $completedBounded = min($completed, $required);

        $status = 'Gedeeltelijk afgerond';
        $statusClass = 'is-warning';

        if (!$progress['hasShortage']) {
            $status = 'Volledig afgerond';
            $statusClass = 'is-ok';
        }

        if (!is_null($daysRemaining) && $daysRemaining < 0) {
            $status = 'Verlopen';
            $statusClass = 'is-expired';
        } elseif (!is_null($daysRemaining) && $daysRemaining <= 90) {
            if ($status !== 'Volledig afgerond') {
                $status = 'Bijna verlopen';
            }
            $statusClass = 'is-warning';
        }

        return compact(
            'required',
            'completed',
            'practiceRequired',
            'practiceCompleted',
            'remaining',
            'practiceRemaining',
            'completedBounded',
            'cycleStart',
            'cycleEnd',
            'daysRemaining',
            'status',
            'statusClass'
        );
    }

    public function renewalValidationRules(Certificate $certificate): array
    {
        return [];
    }

    public function notificationTags(Certificate $certificate): array
    {
        $progress = $this->calculator()->progress($certificate);

        return [
            'code95_hours_required' => $progress['required'],
            'code95_hours_completed' => $progress['completed'],
        ];
    }

    private function calculator(): Code95Calculator
    {
        return new Code95Calculator();
    }

    private function nullIfEmpty($value)
    {
        return $value === '' ? null : $value;
    }
}
