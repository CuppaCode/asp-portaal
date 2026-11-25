<?php

namespace Tests\Feature\Academy;

use App\Models\Certificate;
use App\Models\Code95Course;
use App\Services\Academy\Code95Calculator;
use App\Services\Academy\CertificateStatusService;
use App\Services\Academy\DriverCardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DriverCardTest extends TestCase
{
    use DatabaseTransactions, AcademyFixtures;

    private function certificate($driver, string $code, ?string $expiry, array $extra = []): Certificate
    {
        $category = $this->category($code);

        return Certificate::create(array_merge([
            'driver_id' => $driver->id,
            'category_id' => $category->id,
            'name' => $category->name,
            'expiry_date' => $expiry,
        ], $extra));
    }

    private function evaluateStatus(?Certificate $certificate, string $code): array
    {
        return app(CertificateStatusService::class)->evaluate($certificate, $this->category($code), Carbon::parse('2026-11-24'));
    }

    public function test_status_colours_follow_the_work_instruction(): void
    {
        $driver = $this->makeDriver($this->makeCompany());

        $this->assertSame('green', $this->evaluateStatus($this->certificate($driver, 'rijbewijs', '2027-12-01'), 'rijbewijs')['status']);
        $this->assertSame('orange', $this->evaluateStatus($this->certificate($driver, 'vca', '2027-03-22'), 'vca')['status'], 'within 6 months');
        $this->assertSame('red', $this->evaluateStatus($this->certificate($driver, 'ehbo', '2027-01-31'), 'ehbo')['status'], 'within 90 days');

        $expired = $this->evaluateStatus($this->certificate($driver, 'bhv', '2026-11-23'), 'bhv');
        $this->assertSame('red', $expired['status']);
        $this->assertSame('verlopen', $expired['label']);
    }

    public function test_missing_required_documents_are_red_and_optional_ones_have_to_be_filled_in(): void
    {
        $this->assertSame('ontbreekt', $this->evaluateStatus(null, 'chauffeurspas')['label']);
        $this->assertSame('red', $this->evaluateStatus(null, 'rijbewijs')['status']);
        $this->assertSame('todo', $this->evaluateStatus(null, 'revi')['status']);
    }

    public function test_not_applicable_and_one_off_documents(): void
    {
        $driver = $this->makeDriver($this->makeCompany());

        $na = $this->certificate($driver, 'heftruck', null, ['not_applicable' => true]);
        $this->assertSame('na', $this->evaluateStatus($na, 'heftruck')['status']);

        $this->assertSame('todo', $this->evaluateStatus(null, 'lichte_berging')['status']);
        $oneOff = $this->certificate($driver, 'zware_berging', null, ['issue_date' => '2020-05-01']);
        $this->assertSame('green', $this->evaluateStatus($oneOff, 'zware_berging')['status']);
    }

    public function test_code_95_hours_shortage_makes_the_document_orange(): void
    {
        $driver = $this->makeDriver($this->makeCompany());
        $certificate = $this->certificate($driver, 'code_95', '2030-12-22');
        $certificate->upsertMetaValue(Code95Calculator::META_OPENING_HOURS, 'number', 7);
        $certificate->upsertMetaValue(Code95Calculator::META_OPENING_PRACTICE, 'number', 0);

        $this->assertSame('orange', $this->evaluateStatus($certificate->fresh(), 'code_95')['status']);
        $this->assertSame('uren tekort', $this->evaluateStatus($certificate->fresh(), 'code_95')['label']);

        $certificate->upsertMetaValue(Code95Calculator::META_OPENING_HOURS, 'number', 35);
        $certificate->upsertMetaValue(Code95Calculator::META_OPENING_PRACTICE, 'number', 7);

        $this->assertSame('green', $this->evaluateStatus($certificate->fresh(), 'code_95')['status']);
    }

    public function test_code_95_needs_35_hours_of_which_7_practice(): void
    {
        $driver = $this->makeDriver($this->makeCompany());
        $certificate = $this->certificate($driver, 'code_95', '2030-12-22');
        $certificate->upsertMetaValue(Code95Calculator::META_OPENING_HOURS, 'number', 28);
        $certificate->upsertMetaValue(Code95Calculator::META_OPENING_PRACTICE, 'number', 0);

        $calculator = app(Code95Calculator::class);
        $progress = $calculator->progress($certificate->fresh(), Carbon::parse('2026-11-24'));

        $this->assertSame(28, $progress['completed']);
        $this->assertSame(7, $progress['remaining']);
        $this->assertSame(7, $progress['practiceRemaining']);
        $this->assertTrue($progress['hasShortage']);

        // Hours that are not in the CBR register yet do not count.
        Code95Course::create([
            'certificate_id' => $certificate->id,
            'course_date' => '2026-11-10',
            'name' => 'Module',
            'code' => 'X1',
            'type' => 'practice',
            'hours' => 7,
            'cbr_registered' => false,
        ]);
        $this->assertTrue($calculator->progress($certificate->fresh())['hasShortage']);

        Code95Course::query()->update(['cbr_registered' => true]);
        $progress = $calculator->progress($certificate->fresh());

        $this->assertSame(35, $progress['completed']);
        $this->assertSame(7, $progress['practiceCompleted']);
        $this->assertFalse($progress['hasShortage']);
        $this->assertSame('2030-12-22', $progress['cycleEnd']->format('Y-m-d'), 'cycle ends on the date of the card');
    }

    public function test_a_new_cycle_starts_with_zero_hours(): void
    {
        $driver = $this->makeDriver($this->makeCompany());
        $certificate = $this->certificate($driver, 'code_95', '2026-12-22');
        $certificate->upsertMetaValue(Code95Calculator::META_OPENING_HOURS, 'number', 35);
        $certificate->upsertMetaValue(Code95Calculator::META_OPENING_PRACTICE, 'number', 7);
        Code95Course::create([
            'certificate_id' => $certificate->id,
            'course_date' => '2026-06-01',
            'name' => 'Oud',
            'code' => 'X0',
            'type' => 'theory',
            'hours' => 7,
        ]);

        $calculator = app(Code95Calculator::class);
        $calculator->startNewCycle($certificate->fresh(), Carbon::parse('2026-12-22'));

        $progress = $calculator->progress($certificate->fresh(), Carbon::parse('2026-12-23'));
        $this->assertSame(0, $progress['completed']);
        $this->assertTrue($progress['hasShortage']);
    }

    private function completeCard($driver, DriverCardService $cards, array $overrides = []): void
    {
        $cards->save($driver, array_merge([
            'first_name' => 'Jan',
            'last_name' => 'Jansen',
            'email' => 'jan@example.com',
            'birthdate' => '1990-05-01',
            'scans_attached' => true,
            'in_service' => true,
            'certificates' => [
                $this->category('rijbewijs')->id => ['expiry_date' => '2030-01-01'],
                $this->category('code_95')->id => ['expiry_date' => '2030-12-22'],
                $this->category('chauffeurspas')->id => ['expiry_date' => '2029-10-22'],
            ],
            'code95_hours' => 35,
            'code95_practice_hours' => 7,
        ], $overrides));
    }

    public function test_the_card_is_incomplete_without_the_required_data(): void
    {
        $driver = $this->makeDriver($this->makeCompany());
        $cards = app(DriverCardService::class);

        $this->assertSame(DriverCardService::STATE_INCOMPLETE, $cards->state($driver));
        $this->assertEqualsCanonicalizing(
            ['geboortedatum', 'rijbewijs', 'Code 95', 'chauffeurspas', 'vinkje "Scans weggelakt"'],
            $cards->missing($driver)
        );

        $this->completeCard($driver, $cards);

        $driver = $driver->fresh();
        $this->assertSame([], $cards->missing($driver));
        $this->assertSame(DriverCardService::STATE_UNCHECKED, $cards->state($driver));
    }

    public function test_four_eyes_check_needs_a_second_person_and_a_complete_card(): void
    {
        $driver = $this->makeDriver($this->makeCompany());
        $cards = app(DriverCardService::class);
        $entry = $this->makeUser('Invoerder');
        $colleague = $this->makeUser('Collega');

        try {
            $cards->check($driver->fresh(), $colleague, $entry);
            $this->fail('An incomplete card cannot be checked.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('checked_by_user_id', $exception->errors());
        }

        $this->completeCard($driver, $cards);

        try {
            $cards->check($driver->fresh(), $entry, $entry);
            $this->fail('The check must be done by a second person.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('checked_by_user_id', $exception->errors());
        }

        $cards->check($driver->fresh(), $colleague, $entry);

        $driver = $driver->fresh();
        $this->assertSame($colleague->id, $driver->checked_by_user_id);
        $this->assertSame(DriverCardService::STATE_CHECKED, $cards->state($driver));
    }

    public function test_changing_a_date_invalidates_the_four_eyes_check(): void
    {
        $driver = $this->makeDriver($this->makeCompany());
        $cards = app(DriverCardService::class);
        $entry = $this->makeUser('Invoerder');
        $colleague = $this->makeUser('Collega');

        $this->completeCard($driver, $cards);
        $cards->check($driver->fresh(), $colleague, $entry);
        $this->assertNotNull($driver->fresh()->checked_at);

        // Saving the same data again keeps the check.
        $this->completeCard($driver->fresh(), $cards);
        $this->assertNotNull($driver->fresh()->checked_at);

        // A changed expiry date invalidates it.
        $this->completeCard($driver->fresh(), $cards, [
            'certificates' => [
                $this->category('rijbewijs')->id => ['expiry_date' => '2031-01-01'],
                $this->category('code_95')->id => ['expiry_date' => '2030-12-22'],
                $this->category('chauffeurspas')->id => ['expiry_date' => '2029-10-22'],
            ],
        ]);

        $driver = $driver->fresh();
        $this->assertNull($driver->checked_at);
        $this->assertNull($driver->checked_by_user_id);
        $this->assertSame(DriverCardService::STATE_UNCHECKED, $cards->state($driver));
    }

    public function test_a_new_certificate_invalidates_the_check_via_the_observer(): void
    {
        $driver = $this->makeDriver($this->makeCompany());
        $cards = app(DriverCardService::class);
        $this->completeCard($driver, $cards);
        $cards->check($driver->fresh(), $this->makeUser('Collega'), $this->makeUser('Invoerder'));

        $certificate = $driver->fresh()->certificates->firstWhere('category_id', $this->category('rijbewijs')->id);
        $certificate->expiry_date = '2032-02-02';
        $certificate->save();

        $this->assertNull($driver->fresh()->checked_at);
    }
}
