<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyClaimRequest;
use App\Http\Requests\StoreClaimRequest;
use App\Http\Requests\UpdateClaimRequest;
use App\Models\Claim;
use App\Models\Company;
use App\Models\Contact;
use App\Models\ExpertiseOffice;
use App\Models\InjuryOffice;
use App\Models\RecoveryOffice;
use App\Models\Team;
use App\Models\Vehicle;
use App\Models\VehicleOpposite;
use App\Models\Driver;
use App\Models\Opposite;
use App\Models\User;
use App\Models\MailTemplate;
use App\Models\Note;
use App\Models\SLA;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;


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

    public function open()
    {
        abort_if(Gate::denies('claim_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $claims = Claim::with(['company', 'injury_office', 'vehicle', 'vehicle_opposite', 'recovery_office', 'expertise_office', 'team', 'media'])->WhereNot('status', 'finished')->get();

        $companies = Company::get();
        $contacts = Contact::get(); 
        $opposite = Opposite::get();   

        return view('admin.claims.index', compact('claims', 'companies', 'contacts', 'opposite'));
    }
    
    public function unassigned()
    {
        abort_if(Gate::denies('claim_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $claims = Claim::with(['company', 'injury_office', 'vehicle', 'vehicle_opposite', 'recovery_office', 'expertise_office', 'team', 'media'])->Where('assignee_id', null)->WhereNot('status', 'finished')->get();

        $companies = Company::get();

        return view('admin.claims.index', compact('claims', 'companies'));
    }

    public function closed()
    {
        abort_if(Gate::denies('claim_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $claims = Claim::with(['company', 'injury_office', 'vehicle', 'vehicle_opposite', 'recovery_office', 'expertise_office', 'team', 'media'])->Where('status', 'finished')->get();

        $companies = Company::get();

        return view('admin.claims.index', compact('claims', 'companies'));
    }

    public function create()
    {
        abort_if(Gate::denies('claim_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = auth()->user();
        $isAdminOrAgent = $user->isAdminOrAgent();

        $companies = Company::where('active', 1)->where('company_type', 'salvage')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        
        $injury_offices = InjuryOffice::with('company')->get()->pluck('company.name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $recovery_offices = RecoveryOffice::with('company')->get()->pluck('company.name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $expertise_offices = ExpertiseOffice::with('company')->get()->pluck('company.name', 'id')->prepend(trans('global.pleaseSelect'), '');
        
        $vehicles = Vehicle::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vehicle_opposites = VehicleOpposite::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        if($isAdminOrAgent) {

            $drivers = Driver::with('contact', 'company')->get()->pluck('driver_full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        } else {

            $drivers = Driver::where('team_id', $user->team_id)->with('contact', 'company')->get()->pluck('driver_full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        }


        return view('admin.claims.create', compact('companies', 'expertise_offices', 'injury_offices', 'recovery_offices', 'vehicle_opposites', 'vehicles', 'drivers'));
    }

    public function store(StoreClaimRequest $request)
    {

        /* Custom bit */
        $user = auth()->user();
        $isAdminOrAgent = $user->isAdminOrAgent();

        $multiSelects = ['damaged_area', 'damaged_part', 'damage_origin', 'damaged_part_opposite', 'damage_origin_opposite', 'damaged_area_opposite'];
        
        $claim = Claim::create($request->except($multiSelects));

        $claim->damaged_area = $request->input('damaged_area') ? json_encode($request->input('damaged_area')) : null;
        $claim->damaged_part = $request->input('damaged_part') ? json_encode($request->input('damaged_part')) : null;
        $claim->damage_origin = $request->input('damage_origin') ? json_encode($request->input('damage_origin')) : null;
        $claim->damaged_part_opposite = $request->input('damaged_part_opposite') ? json_encode($request->input('damaged_part_opposite')) : null;
        $claim->damage_origin_opposite = $request->input('damage_origin_opposite') ? json_encode($request->input('damage_origin_opposite')) : null;
        $claim->damaged_area_opposite = $request->input('damaged_area_opposite') ? json_encode($request->input('damaged_area_opposite')) : null;

        $claim->claim_number = date('Y').'-'.str_pad(($claim->id + 99), 5, 0, STR_PAD_LEFT);

        Opposite::create([
            'name'          => $request->op_name,
            'street'        => $request->op_street,
            'zipcode'       => $request->op_zipcode,
            'city'          => $request->op_city,
            'country'       => $request->op_country,
            'phone'         => $request->op_phone,
            'email'         => $request->op_email,
            'claim_id'      => $claim->id,
        ]);
        
        if(!$isAdminOrAgent) {
            
            $claim->company_id = $user->contact->company->id;

        }

        $companyId = $claim->company_id;

        $claim->status = 'new';

        // Standard values claim creation
        $claim->injury = 'no';
        $claim->recoverable_claim = 'no';

        $company = Company::where('id', $companyId)->first();

        $team_id = $company->team_id;
        
        // This doesn't work because of the Multitenanti trait.
        //$claim->team_id = $team_id;

        if(isset($request->vehicle_plates)){
            $vehicle = Vehicle::where('plates', $request->vehicle_plates)->first();

            if(!isset($vehicle)) {

                $vehicleName = 'Voertuig met kenteken: ' . $request->vehicle_plates;

                
                $vehicle = Vehicle::create([
                    'name' => $vehicleName,
                    'plates' => $request->vehicle_plates,
                    'company_id' => $companyId,
                    'team_id' => $team_id
                ]);

            }

            $claim->vehicle_id = $vehicle->id;
        }

        if(isset($request->vehicle_plates_opposite)){

            $vehicleOpposite = VehicleOpposite::where('plates', $request->vehicle_plates_opposite)->first();

            if(!isset($vehicleOpposite)) {

                $vehicleName = 'Voertuig met kenteken: ' . $request->vehicle_plates_opposite;

                
                $vehicleOpposite = VehicleOpposite::create([
                    'name' => $vehicleName,
                    'plates' => $request->vehicle_plates_opposite,
                    'team_id' => $team_id
                ]);

            }

            $claim->vehicle_opposite_id = $vehicleOpposite->id;
        
        }

        $claim->save();
        /* end custom bit */


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

        $message = new \App\Notifications\ClaimCreation($claim, $user);
        Notification::route('mail', [
            'patrick@autoschadeplan.nl' => 'Patrick'])->notify($message);


        if ($claim->assign_self == 1 || auth()->user()->roles->doesntContain(2) == true) {
            return redirect()->route('admin.claims.edit', $claim->id)->with('message', 'Schadedossier: Stap 1 voltooid');
    }
        else {
            return redirect()->route('admin.claims.index')->with('message', 'Schadedossier: Stap 1 voltooid');
        }
    }

    public function edit(Claim $claim)
    {
        abort_if(Gate::denies('claim_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = auth()->user();
        $isAdminOrAgent = $user->isAdminOrAgent();

        abort_if(!$isAdminOrAgent && !$claim->assign_self, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $injury_offices = InjuryOffice::with('company')->get()->pluck('company.name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vehicle = Vehicle::where('company_id', $claim->company->id)->pluck('plates', 'plates')->prepend(trans('global.pleaseSelect'), '');

        $vehicle_opposite = VehicleOpposite::pluck('plates', 'id')->prepend(trans('global.pleaseSelect'), '');

        $recovery_offices = RecoveryOffice::with('company')->get()->pluck('company.name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $expertise_offices = ExpertiseOffice::with('company')->get()->pluck('company.name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $opposite = Opposite::where('claim_id', $claim->id)->get()->first();

        $assignee_options = User::where('team_id', $claim->team->id)->orWhere('team_id', 1)->get();

        $drivers = Driver::where('team_id', $claim->company->team_id)->with('contact', 'company')->get()->pluck('driver_full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $claim->load('company', 'injury_office', 'vehicle', 'vehicle_opposite', 'recovery_office', 'expertise_office', 'team');

        return view('admin.claims.edit', compact('claim', 'companies', 'expertise_offices', 'injury_offices', 'recovery_offices', 'vehicle_opposite', 'vehicle', 'drivers', 'opposite', 'assignee_options'));
    }

    public function update(UpdateClaimRequest $request, Claim $claim)
    {
        $user = auth()->user();
        $isAdminOrAgent = $user->isAdminOrAgent();
        $companies = null;
        
        if(!$isAdminOrAgent) {
            
            $claim->company_id = $user->contact->company->id;
            
        }
        
        $companyId = $claim->company_id;
        
        $company = Company::where('id', $companyId)->first();
        
        $team_id = $company->team_id;
        
        if(isset($request->vehicle_plates)){
            $vehicle = Vehicle::where('plates', $request->vehicle_plates)->first();
            
            if(!isset($vehicle)) {
                
                $vehicleName = 'Voertuig met kenteken: ' . $request->vehicle_plates;
                
                
                $vehicle = Vehicle::create([
                    'name' => $vehicleName,
                    'plates' => $request->vehicle_plates,
                    'company_id' => $companyId,
                    'team_id' => $team_id
                ]);
                
            }
            
            $claim->vehicle_id = $vehicle->id;
        }
        
        //
        
        if(isset($request->vehicle_plates_opposite)){
            
            $vehicleOpposite = VehicleOpposite::where('plates', $request->vehicle_plates_opposite)->first();
            
            if(!isset($vehicleOpposite)) {
                
                $vehicleName = 'Voertuig met kenteken: ' . $request->vehicle_plates_opposite;
                
                
                $vehicleOpposite = VehicleOpposite::create([
                    'name' => $vehicleName,
                    'plates' => $request->vehicle_plates_opposite,
                    'team_id' => $team_id
                ]);
                
            }
            
            $claim->vehicle_opposite_id = $vehicleOpposite->id;
            
        }
        
        $multiSelects = ['damaged_area', 'damaged_part', 'damage_origin', 'damaged_part_opposite', 'damage_origin_opposite', 'damaged_area_opposite', 'vehicle_id', 'vehicle_opposite_id'];
        
        $opposite = Opposite::where('claim_id', $claim->id)->get()->first();

        if(isset($opposite)) {
            $opposite_val = [
                'name' => $request->op_name,
                'street' => $request->op_street,
                'zipcode' => $request->op_zipcode,
                'city' => $request->op_city,
                'country' => $request->op_country,
                'phone' => $request->op_phone,
                'email' => $request->op_email,
            ];
    
            $opposite->update($opposite_val);
        } else {
            Opposite::create([
                'name'          => $request->op_name,
                'street'        => $request->op_street,
                'zipcode'       => $request->op_zipcode,
                'city'          => $request->op_city,
                'country'       => $request->op_country,
                'phone'         => $request->op_phone,
                'email'         => $request->op_email,
                'claim_id'      => $claim->id,
            ]);
        }
        $claim->closed_at = $request->input('closed_at');
        $claim->update($request->except($multiSelects));

        
        $claim->damaged_area = $request->input('damaged_area') ? json_encode($request->input('damaged_area')) : null;
        $claim->damaged_part = $request->input('damaged_part') ? json_encode($request->input('damaged_part')) : null;
        $claim->damage_origin = $request->input('damage_origin') ? json_encode($request->input('damage_origin')) : null;
        $claim->damaged_part_opposite = $request->input('damaged_part_opposite') ? json_encode($request->input('damaged_part_opposite')) : null;
        $claim->damage_origin_opposite = $request->input('damage_origin_opposite') ? json_encode($request->input('damage_origin_opposite')) : null;
        $claim->damaged_area_opposite = $request->input('damaged_area_opposite') ? json_encode($request->input('damaged_area_opposite')) : null;

        $claim->save();

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

        return redirect()->route('admin.claims.show', $claim->id)->with('message', 'Schadedossier succesvol bijgewerkt');
    }

    public function show(Claim $claim)
    {
        abort_if(Gate::denies('claim_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = auth()->user();
        $isAdminOrAgent = $user->isAdminOrAgent();

        $company = Company::where('id', $claim->company->id)->get()->first();

        $opposite = Opposite::where('claim_id', $claim->id)->get()->first();
        $firstContact = Contact::where('id', $company->contact_id)->get()->first();
        $notesAndTasks = $claim->notes->merge($claim->tasks)->sortBy('created_at');

        $sla = SLA::where('company_id', $claim->company->id)->first();
        $users = User::where('team_id', $user->team->id)->get();

        $parentMediaArray = [
            trans('cruds.claim.fields.damage_files') => $claim->damage_files,
            trans('cruds.claim.fields.report_files') => $claim->report_files,
            trans('cruds.claim.fields.financial_files') => $claim->financial_files,
            trans('cruds.claim.fields.other_files') => $claim->other_files
        ];

        $allContactsInCompany = Contact::where('company_id', $claim->company->id)->get();
        $mailTemplates = MailTemplate::all();
      
        $assignee_name = null;
        
        if($claim->assignee_id) {
            $assignee_name = Contact::where('user_id', $claim->assignee_id)->select('first_name', 'last_name')->get()->first();
        }

        if($isAdminOrAgent) {
            
            $users = User::get();
            
        } else {
            
            $users = User::where('team_id', $user->team->id)->get();

        }

        $driver = Driver::find($claim->driver_vehicle)->contact ?? null;

        $oppositeVehicleInfo = null;

        if ($claim->vehicle_opposite) {

            $oppositeVehicleInfo = (object) [
                'vehicle' => $claim->vehicle_opposite,
                'damaged_part' => $claim->damaged_part_opposite ?? null,
                'damage_origin' => $claim->damage_origin_opposite ?? null,
                'damaged_area' => $claim->damaged_area_opposite ?? null,
                'driver' => $claim->driver_vehicle_opposite ?? null
            ];

        }

        $claim->load('company', 'injury_office', 'vehicle', 'vehicle_opposite', 'recovery_office', 'expertise_office', 'team', 'notes', 'tasks');

        return view('admin.claims.show', compact('claim', 'firstContact', 'allContactsInCompany', 'opposite', 'users', 'notesAndTasks', 'mailTemplates', 'assignee_name', 'parentMediaArray', 'sla', 'driver', 'oppositeVehicleInfo'));
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

    public function quickUpdateStatus(Request $request)
    {
        $claim = Claim::find($request->claim_id);

        $new_status = null;

        if( $request->new_status == 'finished' ) {
            

            if(!isset($claim->damage_costs) || !isset($claim->recovery_costs)) {
    
                return response()->json(
                    [
                        'status' => $claim->status,
                        'type'  => 'alert-danger',
                        'message' => 'Status NIET aangepast! U dient eerst financiele en gesloten op gegevens verder in te vullen voordat de claim gesloten kan worden.'
                    ], 200);
            }

            $claim->closed_at = now()->format('d-m-Y');
        }

        if( $request->new_status == 'claim_denied' ) {

            return response()->json(
                [
                    'status' => $claim->status,
                    'type' => 'alert-warning',
                    'message' => 'Geef een reden waarom deze claim wordt afgewezen.'
                ], 200
            );

        }

        

        $claim->status = $request->new_status;

        $claim->save();

        return response()->json(
            [
                'status' => $claim->status,
                'type' => 'alert-success',
                'message' => 'Status is succesvol aangepast!'
            ], 200);
    }

    public function sendMail(Request $request)
    {
        abort_if(Gate::denies('claim_create') && Gate::denies('claim_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $message = new \App\Notifications\PlainMail(
            $request->mailSubject ?? '',
            $request->mailBody ?? '',
            $request->mailAttachments ?? null,
            $request->mailCc ?? [],
            $request->mailBcc ?? []
        );

        foreach($request->mailReceiver as $receiver) {
            
            Notification::route(
                'mail', [
                    $receiver ?? '' => ''
                ]
            )->notify($message);

        }


        $receiverString = implode(', ', $request->mailReceiver);

        $noteDescription = "Ontvanger(s): {$receiverString}<br/>
        CC: {$request->cc}<br/>
        Onderwerp: {$request->mailSubject}<br/>
        Bericht: {$request->mailBody}";

        $note = Note::create([
            'title' => 'Mail:'. $request->mailSubject,
            'user_id' => $request->input('user_id'),
            'description' => $noteDescription,
            'team_id' => auth()->user()->team->id
        ]);

        if ($request->hasFile('mailAttachments')) {
            foreach ($request->file('mailAttachments') as $image) {
                $note->addMedia($image)->toMediaCollection('attachments');
            }
        }

        $note->claims()->sync($request->input('claims', []));

        // Example mail-sending logic with CC
        Mail::send('emails.plain-email', ['body' => $request->mailBody], function ($message) use ($request, $receiverString) {
            $message->to(explode(',', $receiverString))
                    ->subject($request->mailSubject);

            // Add CC recipients if provided
            if ($request->filled('cc')) {
                $message->cc(explode(',', $request->cc));
            }

            // Add attachments if provided
            if ($request->hasFile('mailAttachments')) {
                foreach ($request->file('mailAttachments') as $attachment) {
                    $message->attach($attachment->getRealPath(), [
                        'as' => $attachment->getClientOriginalName(),
                        'mime' => $attachment->getMimeType(),
                    ]);
                }
            }
        });

        $note->claims()->sync($request->input('claims', []));


        return redirect()->back()->with('message', 'Mail is verstuurd en gelogd in dossier');

    }

    public function declineClaim(Request $request)
    {
        $claim = Claim::find($request->claimID);

        $claim->decline_reason = $request->declineReason;

        $claim->status = 'claim_denied';

        $claim->save();

        return response()->json(
            [
                'type' => 'alert-danger',
                'message' => 'Claim afgewezen door: ' . \App\Models\Claim::DECLINE_REASON_SELECT[$claim->decline_reason]
            ], 200);

    }
}
