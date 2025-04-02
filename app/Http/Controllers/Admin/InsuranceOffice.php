<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyInsuranceOfficeRequest;
use App\Http\Requests\StoreInsuranceOfficeRequest;
use App\Http\Requests\UpdateInsuranceOfficeRequest;
use App\Models\Company;
use App\Models\InsuranceOffice;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InsuranceOfficeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('insurance_office_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $insuranceOffices = InsuranceOffice::with(['company', 'team'])->get();

        return view('admin.insuranceOffices.index', compact('insuranceOffices'));
    }

    public function create()
    {
        abort_if(Gate::denies('insurance_office_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.insuranceOffices.create', compact('companies'));
    }

    public function store(StoreInsuranceOfficeRequest $request)
    {
        $insuranceOffice = InsuranceOffice::create($request->all());

        return redirect()->route('admin.insurance-offices.index');
    }

    public function edit(InsuranceOffice $insuranceOffice)
    {
        abort_if(Gate::denies('insurance_office_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $insuranceOffice->load('company', 'team');

        return view('admin.insuranceOffices.edit', compact('companies', 'insuranceOffice'));
    }

    public function update(UpdateInsuranceOfficeRequest $request, InsuranceOffice $insuranceOffice)
    {
        $insuranceOffice->update($request->all());

        return redirect()->route('admin.insurance-offices.index');
    }

    public function destroy(InsuranceOffice $insuranceOffice)
    {
        abort_if(Gate::denies('insurance_office_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $insuranceOffice->delete();

        return back();
    }

    public function massDestroy(MassDestroyInsuranceOfficeRequest $request)
    {
        $insuranceOffices = InsuranceOffice::find(request('ids'));

        foreach ($insuranceOffices as $insuranceOffice) {
            $insuranceOffice->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}


