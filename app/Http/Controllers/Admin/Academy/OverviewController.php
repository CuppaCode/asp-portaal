<?php

namespace App\Http\Controllers\Admin\Academy;

use App\Http\Controllers\Controller;
use App\Models\AcademyChecklistItem;
use App\Models\Certificate;
use App\Models\CertificateRenewal;
use App\Services\Academy\AcademyMonitoringService;
use App\Services\Academy\AcademyTodoService;
use App\Services\Academy\CertificateRenewalHandler;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OverviewController extends Controller
{
    public function __construct(
        protected AcademyTodoService $todo,
        protected AcademyMonitoringService $monitoring
    ) {
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('academy_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tab = $request->input('tab') === 'bewaken' ? 'bewaken' : 'todo';
        $today = Carbon::today();

        if ($tab === 'bewaken') {
            $filter = $request->input('filter') === 'all' ? 'all' : 'action';

            $rows = $this->monitoring->rows($today);
            $anyExpired = $rows->contains('expired', true);

            if ($filter === 'action') {
                $rows = $rows->filter(fn ($row) => $row['needs_action'] || $row['signal_due'])->values();
            }

            $renewalItems = AcademyChecklistItem::where('subject_type', Certificate::class)
                ->where('item_key', 'm12')
                ->whereNull('done_at')
                ->where('not_applicable', false)
                ->get()
                ->map(function (AcademyChecklistItem $item) {
                    $item->setRelation('certificate', Certificate::with('driver.contact', 'driver.company')->find($item->subject_id));

                    return $item;
                })
                ->filter(fn ($item) => $item->certificate);

            return view('admin.academy.overview.index', compact('tab', 'filter', 'rows', 'anyExpired', 'renewalItems', 'today'));
        }

        $data = $this->todo->build($today);

        return view('admin.academy.overview.index', [
            'tab' => $tab,
            'today' => $today,
            'tasks' => $data['tasks'],
            'kpis' => $data['kpis'],
            'routine' => $this->todo->routine($today),
        ]);
    }

    public function toggleRoutine(Request $request)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate(['key' => 'required|string|max:80']);

        $this->todo->toggleRoutine($data['key'], $request->user());

        return redirect()->route('admin.academy.overview');
    }

    /**
     * "Nieuw document": a new expiry date after a renewal. Signals start again, the four-eyes check
     * becomes invalid (observer) and a renewed Code 95 starts a new cycle with an M12 task.
     */
    public function newDocument(Request $request, Certificate $certificate)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $old = $certificate->getRawOriginal('expiry_date');

        $data = $request->validate([
            'new_expiry_date' => 'required|date' . ($old ? '|after:' . $old : ''),
        ], [
            'new_expiry_date.after' => 'De nieuwe vervaldatum moet na de huidige vervaldatum liggen.',
        ]);

        if ($old && empty($certificate->original_expiry_date)) {
            $certificate->original_expiry_date = $old;
        }

        $certificate->expiry_date = $data['new_expiry_date'];
        $certificate->not_applicable = false;
        $certificate->renewed_by_user_id = auth()->id();
        $certificate->renewal_token = null;
        $certificate->renewal_token_expires_at = null;
        $certificate->last_notification_sent_at = null;
        $certificate->save();

        if ($old) {
            CertificateRenewal::create([
                'certificate_id' => $certificate->id,
                'old_expiry_date' => $old,
                'new_expiry_date' => $data['new_expiry_date'],
                'renewed_by_user_id' => auth()->id(),
                'renewal_method' => 'admin_manual',
                'notes' => 'Academy: nieuw document',
            ]);
        }

        app(CertificateRenewalHandler::class)->afterRenewal($certificate);
        AcademyTodoService::forgetCache();

        return redirect()->route('admin.academy.overview', ['tab' => 'bewaken'])
            ->with('message', 'Nieuw document vastgelegd. De signalen beginnen opnieuw en de vierogencontrole moet opnieuw.');
    }
}
