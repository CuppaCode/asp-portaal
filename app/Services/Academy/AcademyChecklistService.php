<?php

namespace App\Services\Academy;

use App\Models\AcademyChecklistItem;
use App\Models\AcademyTraining;
use App\Models\Certificate;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Mailing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * The step lists (stap 1 t/m 7): create the items from config/academy.php, calculate deadlines,
 * lock steps ("wacht") and tick / undo items.
 */
class AcademyChecklistService
{
    public const STATE_LOCKED = 'locked';
    public const STATE_OPEN = 'open';
    public const STATE_DONE = 'done';
    public const STATE_ONGOING = 'ongoing';

    public const AUTO_NA_PREFIX = 'auto:';

    public function __construct(
        protected WorkdayCalculator $workdays,
        protected DriverCardService $cards
    ) {
    }

    /* --------------------------------------------------------------------
     | Subjects
     | ------------------------------------------------------------------ */

    public function subjectKey(Model $subject): string
    {
        return match (true) {
            $subject instanceof Company => 'company',
            $subject instanceof AcademyTraining => 'training',
            $subject instanceof Certificate => 'certificate',
            default => throw new \InvalidArgumentException('Unsupported subject ' . get_class($subject)),
        };
    }

    public function definitions(Model $subject): array
    {
        return config('academy.items.' . $this->subjectKey($subject), []);
    }

    /**
     * Create missing items, update deadlines and derived "n.v.t." states. Safe to call repeatedly.
     *
     * @return Collection<string, AcademyChecklistItem> keyed by item_key (monthly items: latest period)
     */
    public function refresh(Model $subject, ?Carbon $today = null): Collection
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();

        $this->ensureItems($subject, $today);

        $items = $this->items($subject);

        $this->applyDerivedNotApplicable($subject, $items);
        $this->updateDueDates($subject, $items);

