<?php

namespace App\Console\Commands;

use App\Models\Claim;
use App\Models\Comment;
use App\Models\Note;
use App\Models\Role;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSoftDeleteTestData extends Command
{
    protected $signature   = 'test:soft-delete-data {--delete : Soft-delete the test user immediately after creating}';
    protected $description = 'Create a test user with notes, tasks and comments to manually verify "Verwijderd [type]" labels.';

    public function handle(): int
    {
        // ── Resolve a team ────────────────────────────────────────────────
        $team = Team::first();
        if (! $team) {
            $this->error('No team found in the database. Create one first.');
            return self::FAILURE;
        }

        // ── Create the test user ──────────────────────────────────────────
        $email = 'test-soft-delete@example.com';

        /** @var User $user */
        $user = User::withTrashed()->firstOrCreate(
            ['email' => $email],
            [
                'name'              => 'Test Verwijderde Gebruiker',
                'password'          => Hash::make('test-password'),
                'email_verified_at' => now()->format('d-m-Y H:i:s'),
                'team_id'           => $team->id,
            ]
        );

        // Restore if previously soft-deleted
        if ($user->trashed()) {
            $user->restore();
            $this->line("  Restored previously soft-deleted test user (id {$user->id}).");
        }

        // Attach the "User" role so the user shows up normally
        $userRole = Role::find(2);
        if ($userRole && ! $user->roles->contains(2)) {
            $user->roles()->attach(2);
        }

        $this->info("✓ Test user created   — id: {$user->id}, email: {$email}");

        // ── Create a standalone note ──────────────────────────────────────
        $note = Note::create([
            'title'       => '[TEST] Soft-delete notitie',
            'description' => 'Deze notitie is aangemaakt voor soft-delete testen. Verwijder de gebruiker om "Verwijderde gebruiker" te zien op de notities pagina.',
            'user_id'     => $user->id,
            'team_id'     => $team->id,
        ]);

        $this->info("✓ Test note created   — id: {$note->id}");

        // ── Link note to the first available claim ────────────────────────
        $claim = Claim::first();
        if ($claim) {
            $note->claims()->syncWithoutDetaching([$claim->id]);
            $this->info("  Note linked to claim {$claim->claim_number} (id: {$claim->id})");

            // Create a comment on the note
            Comment::create([
                'body'             => '[TEST] Soft-delete opmerking op notitie.',
                'commentable_id'   => $note->id,
                'commentable_type' => Note::class,
                'user_id'          => $user->id,
                'team_id'          => $team->id,
            ]);

            $this->info("✓ Test comment created on note {$note->id}");

            // Create a task on the claim
            $task = Task::create([
                'description' => '[TEST] Soft-delete taak',
                'user_id'     => $user->id,
                'claim_id'    => $claim->id,
                'status'      => 'new',
                'deadline_at' => now()->addDays(7)->format('d-m-Y'),
                'team_id'     => $team->id,
            ]);

            $this->info("✓ Test task created   — id: {$task->id}");
        } else {
            $this->warn('  No claim found — note was not linked to a claim; comment and task skipped.');
        }

        // ── Optionally soft-delete immediately ────────────────────────────
        if ($this->option('delete')) {
            $user->delete();
            $this->warn("✓ Test user soft-deleted immediately (id: {$user->id}).");
        }

        // ── Instructions ─────────────────────────────────────────────────
        $this->newLine();
        $this->line('─────────────────────────────────────────────────────────');
        $this->line('<fg=cyan>Next steps:</>');
        $this->newLine();

        if (! $this->option('delete')) {
            $this->line("  1. Verify the user, note, task and comment exist in the app.");
            $this->line("  2. Then soft-delete the user:");
            $this->newLine();
            $this->line("       php artisan tinker --execute=\"App\\\\Models\\\\User::find({$user->id})->delete();\"");
            $this->newLine();
        }

        $this->line("  3. Check these pages for <fg=yellow>\"Verwijderde gebruiker\"</>:");
        $this->line("       /admin/notes          — email column");
        $this->line("       /admin/tasks          — user column");
        if ($claim) {
            $this->line("       /admin/claims/{$claim->id} — activiteiten tab (note + comment + task)");
        }
        $this->newLine();
        $this->line("  4. Restore when done:");
        $this->newLine();
        $this->line("       php artisan tinker --execute=\"App\\\\Models\\\\User::withTrashed()->find({$user->id})->restore();\"");
        $this->newLine();
        $this->line("  5. Clean up test data:");
        $this->newLine();
        $this->line("       php artisan test:soft-delete-data --cleanup");
        $this->line('─────────────────────────────────────────────────────────');

        return self::SUCCESS;
    }
}
