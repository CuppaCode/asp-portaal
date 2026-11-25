<?php

namespace Tests\Feature\Academy;

use App\Models\AcademyChecklistItem;
use App\Models\AcademyTraining;
use App\Models\Certificate;
use App\Models\Mailing;
use App\Models\MailTemplate;
use App\Notifications\PlainMail;
use App\Services\Academy\AcademyChecklistService;
use App\Services\Academy\DriverCardService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AcademyPagesTest extends TestCase
{
    use DatabaseTransactions, AcademyFixtures;

    private function actingAsAcademyUser()
    {
        // The layout reads the client IP from $_SERVER (User::isSuperAdmin).
        $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $user = $this->makeUser('Academy medewerker');

        $roleId = DB::table('roles')->insertGetId(['title' => 'Academy test', 'created_at' => now(), 'updated_at' => now()]);
        foreach (['academy_access', 'academy_manage'] as $title) {
            $permissionId = DB::table('permissions')->where('title', $title)->value('id')
                ?? DB::table('permissions')->insertGetId(['title' => $title, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('permission_role')->insert(['role_id' => $roleId, 'permission_id' => $permissionId]);
        }
        DB::table('role_user')->insert(['role_id' => $roleId, 'user_id' => $user->id]);

        $this->actingAs($user);

        return $user;
    }

    public function test_pages_require_the_academy_permission(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user)->get(route('admin.academy.overview'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.academy.employers.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.academy.trainings.index'))->assertForbidden();
    }

    public function test_the_overview_pages_render(): void
    {
        $this->actingAsAcademyUser();
        $company = $this->makeCompany(['academy_started_at' => now()->subDays(10)->format('Y-m-d')]);
        $this->makeDriver($company);

        $this->get(route('admin.academy.overview'))
            ->assertOk()
            ->assertSee('Nu te doen')
            ->assertSee('Vaste routine vandaag');

        $this->get(route('admin.academy.overview', ['tab' => 'bewaken']))->assertOk()->assertSee('Bewaken en verlengen');
        $this->get(route('admin.academy.overview', ['tab' => 'bewaken', 'filter' => 'all']))->assertOk();
    }

    public function test_the_employer_flow(): void
    {
        $this->actingAsAcademyUser();
        $company = $this->makeCompany(['academy_enabled' => false, 'academy_started_at' => null]);

        $this->post(route('admin.academy.employers.store'), ['company_id' => $company->id, 'academy_started_at' => '2026-10-02'])
            ->assertRedirect(route('admin.academy.employers.show', $company->id));

        $this->assertTrue($company->fresh()->academy_enabled);

        $this->get(route('admin.academy.employers.index'))->assertOk()->assertSee($company->name);
        $this->get(route('admin.academy.employers.show', $company->id))
            ->assertOk()
            ->assertSee('Verwerkersovereenkomst getekend retour')
            ->assertSee('wacht');

        $this->post(route('admin.academy.employers.drivers.store', $company->id), [
            'first_name' => 'Piet', 'last_name' => 'Pieters', 'function' => 'Berger',
        ])->assertRedirect();
        $this->assertDatabaseHas('contacts', ['first_name' => 'Piet', 'company_id' => $company->id]);

        $item = AcademyChecklistItem::where('subject_id', $company->id)->where('item_key', 'namenlijst')->firstOrFail();
        $this->post(route('admin.academy.items.toggle', $item->id))->assertRedirect();
        $this->assertNotNull($item->fresh()->done_at);

        $this->post(route('admin.academy.items.undo', $item->id))->assertRedirect();
        $this->assertNull($item->fresh()->done_at);

        // Always-required items cannot be set to "n.v.t."
        $this->from(route('admin.academy.employers.show', $company->id))
            ->post(route('admin.academy.items.na', $item->id))
            ->assertSessionHasErrors('item');
    }

    public function test_the_driver_card_page_and_actions(): void
    {
        $user = $this->actingAsAcademyUser();
        $company = $this->makeCompany();
        $driver = $this->makeDriver($company);

        $this->get(route('admin.academy.driver-cards.edit', $driver->id))
            ->assertOk()
            ->assertSee('Chauffeurskaart')
            ->assertSee('onvolledig');

        $this->put(route('admin.academy.driver-cards.update', $driver->id), [
            'first_name' => 'Jan',
            'last_name' => 'Jansen',
            'birthdate' => '1990-05-01',
            'in_service' => 1,
            'scans_attached' => 1,
            'license_categories' => 'C, CE',
            'code95_hours' => 28,
            'code95_practice_hours' => 7,
            'certificates' => [
                $this->category('rijbewijs')->id => ['expiry_date' => '2030-01-01'],
                $this->category('code_95')->id => ['expiry_date' => '2030-12-22'],
                $this->category('chauffeurspas')->id => ['expiry_date' => '2029-10-22'],
                $this->category('revi')->id => ['not_applicable' => 1],
                $this->category('lichte_berging')->id => ['issue_date' => '2020-05-01'],
            ],
        ])->assertRedirect();

        $driver = $driver->fresh();
        $this->assertTrue(app(DriverCardService::class)->isComplete($driver));
        $this->assertTrue($driver->certificates()->where('category_id', $this->category('revi')->id)->first()->not_applicable);

        // Add a training that is not yet in Mijn CBR: it must not count.
        $this->post(route('admin.academy.driver-cards.courses.store', $driver->id), [
            'course_date' => '2026-11-10', 'name' => 'Module', 'type' => 'theory', 'hours' => 7,
        ])->assertRedirect();
        $this->assertDatabaseHas('code95_courses', ['name' => 'Module', 'cbr_registered' => 0]);

        // Four-eyes check by a colleague.
        $colleague = $this->makeUser('Collega');
        $this->post(route('admin.academy.driver-cards.check', $driver->id), [
            'checked_by_user_id' => $colleague->id, 'dates_confirmed' => 1,
        ])->assertRedirect();
        $this->assertSame($colleague->id, $driver->fresh()->checked_by_user_id);

        // Checking as yourself is refused.
        $this->post(route('admin.academy.driver-cards.check', $driver->id), [
            'checked_by_user_id' => $user->id, 'dates_confirmed' => 1,
        ])->assertSessionHasErrors('checked_by_user_id');

        $this->post(route('admin.academy.driver-cards.comment', $driver->id), ['body' => 'Gebeld met de werkgever'])->assertRedirect();
        $this->get(route('admin.academy.driver-cards.edit', $driver->id))->assertOk()->assertSee('Gebeld met de werkgever');
    }

    public function test_the_training_flow_and_code_95_hours_only_after_cbr_confirmation(): void
    {
        $this->actingAsAcademyUser();
        $company = $this->makeCompany();
        $driver = $this->makeDriver($company, ['email' => 'jan@example.com', 'birthdate' => '1990-05-01']);

        $this->get(route('admin.academy.trainings.create', ['company_id' => $company->id]))->assertOk()->assertSee('Jansen');

        $this->post(route('admin.academy.trainings.store'), [
            'company_id' => $company->id,
            'name' => 'Code 95 module',
            'training_date' => now()->subDay()->format('Y-m-d'),
            'provider_type' => 'asp',
            'code95_hours' => 7,
            'code95_type' => 'practice',
            'is_prevention' => 0,
            'drivers' => [$driver->id],
        ])->assertRedirect();

        $training = AcademyTraining::firstOrFail();
        $participant = $training->participants()->firstOrFail();

        $this->get(route('admin.academy.trainings.index'))->assertOk()->assertSee('Code 95 module');
        $this->get(route('admin.academy.trainings.show', $training->id))->assertOk()->assertSee('Deelnemers (1)');

        // Hours cannot be confirmed before the participant attended.
        $this->from(route('admin.academy.trainings.show', $training->id))
            ->patch(route('admin.academy.trainings.participants.update', [$training->id, $participant->id]), ['confirm_hours' => 1])
            ->assertSessionHasErrors('participant');

        $this->patch(route('admin.academy.trainings.participants.update', [$training->id, $participant->id]), ['attended' => 1])->assertRedirect();
        $this->assertDatabaseCount('code95_courses', 0);

        $this->patch(route('admin.academy.trainings.participants.update', [$training->id, $participant->id]), ['confirm_hours' => 1])->assertRedirect();
        $this->assertDatabaseHas('code95_courses', ['academy_training_id' => $training->id, 'hours' => 7, 'type' => 'practice', 'cbr_registered' => 1]);

        // Not-attended removes the hours again.
        $this->patch(route('admin.academy.trainings.participants.update', [$training->id, $participant->id]), ['attended' => 0])->assertRedirect();
        $this->assertDatabaseCount('code95_courses', 0);

        // The training cannot be closed while items are open.
        $this->from(route('admin.academy.trainings.show', $training->id))
            ->post(route('admin.academy.trainings.close', $training->id))
            ->assertSessionHasErrors('close');

        // A new participant is created and added to the dossier.
        $this->post(route('admin.academy.trainings.participants.new', $training->id), [
            'first_name' => 'Kees', 'last_name' => 'Klaassen', 'birthdate' => '1985-03-03',
        ])->assertRedirect();
        $this->assertSame(2, $training->participants()->count());
    }

    public function test_sending_a_mail_registers_the_proof_and_ticks_the_item(): void
    {
        $user = $this->actingAsAcademyUser();
        Notification::fake();

        $company = $this->makeCompany();
        $company->update(['contact_id' => $this->makeDriver($company, ['first_name' => 'Contact', 'last_name' => 'Persoon', 'email' => 'contact@example.com'])->contact_id]);
        app(AcademyChecklistService::class)->refresh($company);

        MailTemplate::where('code', 'M1')->update(['subject' => 'Start [bedrijf]', 'body' => '<p>Hallo [voornaam contactpersoon], de start is op [startdatum].</p>']);

        $item = AcademyChecklistItem::where('subject_id', $company->id)->where('item_key', 'startmail')->firstOrFail();
        $query = ['template' => 'M1', 'context' => 'employer', 'id' => $company->id, 'item' => $item->id];

        $this->get(route('admin.academy.mail.compose', $query))
            ->assertOk()
            ->assertSee('Start Academy Test BV')
            ->assertSee('contact@example.com');

        $payload = $query + [
            'to' => 'contact@example.com',
            'subject' => 'Start Academy Test BV',
            'body' => 'Hallo [datum], welkom',
            'checks' => ['placeholders', 'recipients', 'attachment'],
        ];

        // Unfilled placeholders block the mail.
        $this->post(route('admin.academy.mail.send'), $payload)->assertSessionHasErrors('body');
        Notification::assertNothingSent();

        $payload['body'] = 'Hallo Contact, welkom';
        $this->post(route('admin.academy.mail.send'), $payload)->assertRedirect(route('admin.academy.employers.show', $company->id));

        Notification::assertSentOnDemand(PlainMail::class);

        $mailing = Mailing::where('context_type', get_class($company))->where('context_id', $company->id)->firstOrFail();
        $this->assertSame('sent', $mailing->status);
        $this->assertSame('academy@autoschadeplan.nl', $mailing->reply_to);

        $item = $item->fresh();
        $this->assertNotNull($item->done_at);
        $this->assertSame($mailing->id, $item->mailing_id);
    }

    public function test_the_driver_card_can_be_exported_as_a_form_pdf(): void
    {
        $this->actingAsAcademyUser();
        $company = $this->makeCompany();
        $driver = $this->makeDriver($company, ['email' => 'jan@example.com']);
        $this->makeDriver($company, ['first_name' => 'Piet', 'last_name' => 'Pieters', 'email' => '']);

        $response = $this->get(route('admin.academy.driver-cards.form', $driver->id))->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertStringContainsString('Check-je-papieren-jan-jansen.pdf', $response->headers->get('Content-Disposition'));

        // One PDF for all drivers without e-mail, and for those with gaps.
        $this->get(route('admin.academy.employers.forms', [$company->id, 'filter' => 'no_email']))->assertOk();
        $this->get(route('admin.academy.employers.forms', $company->id))->assertOk();
        $this->get(route('admin.academy.employers.forms', [$company->id, 'filter' => 'all']))->assertOk();

        // The form is reachable from the card and the employer page.
        $this->get(route('admin.academy.driver-cards.edit', $driver->id))->assertSee('Formulier (PDF)');
    }

    public function test_the_personal_form_is_attached_to_the_data_request(): void
    {
        $this->actingAsAcademyUser();
        Notification::fake();

        $company = $this->makeCompany();
        $driver = $this->makeDriver($company, ['email' => 'jan@example.com']);
        app(AcademyChecklistService::class)->refresh($company);

        $item = AcademyChecklistItem::where('subject_id', $company->id)->where('item_key', 'gegevensverzoek')->firstOrFail();
        $item->update(['not_applicable' => false]);

        MailTemplate::where('code', 'M2')->update(['subject' => 'Gegevens', 'body' => '<p>Hoi [voornaam], vul het formulier in.</p>']);

        $query = ['template' => 'M2', 'context' => 'employer', 'id' => $company->id, 'item' => $item->id];

        // The first steps are open, so the item is locked? It is in step 1 which is always open.
        $this->get(route('admin.academy.mail.compose', $query))->assertOk()->assertSee('Check je papieren');

        $this->post(route('admin.academy.mail.send'), $query + [
            'bulk' => 1,
            'drivers' => [$driver->id],
            'subject' => 'Gegevens',
            'body' => '<p>Hoi [voornaam], vul het formulier in.</p>',
            'attach_form' => 1,
            'checks' => ['placeholders', 'recipients', 'attachment'],
        ])->assertRedirect();

        $mailing = Mailing::where('context_id', $company->id)->firstOrFail();
        $this->assertSame('sent', $mailing->status);
        $this->assertStringContainsString('Hoi Jan', $mailing->body);
        $this->assertSame(['Check-je-papieren-jan-jansen.pdf'], $mailing->getMedia('attachments')->pluck('file_name')->all());
    }

    public function test_a_new_document_resets_signals_and_the_check_and_starts_m12_for_code_95(): void
    {
        $this->actingAsAcademyUser();
        $company = $this->makeCompany();
        $driver = $this->makeDriver($company);
        $category = $this->category('code_95');

        $certificate = Certificate::create([
            'driver_id' => $driver->id, 'category_id' => $category->id, 'name' => $category->name, 'expiry_date' => '2026-12-22',
        ]);
        $certificate->upsertMetaValue('code95_hours_completed', 'number', 35);
        $certificate->upsertMetaValue('code95_hours_practice_completed', 'number', 7);
        $driver->forceFill(['checked_at' => now()])->save();

        $this->post(route('admin.academy.monitoring.new-document', $certificate->id), ['new_expiry_date' => '2031-12-22'])->assertRedirect();

        $this->assertSame('2031-12-22', $certificate->fresh()->getRawOriginal('expiry_date'));
        $this->assertNull($driver->fresh()->checked_at, 'four-eyes check becomes invalid');
        $this->assertDatabaseHas('certificate_renewals', ['certificate_id' => $certificate->id]);
        $this->assertDatabaseHas('academy_checklist_items', [
            'subject_type' => Certificate::class, 'subject_id' => $certificate->id, 'item_key' => 'm12',
        ]);

        // New cycle: hours start again at zero.
        $this->assertSame(0, (int) $certificate->fresh()->getMetaValue('code95_hours_completed'));

        // The new date must be later than the current one.
        $this->post(route('admin.academy.monitoring.new-document', $certificate->id), ['new_expiry_date' => '2030-01-01'])
            ->assertSessionHasErrors('new_expiry_date');
    }
}
