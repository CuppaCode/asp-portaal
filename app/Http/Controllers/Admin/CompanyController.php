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
use App\Models\Contact;
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

        $contacts = Contact::where('is_driver', 0)->get();

        return view('admin.companies.create', compact('contacts'));
    }

    public function store(StoreCompanyRequest $request)
    {
        $data = $request->all();
        foreach(['start_fee', 'claims_fee', 'additional_costs'] as $field) {
            if(isset($data[$field])) {
                $data[$field] = str_replace(',', '.', $data[$field]);
            }
        }
        $company = Company::create($data);

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

        $contacts = Contact::where('is_driver', 0)->get();
        $company->load('team');

        return view('admin.companies.edit', compact('company', 'contacts'));
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $data = $request->all();
        foreach(['start_fee', 'claims_fee', 'additional_costs'] as $field) {
            if(isset($data[$field])) {
                $data[$field] = str_replace(',', '.', $data[$field]);
            }
        }
        $company->update($data);

        return redirect()->route('admin.companies.index');
    }

    public function show(Company $company)
    {
        abort_if(Gate::denies('company_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $company->load(['team', 'sla']);
        $contact = Contact::where('id', $company->contact_id)->first();

        // Extra fields
        $bankAccountNumber = $company->bank_account_number;
        $companySize = $company->company_size;
        $truckCount = $company->truck_count;
        $additionalInformation = $company->additional_information;

        // Statistics
        $openStatuses = array_keys(\App\Models\Claim::STATUS_SELECT);
        $openStatuses = array_filter($openStatuses, fn($status) => $status !== 'finished' && $status !== 'claim_denied');

        $openClaims = \App\Models\Claim::where('company_id', $company->id)
            ->whereIn('status', $openStatuses)
            ->count();

        $closedClaims = \App\Models\Claim::where('company_id', $company->id)
            ->where('status', 'finished')
            ->count();

        $closedClaimsThisYear = \App\Models\Claim::where('company_id', $company->id)
            ->where('status', 'finished')
            ->whereYear('closed_at', now()->year)
            ->count();

        $driverCount = \App\Models\Driver::where('company_id', $company->id)->count();

        // Claims list
        $claims = \App\Models\Claim::where('company_id', $company->id)
            ->orderByDesc('created_at')
            ->get();

        // Drivers list
        $drivers = \App\Models\Driver::where('company_id', $company->id)
            ->with('contact')
            ->get();

        // Attachments (using Spatie MediaLibrary)
        $attachments = $company->getMedia('attachments');

        $sla = $company->sla;
        return view('admin.companies.show', compact(
            'company',
            'contact',
            'bankAccountNumber',
            'companySize',
            'truckCount',
            'additionalInformation',
            'openClaims',
            'closedClaims',
            'closedClaimsThisYear',
            'driverCount',
            'claims',
            'drivers',
            'attachments',
            'sla'
        ));
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
                'type' => 'alert-success',
                'message' => $message
            ], 200);
    }

}
