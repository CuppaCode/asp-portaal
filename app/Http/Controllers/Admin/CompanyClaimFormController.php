<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyClaimToken;
use App\Models\CompanyClaimFormConfig;
use App\Models\CompanyClaimFormNotification;
use App\Models\CompanyCustomClaimField;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CompanyClaimFormController extends Controller
{
    public function index(Company $company)
    {
        abort_if(Gate::denies('company_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tokens = $company->claimTokens()->latest()->get();
        $formConfigs = $company->claimFormConfigs()->orderBy('display_order')->get();
        $notifications = $company->claimFormNotifications()->get();
        $customFields = $company->customClaimFields()->orderBy('display_order')->get();
        $availableFields = CompanyClaimFormConfig::getAvailableFields();

        return view('admin.company-claim-forms.index', compact(
            'company',
            'tokens',
            'formConfigs',
            'notifications',
            'customFields',
            'availableFields'
        ));
    }

    public function updateConfig(Request $request, Company $company)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $configs = $request->input('fields', []);

        foreach ($configs as $fieldName => $config) {
            // Parse conditional logic JSON if provided
            $conditionalLogic = null;
            if (!empty($config['conditional_logic'])) {
                $conditionalLogic = is_string($config['conditional_logic']) 
                    ? json_decode($config['conditional_logic'], true) 
                    : $config['conditional_logic'];
            }

            // Auto-set conditional logic for complaint_description and vehicle_plates_opposite if not provided
            if ($fieldName === 'complaint_description' && empty($conditionalLogic)) {
                $conditionalLogic = [
                    'operator' => 'AND',
                    'conditions' => [
                        [
                            'field' => 'form_type',
                            'operator' => 'equals',
                            'value' => 'complaint'
                        ]
                    ]
                ];
            }

            if ($fieldName === 'vehicle_plates_opposite' && empty($conditionalLogic)) {
                $conditionalLogic = [
                    'operator' => 'AND',
                    'conditions' => [
                        [
                            'field' => 'form_type',
                            'operator' => 'equals',
                            'value' => 'claim'
                        ]
                    ]
                ];
            }

            CompanyClaimFormConfig::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'field_name' => $fieldName,
                ],
                [
                    'is_enabled' => $config['is_enabled'] ?? false,
                    'is_required' => $config['is_required'] ?? false,
                    'include_in_notification' => $config['include_in_notification'] ?? false,
                    'notification_label' => $config['notification_label'] ?? null,
                    'conditional_logic' => $conditionalLogic,
                    'display_order' => $config['display_order'] ?? 0,
                    'field_width' => $config['field_width'] ?? 'full',
                    'field_group' => $config['field_group'] ?? null,
                ]
            );
        }

        return redirect()->route('admin.company-claim-forms.index', $company)
            ->with('message', 'Formulier configuratie bijgewerkt.');
    }

    public function updateStandardField(Request $request, Company $company, $fieldName)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Get or create the config record
        $config = CompanyClaimFormConfig::firstOrCreate(
            [
                'company_id' => $company->id,
                'field_name' => $fieldName,
            ],
            [
                'is_enabled' => false,
                'is_required' => false,
                'include_in_notification' => false,
                'display_order' => 0,
                'field_width' => 'full',
            ]
        );

        // Update only the fields that are provided in the request
        $updateData = [];

        if ($request->has('is_enabled')) {
            $updateData['is_enabled'] = filter_var($request->is_enabled, FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->has('is_required')) {
            $updateData['is_required'] = filter_var($request->is_required, FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->has('include_in_notification')) {
            $updateData['include_in_notification'] = filter_var($request->include_in_notification, FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->has('notification_label')) {
            $updateData['notification_label'] = $request->notification_label;
        }

        if ($request->has('field_width')) {
            $updateData['field_width'] = $request->field_width;
        }

        if ($request->has('field_group')) {
            $updateData['field_group'] = $request->field_group;
        }

        if ($request->has('display_order')) {
            $updateData['display_order'] = (int) $request->display_order;
        }

        if ($request->has('conditional_logic')) {
            $conditionalLogic = $request->conditional_logic;
            if (!empty($conditionalLogic)) {
                $conditionalLogic = is_string($conditionalLogic) 
                    ? json_decode($conditionalLogic, true) 
                    : $conditionalLogic;
            }
            $updateData['conditional_logic'] = $conditionalLogic;
        }

        // Auto-set conditional logic for complaint_description and vehicle_plates_opposite if not already set
        if ($fieldName === 'complaint_description' && !$config->conditional_logic && !$request->has('conditional_logic')) {
            $updateData['conditional_logic'] = [
                'operator' => 'AND',
                'conditions' => [
                    [
                        'field' => 'form_type',
                        'operator' => 'equals',
                        'value' => 'complaint'
                    ]
                ]
            ];
        }

        if ($fieldName === 'vehicle_plates_opposite' && !$config->conditional_logic && !$request->has('conditional_logic')) {
            $updateData['conditional_logic'] = [
                'operator' => 'AND',
                'conditions' => [
                    [
                        'field' => 'form_type',
                        'operator' => 'equals',
                        'value' => 'claim'
                    ]
                ]
            ];
        }

        // Perform the update
        if (!empty($updateData)) {
            $config->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Field updated successfully',
            'config' => $config->fresh()
        ]);
    }

    public function updateExpirySettings(Request $request, Company $company)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'draft_expiry_days' => 'required|integer|min:1',
            'draft_reminder_days' => 'required|integer|min:1',
            'draft_reminder_frequency_days' => 'required|integer|min:1',
        ]);

        $company->update([
            'draft_expiry_days' => $request->input('draft_expiry_days'),
            'draft_reminder_days' => $request->input('draft_reminder_days'),
            'draft_reminder_frequency_days' => $request->input('draft_reminder_frequency_days'),
        ]);

        return redirect()->route('admin.company-claim-forms.index', $company)
            ->with('message', 'Verval instellingen bijgewerkt.');
    }

    public function createToken(Request $request, Company $company)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'label' => 'required|string|max:255',
        ]);

        $token = CompanyClaimToken::create([
            'company_id' => $company->id,
            'token' => CompanyClaimToken::generateToken(),
            'label' => $request->input('label'),
            'is_active' => true,
        ]);

        return redirect()->route('admin.company-claim-forms.index', $company)
            ->with('message', 'Token aangemaakt: ' . $token->url);
    }

    public function toggleToken(Request $request, Company $company, CompanyClaimToken $token)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($token->company_id !== $company->id) {
            abort(Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        $token->update(['is_active' => !$token->is_active]);

        $status = $token->is_active ? 'geactiveerd' : 'gedeactiveerd';
        return redirect()->route('admin.company-claim-forms.index', $company)
            ->with('message', "Token {$status}.");
    }

    public function deleteToken(Request $request, Company $company, CompanyClaimToken $token)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($token->company_id !== $company->id) {
            abort(Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        $token->delete();

        return redirect()->route('admin.company-claim-forms.index', $company)
            ->with('message', 'Token verwijderd.');
    }

    public function storeNotification(Request $request, Company $company)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string|max:255',
        ]);

        CompanyClaimFormNotification::create([
            'company_id' => $company->id,
            'email' => $request->input('email'),
            'name' => $request->input('name'),
        ]);

        return redirect()->route('admin.company-claim-forms.index', $company)
            ->with('message', 'Notificatie ontvanger toegevoegd.');
    }

    public function deleteNotification(Request $request, Company $company, CompanyClaimFormNotification $notification)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($notification->company_id !== $company->id) {
            abort(Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        $notification->delete();

        return redirect()->route('admin.company-claim-forms.index', $company)
            ->with('message', 'Notificatie ontvanger verwijderd.');
    }

    public function storeCustomField(Request $request, Company $company)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'field_type' => 'required|in:text,textarea,select,html',
            'field_name' => 'nullable|string|max:255|regex:/^[a-z0-9_]+$/',
            'field_label' => 'nullable|string|max:255',
            'html_content' => 'nullable|string',
            'options' => 'nullable|string',
        ]);

        $fieldType = $request->input('field_type');
        
        // For HTML fields, use html_content as field_label and generate unique field_name
        if ($fieldType === 'html') {
            $fieldName = 'html_' . uniqid();
            $fieldLabel = $request->input('html_content');
        } else {
            $fieldName = $request->input('field_name');
            $fieldLabel = $request->input('field_label');
            
            // Check if field_name is unique for this company
            $exists = $company->customClaimFields()->where('field_name', $fieldName)->exists();
            if ($exists) {
                return redirect()->back()->with('error', 'Een veld met deze naam bestaat al.');
            }
        }

        // Parse options if select field
        $options = null;
        if ($fieldType === 'select' && $request->input('options')) {
            $options = array_map('trim', explode("\n", $request->input('options')));
        }

        CompanyCustomClaimField::create([
            'company_id' => $company->id,
            'field_type' => $fieldType,
            'field_name' => $fieldName,
            'field_label' => $fieldLabel,
            'options' => $options,
            'is_required' => false,
            'include_in_notification' => false,
            'display_order' => $company->customClaimFields()->max('display_order') + 1,
            'field_width' => 'full',
            'field_group' => null,
        ]);

        return redirect()->route('admin.company-claim-forms.index', $company)
            ->with('message', 'Custom veld aangemaakt.');
    }

    public function updateCustomField(Request $request, Company $company, CompanyCustomClaimField $customField)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($customField->company_id !== $company->id) {
            abort(Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        // Check if this is a full edit (has field_label or html_content) or just checkbox update
        $isFullEdit = $request->has('field_label') || $request->has('html_content');

        if ($isFullEdit) {
            // Full edit validation
            $request->validate([
                'field_label' => 'nullable|string|max:255',
                'html_content' => 'nullable|string',
                'options' => 'nullable|string',
            ]);

            $fieldType = $customField->field_type;
            
            // For HTML fields, use html_content as field_label
            if ($fieldType === 'html') {
                $fieldLabel = $request->input('html_content');
            } else {
                $fieldLabel = $request->input('field_label');
            }

            // Parse options if select field
            $options = null;
            if ($fieldType === 'select' && $request->input('options')) {
                $options = array_map('trim', explode("\n", $request->input('options')));
            }

            $customField->update([
                'field_label' => $fieldLabel,
                'options' => $options,
                'field_width' => $request->input('field_width', 'full'),
                'field_group' => $request->input('field_group'),
            ]);

            return redirect()->route('admin.company-claim-forms.index', $company)
                ->with('message', 'Custom veld bijgewerkt.');
        } else {
            // Checkbox/AJAX update validation
            $request->validate([
                'is_enabled' => 'nullable|boolean',
                'is_required' => 'nullable|boolean',
                'include_in_notification' => 'nullable|boolean',
                'conditional_logic' => 'nullable|string',
                'display_order' => 'nullable|integer',
                'field_width' => 'nullable|string',
                'field_group' => 'nullable|string',
            ]);

            $conditionalLogic = null;
            if (!empty($request->input('conditional_logic'))) {
                $conditionalLogic = is_string($request->input('conditional_logic')) 
                    ? json_decode($request->input('conditional_logic'), true) 
                    : $request->input('conditional_logic');
            }

            $customField->update([
                'is_enabled' => $request->input('is_enabled', $customField->is_enabled),
                'is_required' => $request->input('is_required', false),
                'include_in_notification' => $request->input('include_in_notification', false),
                'conditional_logic' => $conditionalLogic,
                'display_order' => $request->input('display_order', $customField->display_order),
                'field_width' => $request->input('field_width', $customField->field_width),
                'field_group' => $request->input('field_group', $customField->field_group),
            ]);

            return response()->json(['success' => true]);
        }
    }

    public function deleteCustomField(Request $request, Company $company, CompanyCustomClaimField $customField)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($customField->company_id !== $company->id) {
            abort(Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        $customField->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.company-claim-forms.index', $company)
            ->with('message', 'Custom veld verwijderd.');
    }

    public function copyFromCompany(Request $request, Company $company)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'source_company_id' => 'required|exists:companies,id'
        ]);

        $sourceCompany = Company::findOrFail($request->source_company_id);

        // Copy standard field configurations
        $sourceConfigs = $sourceCompany->claimFormConfigs()->get();
        
        foreach ($sourceConfigs as $sourceConfig) {
            CompanyClaimFormConfig::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'field_name' => $sourceConfig->field_name,
                ],
                [
                    'is_enabled' => $sourceConfig->is_enabled,
                    'is_required' => $sourceConfig->is_required,
                    'include_in_notification' => $sourceConfig->include_in_notification,
                    'display_order' => $sourceConfig->display_order,
                    'conditional_logic' => $sourceConfig->conditional_logic,
                    'notification_label' => $sourceConfig->notification_label,
                ]
            );
        }

        // Copy custom fields
        $sourceCustomFields = $sourceCompany->customClaimFields()->get();
        
        // Get existing custom field names for the target company
        $existingFieldNames = $company->customClaimFields()->pluck('field_name')->toArray();
        
        foreach ($sourceCustomFields as $sourceField) {
            // Skip if field name already exists
            if (in_array($sourceField->field_name, $existingFieldNames)) {
                continue;
            }

            CompanyCustomClaimField::create([
                'company_id' => $company->id,
                'field_type' => $sourceField->field_type,
                'field_name' => $sourceField->field_name,
                'field_label' => $sourceField->field_label,
                'options' => $sourceField->options,
                'is_required' => $sourceField->is_required,
                'include_in_notification' => $sourceField->include_in_notification,
                'conditional_logic' => $sourceField->conditional_logic,
                'display_order' => $sourceField->display_order,
                'field_width' => $sourceField->field_width ?? 'full',
                'field_group' => $sourceField->field_group,
            ]);
        }

        return redirect()->route('admin.company-claim-forms.index', $company)
            ->with('message', 'Velden succesvol gekopieerd van ' . $sourceCompany->name . '. Notificaties zijn niet gekopieerd omdat deze bedrijfsspecifiek zijn.');
    }

    public function bulkUpdate(Request $request, Company $company)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'action' => 'required|in:enable,disable,require,unrequire,include_notification,exclude_notification,set_width,set_group',
            'fields' => 'required|array|min:1',
            'fields.*.field_name' => 'required|string',
            'fields.*.type' => 'required|in:standard,custom',
            'fields.*.id' => 'nullable|integer',
            'value' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            $results = [
                'success' => [],
                'failed' => [],
                'skipped' => []
            ];

            $action = $validated['action'];
            $value = $validated['value'] ?? null;

            foreach ($validated['fields'] as $fieldData) {
                $fieldName = $fieldData['field_name'];
                $fieldType = $fieldData['type'];

                try {
                    if ($fieldType === 'standard') {
                        // Update or create standard field config
                        $config = CompanyClaimFormConfig::firstOrCreate(
                            [
                                'company_id' => $company->id,
                                'field_name' => $fieldName,
                            ],
                            [
                                'is_enabled' => false,
                                'is_required' => false,
                                'include_in_notification' => false,
                                'display_order' => 999,
                            ]
                        );

                        switch ($action) {
                            case 'enable':
                                $config->is_enabled = true;
                                break;
                            case 'disable':
                                $config->is_enabled = false;
                                break;
                            case 'require':
                                $config->is_required = true;
                                $config->is_enabled = true; // Auto-enable when requiring
                                break;
                            case 'unrequire':
                                $config->is_required = false;
                                break;
                            case 'include_notification':
                                $config->include_in_notification = true;
                                break;
                            case 'exclude_notification':
                                $config->include_in_notification = false;
                                break;
                            case 'set_width':
                                if (in_array($value, ['full', 'half', 'third', 'quarter'])) {
                                    $config->field_width = $value;
                                } else {
                                    throw new \Exception('Invalid width value');
                                }
                                break;
                            case 'set_group':
                                $config->field_group = $value;
                                break;
                        }

                        $config->save();
                        $results['success'][] = $fieldName;

                    } elseif ($fieldType === 'custom') {
                        // Update custom field
                        $customFieldId = $fieldData['id'] ?? null;
                        
                        if (!$customFieldId) {
                            $results['failed'][] = [
                                'field' => $fieldName,
                                'error' => 'Custom field ID missing'
                            ];
                            continue;
                        }

                        $customField = CompanyCustomClaimField::where('company_id', $company->id)
                            ->findOrFail($customFieldId);

                        // Skip HTML fields for certain actions
                        if ($customField->field_type === 'html' && 
                            in_array($action, ['require', 'unrequire', 'include_notification', 'exclude_notification'])) {
                            $results['skipped'][] = [
                                'field' => $fieldName,
                                'reason' => 'HTML fields cannot be required or included in notifications'
                            ];
                            continue;
                        }

                        switch ($action) {
                            case 'enable':
                                $customField->is_enabled = true;
                                break;
                            case 'disable':
                                $customField->is_enabled = false;
                                break;
                            case 'require':
                                $customField->is_required = true;
                                $customField->is_enabled = true; // Auto-enable when requiring
                                break;
                            case 'unrequire':
                                $customField->is_required = false;
                                break;
                            case 'include_notification':
                                $customField->include_in_notification = true;
                                break;
                            case 'exclude_notification':
                                $customField->include_in_notification = false;
                                break;
                            case 'set_width':
                                if (in_array($value, ['full', 'half', 'third', 'quarter'])) {
                                    $customField->field_width = $value;
                                } else {
                                    throw new \Exception('Invalid width value');
                                }
                                break;
                            case 'set_group':
                                $customField->field_group = $value;
                                break;
                        }

                        $customField->save();
                        $results['success'][] = $fieldName;
                    }

                } catch (\Exception $e) {
                    $results['failed'][] = [
                        'field' => $fieldName,
                        'error' => $e->getMessage()
                    ];
                }
            }

            DB::commit();

            $totalProcessed = count($results['success']);
            $totalFailed = count($results['failed']);
            $totalSkipped = count($results['skipped']);

            return response()->json([
                'success' => true,
                'message' => "Bulk update completed: {$totalProcessed} updated, {$totalSkipped} skipped, {$totalFailed} failed",
                'results' => $results,
                'counts' => [
                    'success' => $totalProcessed,
                    'failed' => $totalFailed,
                    'skipped' => $totalSkipped
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Bulk update failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
