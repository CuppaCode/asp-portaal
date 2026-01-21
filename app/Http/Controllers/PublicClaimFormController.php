<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Company;
use App\Models\CompanyClaimToken;
use App\Models\CompanyClaimFormConfig;
use App\Models\Vehicle;
use App\Models\VehicleOpposite;
use App\Models\Opposite;
use App\Notifications\DraftClaimNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class PublicClaimFormController extends Controller
{
    public function show($token)
    {
        $claimToken = CompanyClaimToken::where('token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $company = $claimToken->company;

        // Get form configuration
        $formConfigs = $company->claimFormConfigs()
            ->where('is_enabled', true)
            ->orderBy('display_order')
            ->get();

        // If no config exists, create default configuration
        if ($formConfigs->isEmpty()) {
            $this->createDefaultConfig($company);
            $formConfigs = $company->claimFormConfigs()
                ->where('is_enabled', true)
                ->orderBy('display_order')
                ->get();
        }

        $availableFields = CompanyClaimFormConfig::getAvailableFields();

        return view('public.claim-form', compact(
            'claimToken',
            'company',
            'formConfigs',
            'availableFields'
        ));
    }

    public function store(Request $request, $token)
    {
        $claimToken = CompanyClaimToken::where('token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $company = $claimToken->company;

        // Get enabled and required fields
        $formConfigs = $company->claimFormConfigs()
            ->where('is_enabled', true)
            ->get();

        // Build validation rules dynamically
        $rules = $this->buildValidationRules($formConfigs, $request->all());

        $validated = $request->validate($rules);

        // Process vehicle if plates provided
        $vehicleId = null;
        if (!empty($validated['vehicle_plates'])) {
            $vehicle = Vehicle::firstOrCreate(
                ['plates' => $validated['vehicle_plates']],
                ['company_id' => $company->id, 'team_id' => $company->team_id]
            );
            $vehicleId = $vehicle->id;
        }

        // Process opposite vehicle if plates provided
        $vehicleOppositeId = null;
        if (!empty($validated['vehicle_plates_opposite'])) {
            $vehicleOpposite = VehicleOpposite::firstOrCreate(
                ['plates' => $validated['vehicle_plates_opposite']]
            );
            $vehicleOppositeId = $vehicleOpposite->id;
        }

        // Generate claim number
        $year = now()->year;
        $lastClaim = Claim::whereYear('created_at', $year)->latest('id')->first();
        $nextNumber = $lastClaim ? ($lastClaim->id + 100) : 100;
        $claimNumber = $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        // Calculate draft expiry
        $draftExpiresAt = now()->addDays($company->draft_expiry_days ?? 30);

        // Create claim
        $claim = Claim::create([
            'company_id' => $company->id,
            'team_id' => $company->team_id,
            'claim_number' => $claimNumber,
            'status' => 'draft',
            'subject' => $validated['subject'] ?? 'Schademelding via formulier',
            'date_accident' => $validated['date_accident'] ?? null,
            'injury' => $validated['injury'] ?? 'no',
            'injury_other' => $validated['injury_other'] ?? null,
            'injury_office_id' => $validated['injury_office_id'] ?? null,
            'damage_kind' => $validated['damage_kind'] ?? null,
            'recoverable_claim' => $validated['recoverable_claim'] ?? 'unknown',
            'vehicle_id' => $vehicleId,
            'vehicle_opposite_id' => $vehicleOppositeId,
            'driver_vehicle' => $validated['driver_vehicle'] ?? null,
            'driver_vehicle_opposite' => $validated['driver_vehicle_opposite'] ?? null,
            'damaged_part' => !empty($validated['damaged_part']) ? json_encode($validated['damaged_part']) : null,
            'damaged_area' => !empty($validated['damaged_area']) ? json_encode($validated['damaged_area']) : null,
            'damage_origin' => !empty($validated['damage_origin']) ? json_encode($validated['damage_origin']) : null,
            'damaged_part_opposite' => !empty($validated['damaged_part_opposite']) ? json_encode($validated['damaged_part_opposite']) : null,
            'damaged_area_opposite' => !empty($validated['damaged_area_opposite']) ? json_encode($validated['damaged_area_opposite']) : null,
            'damage_origin_opposite' => !empty($validated['damage_origin_opposite']) ? json_encode($validated['damage_origin_opposite']) : null,
            'opposite_type' => $validated['opposite_type'] ?? null,
            'obstacle' => $validated['obstacle'] ?? null,
            'loading_photos' => $validated['loading_photos'] ?? null,
            'unloading_photos' => $validated['unloading_photos'] ?? null,
            'waybill_signed_at_loading' => $validated['waybill_signed_at_loading'] ?? null,
            'waybill_signed_at_unloading' => $validated['waybill_signed_at_unloading'] ?? null,
            'opposite_claim_no' => $validated['opposite_claim_no'] ?? null,
            'draft_expires_at' => $draftExpiresAt,
            'invoice_amount' => $company->claims_fee ?? 0,
        ]);

        // Create opposite party if data provided
        if ($this->hasOppositeData($validated)) {
            Opposite::create([
                'claim_id' => $claim->id,
                'name' => $validated['op_name'] ?? null,
                'street' => $validated['op_street'] ?? null,
                'zipcode' => $validated['op_zipcode'] ?? null,
                'city' => $validated['op_city'] ?? null,
                'country' => $validated['op_country'] ?? 'Nederland',
                'phone' => $validated['op_phone'] ?? null,
                'email' => $validated['op_email'] ?? null,
            ]);
        }

        // Handle file uploads
        $this->handleFileUploads($claim, $request);

        // Increment token usage
        $claimToken->incrementUsage();

        // Send notifications
        $this->sendDraftNotifications($claim, $company, $formConfigs);

        return view('public.claim-form-success', compact('claim', 'company'));
    }

    private function createDefaultConfig(Company $company)
    {
        $defaultFields = [
            'subject' => ['enabled' => true, 'required' => true, 'notify' => true, 'label' => 'Onderwerp', 'order' => 10],
            'date_accident' => ['enabled' => true, 'required' => true, 'notify' => true, 'label' => 'Datum ongeval', 'order' => 20],
            'damage_kind' => ['enabled' => true, 'required' => false, 'notify' => true, 'label' => 'Soort schade', 'order' => 30],
            'vehicle_plates' => ['enabled' => true, 'required' => true, 'notify' => true, 'label' => 'Kenteken', 'order' => 40],
            'damaged_part' => ['enabled' => true, 'required' => false, 'notify' => false, 'label' => 'Beschadigd onderdeel', 'order' => 50],
            'damaged_area' => ['enabled' => true, 'required' => false, 'notify' => false, 'label' => 'Beschadigd gebied', 'order' => 60],
            'damage_files' => ['enabled' => true, 'required' => false, 'notify' => false, 'label' => 'Foto\'s schade', 'order' => 70],
        ];

        foreach ($defaultFields as $fieldName => $config) {
            CompanyClaimFormConfig::create([
                'company_id' => $company->id,
                'field_name' => $fieldName,
                'is_enabled' => $config['enabled'],
                'is_required' => $config['required'],
                'include_in_notification' => $config['notify'],
                'notification_label' => $config['label'],
                'display_order' => $config['order'],
            ]);
        }
    }

    private function buildValidationRules($formConfigs, $formData)
    {
        $rules = [];

        foreach ($formConfigs as $config) {
            // Skip if conditional logic doesn't match
            if (!$config->evaluateCondition($formData)) {
                continue;
            }

            $fieldRules = [];

            if ($config->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Add type-specific validation
            switch ($config->field_name) {
                case 'date_accident':
                    $fieldRules[] = 'date_format:d-m-Y';
                    break;
                case 'op_email':
                    $fieldRules[] = 'email';
                    break;
                case 'damaged_part':
                case 'damaged_area':
                case 'damage_origin':
                case 'damaged_part_opposite':
                case 'damaged_area_opposite':
                case 'damage_origin_opposite':
                    $fieldRules[] = 'array';
                    break;
                case 'damage_files':
                case 'report_files':
                case 'financial_files':
                case 'other_files':
                    $fieldRules[] = 'array';
                    $fieldRules[] = 'max:10';
                    break;
                default:
                    if (!in_array('array', $fieldRules)) {
                        $fieldRules[] = 'string';
                    }
            }

            $rules[$config->field_name] = implode('|', $fieldRules);
        }

        return $rules;
    }

    private function hasOppositeData($data)
    {
        $oppositeFields = ['op_name', 'op_street', 'op_zipcode', 'op_city', 'op_phone', 'op_email'];
        foreach ($oppositeFields as $field) {
            if (!empty($data[$field])) {
                return true;
            }
        }
        return false;
    }

    private function handleFileUploads(Claim $claim, Request $request)
    {
        $fileCollections = ['damage_files', 'report_files', 'financial_files', 'other_files'];

        foreach ($fileCollections as $collection) {
            if ($request->hasFile($collection)) {
                foreach ($request->file($collection) as $file) {
                    $claim->addMedia($file)->toMediaCollection($collection);
                }
            }
        }
    }

    private function sendDraftNotifications(Claim $claim, Company $company, $formConfigs)
    {
        $recipients = $company->claimFormNotifications;

        if ($recipients->isEmpty()) {
            return;
        }

        // Build notification summary
        $summary = [];
        foreach ($formConfigs as $config) {
            if ($config->include_in_notification) {
                $value = $this->getClaimFieldValue($claim, $config->field_name);
                if (!empty($value)) {
                    $summary[$config->notification_label ?? $config->field_name] = $value;
                }
            }
        }

        $notification = new DraftClaimNotification($claim, $summary);

        foreach ($recipients as $recipient) {
            Notification::route('mail', $recipient->email)->notify($notification);
        }
    }

    private function getClaimFieldValue(Claim $claim, $fieldName)
    {
        $value = $claim->$fieldName;

        if (is_null($value)) {
            return null;
        }

        // Handle JSON fields
        if (in_array($fieldName, ['damaged_part', 'damaged_area', 'damage_origin', 'damaged_part_opposite', 'damaged_area_opposite', 'damage_origin_opposite'])) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? implode(', ', $decoded) : $value;
        }

        // Handle select fields
        if ($fieldName === 'injury' && isset(Claim::INJURY_SELECT[$value])) {
            return Claim::INJURY_SELECT[$value];
        }

        if ($fieldName === 'damage_kind' && isset(Claim::DAMAGE_KIND[$value])) {
            return Claim::DAMAGE_KIND[$value];
        }

        if ($fieldName === 'recoverable_claim' && isset(Claim::RECOVERABLE_CLAIM_SELECT[$value])) {
            return Claim::RECOVERABLE_CLAIM_SELECT[$value];
        }

        if (in_array($fieldName, ['loading_photos', 'unloading_photos', 'waybill_signed_at_loading', 'waybill_signed_at_unloading']) && isset(Claim::WAYBILL_SELECT[$value])) {
            return Claim::WAYBILL_SELECT[$value];
        }

        return $value;
    }
}
