<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyRecoveryOfficeRequest;
use App\Http\Requests\StoreRecoveryOfficeRequest;
use App\Http\Requests\UpdateRecoveryOfficeRequest;
use App\Models\Company;
use App\Models\RecoveryOffice;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecoveryOfficeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('recovery_office_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $recoveryOffices = RecoveryOffice::with(['company', 'team'])->get();

        return view('admin.recoveryOffices.index', compact('recoveryOffices'));
    }

    public function create()
    {
        abort_if(Gate::denies('recovery_office_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.recoveryOffices.create', compact('companies'));
    }

    public function store(StoreRecoveryOfficeRequest $request)
    {
        $recoveryOffice = RecoveryOffice::create($request->all());

        return redirect()->route('admin.recovery-offices.index');
    }

    public function edit(RecoveryOffice $recoveryOffice)
    {
        abort_if(Gate::denies('recovery_office_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $recoveryOffice->load('company', 'team');

        return view('admin.recoveryOffices.edit', compact('companies', 'recoveryOffice'));
    }

    public function update(UpdateRecoveryOfficeRequest $request, RecoveryOffice $recoveryOffice)
    {
        $recoveryOffice->update($request->all());

        return redirect()->route('admin.recovery-offices.index');
    }

    public function destroy(RecoveryOffice $recoveryOffice)
    {
        abort_if(Gate::denies('recovery_office_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $recoveryOffice->delete();

        return back();
    }

    public function massDestroy(MassDestroyRecoveryOfficeRequest $request)
    {
        $recoveryOffices = RecoveryOffice::find(request('ids'));

        foreach ($recoveryOffices as $recoveryOffice) {
            $recoveryOffice->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
