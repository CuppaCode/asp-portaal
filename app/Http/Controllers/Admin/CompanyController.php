<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyCompanyRequest;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\ExpertiseOffice;
use App\Models\InjuryOffice;
use App\Models\RecoveryOffice;
use App\Models\Company;
use App\Models\Team;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('company_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::with(['team'])->get();

        $teams = Team::get();

        return view('admin.companies.index', compact('companies', 'teams'));
    }

    public function create()
    {
        abort_if(Gate::denies('company_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.companies.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        $company = Company::create($request->all());

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $company->id]);
        }

        if ($company->company_type != 'injury' && $company->company_type != 'recovery' && $company->company_type != 'expertise') {

            $team = Team::create([
                'name' => $company->name,
            ]);
    
            $company->team_id = $team->id;
        }
        

        $company->save();
        
        if ($company->company_type == 'injury' || $company->company_type == 'recovery' || $company->company_type == 'expertise') {

            $identifier = str_replace(' ', '_', strtolower($company->company_type . '_' . $company->name));

            $office = [
                'company_id' => $company->id,
                'team_id'    => NULL,
                'identifier' => $identifier
            ];

            switch ($company->company_type) {
                case 'injury':

                    InjuryOffice::create($office);

                    break;

                case 'recovery':

                    RecoveryOffice::create($office);

                    break;

                case 'expertise':

                    ExpertiseOffice::create($office);

                    break;

                default:
                    // This should not happen
                    break;
            }

        }

        return redirect()->route('admin.companies.index');
    }

    public function edit(Company $company)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $company->load('team');

        return view('admin.companies.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $company->update($request->all());

        return redirect()->route('admin.companies.index');
    }

    public function show(Company $company)
    {
        abort_if(Gate::denies('company_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $company->load('team');

        return view('admin.companies.show', compact('company'));
    }

    public function destroy(Company $company)
    {
        abort_if(Gate::denies('company_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $company->delete();

        return back();
    }

    public function massDestroy(MassDestroyCompanyRequest $request)
    {
        $companies = Company::find(request('ids'));

        foreach ($companies as $company) {
            $company->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('company_create') && Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Company();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function quickStore(Request $request)
    {
        $company = Company::create([
            'name'          => $request->name,
            'company_type'  => $request->company_type,
            'street'        => 'x',
            'zipcode'       => 'x',
            'city'          => 'x',
            'country'       => 'x',
            'phone'         => 'x',
            'active'        => true,
            'description'   => null
        ]);

        if ($company->company_type != 'injury' && $company->company_type != 'recovery' && $company->company_type != 'expertise') {

            $team = Team::create([
                'name' => $company->name,
            ]);
    
            $company->team_id = $team->id;
        }

        $company->save();

        $message = 'Bedrijf is succesvol aangemaakt!';
        
        if ($company->company_type == 'injury' || $company->company_type == 'recovery' || $company->company_type == 'expertise') {

            $identifier = str_replace(' ', '_', strtolower($company->company_type . '_' . $company->name));

            $office = [
                'company_id' => $company->id,
                'team_id'    => NULL,
                'identifier' => $identifier
            ];

            switch ($company->company_type) {
                case 'injury':

                    $company = InjuryOffice::create($office);
                    $message = 'Letselbedrijf is succesvol aangemaakt!';

                    break;

                case 'recovery':

                    $company = RecoveryOffice::create($office);
                    $message = 'Herstellerbedrijf is succesvol aangemaakt!';

                    break;

                case 'expertise':

                    $company = ExpertiseOffice::create($office);
                    $message = 'Expertisebureau is succesvol aangemaakt!';

                    break;

                default:
                    // This should not happen
                    break;
            }

        }

        return response()->json(
            [
                'company_id' => $company->id,
                'message' => $message
            ], 200);
    }

}
