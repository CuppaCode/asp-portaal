<?php

namespace App\Http\Controllers\Admin\Academy;

use App\Http\Controllers\Controller;
use App\Models\AcademyTraining;
use App\Models\AcademyTrainingParticipant;
use App\Models\Company;
use App\Models\Driver;
use App\Services\Academy\AcademyChecklistService;
use App\Services\Academy\AcademyTrainingService;
use App\Services\Academy\DriverCardService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrainingController extends Controller
{
    public function __construct(
        protected AcademyTrainingService $trainings,
        protected AcademyChecklistService $checklist,
        protected DriverCardService $cards
    ) {
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('academy_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $status = $request->input('status', 'open');

        $trainings = AcademyTraining::with('company')
            ->withCount('participants')
            ->when($status === 'closed', fn ($query) => $query->whereNotNull('closed_at'))
            ->when($status !== 'closed', fn ($query) => $query->whereNull('closed_at'))
            ->orderBy('training_date', $status === 'closed' ? 'desc' : 'asc')
            ->get()
            ->map(function (AcademyTraining $training) {
                $steps = $this->checklist->steps($training, [4, 5, 6]);
                $overdue = collect($steps)->flatMap(fn ($step) => $step['items'])->where('overdue', true)->count();

                return ['training' => $training, 'steps' => $steps, 'overdue' => $overdue];
            });

        return view('admin.academy.trainings.index', compact('trainings', 'status'));
    }

    public function create(Request $request)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::academy()->orderBy('name')->get(['id', 'name']);
        $companyId = $request->input('company_id');
        $drivers = $companyId
            ? Driver::with('contact')->where('company_id', $companyId)->where('in_service', true)->get()
                ->sortBy(fn (Driver $driver) => $driver->contact?->last_name)
            : collect();

        return view('admin.academy.trainings.create', compact('companies', 'companyId', 'drivers'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $this->validated($request);
        $company = Company::academy()->findOrFail($data['company_id']);

        $training = AcademyTraining::create(array_merge($data, [
            'team_id' => $company->team_id ?? auth()->user()->team_id,
        ]));

        $this->trainings->addParticipants($training, $request->input('drivers', []));
        $this->checklist->refresh($training);

        return redirect()->route('admin.academy.trainings.show', $training->id)
            ->with('message', 'Trainingsdossier aangemaakt.');
    }

    public function show(AcademyTraining $training)
    {
        abort_if(Gate::denies('academy_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $training->load('company.contactPerson');

        $steps = $this->checklist->steps($training, [4, 5, 6]);
        $participants = $training->participants()->with('driver.contact', 'course')->get()
            ->sortBy(fn ($participant) => $participant->driver?->contact?->last_name);

        $available = Driver::with('contact')
            ->where('company_id', $training->company_id)
            ->where('in_service', true)
            ->whereNotIn('id', $participants->pluck('driver_id'))
            ->get()
            ->sortBy(fn (Driver $driver) => $driver->contact?->last_name);

        return view('admin.academy.trainings.show', [
            'training' => $training,
            'steps' => $steps,
            'participants' => $participants,
            'available' => $available,
            'withoutBirthdate' => $this->trainings->participantsWithoutBirthdate($training),
            'closeBlockers' => $training->isClosed() ? [] : $this->trainings->closeBlockers($training),
            'mailContext' => 'training',
        ]);
    }

    public function update(Request $request, AcademyTraining $training)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $this->validated($request);
        unset($data['company_id']);

        $training->update($data);
        $this->checklist->refresh($training);

        return redirect()->back()->with('message', 'Trainingsdossier bijgewerkt.');
    }

    public function addParticipants(Request $request, AcademyTraining $training)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate(['drivers' => 'required|array|min:1', 'drivers.*' => 'integer']);

        $added = $this->trainings->addParticipants($training, $request->input('drivers'));

        return redirect()->back()->with('message', $added . ' deelnemer(s) toegevoegd.');
    }

    public function newParticipant(Request $request, AcademyTraining $training)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthdate' => 'required|date|before:today',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'function' => 'nullable|string|max:255',
        ]);

        $this->trainings->createParticipant($training, $data);

        return redirect()->back()->with('message', 'Deelnemer is aangemaakt en toegevoegd aan het dossier.');
    }

    public function updateParticipant(Request $request, AcademyTraining $training, AcademyTrainingParticipant $participant)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_unless($participant->training_id === $training->id, Response::HTTP_NOT_FOUND);

        $request->validate([
            'attended' => 'nullable|boolean',
            'confirm_hours' => 'nullable|boolean',
        ]);

        if ($request->has('attended')) {
            $this->trainings->setAttendance($training, $participant, $request->boolean('attended'));
        }

        if ($request->boolean('confirm_hours')) {
            $this->trainings->confirmHours($training, $participant->fresh());
        } elseif ($request->has('confirm_hours')) {
            $this->trainings->unconfirmHours($participant->fresh());
        }

        $this->checklist->refresh($training);

        return redirect()->back();
    }

    public function removeParticipant(AcademyTraining $training, AcademyTrainingParticipant $participant)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_unless($participant->training_id === $training->id, Response::HTTP_NOT_FOUND);

        $this->trainings->removeParticipant($training, $participant);

        return redirect()->back()->with('message', 'Deelnemer verwijderd.');
    }

    public function close(AcademyTraining $training)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->trainings->close($training);

        return redirect()->route('admin.academy.trainings.index')->with('message', 'Dossier gesloten.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:255',
            'cbr_code' => 'nullable|string|max:50',
            'training_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'provider_type' => 'required|in:asp,partner',
            'provider_name' => 'nullable|string|max:255',
            'provider_email' => 'nullable|email|max:255',
            'code95_hours' => 'nullable|integer|min:1|max:35',
            'code95_type' => 'nullable|required_with:code95_hours|in:theory,practice',
            'is_prevention' => 'nullable|boolean',
            'notes' => 'nullable|string|max:2000',
        ]);

        $data['is_prevention'] = $request->boolean('is_prevention');

        if (empty($data['code95_hours'])) {
            $data['code95_hours'] = null;
            $data['code95_type'] = null;
        }

        return $data;
    }
}
