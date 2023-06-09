<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyClaimRequest;
use App\Http\Requests\StoreClaimRequest;
use App\Http\Requests\UpdateClaimRequest;
use App\Models\Claim;
use App\Models\Company;
use App\Models\ExpertiseOffice;
use App\Models\InjuryOffice;
use App\Models\RecoveryOffice;
use App\Models\Team;
use App\Models\Vehicle;
use App\Models\VehicleOpposite;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class ClaimController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('claim_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $claims = Claim::with(['company', 'injury_office', 'vehicle', 'vehicle_opposite', 'recovery_office', 'expertise_office', 'team', 'media'])->get();

        $companies = Company::get();

        $injury_offices = InjuryOffice::get();

        $vehicles = Vehicle::get();

        $vehicle_opposites = VehicleOpposite::get();

        $recovery_offices = RecoveryOffice::get();

        $expertise_offices = ExpertiseOffice::get();

        $teams = Team::get();

        return view('admin.claims.index', compact('claims', 'companies', 'expertise_offices', 'injury_offices', 'recovery_offices', 'teams', 'vehicle_opposites', 'vehicles'));
    }

    public function create()
    {
        abort_if(Gate::denies('claim_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $isAdmin = auth()->user()->roles->contains(1);

        if($isAdmin) {

            $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        } else {

            dd(auth()->user());

            $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        }

        

        $injury_offices = InjuryOffice::pluck('identifier', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vehicles = Vehicle::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vehicle_opposites = VehicleOpposite::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $recovery_offices = RecoveryOffice::pluck('identifier', 'id')->prepend(trans('global.pleaseSelect'), '');

        $expertise_offices = ExpertiseOffice::pluck('identifier', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.claims.create', compact('companies', 'expertise_offices', 'injury_offices', 'recovery_offices', 'vehicle_opposites', 'vehicles'));
    }

    public function store(StoreClaimRequest $request)
    {
        $claim = Claim::create($request->all());

        foreach ($request->input('damage_files', []) as $file) {
            $claim->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('damage_files');
        }

        foreach ($request->input('report_files', []) as $file) {
            $claim->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('report_files');
        }

        foreach ($request->input('financial_files', []) as $file) {
            $claim->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('financial_files');
        }

        foreach ($request->input('other_files', []) as $file) {
            $claim->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('other_files');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $claim->id]);
        }

        return redirect()->route('admin.claims.index');
    }

    public function edit(Claim $claim)
    {
        abort_if(Gate::denies('claim_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $injury_offices = InjuryOffice::pluck('identifier', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vehicles = Vehicle::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vehicle_opposites = VehicleOpposite::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $recovery_offices = RecoveryOffice::pluck('identifier', 'id')->prepend(trans('global.pleaseSelect'), '');

        $expertise_offices = ExpertiseOffice::pluck('identifier', 'id')->prepend(trans('global.pleaseSelect'), '');

        $claim->load('company', 'injury_office', 'vehicle', 'vehicle_opposite', 'recovery_office', 'expertise_office', 'team');

        return view('admin.claims.edit', compact('claim', 'companies', 'expertise_offices', 'injury_offices', 'recovery_offices', 'vehicle_opposites', 'vehicles'));
    }

    public function update(UpdateClaimRequest $request, Claim $claim)
    {
        $claim->update($request->all());

        if (count($claim->damage_files) > 0) {
            foreach ($claim->damage_files as $media) {
                if (! in_array($media->file_name, $request->input('damage_files', []))) {
                    $media->delete();
                }
            }
        }
        $media = $claim->damage_files->pluck('file_name')->toArray();
        foreach ($request->input('damage_files', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $claim->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('damage_files');
            }
        }

        if (count($claim->report_files) > 0) {
            foreach ($claim->report_files as $media) {
                if (! in_array($media->file_name, $request->input('report_files', []))) {
                    $media->delete();
                }
            }
        }
        $media = $claim->report_files->pluck('file_name')->toArray();
        foreach ($request->input('report_files', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $claim->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('report_files');
            }
        }

        if (count($claim->financial_files) > 0) {
            foreach ($claim->financial_files as $media) {
                if (! in_array($media->file_name, $request->input('financial_files', []))) {
                    $media->delete();
                }
            }
        }
        $media = $claim->financial_files->pluck('file_name')->toArray();
        foreach ($request->input('financial_files', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $claim->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('financial_files');
            }
        }

        if (count($claim->other_files) > 0) {
            foreach ($claim->other_files as $media) {
                if (! in_array($media->file_name, $request->input('other_files', []))) {
                    $media->delete();
                }
            }
        }
        $media = $claim->other_files->pluck('file_name')->toArray();
        foreach ($request->input('other_files', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $claim->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('other_files');
            }
        }

        return redirect()->route('admin.claims.index');
    }

    public function show(Claim $claim)
    {
        abort_if(Gate::denies('claim_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $claim->load('company', 'injury_office', 'vehicle', 'vehicle_opposite', 'recovery_office', 'expertise_office', 'team', 'claimNotes');

        return view('admin.claims.show', compact('claim'));
    }

    public function destroy(Claim $claim)
    {
        abort_if(Gate::denies('claim_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $claim->delete();

        return back();
    }

    public function massDestroy(MassDestroyClaimRequest $request)
    {
        $claims = Claim::find(request('ids'));

        foreach ($claims as $claim) {
            $claim->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('claim_create') && Gate::denies('claim_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Claim();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
