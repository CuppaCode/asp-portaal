<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyExpertiseOfficeRequest;
use App\Http\Requests\StoreExpertiseOfficeRequest;
use App\Http\Requests\UpdateExpertiseOfficeRequest;
use App\Models\Company;
use App\Models\ExpertiseOffice;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpertiseOfficeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('expertise_office_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expertiseOffices = ExpertiseOffice::with(['company', 'team'])->get();

        return view('admin.expertiseOffices.index', compact('expertiseOffices'));
    }

    public function create()
    {
        abort_if(Gate::denies('expertise_office_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.expertiseOffices.create', compact('companies'));
    }

    public function store(StoreExpertiseOfficeRequest $request)
    {
        $expertiseOffice = ExpertiseOffice::create($request->all());

        return redirect()->route('admin.expertise-offices.index');
    }

    public function edit(ExpertiseOffice $expertiseOffice)
    {
        abort_if(Gate::denies('expertise_office_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $expertiseOffice->load('company', 'team');

        return view('admin.expertiseOffices.edit', compact('companies', 'expertiseOffice'));
    }

    public function update(UpdateExpertiseOfficeRequest $request, ExpertiseOffice $expertiseOffice)
    {
        $expertiseOffice->update($request->all());

        return redirect()->route('admin.expertise-offices.index');
    }

    public function destroy(ExpertiseOffice $expertiseOffice)
    {
        abort_if(Gate::denies('expertise_office_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expertiseOffice->delete();

        return back();
    }

    public function massDestroy(MassDestroyExpertiseOfficeRequest $request)
    {
        $expertiseOffices = ExpertiseOffice::find(request('ids'));

        foreach ($expertiseOffices as $expertiseOffice) {
            $expertiseOffice->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
