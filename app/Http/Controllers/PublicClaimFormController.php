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

        // Get custom fields
        $customFields = $company->customClaimFields()
            ->where('is_enabled', true)
            ->orderBy('display_order')
            ->get();

        // Merge standard and custom fields into a unified collection sorted by display_order
        $allFields = collect();
        
        foreach ($formConfigs as $config) {
            $allFields->push([
                'type' => 'standard',
                'order' => $config->display_order,
                'data' => $config,
            ]);
        }
        
        foreach ($customFields as $customField) {
            $allFields->push([
                'type' => 'custom',
                'order' => $customField->display_order,
                'data' => $customField,
            ]);
        }
        
        $allFields = $allFields->sortBy('order')->values();

        // Group fields by field_group
        $groupedFields = [];
        foreach ($allFields as $field) {
            $group = null;
            if ($field['type'] === 'standard') {
                $group = $field['data']->field_group;
            } else {
                $group = $field['data']->field_group;
            }
            
            if ($group) {
                if (!isset($groupedFields[$group])) {
                    $groupedFields[$group] = [];
                }
                $groupedFields[$group][] = $field;
            } else {
                $groupedFields['_ungrouped_' . $field['order']][] = $field;
            }
        }

        $availableFields = CompanyClaimFormConfig::getAvailableFields();

        return view('public.claim-form', compact(
            'claimToken',
            'company',
            'allFields',
            'groupedFields',
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

        // Check form type - handle complaint vs claim
        $formType = $validated['form_type'] ?? 'claim';

        if ($formType === 'complaint') {
            // Handle complaint - send notification only, no claim creation
            $this->handleComplaint($validated, $company, $formConfigs);
            
            // Increment token usage
            $claimToken->incrementUsage();
            
            return view('public.claim-form-success', [
                'claim' => null,
                'company' => $company,
                'isComplaint' => true
            ]);
        }

        // Process vehicle if plates provided
        $vehicleId = null;
        if (!empty($validated['vehicle_plates'])) {
            $formattedPlate = !empty($validated['vehicle_plates_foreign'])
                ? strtoupper($validated['vehicle_plates'])
                : format_license_plate($validated['vehicle_plates']);
            $vehicle = Vehicle::firstOrCreate(
                ['plates' => $formattedPlate],
                ['company_id' => $company->id, 'team_id' => $company->team_id]
            );
            $vehicleId = $vehicle->id;
        }

        // Process opposite vehicle if plates provided
        $vehicleOppositeId = null;
        if (!empty($validated['vehicle_plates_opposite'])) {
            $formattedPlate = !empty($validated['vehicle_plates_opposite_foreign'])
                ? strtoupper($validated['vehicle_plates_opposite'])
                : format_license_plate($validated['vehicle_plates_opposite']);
            $vehicleOpposite = VehicleOpposite::updateOrCreate(
                ['plates' => $formattedPlate],
                [
                    'name'           => 'Voertuig met kenteken: ' . $formattedPlate,
                    'brand'          => $validated['vehicle_brand_opposite'] ?? null,
                    'chassis_number' => $validated['vehicle_chassis_number_opposite'] ?? null,
                    'build_year'     => $validated['vehicle_build_year_opposite'] ?? null,
                ]
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

        // Extract custom fields data
        $customFieldsData = [];
        $customFields = $company->customClaimFields;
        foreach ($customFields as $customField) {
            $fieldKey = 'custom_' . $customField->field_name;
            if (isset($validated[$fieldKey])) {
                $customFieldsData[$customField->field_name] = $validated[$fieldKey];
            }
        }

        // Determine subject: for claims, use vehicle_plates_opposite; otherwise use provided subject or default
        if ($formType === 'claim') {
            $subject = !empty($validated['vehicle_plates_opposite']) 
                ? format_license_plate($validated['vehicle_plates_opposite'])
                : 'Schademelding via formulier';
        } else {
            $subject = $validated['subject'] ?? 'Schademelding via formulier';
        }

        // Create claim
        $claim = Claim::create([
            'company_id' => $company->id,
            'team_id' => $company->team_id,
            'claim_number' => $claimNumber,
            'status' => 'draft',
            'subject' => $subject,
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
            'custom_fields_data' => !empty($customFieldsData) ? $customFieldsData : null,
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

            // vehicle_plates_opposite is always required if enabled
            if ($config->field_name === 'vehicle_plates_opposite') {
                $fieldRules[] = 'required';
            } elseif ($config->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Add type-specific validation
            switch ($config->field_name) {
                case 'form_type':
                    $fieldRules[] = 'in:claim,complaint';
                    break;
                case 'complaint_description':
                    $fieldRules[] = 'string';
                    break;
                case 'vehicle_plates_opposite':
                    $fieldRules[] = 'string';
                    if (empty($formData['vehicle_plates_opposite_foreign'])) {
                        $fieldRules[] = 'min:4';
                        $fieldRules[] = 'max:8';
                    } else {
                        $fieldRules[] = 'max:20';
                    }
                    break;
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

        // Add validation for foreign plate flags
        $rules['vehicle_plates_foreign'] = 'nullable|boolean';
        $rules['vehicle_plates_opposite_foreign'] = 'nullable|boolean';

        // Add custom fields validation
        $company = Company::find($formConfigs->first()->company_id);
        if ($company) {
            $customFields = $company->customClaimFields()->where('is_enabled', true)->get();
            foreach ($customFields as $customField) {
                // Skip HTML fields (display only)
                if ($customField->field_type === 'html') {
                    continue;
                }

                // Skip if conditional logic doesn't match
                if (!$this->evaluateCustomFieldCondition($customField, $formData)) {
                    continue;
                }

                $fieldKey = 'custom_' . $customField->field_name;
                $fieldRules = [];

                if ($customField->is_required) {
                    $fieldRules[] = 'required';
                } else {
                    $fieldRules[] = 'nullable';
                }

                // Add type-specific validation
                switch ($customField->field_type) {
                    case 'text':
                        $fieldRules[] = 'string';
                        $fieldRules[] = 'max:255';
                        break;
                    case 'textarea':
                        $fieldRules[] = 'string';
                        break;
                    case 'select':
                        $fieldRules[] = 'string';
                        if (!empty($customField->options)) {
                            $fieldRules[] = 'in:' . implode(',', $customField->options);
                        }
                        break;
                }

                $rules[$fieldKey] = implode('|', $fieldRules);
            }
        }

        return $rules;
    }

    private function evaluateCustomFieldCondition($customField, $formData)
    {
        if (empty($customField->conditional_logic)) {
            return true;
        }

        $logic = $customField->conditional_logic;
        $operator = $logic['operator'] ?? 'AND';
        $conditions = $logic['conditions'] ?? [];

        if (empty($conditions)) {
            return true;
        }

        $results = [];
        foreach ($conditions as $condition) {
            $field = $condition['field'] ?? '';
            $comparison = $condition['operator'] ?? 'equals';
            $expectedValue = $condition['value'] ?? '';

            // Handle custom_ prefix for custom fields
            $actualValue = $formData[$field] ?? null;

            $result = false;
            switch ($comparison) {
                case 'equals':
                    $result = $actualValue == $expectedValue;
                    break;
                case 'not_equals':
                    $result = $actualValue != $expectedValue;
                    break;
                case 'contains':
                    $result = is_string($actualValue) && str_contains($actualValue, $expectedValue);
                    break;
                case 'not_contains':
                    $result = is_string($actualValue) && !str_contains($actualValue, $expectedValue);
                    break;
                case 'empty':
                    $result = empty($actualValue);
                    break;
                case 'not_empty':
                    $result = !empty($actualValue);
                    break;
            }

            $results[] = $result;
        }

        if ($operator === 'AND') {
            return !in_array(false, $results);
        } else {
            return in_array(true, $results);
        }
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
        $config = config('file-uploads');
        
        $maxFilesPerCollection = $config['max_files_per_collection'] ?? 10;
        $maxFileSize = ($config['max_file_size_mb'] ?? 10) * 1024 * 1024; // Convert MB to bytes
        
        // Build allowed MIME types list
        $allowedMimeTypes = [];
        foreach ($config['allowed_mime_types'] as $category => $types) {
            $allowedMimeTypes = array_merge($allowedMimeTypes, $types);
        }
        $allowedMimeTypes = array_unique($allowedMimeTypes);
        
        $allowedExtensions = array_map('strtolower', $config['allowed_extensions'] ?? []);

        foreach ($fileCollections as $collection) {
            if ($request->hasFile($collection)) {
                $files = $request->file($collection);
                $fileCount = is_array($files) ? count($files) : 1;

                // Validate number of files
                if ($fileCount > $maxFilesPerCollection) {
                    \Log::warning("Too many files uploaded for {$collection}: {$fileCount} files (max: {$maxFilesPerCollection})", ['claim_id' => $claim->id]);
                    continue;
                }

                foreach ((array)$files as $file) {
                    // Validate file size
                    if ($file->getSize() > $maxFileSize) {
                        $sizeMB = round($file->getSize() / 1024 / 1024, 2);
                        $maxSizeMB = $config['max_file_size_mb'] ?? 10;
                        \Log::warning("File too large: {$file->getClientOriginalName()} ({$sizeMB}MB, max: {$maxSizeMB}MB)", ['claim_id' => $claim->id]);
                        continue;
                    }

                    // Validate file extension
                    $extension = strtolower($file->getClientOriginalExtension());
                    if (!in_array($extension, $allowedExtensions)) {
                        \Log::warning("Invalid file extension: {$file->getClientOriginalName()} (.{$extension})", ['claim_id' => $claim->id]);
                        continue;
                    }

                    // Validate MIME type
                    if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                        \Log::warning("Invalid file MIME type: {$file->getClientOriginalName()} (" . $file->getMimeType() . ")", ['claim_id' => $claim->id]);
                        continue;
                    }

                    // Add file to media collection
                    try {
                        $claim->addMedia($file)->toMediaCollection($collection);
                    } catch (\Exception $e) {
                        \Log::error("Error uploading file: {$file->getClientOriginalName()}", [
                            'claim_id' => $claim->id,
                            'error' => $e->getMessage()
                        ]);
                    }
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

        // Add custom fields to summary
        $customFields = $company->customClaimFields;
        foreach ($customFields as $customField) {
            // Skip HTML fields (display only)
            if ($customField->field_type === 'html') {
                continue;
            }
            
            if ($customField->include_in_notification && !empty($claim->custom_fields_data[$customField->field_name])) {
                $summary[$customField->field_label] = $claim->custom_fields_data[$customField->field_name];
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

        if ($fieldName === 'form_type') {
            return $value === 'claim' ? 'Schademelding' : 'Klacht';
        }

        return $value;
    }

    private function handleComplaint($validated, Company $company, $formConfigs)
    {
        $recipients = $company->claimFormNotifications;

        if ($recipients->isEmpty()) {
            return;
        }

        // Build notification summary
        $summary = [];
        foreach ($formConfigs as $config) {
            if ($config->include_in_notification && $config->field_name !== 'form_type') {
                $value = $this->getComplaintFieldValue($validated, $config->field_name);
                if (!empty($value)) {
                    $summary[$config->notification_label ?? $config->field_name] = $value;
                }
            }
        }

        // Add custom fields to summary
        $customFields = $company->customClaimFields()->where('is_enabled', true)->get();
        foreach ($customFields as $customField) {
            // Skip HTML fields (display only)
            if ($customField->field_type === 'html') {
                continue;
            }
            
            $fieldKey = 'custom_' . $customField->field_name;
            if ($customField->include_in_notification && isset($validated[$fieldKey])) {
                $summary[$customField->field_label] = $validated[$fieldKey];
            }
        }

        $notification = new \App\Notifications\ComplaintNotification($company, $summary);

        foreach ($recipients as $recipient) {
            \Illuminate\Support\Facades\Notification::route('mail', $recipient->email)->notify($notification);
        }
    }

    private function getComplaintFieldValue($validated, $fieldName)
    {
        $value = $validated[$fieldName] ?? null;

        if (is_null($value)) {
            return null;
        }

        // Handle complaint description
        if ($fieldName === 'complaint_description') {
            return $value;
        }

        // Handle JSON fields
        if (in_array($fieldName, ['damaged_part', 'damaged_area', 'damage_origin', 'damaged_part_opposite', 'damaged_area_opposite', 'damage_origin_opposite'])) {
            return is_array($value) ? implode(', ', $value) : $value;
        }

        // Handle select fields
        if ($fieldName === 'injury' && isset(\App\Models\Claim::INJURY_SELECT[$value])) {
            return \App\Models\Claim::INJURY_SELECT[$value];
        }

        if ($fieldName === 'damage_kind' && isset(\App\Models\Claim::DAMAGE_KIND[$value])) {
            return \App\Models\Claim::DAMAGE_KIND[$value];
        }

        if ($fieldName === 'recoverable_claim' && isset(\App\Models\Claim::RECOVERABLE_CLAIM_SELECT[$value])) {
            return \App\Models\Claim::RECOVERABLE_CLAIM_SELECT[$value];
        }

        return $value;
    }
}
