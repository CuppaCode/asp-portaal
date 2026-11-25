<?php

namespace App\Http\Controllers\Admin\Academy;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateCategory;
use App\Models\Code95Course;
use App\Models\Comment;
use App\Models\Driver;
use App\Models\User;
use App\Services\Academy\DriverCardFormService;
use App\Services\Academy\DriverCardService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DriverCardController extends Controller
{
    public function __construct(protected DriverCardService $cards, protected DriverCardFormService $forms)
    {
    }

    /**
     * The card as a form ("Check je papieren") for the driver: known data printed, gaps left open.
     */
    public function form(Driver $driver)
    {
        abort_if(Gate::denies('academy_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response($this->forms->pdf($driver), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $this->forms->fileName($driver) . '"',
        ]);
    }

    public function edit(Driver $driver)
    {
        abort_if(Gate::denies('academy_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $driver->load('contact', 'company', 'checkedBy', 'certificates.metaFields', 'certificates.code95Courses', 'certificates.category');

        $rows = $this->cards->rows($driver);
        $code95Certificate = optional($rows->first(fn ($row) => $row['category']->isCode95()))['certificate'] ?? null;
        $courses = $code95Certificate ? $code95Certificate->code95Courses : collect();

        return view('admin.academy.driver_card.edit', [
            'driver' => $driver,
            'rows' => $rows,
            'missing' => $this->cards->missing($driver),
            'toFillIn' => $this->cards->toFillIn($driver),
            'state' => $this->cards->state($driver),
            'stateLabels' => DriverCardService::STATE_LABELS,
            'courses' => $courses,
            'plannedTrainings' => $this->cards->plannedTrainings($driver),
            'comments' => Comment::where('commentable_type', Driver::class)
                ->where('commentable_id', $driver->id)
                ->with('user')
                ->latest()
                ->get(),
            'checkers' => User::whereKeyNot(auth()->id())->orderBy('name')->get(['id', 'name']),
            'backUrl' => request('back_to'),
        ]);
    }

    public function update(Request $request, Driver $driver)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'birthdate' => 'nullable|date|before:today',
            'driver_license_id' => 'nullable|string|max:50',
            'function' => 'nullable|string|max:255',
            'in_service' => 'nullable|boolean',
            'street' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'cbr_customer_number' => 'nullable|string|max:50',
            'driver_pass_number' => 'nullable|string|max:50',
            'scans_attached' => 'nullable|boolean',
            'license_categories' => 'nullable|string|max:50',
            'code95_hours' => 'nullable|integer|min:0|max:1000',
            'code95_practice_hours' => 'nullable|integer|min:0|max:1000|lte:code95_hours',
            'certificates' => 'nullable|array',
            'certificates.*.expiry_date' => 'nullable|date',
            'certificates.*.issue_date' => 'nullable|date|before_or_equal:today',
            'certificates.*.not_applicable' => 'nullable|boolean',
        ]);

        $data['in_service'] = $request->boolean('in_service');
        $data['scans_attached'] = $request->boolean('scans_attached');

        $this->cards->save($driver, $data);

        return redirect()
            ->route('admin.academy.driver-cards.edit', ['driver' => $driver->id, 'back_to' => $request->input('back_to')])
            ->with('message', 'Chauffeurskaart opgeslagen.');
    }

    public function storeCourse(Request $request, Driver $driver)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'course_date' => 'required|date|before_or_equal:today',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:theory,practice',
            'hours' => 'required|integer|min:1|max:35',
            'valid_until' => 'nullable|date|after_or_equal:course_date',
            'trainer' => 'nullable|string|max:255',
            'cbr_registered' => 'nullable|boolean',
        ]);

        $category = CertificateCategory::where('code', 'code_95')->firstOrFail();
        $certificate = Certificate::where('driver_id', $driver->id)
            ->where('category_id', $category->id)
            ->orderByDesc('expiry_date')
            ->first();

        if (!$certificate) {
            $certificate = Certificate::create([
                'driver_id' => $driver->id,
                'category_id' => $category->id,
                'name' => $category->name,
                'team_id' => $driver->team_id ?? auth()->user()->team_id,
            ]);
        }

        Code95Course::create([
            'certificate_id' => $certificate->id,
            'course_date' => $data['course_date'],
            'name' => $data['name'],
            'code' => $data['code'] ?? '',
            'type' => $data['type'],
            'hours' => $data['hours'],
            'valid_until' => $data['valid_until'] ?? null,
            'trainer' => $data['trainer'] ?? null,
            'cbr_registered' => $request->boolean('cbr_registered'),
            'team_id' => auth()->user()->team_id,
        ]);

        return redirect()
            ->route('admin.academy.driver-cards.edit', ['driver' => $driver->id, 'back_to' => $request->input('back_to')])
            ->with('message', 'Training toegevoegd.');
    }

    public function destroyCourse(Request $request, Driver $driver, Code95Course $course)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_unless($course->certificate && $course->certificate->driver_id === $driver->id, Response::HTTP_NOT_FOUND);

        $course->delete();

        return redirect()
            ->route('admin.academy.driver-cards.edit', ['driver' => $driver->id, 'back_to' => $request->input('back_to')])
            ->with('message', 'Training verwijderd.');
    }

    public function check(Request $request, Driver $driver)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'checked_by_user_id' => 'required|integer|exists:users,id',
            'dates_confirmed' => 'accepted',
        ], [
            'checked_by_user_id.required' => 'Kies de tweede persoon die de kaart heeft gecontroleerd.',
            'dates_confirmed.accepted' => 'Bevestig dat alle datums kloppen met de originele documenten.',
        ]);

        $this->cards->check($driver, User::findOrFail($data['checked_by_user_id']), $request->user());

        return redirect()
            ->route('admin.academy.driver-cards.edit', ['driver' => $driver->id, 'back_to' => $request->input('back_to')])
            ->with('message', 'Vierogencontrole vastgelegd.');
    }

    public function comment(Request $request, Driver $driver)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        Comment::create([
            'body' => $data['body'],
            'commentable_id' => $driver->id,
            'commentable_type' => Driver::class,
            'user_id' => auth()->id(),
            'team_id' => $driver->team_id,
        ]);

        return redirect()
            ->route('admin.academy.driver-cards.edit', ['driver' => $driver->id, 'back_to' => $request->input('back_to')])
            ->with('message', 'Opmerking toegevoegd.');
    }
}
