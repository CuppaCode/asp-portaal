<?php

namespace App\Services\Academy;

use App\Models\Certificate;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Code 95 hours: 35 hours per 5 years, of which at least 7 hours practice.
 *
 * The end of the cycle is the "geldig tot" date of the Code 95 certificate (rijbewijs kolom 12).
 * Hours already achieved according to Mijn CBR at the moment of registration are stored as an
 * opening balance; trainings registered in the CBR register (cbr_registered) are added to that.
 */
class Code95Calculator
{
    public const META_OPENING_HOURS = 'code95_hours_completed';
    public const META_OPENING_PRACTICE = 'code95_hours_practice_completed';
    public const META_CYCLE_START = 'code95_cycle_start';

    public function required(): int
    {
        return (int) config('academy.code95.hours_required', 35);
    }

    public function practiceRequired(): int
    {
        return (int) config('academy.code95.practice_required', 7);
    }

    public function progress(Certificate $certificate, ?Carbon $today = null): array
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();

        $required = $this->required();
        $practiceRequired = $this->practiceRequired();

        $openingHours = max(0, (int) ($certificate->getMetaValue(self::META_OPENING_HOURS, 0) ?? 0));
        $openingPractice = max(0, (int) ($certificate->getMetaValue(self::META_OPENING_PRACTICE, 0) ?? 0));

        $cycleStart = $this->toCarbon($certificate->getMetaValue(self::META_CYCLE_START));
        $courses = $this->registeredCourses($certificate, $cycleStart);

        $completed = $openingHours + (int) $courses->sum('hours');
        $practiceCompleted = $openingPractice + (int) $courses->where('type', 'practice')->sum('hours');

        $cycleEnd = $this->cycleEnd($certificate);
        $daysRemaining = $cycleEnd ? (int) $today->diffInDays($cycleEnd, false) : null;

        $remaining = max(0, $required - $completed);
        $practiceRemaining = max(0, $practiceRequired - $practiceCompleted);

        return [
            'required' => $required,
            'completed' => $completed,
            'remaining' => $remaining,
            'practiceRequired' => $practiceRequired,
            'practiceCompleted' => $practiceCompleted,
            'practiceRemaining' => $practiceRemaining,
            'openingHours' => $openingHours,
            'openingPractice' => $openingPractice,
            'cycleStart' => $cycleStart,
            'cycleEnd' => $cycleEnd,
            'daysRemaining' => $daysRemaining,
            'hasShortage' => $remaining > 0 || $practiceRemaining > 0,
        ];
    }

    public function hasShortage(Certificate $certificate): bool
    {
        return $this->progress($certificate)['hasShortage'];
    }

    /**
     * Start a new 5 year cycle after a renewal: the previous hours belong to the previous cycle.
     */
    public function startNewCycle(Certificate $certificate, ?Carbon $from = null): void
    {
        $from = ($from ?? Carbon::today())->copy()->startOfDay();

        $certificate->upsertMetaValue(self::META_CYCLE_START, 'date', $from->format('Y-m-d'), 'internal');
        $certificate->upsertMetaValue(self::META_OPENING_HOURS, 'number', 0, 'internal');
        $certificate->upsertMetaValue(self::META_OPENING_PRACTICE, 'number', 0, 'internal');
    }

    public function cycleEnd(Certificate $certificate): ?Carbon
    {
        try {
            $expiry = $certificate->expiryCarbon();
        } catch (\Throwable $exception) {
            $expiry = null;
        }

        return $expiry ?? $this->toCarbon($certificate->getMetaValue('code95_cycle_end'));
    }

    /**
     * Courses that count: confirmed in the CBR register and inside the current cycle.
     */
    protected function registeredCourses(Certificate $certificate, ?Carbon $cycleStart): Collection
    {
        try {
            $courses = $certificate->relationLoaded('code95Courses')
                ? $certificate->code95Courses
                : $certificate->code95Courses()->get();
        } catch (\Throwable $exception) {
            return collect();
        }

        return $courses
            ->filter(fn ($course) => (bool) $course->cbr_registered)
            ->filter(fn ($course) => !$cycleStart || $course->course_date === null || Carbon::parse($course->course_date)->gte($cycleStart))
            ->values();
    }

    protected function toCarbon($value): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        return Carbon::parse($value)->startOfDay();
    }
}
