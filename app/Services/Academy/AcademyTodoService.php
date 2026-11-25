<?php

namespace App\Services\Academy;

use App\Models\AcademyChecklistItem;
use App\Models\AcademyRoutineCheck;
use App\Models\AcademyTraining;
use App\Models\Certificate;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * "Nu te doen": everything that is late or due within a week, over all employers and trainings.
 */
class AcademyTodoService
{
    public const CACHE_KEY = 'academy.overdue_count';

    public function __construct(
        protected AcademyChecklistService $checklist,
        protected AcademyMonitoringService $monitoring,
        protected DriverCardService $cards,
        protected WorkdayCalculator $workdays
    ) {
    }

    /**
     * @return array{tasks: Collection, kpis: array<string, int>, monitoring: Collection}
     */
    public function build(?Carbon $today = null): array
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();
        $limit = $today->copy()->addDays((int) config('academy.todo.days_ahead', 7));

        $tasks = collect();

        foreach (Company::academy()->orderBy('name')->get() as $company) {
            $steps = $this->checklist->steps($company, [1, 2, 3], $today);

            if ($steps[2]['state'] === AcademyChecklistService::STATE_OPEN) {
                $notFinal = $this->checklist->companyDrivers($company)
                    ->filter(fn ($driver) => !$this->cards->isFinal($driver))
                    ->count();

                if ($notFinal > 0) {
                    $tasks->push($this->task(
                        key: 'cards-' . $company->id,
                        title: $notFinal . ' ' . ($notFinal === 1 ? 'chauffeurskaart' : 'chauffeurskaarten') . ' nog niet volledig of niet gecontroleerd',
                        where: 'Stap 2 · ' . $company->name,
                        url: route('admin.academy.employers.show', $company->id),
                        status: 'open'
                    ));
                }
            }

            $tasks = $tasks->merge($this->itemTasks($steps, $company->name, route('admin.academy.employers.show', $company->id), 'employer', $company->id, $limit));
        }

        foreach (AcademyTraining::with('company')->open()->orderBy('training_date')->get() as $training) {
            $steps = $this->checklist->steps($training, [4, 5, 6], $today);

            $tasks = $tasks->merge($this->itemTasks(
                $steps,
                $training->name . ' · ' . $training->company?->name,
                route('admin.academy.trainings.show', $training->id),
                'training',
                $training->id,
                $limit
            ));
        }

        $tasks = $tasks->merge($this->renewalTasks($today));

        $monitoringRows = $this->monitoring->rows($today);
        $tasks = $tasks->merge($this->signalTasks($monitoringRows, $today, $limit));

        $tasks = $tasks->sortBy(fn (array $task) => ($task['due'] ? '1' . $task['due']->format('Ymd') : '0') . '|' . $task['key'])->values();

