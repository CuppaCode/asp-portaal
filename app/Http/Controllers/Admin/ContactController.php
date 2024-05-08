<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\TeamMembersController;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyContactRequest;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Team;
use App\Models\User;
use App\Models\Driver;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('contact_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $contacts = Contact::with(['company', 'user', 'team'])->get();

        $companies = Company::get();

        $users = User::get();

        $teams = Team::get();

        return view('admin.contacts.index', compact('companies', 'contacts', 'teams', 'users'));
    }

    public function create()
    {
        abort_if(Gate::denies('contact_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.contacts.create', compact('companies', 'users'));
    }

    public function store(StoreContactRequest $request)
    {
        $user = auth()->user();
        $canAssignCompany = $user->can('assign_company');

        $contact = Contact::create($request->all());

        if(!$canAssignCompany) {
            
            $contact->company_id = $user->contact->company->id;

        }

        $contact->save();

        if(!isset($contact->team_id)){

            $contact->team_id = Company::find($contact->company_id)->team_id;
            $contact->save();

        }

        if($request->create_user) {

            (new TeamMembersController)->invite($request, $contact);

        }

        if($request->is_driver) {

            Driver::create([
                'team_id' => $contact->team_id,
                'company_id' => $contact->company_id,
                'contact_id' => $contact->id
            ]);

        }

        return redirect()->route('admin.contacts.index');
    }

    public function edit(Contact $contact)
    {
        abort_if(Gate::denies('contact_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $contact->load('company', 'user', 'team');

        return view('admin.contacts.edit', compact('companies', 'contact', 'users'));
    }

    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $contact->update($request->all());

        return redirect()->route('admin.contacts.index');
    }

    public function show(Contact $contact)
    {
        abort_if(Gate::denies('contact_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $contact->load('company', 'user', 'team');

        return view('admin.contacts.show', compact('contact'));
    }

    public function destroy(Contact $contact)
    {
        abort_if(Gate::denies('contact_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $contact->delete();

        return back();
    }

    public function massDestroy(MassDestroyContactRequest $request)
    {
        $contacts = Contact::find(request('ids'));

        foreach ($contacts as $contact) {
            $contact->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
