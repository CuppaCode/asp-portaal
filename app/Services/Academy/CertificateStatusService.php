<?php

namespace App\Services\Academy;

use App\Models\Certificate;
use App\Models\CertificateCategory;
use Carbon\Carbon;

/**
 * Status of a certificate on the Academy driver card.
 *
 * green   valid longer than 6 months
 * orange  within 6 months, or Code 95 hours short
 * red     within 90 days, expired or missing
 * na      not applicable
 * todo    not filled in yet ("nog invullen")
 */
class CertificateStatusService
{
    public const GREEN = 'green';
    public const ORANGE = 'orange';
    public const RED = 'red';
    public const NA = 'na';
    public const TODO = 'todo';

    public const LABELS = [
        self::GREEN => 'groen',
        self::ORANGE => 'oranje',
        self::RED => 'rood',
        self::NA => 'n.v.t.',
        self::TODO => 'nog invullen',
    ];

    public function __construct(protected Code95Calculator $code95)
    {
    }

    /**
     * @param Certificate|null $certificate The certificate, or null when there is none for the category.
     */
    public function evaluate(?Certificate $certificate, CertificateCategory $category, ?Carbon $today = null): array
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();

        if ($certificate && $certificate->not_applicable) {
            return $this->result(self::NA, 'n.v.t.');
        }

        if ($category->one_off) {
            return $this->evaluateOneOff($certificate);
        }

        $expiry = $certificate?->expiryCarbon();

        if (!$expiry) {
            // Documents that are always needed are missing (red); optional ones still have to be filled in.
            return $category->allow_not_applicable
                ? $this->result(self::TODO, 'nog invullen')
                : $this->result(self::RED, 'ontbreekt');
        }

        $days = (int) $today->diffInDays($expiry, false);

        if ($days < 0) {
            return $this->result(self::RED, 'verlopen', $days, $expiry);
        }

        if ($days <= (int) config('academy.status.red_days', 90)) {
            return $this->result(self::RED, 'nog ' . $days . ' dagen', $days, $expiry);
        }

        if ($category->isCode95() && $certificate && $this->code95->hasShortage($certificate)) {
            return $this->result(self::ORANGE, 'uren tekort', $days, $expiry);
        }

        $greenLimit = $today->copy()->addMonths((int) config('academy.status.green_months', 6));
        if ($expiry->lte($greenLimit)) {
            return $this->result(self::ORANGE, 'binnen 6 mnd', $days, $expiry);
        }

        return $this->result(self::GREEN, 'geldig', $days, $expiry);
    }

    /**
     * One-off documents (lichte en zware berging) have a date of achievement and never expire.
     */
    protected function evaluateOneOff(?Certificate $certificate): array
    {
        $issued = $certificate?->getRawOriginal('issue_date');

        if (empty($issued)) {
            return $this->result(self::TODO, 'nog invullen');
        }

        return $this->result(self::GREEN, 'eenmalig behaald');
    }

    protected function result(string $status, string $label, ?int $days = null, ?Carbon $expiry = null): array
    {
        return [
            'status' => $status,
            'label' => $label,
            'days' => $days,
            'expiry' => $expiry,
        ];
    }

    /**
     * Whether the status needs attention from the Academy team.
     */
    public function needsAction(string $status): bool
    {
        return in_array($status, [self::RED, self::ORANGE], true);
    }
}
