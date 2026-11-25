<?php

namespace App\Services\Academy;

use App\Models\Certificate;
use App\Models\CertificateCategory;
use App\Models\Company;
use App\Models\Driver;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Stap 7: per document the expiry date, status and the signals (M11).
 */
class AcademyMonitoringService
{
    public function __construct(
        protected DriverCardService $cards,
        protected Code95Calculator $code95
    ) {
    }

    /**
     * Signals of a certificate: 1st and 2nd signal before the expiry date.
     *
     * @return array<int, array{level: int, date: ?Carbon, applicable: bool, sent: mixed, due: bool}>
     */
    public function signals(?Certificate $certificate, CertificateCategory $category, ?Carbon $today = null): array
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();
        $expiry = $certificate?->expiryCarbon();

        $levels = [
            1 => $category->signal_1_months && $expiry ? $expiry->copy()->subMonthsNoOverflow((int) $category->signal_1_months) : null,
            2 => $category->signal_2_days && $expiry ? $expiry->copy()->subDays((int) $category->signal_2_days) : null,
        ];

        $result = [];

        foreach ($levels as $level => $date) {
            $applicable = $date !== null;

            if ($level === 1 && $applicable && $category->signal_1_only_if_hours_open && $certificate) {
                $applicable = $this->code95->hasShortage($certificate);
            }

            $sent = ($applicable && $certificate)
                ? $certificate->signals->first(fn ($signal) => $signal->level === $level && $signal->expiry_date->isSameDay($expiry))
                : null;

            $result[$level] = [
                'level' => $level,
                'date' => $date,
                'applicable' => $applicable,
                'sent' => $sent,
                'due' => $applicable && !$sent && $date->lte($today),
            ];
        }

        return $result;
    }

    /**
     * One row per driver in service and document type that has a status that matters.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function rows(?Carbon $today = null): Collection
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();

        $companyIds = Company::academy()->pluck('id');

        $drivers = Driver::query()
            ->with('contact', 'company', 'certificates.category', 'certificates.metaFields', 'certificates.code95Courses', 'certificates.signals')
            ->whereIn('company_id', $companyIds)
            ->where('in_service', true)
            ->get();

        $rows = collect();

        foreach ($drivers as $driver) {
            foreach ($this->cards->rows($driver, $today) as $row) {
                $category = $row['category'];
                $status = $row['status'];

                if ($category->one_off || in_array($status['status'], [CertificateStatusService::NA, CertificateStatusService::TODO], true)) {
                    continue;
                }

                $certificate = $row['certificate'];
                $signals = $this->signals($certificate, $category, $today);
                $expired = $status['status'] === CertificateStatusService::RED && ($status['days'] === null || $status['days'] < 0);

                $rows->push([
                    'driver' => $driver,
                    'company' => $driver->company,
                    'category' => $category,
                    'certificate' => $certificate,
                    'status' => $status,
                    'signals' => $signals,
                    'code95' => $row['code95'],
                    'action' => $this->actionText($category, $row),
                    'needs_action' => $status['status'] !== CertificateStatusService::GREEN,
                    'expired' => $expired,
                    'signal_due' => collect($signals)->contains('due', true),
                ]);
            }
        }

        return $rows->sortBy(fn ($row) => ($row['certificate']?->getRawOriginal('expiry_date') ?: '0000-00-00') . '|' . $row['driver']->id)->values();
    }

    protected function actionText(CertificateCategory $category, array $row): string
    {
        $status = $row['status'];

        if ($status['label'] === 'ontbreekt') {
            return match ($category->code) {
                'chauffeurspas' => 'Chauffeur vraagt nieuwe pas aan bij Kiwa',
                default => 'Document opvragen en op de kaart zetten',
            };
        }

        if ($category->isCode95() && ($row['code95']['hasShortage'] ?? false)) {
            return 'Ontbrekende uren plannen (stap 4)';
        }

        return config('academy.monitoring.actions.' . $category->code)
            ?? 'Chauffeur vernieuwt het document';
    }
}
