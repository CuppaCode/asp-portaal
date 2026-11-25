<?php

namespace Tests\Unit\Academy;

use App\Services\Academy\WorkdayCalculator;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class WorkdayCalculatorTest extends TestCase
{
    private function calculator(bool $skipHolidays = true): WorkdayCalculator
    {
        return new WorkdayCalculator($skipHolidays);
    }

    public function test_it_counts_working_days_like_the_work_instruction_examples(): void
    {
        $calculator = $this->calculator();

        // Employer start 02-10-2026: names list within 5 working days.
        $this->assertSame('2026-10-09', $calculator->addWorkdays(Carbon::parse('2026-10-02'), 5)->format('Y-m-d'));
        // Names list received on Thursday 08-10: data request within 2 working days.
        $this->assertSame('2026-10-12', $calculator->addWorkdays(Carbon::parse('2026-10-08'), 2)->format('Y-m-d'));
        // Data request on 09-10: reminders after 5, 10 and 15 working days.
        $this->assertSame('2026-10-16', $calculator->addWorkdays(Carbon::parse('2026-10-09'), 5)->format('Y-m-d'));
        $this->assertSame('2026-10-23', $calculator->addWorkdays(Carbon::parse('2026-10-09'), 10)->format('Y-m-d'));
        $this->assertSame('2026-10-30', $calculator->addWorkdays(Carbon::parse('2026-10-09'), 15)->format('Y-m-d'));
    }

    public function test_it_counts_backwards_from_the_training_day(): void
    {
        $calculator = $this->calculator();
        $training = Carbon::parse('2026-11-17');

        $this->assertSame('2026-11-03', $calculator->addWorkdays($training, -10)->format('Y-m-d'));
        $this->assertSame('2026-11-13', $calculator->addWorkdays($training, -2)->format('Y-m-d'));
        $this->assertSame('2026-11-16', $calculator->addWorkdays($training, -1)->format('Y-m-d'));
    }

    public function test_zero_days_keeps_the_date(): void
    {
        $this->assertSame('2026-10-08', $this->calculator()->addWorkdays(Carbon::parse('2026-10-08'), 0)->format('Y-m-d'));
    }

    public function test_it_finds_the_first_working_day_of_the_month(): void
    {
        $calculator = $this->calculator();

        // 1 November 2026 is a Sunday.
        $this->assertSame('2026-11-02', $calculator->firstWorkdayOfMonth(Carbon::parse('2026-11-15'))->format('Y-m-d'));
        $this->assertTrue($calculator->isFirstWorkdayOfMonth(Carbon::parse('2026-11-02')));
        $this->assertFalse($calculator->isFirstWorkdayOfMonth(Carbon::parse('2026-11-03')));
        // 1 January is a holiday, 2 January 2026 is a Friday.
        $this->assertSame('2026-01-02', $calculator->firstWorkdayOfMonth(Carbon::parse('2026-01-20'))->format('Y-m-d'));
    }

    public function test_it_knows_dutch_holidays(): void
    {
        $calculator = $this->calculator();

        foreach (['2026-04-06', '2026-04-27', '2026-05-14', '2026-05-25', '2026-12-25', '2026-12-26'] as $holiday) {
            $this->assertFalse($calculator->isWorkday(Carbon::parse($holiday)), $holiday . ' is a holiday');
        }

        $this->assertTrue($calculator->isWorkday(Carbon::parse('2026-04-07')));
        $this->assertTrue($this->calculator(false)->isWorkday(Carbon::parse('2026-04-06')));
    }

    public function test_holidays_are_skipped_when_adding_working_days(): void
    {
        // Friday 3 April + 1 working day skips the weekend and Easter Monday (6 April).
        $this->assertSame('2026-04-07', $this->calculator()->addWorkdays(Carbon::parse('2026-04-03'), 1)->format('Y-m-d'));
    }
}
