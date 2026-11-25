<?php

namespace Tests\Unit;

use App\Certificates\Modules\Code95CertificateCategoryModule;
use App\Models\Certificate;
use Carbon\Carbon;
use Tests\TestCase;

class Code95CertificateCategoryModuleTest extends TestCase
{
    public function test_it_calculates_partial_progress(): void
    {
        $certificate = \Mockery::mock(Certificate::class);
        $values = [
            'code95_hours_required' => 35,
            'code95_hours_completed' => 28,
            'code95_hours_practice_required' => 7,
            'code95_hours_practice_completed' => 7,
            'code95_cycle_start' => Carbon::parse('2021-11-08'),
            'code95_cycle_end' => Carbon::parse('2026-11-30'),
        ];

        $certificate->shouldReceive('getMetaValue')->andReturnUsing(
            fn (string $key, $default = null) => $values[$key] ?? $default
        );

        $summary = (new Code95CertificateCategoryModule())->buildSummary($certificate);

        $this->assertSame(35, $summary['required']);
        $this->assertSame(28, $summary['completed']);
        $this->assertSame(7, $summary['remaining']);
        $this->assertSame(7, $summary['practiceCompleted']);
        $this->assertSame(0, $summary['practiceRemaining']);
        $this->assertSame('Bijna verlopen', $summary['status']);
    }

    public function test_it_marks_cycle_expired_independently_from_completed_hours(): void
    {
        $certificate = \Mockery::mock(Certificate::class);
        $values = [
            'code95_hours_required' => 35,
            'code95_hours_completed' => 35,
            'code95_hours_practice_required' => 7,
            'code95_hours_practice_completed' => 7,
            'code95_cycle_end' => Carbon::parse('2020-11-08'),
        ];

        $certificate->shouldReceive('getMetaValue')->andReturnUsing(
            fn (string $key, $default = null) => $values[$key] ?? $default
        );

        $summary = (new Code95CertificateCategoryModule())->buildSummary($certificate);

        $this->assertSame('Verlopen', $summary['status']);
        $this->assertSame('is-expired', $summary['statusClass']);
        $this->assertLessThan(0, $summary['daysRemaining']);
    }
}
