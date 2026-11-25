<?php

namespace App\Http\Controllers\Admin\Academy;

use App\Http\Controllers\Controller;
use App\Models\AcademyChecklistItem;
use App\Services\Academy\AcademyChecklistService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChecklistItemController extends Controller
{
    public function __construct(protected AcademyChecklistService $checklist)
    {
    }

    public function toggle(Request $request, AcademyChecklistItem $item)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->checklist->markDone($item, $request->user());

        return redirect()->back()->with('message', 'Afgevinkt.');
    }

    public function notApplicable(Request $request, AcademyChecklistItem $item)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate(['reason' => 'nullable|string|max:255']);

        $this->checklist->markNotApplicable($item, $request->user(), $request->input('reason'));

        return redirect()->back()->with('message', 'Regel is n.v.t.');
    }

    public function undo(Request $request, AcademyChecklistItem $item)
    {
        abort_if(Gate::denies('academy_manage'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->checklist->undo($item);

        return redirect()->back()->with('message', 'Regel teruggezet.');
    }
}