        return [
            'tasks' => $tasks,
            'monitoring' => $monitoringRows,
            'kpis' => [
                'overdue' => $tasks->where('status', 'overdue')->count(),
                'upcoming' => $tasks->whereIn('status', ['warning', 'upcoming'])->count(),
                'red' => $monitoringRows->where('status.status', CertificateStatusService::RED)->count(),
                'orange' => $monitoringRows->where('status.status', CertificateStatusService::ORANGE)->count(),
                'open_trainings' => AcademyTraining::open()->count(),
                'drivers' => Company::academy()->get()->sum(fn ($company) => $company->drivers()->where('in_service', true)->count()),
            ],
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function itemTasks(array $steps, string $where, string $url, string $mailContext, int $subjectId, Carbon $limit): Collection
    {
        $tasks = collect();

        foreach ($steps as $step) {
            foreach ($step['items'] as $entry) {
                /** @var AcademyChecklistItem $item */
                $item = $entry['model'];

                if ($entry['locked'] || !$item->isOpen() || !$item->due_date || $item->due_date->gt($limit)) {
                    continue;
                }

                $label = $entry['definition']['label'] ?? $item->item_key;
                if ($item->period) {
                    $label .= ' - ' . Carbon::createFromFormat('Y-m', $item->period)->locale('nl')->translatedFormat('F Y');
                }

                $mailCode = $entry['definition']['mail'] ?? null;

                $tasks->push($this->task(
                    key: 'item-' . $item->id,
                    title: $label,
                    where: 'Stap ' . $item->step . ' · ' . $where,
                    url: $url,
                    due: $item->due_date,
                    status: $entry['overdue'] ? 'overdue' : ($entry['warning'] ? 'warning' : 'upcoming'),
                    mail: $mailCode ? ['code' => $mailCode, 'url' => route('admin.academy.mail.compose', ['template' => $mailCode, 'context' => $mailContext, 'id' => $subjectId, 'item' => $item->id])] : null
                ));
            }
        }

        return $tasks;
    }

    /**
     * Stap 7: Code 95 renewed, M12 and the yearly planning still have to be sent.
     */
    protected function renewalTasks(Carbon $today): Collection
    {
        return AcademyChecklistItem::query()
            ->where('subject_type', Certificate::class)
            ->where('item_key', 'm12')
            ->whereNull('done_at')
            ->where('not_applicable', false)
            ->get()
            ->map(function (AcademyChecklistItem $item) use ($today) {
                $certificate = Certificate::with('driver.contact', 'driver.company')->find($item->subject_id);
                $driver = $certificate?->driver;

                if (!$certificate || !$driver) {
                    return null;
                }

                $name = trim(($driver->contact?->first_name ?? '') . ' ' . ($driver->contact?->last_name ?? ''));

                return $this->task(
                    key: 'item-' . $item->id,
                    title: 'Code 95 van ' . $name . ' is verlengd: M12 en jaarplanning versturen',
                    where: 'Stap 7 · ' . $driver->company?->name,
                    url: route('admin.academy.overview', ['tab' => 'bewaken']),
                    due: $item->due_date ?? $today,
                    status: ($item->due_date ?? $today)->lt($today) ? 'overdue' : 'warning',
                    mail: ['code' => 'M12', 'url' => route('admin.academy.mail.compose', ['template' => 'M12', 'context' => 'certificate', 'id' => $certificate->id, 'item' => $item->id])]
                );
            })
            ->filter();
    }

    protected function signalTasks(Collection $rows, Carbon $today, Carbon $limit): Collection
    {
        $tasks = collect();

        foreach ($rows as $row) {
            $certificate = $row['certificate'];

            if (!$certificate) {
                continue;
            }

            foreach ($row['signals'] as $signal) {
                if (!$signal['applicable'] || $signal['sent'] || $signal['date']->gt($limit)) {
                    continue;
                }

                $driver = $row['driver'];
                $name = trim(($driver->contact?->first_name ?? '') . ' ' . ($driver->contact?->last_name ?? ''));
                $expiry = $certificate->expiryCarbon();
                $verb = $expiry && $expiry->lt($today) ? 'is verlopen op' : 'verloopt';

                $title = $signal['level'] === 1
                    ? '1e signaal: ' . $row['category']->name . ' van ' . $name . ' ' . $verb . ' ' . $expiry?->format('d-m-Y')
                    : 'Signaal (binnen ' . $row['category']->signal_2_days . ' dagen) + werkgever bellen: ' . $row['category']->name . ' van ' . $name . ' ' . $verb . ' ' . $expiry?->format('d-m-Y');

                $tasks->push($this->task(
                    key: 'signal-' . $certificate->id . '-' . $signal['level'],
                    title: $title,
                    where: 'Stap 7 · ' . $row['company']?->name,
                    url: route('admin.academy.overview', ['tab' => 'bewaken']),
                    due: $signal['date'],
                    status: $signal['date']->lt($today) ? 'overdue' : ($today->diffInDays($signal['date']) <= (int) config('academy.todo.warn_days', 2) ? 'warning' : 'upcoming'),
                    mail: ['code' => 'M11', 'url' => route('admin.academy.mail.compose', ['template' => 'M11', 'context' => 'certificate', 'id' => $certificate->id, 'level' => $signal['level']])]
                ));
            }
        }

        return $tasks;
    }

    protected function task(string $key, string $title, string $where, string $url, string $status, ?Carbon $due = null, ?array $mail = null): array
    {
        return compact('key', 'title', 'where', 'url', 'status', 'due', 'mail');
    }

    /* --------------------------------------------------------------------
     | Routine
     | ------------------------------------------------------------------ */

    /**
     * Fixed routine of today: daily checks, plus Monday and first working day of the month.
     *
     * @return array<int, array{key: string, label: string, group: string, done: bool, done_by: ?string}>
     */
    public function routine(?Carbon $today = null): array
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();

        $definitions = [];
        foreach (config('academy.routine.daily', []) as $key => $label) {
            $definitions['daily'][$key] = $label;
        }
        if ($today->isMonday()) {
            foreach (config('academy.routine.monday', []) as $key => $label) {
                $definitions['monday'][$key] = $label;
            }
        }
        if ($this->workdays->isFirstWorkdayOfMonth($today)) {
            foreach (config('academy.routine.first_workday', []) as $key => $label) {
                $definitions['first_workday'][$key] = $label;
            }
        }

        $checks = AcademyRoutineCheck::with('doneBy')->whereDate('check_date', $today)->get()->keyBy('check_key');

        $routine = [];
        foreach ($definitions as $group => $items) {
            foreach ($items as $key => $label) {
                $check = $checks->get($key);

                $routine[] = [
                    'key' => $key,
                    'label' => $label,
                    'group' => $group,
                    'done' => (bool) $check?->done_at,
                    'done_by' => $check?->doneBy?->name,
                ];
            }
        }

        return $routine;
    }

    public function toggleRoutine(string $key, User $user, ?Carbon $today = null): void
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();

        $known = collect(config('academy.routine', []))->flatMap(fn ($items) => array_keys($items))->all();
        abort_unless(in_array($key, $known, true), 404);

        $check = AcademyRoutineCheck::where('check_date', $today->format('Y-m-d'))->where('check_key', $key)->first();

        if ($check) {
            $check->delete();

            return;
        }

        AcademyRoutineCheck::create([
            'check_date' => $today,
            'check_key' => $key,
            'done_by_user_id' => $user->id,
            'done_at' => now(),
        ]);
    }

    /* --------------------------------------------------------------------
     | Menu badge
     | ------------------------------------------------------------------ */

    public function overdueCount(): int
    {
        // The menu badge must never break a page.
        try {
            return (int) Cache::remember(self::CACHE_KEY, 300, fn () => $this->build()['kpis']['overdue']);
        } catch (\Throwable $exception) {
            \Log::error('Academy overdue count failed: ' . $exception->getMessage());

            return 0;
        }
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
