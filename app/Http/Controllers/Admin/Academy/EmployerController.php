<?php

namespace App\Http\Controllers\Admin\Academy;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Driver;
use App\Services\Academy\AcademyChecklistService;
use App\Services\Academy\DriverCardFormService;
use App\Services\Academy\DriverCardService;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployerController extends Controller
{
    public function __construct(
        protected AcademyChecklistService $checklist,
        protected DriverCardService $cards,
        protected DriverCardFormService $forms
    ) {
    }

    /**
     * One PDF with a form page per driver: ?filter=gaps (default), no_email or all.
     */
    public function forms(Request $request, Company $company)
    {
        abort_if(Gate::denies('academy_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_unless($company->academy_enabled, Response::HTTP_NOT_FOUND);

        $filter = in_array($request->input('filter'), ['no_email', 'all'], true) ? $request->input('filter') : 'gaps';

        $drivers = $this->checklist->companyDrivers($company)
            ->sortBy(fn (Driver $driver) => $driver->contact?->last_name)
            ->filter(fn (Driver $driver) => match ($filter) {
                'no_email' => empty(trim((string) $driver->contact?->email)),
                'all' => true,
                default => $this->forms->hasGaps($driver),
            })
            ->values();

        if ($drivers->isEmpty()) {
            return redirect()->back()->with('message', 'Er zijn geen chauffeurs voor dit formulier.');
        }

        return response($this->forms->pdf($drivers), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $this->forms->fileName($drivers) . '"',
        ]);
    }

    public function index()
    {
        abort_if(Gate::denies('academy_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employers = Company::academy()
            ->with('contactPerson')
            ->orderBy('name')
            ->get()
            ->map(function (Company $company) {
                $steps = $this->checklist->steps($company, [1, 2, 3]);
                $items = collect($steps)->flatMap(fn ($step) => $step['items']);
                $drivers = $this->checklist->companyDrivers($company);

                return [
                    'company' => $company,
                    'steps' => $steps,
                    'driver_count' => $drivers->count(),
                    'incomplete_cards' => $drivers->filter(fn (Driver $driver) => !$this->cards->isFinal($driver))->count(),
                    'overdue' => $items->where('overdue', true)->count(),
                    'open' => $items->filter(fn ($item) => $item['model']->isOpen() && !$item['locked'])->count(),
                ];
            });

        $availableCompanies = Company::where(function ($query) {
            $query->where('academy_enabled', false)->orWhereNull('academy_enabled');
        })->orderBy('name')->get(['id', 'name']);

        return view('admin.academy.employers.index', compact('employers', 'availableCompanies'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'academy_started_at' => 'nullable|date',
        ]);

        $company = Company::findOrFail($data['company_id']);
        $company->update([
            'academy_enabled' => true,
            'academy_started_at' => $data['academy_started_at'] ?? Carbon::today(),
            'academy_completed_at' => null,
        ]);

        $this->checklist->refresh($company);

        return redirect()->route('admin.academy.employers.show', $company->id)
            ->with('message', $company->name . ' is toegevoegd aan de Academy.');
    }

    public function show(Company $company)
    {
        abort_if(Gate::denies('academy_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_unless($company->academy_enabled, Response::HTTP_NOT_FOUND);

        $company->load('contactPerson');

        $steps = $this->checklist->steps($company, [1, 2, 3]);

        $drivers = $this->checklist->companyDrivers($company)->sortBy(fn (Driver $driver) => $driver->contact?->last_name);
        $driverRows = $drivers->map(function (Driver $driver) {
            $rows = $this->cards->rows($driver)->keyBy(fn ($row) => $row['category']->code);

            return [
                'driver' => $driver,
                'state' => $this->cards->state($driver),
                'missing' => $this->cards->missing($driver),
                'rows' => $rows,
                'planned' => $this->cards->plannedTrainings($driver)->count(),
            ];
        });

        $formerDrivers = Driver::with('contact')
            ->where('company_id', $company->id)
            ->where('in_service', false)
            ->get();

        $trainings = $company->academyTrainings()->orderByDesc('training_date')->get();

        return view('admin.academy.employers.show', [
            'company' => $company,
            'steps' => $steps,
            'driverRows' => $driverRows,
            'formerDrivers' => $formerDrivers,
            'trainings' => $trainings,
            'stateLabels' => DriverCardService::STATE_LABELS,
            'mailContext' => 'employer',
        ]);
    }

    public function toggle(Request $request, Company $company)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->has('disable')) {
            $company->update(['academy_enabled' => false]);

            return redirect()->route('admin.academy.employers.index')
                ->with('message', $company->name . ' is uit de Academy gehaald.');
        }

        $data = $request->validate(['academy_started_at' => 'required|date']);
        $company->update(['academy_started_at' => $data['academy_started_at']]);
        $this->checklist->refresh($company);

        return redirect()->back()->with('message', 'Startdatum bijgewerkt.');
    }

    /**
     * Add a driver from the names list (stap 1/2).
     */
    public function storeDriver(Request $request, Company $company)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'function' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $teamId = $company->team_id ?? auth()->user()->team_id;

        $contact = Contact::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'] ?? '',
            'company_id' => $company->id,
            'team_id' => $teamId,
            'is_driver' => true,
            'create_user' => 0,
        ]);

        Driver::create([
            'contact_id' => $contact->id,
            'company_id' => $company->id,
            'team_id' => $teamId,
            'function' => $data['function'] ?? null,
        ]);

        return redirect()->back()->with('message', 'Chauffeur ' . $data['first_name'] . ' ' . $data['last_name'] . ' is toegevoegd.');
    }
}
