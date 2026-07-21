<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\CertificateCategory;
use App\Models\Claim;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Driver;
use App\Models\Mailing;
use App\Models\Note;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * Verifies that pages show the correct "Verwijderd [type]" label
 * when a soft-deleted record is referenced, instead of crashing or
 * displaying blank/null values.
 *
 * Uses DatabaseTransactions so every test is rolled back and
 * the real database is left untouched.
 */
class SoftDeleteDisplayTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // REMOTE_ADDR is accessed by User::getIsSuperAdminAttribute() for IP-whitelisting.
        // Set a safe default so menu rendering doesn't crash in the test environment.
        $_SERVER['REMOTE_ADDR'] ??= '127.0.0.1';

        // Bypass all Gate checks — we're testing display, not authorisation.
        Gate::before(fn ($user) => true);

        $this->admin = User::factory()->create([
            'team_id' => Team::first()?->id,
        ]);
    }

    // ---------------------------------------------------------------
    // User soft-delete
    // ---------------------------------------------------------------

    public function test_notes_index_shows_verwijderde_gebruiker_when_note_owner_is_soft_deleted(): void
    {
        $owner = User::factory()->create();

        Note::create([
            'title'       => 'Soft-delete test note',
            'description' => 'Test description',
            'user_id'     => $owner->id,
        ]);

        $owner->delete();

        $this->actingAs($this->admin)
            ->get(route('admin.notes.index'))
            ->assertOk()
            ->assertSee('Verwijderde gebruiker');
    }

    public function test_notes_show_displays_verwijderde_gebruiker_when_note_owner_is_soft_deleted(): void
    {
        $owner = User::factory()->create();

        $note = Note::create([
            'title'       => 'Soft-delete show test',
            'description' => 'Test description',
            'user_id'     => $owner->id,
        ]);

        $owner->delete();

        $this->actingAs($this->admin)
            ->get(route('admin.notes.show', $note))
            ->assertOk()
            ->assertSee('Verwijderde gebruiker');
    }

    public function test_tasks_index_shows_verwijderde_gebruiker_when_task_owner_is_soft_deleted(): void
    {
        $owner = User::factory()->create();

        Task::create([
            'description' => 'Soft-delete test task',
            'user_id'     => $owner->id,
            'status'      => 'new',
            'deadline_at' => now()->addDays(7)->format('d-m-Y'),
        ]);

        $owner->delete();

        $this->actingAs($this->admin)
            ->get(route('admin.tasks.index'))
            ->assertOk()
            ->assertSee('Verwijderde gebruiker');
    }

    public function test_mailings_index_shows_verwijderde_gebruiker_when_sender_is_soft_deleted(): void
    {
        $sender = User::factory()->create();

        Mailing::create([
            'subject'    => 'Soft-delete mailing test',
            'body'       => 'Test body',
            'status'     => 'sent',
            'user_id'    => $sender->id,
            'recipients' => [],
        ]);

        $sender->delete();

        $this->actingAs($this->admin)
            ->get(route('admin.mailings.index'))
            ->assertOk()
            ->assertSee('Verwijderde gebruiker');
    }

    // ---------------------------------------------------------------
    // Contact soft-delete (via Driver)
    // ---------------------------------------------------------------

    public function test_drivers_index_shows_verwijderd_contact_when_contact_is_soft_deleted(): void
    {
        $contact = Contact::create([
            'first_name'  => 'Test',
            'last_name'   => 'Contact',
            'email'       => 'soft_delete_test@example.com',
            'create_user' => 0,
            'is_driver'   => 0,
        ]);

        Driver::create(['contact_id' => $contact->id]);

        $contact->delete();

        $this->actingAs($this->admin)
            ->get(route('admin.drivers.index'))
            ->assertOk()
            ->assertSee('Verwijderd contact');
    }

    public function test_drivers_show_displays_verwijderd_contact_when_contact_is_soft_deleted(): void
    {
        $contact = Contact::create([
            'first_name'  => 'Test',
            'last_name'   => 'Contact',
            'email'       => 'soft_delete_test2@example.com',
            'create_user' => 0,
            'is_driver'   => 0,
        ]);

        $driver = Driver::create(['contact_id' => $contact->id]);

        $contact->delete();

        $this->actingAs($this->admin)
            ->get(route('admin.drivers.show', $driver))
            ->assertOk()
            ->assertSee('Verwijderd contact');
    }

    // ---------------------------------------------------------------
    // Driver soft-delete (via Certificate)
    // ---------------------------------------------------------------

    public function test_certificate_index_shows_verwijderde_chauffeur_when_driver_is_soft_deleted(): void
    {
        $category = CertificateCategory::create(['name' => 'Test Categorie SD']);
        $driver   = Driver::create([]);

        Certificate::create([
            'driver_id'   => $driver->id,
            'category_id' => $category->id,
            'name'        => 'Test Certificaat',
            'expiry_date' => now()->addDays(15)->format('Y-m-d'),
        ]);

        $driver->delete();

        $this->actingAs($this->admin)
            ->get(route('admin.certificate.index'))
            ->assertOk()
            ->assertSee('Verwijderde chauffeur');
    }

    public function test_certificate_show_displays_verwijderde_chauffeur_when_driver_is_soft_deleted(): void
    {
        $category = CertificateCategory::create(['name' => 'Test Categorie SD2']);
        $driver   = Driver::create([]);

        $cert = Certificate::create([
            'driver_id'   => $driver->id,
            'category_id' => $category->id,
            'name'        => 'Test Certificaat 2',
            'expiry_date' => now()->addDays(15)->format('Y-m-d'),
        ]);

        $driver->delete();

        $this->actingAs($this->admin)
            ->get(route('admin.certificate.show', $cert))
            ->assertOk()
            ->assertSee('Verwijderde chauffeur');
    }

    // ---------------------------------------------------------------
    // Claim activities (note + comment + task with deleted user)
    // ---------------------------------------------------------------

    public function test_claim_activities_show_verwijderde_gebruiker_for_note_comment_and_task(): void
    {
        $owner = User::factory()->create();
        $claim = Claim::factory()->create();

        $note = Note::create([
            'title'       => 'Activity note SD test',
            'description' => 'Test',
            'user_id'     => $owner->id,
        ]);
        $note->claims()->attach($claim->id);

        Comment::create([
            'body'             => 'Test comment SD',
            'commentable_id'   => $note->id,
            'commentable_type' => Note::class,
            'user_id'          => $owner->id,
        ]);

        Task::create([
            'description' => 'Activity task SD test',
            'user_id'     => $owner->id,
            'claim_id'    => $claim->id,
            'status'      => 'new',
            'deadline_at' => now()->addDays(7)->format('d-m-Y'),
        ]);

        $owner->delete();

        $this->actingAs($this->admin)
            ->get(route('admin.claims.show', $claim))
            ->assertOk()
            ->assertSee('Verwijderde gebruiker');
    }
}
