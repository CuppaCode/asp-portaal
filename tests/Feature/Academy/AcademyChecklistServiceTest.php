<?php

namespace Tests\Feature\Academy;

use App\Models\AcademyChecklistItem;
use App\Models\AcademyTraining;
use App\Services\Academy\AcademyChecklistService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AcademyChecklistServiceTest extends TestCase
{
    use DatabaseTransactions, AcademyFixtures;

    private function checklist(): AcademyChecklistService
    {
        return app(AcademyChecklistService::class);
    }

    private function item($subject, string $key, string $period = ''): AcademyChecklistItem
    {
        return AcademyChecklistItem::where('subject_type', get_class($subject))
            ->where('subject_id', $subject->id)
            ->where('item_key', $key)
            ->where('period', $period)
            ->firstOrFail();
    }

    public function test_company_deadlines_follow_the_anchors_of_the_work_instruction(): void
    {
        $company = $this->makeCompany();
        $user = $this->makeUser();
        $service = $this->checklist();

        $service->refresh($company, Carbon::parse('2026-10-02'));

        $this->assertSame('2026-10-02', $this->item($company, 'startmail')->due_date->format('Y-m-d'));
        $this->assertSame('2026-10-09', $this->item($company, 'namenlijst')->due_date->format('Y-m-d'));
        $this->assertNull($this->item($company, 'gegevensverzoek')->due_date, 'no anchor until the names list is received');

        $this->item($company, 'verwerkersovereenkomst')->update(['done_at' => '2026-10-02 10:00:00']);
        $this->item($company, 'startmail')->update(['done_at' => '2026-10-02 10:00:00']);
        $this->item($company, 'namenlijst')->update(['done_at' => '2026-10-08 09:00:00']);
        $service->refresh($company, Carbon::parse('2026-10-08'));

        $this->assertSame('2026-10-12', $this->item($company, 'gegevensverzoek')->due_date->format('Y-m-d'));

        $this->item($company, 'gegevensverzoek')->update(['done_at' => '2026-10-09 09:00:00']);
        $service->refresh($company, Carbon::parse('2026-10-09'));

        $this->assertSame('2026-10-16', $this->item($company, 'herinnering_1')->due_date->format('Y-m-d'));
        $this->assertSame('2026-10-23', $this->item($company, 'herinnering_2')->due_date->format('Y-m-d'));
        $this->assertSame('2026-10-30', $this->item($company, 'namen_gemeld')->due_date->format('Y-m-d'));
    }

    public function test_step_two_waits_until_step_one_is_done(): void
    {
        $company = $this->makeCompany();
        $service = $this->checklist();
        $items = $service->refresh($company);

        $this->assertSame(AcademyChecklistService::STATE_OPEN, $service->stepState($company, 1, $items));
        $this->assertSame(AcademyChecklistService::STATE_LOCKED, $service->stepState($company, 2, $items));
        $this->assertSame(AcademyChecklistService::STATE_LOCKED, $service->stepState($company, 3, $items));

        $this->assertTrue($service->isLocked($this->item($company, 'verwerkersovereenkomst')) === false);
    }

    public function test_items_that_must_always_happen_cannot_be_marked_not_applicable(): void
    {
        $company = $this->makeCompany();
        $user = $this->makeUser();
        $this->checklist()->refresh($company);

        $this->expectException(ValidationException::class);
        $this->checklist()->markNotApplicable($this->item($company, 'namenlijst'), $user);
    }

    public function test_marking_done_and_undo(): void
    {
        $company = $this->makeCompany();
        $user = $this->makeUser();
        $this->checklist()->refresh($company);

        $item = $this->checklist()->markDone($this->item($company, 'namenlijst'), $user);
        $this->assertNotNull($item->done_at);
        $this->assertSame($user->id, $item->done_by_user_id);

        $item = $this->checklist()->undo($item);
        $this->assertNull($item->done_at);
        $this->assertNull($item->done_by_user_id);
    }

    public function test_m2b_is_not_applicable_when_every_driver_has_an_email(): void
    {
        $company = $this->makeCompany();
        $this->makeDriver($company, ['email' => 'jan@example.com']);

        $this->checklist()->refresh($company);
        $item = $this->item($company, 'chauffeurs_zonder_mail');
        $this->assertTrue($item->not_applicable);
        $this->assertStringStartsWith(AcademyChecklistService::AUTO_NA_PREFIX, $item->not_applicable_reason);

        // A driver without e-mail: the item is needed again.
        $this->makeDriver($company, ['first_name' => 'Piet', 'email' => '']);
        $this->checklist()->refresh($company);
        $this->assertFalse($this->item($company, 'chauffeurs_zonder_mail')->fresh()->not_applicable);
    }

    public function test_training_deadlines_count_back_from_the_training_day(): void
    {
        $company = $this->makeCompany();
        $training = AcademyTraining::create([
            'company_id' => $company->id,
            'name' => 'Code 95 module',
            'training_date' => '2026-11-17',
            'provider_type' => 'asp',
            'is_prevention' => true,
        ]);

        $this->checklist()->refresh($training);

        $this->assertSame('2026-10-27', $this->item($training, 'deelnemerslijst')->due_date->format('Y-m-d'));
        $this->assertSame('2026-10-27', $this->item($training, 'schadepreventie_f2')->due_date->format('Y-m-d'));
        $this->assertSame('2026-11-03', $this->item($training, 'f2_retour')->due_date->format('Y-m-d'));
        $this->assertSame('2026-11-10', $this->item($training, 'bevestiging_chauffeurs')->due_date->format('Y-m-d'));
        $this->assertSame('2026-11-13', $this->item($training, 'wijzigingen_verwerkt')->due_date->format('Y-m-d'));
        $this->assertSame('2026-11-16', $this->item($training, 'definitieve_namen')->due_date->format('Y-m-d'));
    }

    public function test_schadepreventie_items_are_not_applicable_for_other_trainings(): void
    {
        $company = $this->makeCompany();
        $training = AcademyTraining::create([
            'company_id' => $company->id,
            'name' => 'Rijvaardigheid',
            'training_date' => '2026-11-17',
            'provider_type' => 'asp',
            'is_prevention' => false,
        ]);

        $this->checklist()->refresh($training);

        $this->assertTrue($this->item($training, 'schadepreventie_f2')->not_applicable);
        $this->assertTrue($this->item($training, 'f2_retour')->not_applicable);
        $this->assertTrue($this->item($training, 'cbr_uren')->not_applicable, 'no Code 95 hours: no CBR step');
    }

    public function test_step_five_opens_after_the_written_approval(): void
    {
        $company = $this->makeCompany();
        $user = $this->makeUser();
        $training = AcademyTraining::create([
            'company_id' => $company->id,
            'name' => 'Rijvaardigheid',
            'training_date' => Carbon::today()->addWeeks(4)->format('Y-m-d'),
            'provider_type' => 'asp',
        ]);

        $service = $this->checklist();
        $items = $service->refresh($training);

        $this->assertSame(AcademyChecklistService::STATE_LOCKED, $service->stepState($training, 5, $items));
        $this->assertSame(AcademyChecklistService::STATE_LOCKED, $service->stepState($training, 6, $items));

        try {
            $service->markDone($this->item($training, 'opdrachtbevestiging'), $user);
            $this->fail('A locked step must not be tickable.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('item', $exception->errors());
        }

        $service->markDone($this->item($training, 'akkoord'), $user);
        $this->assertSame(
            AcademyChecklistService::STATE_OPEN,
            $service->stepState($training, 5, $service->items($training))
        );
    }
}