        return $this->items($subject);
    }

    public function items(Model $subject): Collection
    {
        return AcademyChecklistItem::query()
            ->where('subject_type', get_class($subject))
            ->where('subject_id', $subject->getKey())
            ->get();
    }

    protected function ensureItems(Model $subject, Carbon $today): void
    {
        foreach ($this->definitions($subject) as $key => $definition) {
            if (!empty($definition['recurring'])) {
                continue;
            }

            AcademyChecklistItem::firstOrCreate([
                'subject_type' => get_class($subject),
                'subject_id' => $subject->getKey(),
                'item_key' => $key,
                'period' => '',
            ], [
                'step' => $definition['step'],
            ]);
        }

        if ($subject instanceof Company) {
            $this->ensureMonthlyOverview($subject, $today);
        }
    }

    /**
     * Step 3: one "geldigheidsoverzicht" (M4) per month, due on the first working day, once step 2 is done.
     */
    protected function ensureMonthlyOverview(Company $company, Carbon $today): void
    {
        $definition = config('academy.items.company.monthly_overview');

        if (!$definition || $this->stepState($company, 3, $this->items($company)) === self::STATE_LOCKED) {
            return;
        }

        $month = $today->copy()->startOfMonth();

        AcademyChecklistItem::firstOrCreate([
            'subject_type' => Company::class,
            'subject_id' => $company->id,
            'item_key' => 'monthly_overview',
            'period' => $month->format('Y-m'),
        ], [
            'step' => $definition['step'],
            'due_date' => $this->workdays->firstWorkdayOfMonth($month)->format('Y-m-d'),
        ]);
    }

    /* --------------------------------------------------------------------
     | Deadlines
     | ------------------------------------------------------------------ */

    protected function updateDueDates(Model $subject, Collection $items): void
    {
        $byKey = $items->keyBy('item_key');

        foreach ($items as $item) {
            $definition = $item->definition();

            if (!$definition || !empty($definition['recurring'])) {
                continue;
            }

            $due = $this->dueDate($subject, $definition, $byKey);
            $current = $item->due_date?->format('Y-m-d');
            $new = $due?->format('Y-m-d');

            if ($current !== $new) {
                $item->due_date = $due;
                $item->save();
            }
        }
    }

    protected function dueDate(Model $subject, array $definition, Collection $byKey): ?Carbon
    {
        $anchor = $this->anchorDate($subject, $definition['anchor'] ?? null, $byKey);

        if (!$anchor) {
            return null;
        }

        $offset = (int) ($definition['offset'] ?? 0);
        $offset = ($definition['direction'] ?? 'after') === 'before' ? -$offset : $offset;

        if (($definition['unit'] ?? 'days') === 'workdays') {
            return $this->workdays->addWorkdays($anchor, $offset);
        }

        return $anchor->copy()->addDays($offset);
    }

    protected function anchorDate(Model $subject, ?string $anchor, Collection $byKey): ?Carbon
    {
        if ($anchor === null) {
            return null;
        }

        if ($anchor === 'company_start' && $subject instanceof Company) {
            return $subject->academy_started_at?->copy()->startOfDay();
        }

        if ($anchor === 'training_date' && $subject instanceof AcademyTraining) {
            return $subject->training_date?->copy()->startOfDay();
        }

        /** @var AcademyChecklistItem|null $anchorItem */
        $anchorItem = $byKey->get($anchor);

        if (!$anchorItem) {
            return null;
        }

        if ($anchorItem->done_at) {
            return $anchorItem->done_at->copy()->startOfDay();
        }

        // Not needed: continue with the deadline of that item.
        return $anchorItem->not_applicable ? $anchorItem->due_date?->copy()->startOfDay() : null;
    }

    /* --------------------------------------------------------------------
     | Derived "n.v.t."
     | ------------------------------------------------------------------ */

    protected function applyDerivedNotApplicable(Model $subject, Collection $items): void
    {
        foreach ($items as $item) {
            $rule = $item->definition()['derived_na'] ?? null;

            if (!$rule || $item->done_at) {
                continue;
            }

            $applies = $this->evaluateRule($rule, $subject);
            $auto = str_starts_with((string) $item->not_applicable_reason, self::AUTO_NA_PREFIX);

            if ($applies && !$item->not_applicable) {
                $item->update([
                    'not_applicable' => true,
                    'not_applicable_reason' => self::AUTO_NA_PREFIX . $rule,
                ]);
            } elseif (!$applies && $item->not_applicable && $auto) {
                $item->update(['not_applicable' => false, 'not_applicable_reason' => null]);
            }
        }
    }

    public function evaluateRule(string $rule, Model $subject): bool
    {
        switch ($rule) {
            case 'all_drivers_have_email':
                $drivers = $this->companyDrivers($subject);

                return $drivers->isNotEmpty()
                    && $drivers->every(fn (Driver $driver) => !empty(trim((string) $driver->contact?->email)));

            case 'not_prevention':
                return $subject instanceof AcademyTraining && !$subject->is_prevention;

            case 'no_code95_module':
                return $subject instanceof AcademyTraining && !$subject->hasCode95Module();

            case 'all_participants_have_email':
                if (!$subject instanceof AcademyTraining) {
                    return false;
                }
                $participants = $subject->participants()->with('driver.contact')->get();

                return $participants->isNotEmpty()
                    && $participants->every(fn ($participant) => !empty(trim((string) $participant->driver?->contact?->email)));

            case 'attendance_recorded':
                return $subject instanceof AcademyTraining
                    && $subject->participants()->count() > 0
                    && $subject->participants()->whereNull('attended')->doesntExist();

            case 'cbr_confirmed':
                return $subject instanceof AcademyTraining
                    && $subject->participants()->where('attended', true)->where('cbr_confirmed', false)->doesntExist();
        }

        return false;
    }

    public function companyDrivers(Model $company): Collection
    {
        return Driver::query()
            ->with('contact', 'certificates.metaFields', 'certificates.category')
            ->where('company_id', $company->getKey())
            ->where('in_service', true)
            ->get();
    }

    /* --------------------------------------------------------------------
     | Steps
     | ------------------------------------------------------------------ */

    /**
     * locked: "wacht"; open: in progress; done; ongoing: recurring step (stap 3).
     */
    public function stepState(Model $subject, int $step, ?Collection $items = null): string
    {
        $items ??= $this->items($subject);

        if ($subject instanceof Company) {
            return $this->companyStepState($subject, $step, $items);
        }

        if ($subject instanceof AcademyTraining) {
            return $this->trainingStepState($subject, $step, $items);
        }

        return self::STATE_OPEN;
    }

    protected function companyStepState(Company $company, int $step, Collection $items): string
    {
        $step1Done = $this->allResolved($items, 1);

        return match ($step) {
            1 => $step1Done ? self::STATE_DONE : self::STATE_OPEN,
            2 => !$step1Done
                ? self::STATE_LOCKED
                : ($this->cardsDone($company) ? self::STATE_DONE : self::STATE_OPEN),
            3 => ($step1Done && $this->cardsDone($company)) ? self::STATE_ONGOING : self::STATE_LOCKED,
            default => self::STATE_OPEN,
        };
    }

    protected function trainingStepState(AcademyTraining $training, int $step, Collection $items): string
    {
        $akkoord = $items->firstWhere('item_key', 'akkoord');

        return match ($step) {
            4 => $this->allResolved($items, 4) ? self::STATE_DONE : self::STATE_OPEN,
            5 => !($akkoord && $akkoord->isResolved())
                ? self::STATE_LOCKED
                : ($this->allResolved($items, 5) ? self::STATE_DONE : self::STATE_OPEN),
            6 => $training->training_date->isFuture() && !$training->training_date->isToday()
                ? self::STATE_LOCKED
                : ($this->allResolved($items, 6) ? self::STATE_DONE : self::STATE_OPEN),
            default => self::STATE_OPEN,
        };
    }

    protected function allResolved(Collection $items, int $step): bool
    {
        $stepItems = $items->where('step', $step);

        return $stepItems->isNotEmpty() && $stepItems->every(fn (AcademyChecklistItem $item) => $item->isResolved());
    }

    /**
     * Step 2 is done when every driver in service has a complete card that is checked by a second person.
     */
    public function cardsDone(Company $company): bool
    {
        $drivers = $this->companyDrivers($company);

        return $drivers->isNotEmpty()
            && $drivers->every(fn (Driver $driver) => $this->cards->isFinal($driver));
    }

    public function isLocked(AcademyChecklistItem $item): bool
    {
        $subject = $item->subject;

        if (!$subject) {
            return false;
        }

        return $this->stepState($subject, $item->step) === self::STATE_LOCKED;
    }

    /* --------------------------------------------------------------------
     | Actions
     | ------------------------------------------------------------------ */

    /**
     * Tick an item. Only when the step is open and the proof is there.
     *
     * @throws ValidationException
     */
    public function markDone(AcademyChecklistItem $item, User $user, ?Mailing $mailing = null): AcademyChecklistItem
    {
        if ($item->done_at) {
            return $item;
        }

        $subject = $item->subject;

        if ($this->isLocked($item)) {
            throw ValidationException::withMessages(['item' => 'Deze stap wacht: rond eerst de vorige stap af.']);
        }

        $requires = $item->definition()['requires'] ?? null;
        if ($requires && $subject && !$this->evaluateRule($requires, $subject)) {
            throw ValidationException::withMessages(['item' => $this->requiresMessage($requires)]);
        }

        $item->update([
            'done_at' => now(),
            'done_by_user_id' => $user->id,
            'not_applicable' => false,
            'not_applicable_reason' => null,
            'mailing_id' => $mailing?->id ?? $item->mailing_id,
        ]);

        AcademyTodoService::forgetCache();

        if ($subject) {
            $this->refresh($subject);
        }

        return $item->fresh();
    }

    /**
     * @throws ValidationException
     */
    public function markNotApplicable(AcademyChecklistItem $item, User $user, ?string $reason = null): AcademyChecklistItem
    {
        $definition = $item->definition() ?? [];

        if (empty($definition['can_na'])) {
            throw ValidationException::withMessages(['item' => 'Deze regel moet altijd gebeuren en kan niet n.v.t. worden gezet.']);
        }

        if ($item->done_at) {
            throw ValidationException::withMessages(['item' => 'Deze regel is al afgevinkt. Maak hem eerst ongedaan.']);
        }

        if ($this->isLocked($item)) {
            throw ValidationException::withMessages(['item' => 'Deze stap wacht: rond eerst de vorige stap af.']);
        }

        $item->update([
            'not_applicable' => true,
            'not_applicable_reason' => $reason ?: 'handmatig door ' . $user->name,
        ]);

        AcademyTodoService::forgetCache();

        if ($item->subject) {
            $this->refresh($item->subject);
        }

        return $item->fresh();
    }

    public function undo(AcademyChecklistItem $item): AcademyChecklistItem
    {
        $item->update([
            'done_at' => null,
            'done_by_user_id' => null,
            'mailing_id' => null,
            'not_applicable' => false,
            'not_applicable_reason' => null,
        ]);

        AcademyTodoService::forgetCache();

        if ($item->subject) {
            // Derived "n.v.t." is applied again by refresh().
            $this->refresh($item->subject);
        }

        return $item->fresh();
    }

    /**
     * Everything a screen needs: the steps of this subject with their state and decorated items.
     *
     * @param int[] $steps Step numbers to include.
     */
    public function steps(Model $subject, array $steps, ?Carbon $today = null): array
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();
        $items = $this->refresh($subject, $today);
        $warnDays = (int) config('academy.todo.warn_days', 2);
        $keys = array_keys($this->definitions($subject));
        $result = [];

        foreach ($steps as $number) {
            $stepItems = $items
                ->where('step', $number)
                ->sortBy(fn (AcademyChecklistItem $item) => str_pad((string) array_search($item->item_key, $keys, true), 3, '0', STR_PAD_LEFT) . '|' . $item->period)
                ->values();

            if ($number === 3) {
                $stepItems = $stepItems->sortByDesc('period')->take(12)->values();
            }

            $state = $this->stepState($subject, $number, $items);

            $result[$number] = array_merge(config("academy.steps.{$number}", []), [
                'number' => $number,
                'state' => $state,
                'items' => $stepItems->map(function (AcademyChecklistItem $item) use ($state, $today, $warnDays) {
                    $due = $item->due_date;
                    $open = $item->isOpen();

                    return [
                        'model' => $item,
                        'definition' => $item->definition() ?? [],
                        'locked' => $state === self::STATE_LOCKED,
                        'overdue' => $open && $due && $due->lt($today),
                        'warning' => $open && $due && $due->gte($today) && $today->diffInDays($due) <= $warnDays,
                        'auto_na' => str_starts_with((string) $item->not_applicable_reason, self::AUTO_NA_PREFIX),
                    ];
                }),
            ]);
        }

        return $result;
    }

    /**
     * Item for a renewed Code 95: send M12 and the yearly planning.
     */
    public function createRenewalItem(Certificate $certificate): ?AcademyChecklistItem
    {
        $definition = config('academy.items.certificate.m12');
        $certificate->loadMissing('driver.company');

        if (!$definition || !$certificate->driver?->company?->academy_enabled) {
            return null;
        }

        return AcademyChecklistItem::firstOrCreate([
            'subject_type' => Certificate::class,
            'subject_id' => $certificate->id,
            'item_key' => 'm12',
            'period' => $certificate->getRawOriginal('expiry_date') ?: '',
        ], [
            'step' => $definition['step'],
            'due_date' => Carbon::today(),
        ]);
    }

    protected function requiresMessage(string $rule): string
    {
        return match ($rule) {
            'attendance_recorded' => 'Leg eerst bij elke deelnemer de aanwezigheid vast.',
            'cbr_confirmed' => 'Zet geen uren op de kaart die nog niet in het CBR-register staan: bevestig eerst de uren van alle aanwezigen.',
            default => 'Nog niet aan de voorwaarden voldaan.',
        };
    }
}
