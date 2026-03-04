<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyMailingRequest;
use App\Http\Requests\StoreMailingRequest;
use App\Http\Requests\UpdateMailingRequest;
use App\Models\Claim;
use App\Models\Mailing;
use App\Models\MailTemplate;
use App\Models\Team;
use App\Models\User;
use App\Services\MailTriggerService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MailingController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('mailing_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $mailings = Mailing::with(['user', 'team', 'mailTemplate', 'claims'])->get();

        return view('admin.mailings.index', compact('mailings'));
    }

    public function create()
    {
        abort_if(Gate::denies('mailing_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $teams = Team::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $mailTemplates = MailTemplate::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $claims = Claim::pluck('claim_number', 'id');

        return view('admin.mailings.create', compact('users', 'teams', 'mailTemplates', 'claims'));
    }

    public function store(StoreMailingRequest $request)
    {
        $mailing = Mailing::create($request->all());
        $mailing->claims()->sync($request->input('claims', []));

        return redirect()->route('admin.mailings.index');
    }

    public function edit(Mailing $mailing)
    {
        abort_if(Gate::denies('mailing_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $teams = Team::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $mailTemplates = MailTemplate::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $claims = Claim::pluck('claim_number', 'id');

        $mailing->load('user', 'team', 'mailTemplate', 'claims');

        return view('admin.mailings.edit', compact('users', 'teams', 'mailTemplates', 'claims', 'mailing'));
    }

    public function update(UpdateMailingRequest $request, Mailing $mailing)
    {
        $mailing->update($request->all());
        $mailing->claims()->sync($request->input('claims', []));

        return redirect()->route('admin.mailings.index');
    }

    public function show(Mailing $mailing)
    {
        abort_if(Gate::denies('mailing_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $mailing->load('user', 'team', 'mailTemplate', 'claims');

        return view('admin.mailings.show', compact('mailing'));
    }

    public function destroy(Mailing $mailing)
    {
        abort_if(Gate::denies('mailing_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $mailing->delete();

        return back();
    }

    public function massDestroy(MassDestroyMailingRequest $request)
    {
        $mailings = Mailing::find(request('ids'));

        foreach ($mailings as $mailing) {
            $mailing->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Send a mailing
     */
    public function send(Request $request, Mailing $mailing)
    {
        abort_if(Gate::denies('mailing_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $service = new MailTriggerService();
        $result = $service->sendMailing($mailing->id);

        if ($result) {
            return redirect()->route('admin.mailings.index')->with('message', 'Mailing sent successfully');
        }

        return redirect()->route('admin.mailings.index')->with('error', 'Failed to send mailing');
    }

    /**
     * Send multiple mailings
     */
    public function sendBatch(Request $request)
    {
        abort_if(Gate::denies('mailing_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $mailingIds = $request->input('ids', []);
        $service = new MailTriggerService();
        $results = $service->sendBatch($mailingIds);

        return response()->json($results);
    }
}
