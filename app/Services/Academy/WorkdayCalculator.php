<?php

namespace App\Services\Academy;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class WorkdayCalculator
{
    /** @var array<int, array<string, bool>> */
    protected array $holidayCache = [];

    protected bool $skipHolidays;

    public function __construct(?bool $skipHolidays = null)
    {
        $this->skipHolidays = $skipHolidays ?? (bool) config('academy.workdays.skip_holidays', true);
    }

    public function isWorkday(CarbonInterface $date): bool
    {
        if ($date->isWeekend()) {
            return false;
        }

        return !($this->skipHolidays && $this->isHoliday($date));
    }

    /**
     * Add (or subtract with a negative number) working days. Zero days returns the same date.
     */
    public function addWorkdays(CarbonInterface $date, int $days): Carbon
    {
        $result = Carbon::instance($date)->startOfDay();
        $step = $days >= 0 ? 1 : -1;
        $remaining = abs($days);

        while ($remaining > 0) {
            $result->addDays($step);

            if ($this->isWorkday($result)) {
                $remaining--;
            }
        }

        return $result;
    }

    public function firstWorkdayOfMonth(CarbonInterface $date): Carbon
    {
        $result = Carbon::instance($date)->startOfMonth()->startOfDay();

        while (!$this->isWorkday($result)) {
            $result->addDay();
        }

        return $result;
    }

    public function isFirstWorkdayOfMonth(CarbonInterface $date): bool
    {
        return $this->firstWorkdayOfMonth($date)->isSameDay($date);
    }

    public function isHoliday(CarbonInterface $date): bool
    {
        return isset($this->holidays($date->year)[$date->format('Y-m-d')]);
    }

    /**
     * Dutch national holidays (Goede Vrijdag and Bevrijdingsdag are not counted as holidays).
     *
     * @return array<string, bool>
     */
    public function holidays(int $year): array
    {
        if (isset($this->holidayCache[$year])) {
            return $this->holidayCache[$year];
        }

        $easter = $this->easter($year);

        $dates = [
            Carbon::create($year, 1, 1),
            $easter->copy(),
            $easter->copy()->addDay(),
            $this->kingsDay($year),
            $easter->copy()->addDays(39),
            $easter->copy()->addDays(49),
            $easter->copy()->addDays(50),
            Carbon::create($year, 12, 25),
            Carbon::create($year, 12, 26),
        ];

        $holidays = [];
        foreach ($dates as $date) {
            $holidays[$date->format('Y-m-d')] = true;
        }

        return $this->holidayCache[$year] = $holidays;
    }

    /**
     * Koningsdag: 27 April, or 26 April when the 27th is a Sunday.
     */
    protected function kingsDay(int $year): Carbon
    {
        $date = Carbon::create($year, 4, 27);

        return $date->isSunday() ? $date->subDay() : $date;
    }

    /**
     * Easter Sunday (Gregorian calendar, anonymous algorithm).
     */
    protected function easter(int $year): Carbon
    {
        $a = $year % 19;
        $b = intdiv($year, 100);
        $c = $year % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $month = intdiv($h + $l - 7 * $m + 114, 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;

        return Carbon::create($year, $month, $day)->startOfDay();
    }
}
