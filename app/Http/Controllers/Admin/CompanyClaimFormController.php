<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyClaimToken;
use App\Models\CompanyClaimFormConfig;
use App\Models\CompanyClaimFormNotification;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyClaimFormController extends Controller
{
    public function index(Company $company)
    {
        abort_if(Gate::denies('company_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tokens = $company->claimTokens()->latest()->get();
        $formConfigs = $company->claimFormConfigs()->orderBy('display_order')->get();
        $notifications = $company->claimFormNotifications()->get();
        $availableFields = CompanyClaimFormConfig::getAvailableFields();

        return view('admin.company-claim-forms.index', compact(
            'company',
            'tokens',
            'formConfigs',
            'notifications',
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
                ]
            );
        }

        return redirect()->route('admin.company-claim-forms.index', $company)
            ->with('message', 'Formulier configuratie bijgewerkt.');
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
}
