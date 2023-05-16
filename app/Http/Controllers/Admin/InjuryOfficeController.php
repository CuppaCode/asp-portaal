<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyInjuryOfficeRequest;
use App\Http\Requests\StoreInjuryOfficeRequest;
use App\Http\Requests\UpdateInjuryOfficeRequest;
use App\Models\Company;
use App\Models\InjuryOffice;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjuryOfficeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('injury_office_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $injuryOffices = InjuryOffice::with(['company', 'team'])->get();

        return view('admin.injuryOffices.index', compact('injuryOffices'));
    }

    public function create()
    {
        abort_if(Gate::denies('injury_office_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.injuryOffices.create', compact('companies'));
    }

    public function store(StoreInjuryOfficeRequest $request)
    {
        $injuryOffice = InjuryOffice::create($request->all());

        return redirect()->route('admin.injury-offices.index');
    }

    public function edit(InjuryOffice $injuryOffice)
    {
        abort_if(Gate::denies('injury_office_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $injuryOffice->load('company', 'team');

        return view('admin.injuryOffices.edit', compact('companies', 'injuryOffice'));
    }

    public function update(UpdateInjuryOfficeRequest $request, InjuryOffice $injuryOffice)
    {
        $injuryOffice->update($request->all());

        return redirect()->route('admin.injury-offices.index');
    }

    public function destroy(InjuryOffice $injuryOffice)
    {
        abort_if(Gate::denies('injury_office_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $injuryOffice->delete();

        return back();
    }

    public function massDestroy(MassDestroyInjuryOfficeRequest $request)
    {
        $injuryOffices = InjuryOffice::find(request('ids'));

        foreach ($injuryOffices as $injuryOffice) {
            $injuryOffice->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
