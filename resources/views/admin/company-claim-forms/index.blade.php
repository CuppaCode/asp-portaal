@extends('layouts.admin')
@section('styles')
<style>
    .ui-state-highlight {
        height: 50px;
        background-color: #f0f0f0;
        border: 2px dashed #ccc;
    }
    #sortable-all-fields tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <h3>Claim Formulier Configuratie - {{ $company->name }}</h3>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('admin.companies.show', $company->id) }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Terug naar bedrijf
            </a>
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#copyFieldsModal">
                <i class="fa fa-copy"></i> Kopieer Velden van Ander Bedrijf
            </button>
        </div>

        {{-- Tokens Section --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Formulier Tokens</h4>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createTokenModal">
                    <i class="fa fa-plus"></i> Nieuwe Token
                </button>
            </div>
            <div class="card-body">
                @if($tokens->isEmpty())
                    <p class="text-muted">Geen tokens aangemaakt. Maak een token aan om het formulier te delen.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Label</th>
                                    <th>Token URL</th>
                                    <th>Status</th>
                                    <th>Inzendingen</th>
                                    <th>Laatst gebruikt</th>
                                    <th>Acties</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tokens as $token)
                                <tr>
                                    <td>{{ $token->label }}</td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm" value="{{ $token->url }}" id="token-{{ $token->id }}" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-sm btn-outline-secondary" onclick="copyToken('token-{{ $token->id }}')">
                                                    <i class="fa fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $token->is_active ? 'success' : 'secondary' }}">
                                            {{ $token->is_active ? 'Actief' : 'Inactief' }}
                                        </span>
                                    </td>
                                    <td>{{ $token->submission_count }}</td>
                                    <td>{{ $token->last_used_at ? $token->last_used_at->format('d-m-Y H:i') : '-' }}</td>
                                    <td>
                                        <form action="{{ route('admin.company-claim-forms.toggle-token', [$company, $token]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-{{ $token->is_active ? 'warning' : 'success' }}">
                                                {{ $token->is_active ? 'Deactiveren' : 'Activeren' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.company-claim-forms.delete-token', [$company, $token]) }}" method="POST" class="d-inline" onsubmit="return confirm('Weet u zeker dat u deze token wilt verwijderen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Expiry Settings Section --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0">Verval & Herinnering Instellingen</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.company-claim-forms.update-expiry', $company) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="draft_expiry_days">Concept verloopt na (dagen)</label>
                                <input type="number" class="form-control" id="draft_expiry_days" name="draft_expiry_days" 
                                    value="{{ $company->draft_expiry_days ?? 30 }}" min="1" required>
                                <small class="form-text text-muted">Aantal dagen voordat een concept automatisch wordt afgewezen</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="draft_reminder_days">Eerste herinnering na (dagen)</label>
                                <input type="number" class="form-control" id="draft_reminder_days" name="draft_reminder_days" 
                                    value="{{ $company->draft_reminder_days ?? 7 }}" min="1" required>
                                <small class="form-text text-muted">Aantal dagen na indiening voor eerste herinnering</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="draft_reminder_frequency_days">Herinnering elke (dagen)</label>
                                <input type="number" class="form-control" id="draft_reminder_frequency_days" name="draft_reminder_frequency_days" 
                                    value="{{ $company->draft_reminder_frequency_days ?? 7 }}" min="1" required>
                                <small class="form-text text-muted">Aantal dagen tussen herhaalde herinneringen</small>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Opslaan</button>
                </form>
            </div>
        </div>

        {{-- Form Configuration Section --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">Formulier Velden Configuratie</h4>
                    <small class="text-muted">Sleep velden om de volgorde te wijzigen</small>
                </div>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCustomFieldModal">
                    <i class="fa fa-plus"></i> Aangepast Veld Toevoegen
                </button>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.company-claim-forms.update-config', $company) }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 30px;"></th>
                                    <th>Veld</th>
                                    <th>Type</th>
                                    <th>Ingeschakeld</th>
                                    <th>Verplicht</th>
                                    <th>In notificatie</th>
                                    <th>Label</th>
                                    <th>Voorwaardelijke logica</th>
                                    <th style="width: 80px;">Acties</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-all-fields">
                                @php
                                    $existingConfigs = $formConfigs->keyBy('field_name');
                                    
                                    // Combine standard and custom fields with their display orders
                                    $allFields = collect();
                                    
                                    // Add standard fields
                                    foreach($availableFields as $fieldName => $fieldLabel) {
                                        $config = $existingConfigs->get($fieldName);
                                        $allFields->push([
                                            'type' => 'standard',
                                            'name' => $fieldName,
                                            'label' => $fieldLabel,
                                            'order' => $config ? $config->display_order : 999,
                                            'data' => $config
                                        ]);
                                    }
                                    
                                    // Add custom fields
                                    foreach($customFields as $customField) {
                                        $allFields->push([
                                            'type' => 'custom',
                                            'name' => $customField->field_name,
                                            'label' => $customField->field_label,
                                            'order' => $customField->display_order,
                                            'data' => $customField
                                        ]);
                                    }
                                    
                                    // Sort by display order
                                    $allFields = $allFields->sortBy('order')->values();
                                @endphp
                                
                                @foreach($allFields as $field)
                                    @if($field['type'] === 'standard')
                                        @php
                                            $config = $field['data'];
                                            $fieldName = $field['name'];
                                            $fieldLabel = $field['label'];
                                            $isEnabled = $config ? $config->is_enabled : false;
                                            $isRequired = $config ? $config->is_required : false;
                                            // Default complaint_description to be included in notifications
                                            $includeInNotification = $config ? $config->include_in_notification : ($fieldName === 'complaint_description');
                                            $notificationLabel = $config ? $config->notification_label : $fieldLabel;
                                            $conditionalLogic = $config ? $config->conditional_logic : null;
                                            
                                            // Auto-set conditional logic for complaint_description field
                                            if ($fieldName === 'complaint_description' && !$conditionalLogic) {
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
                                        @endphp
                                        <tr data-field="{{ $fieldName }}" data-type="standard" style="cursor: move;">
                                            <td class="text-center" style="cursor: grab;">
                                                <i class="fa fa-bars text-muted"></i>
                                            </td>
                                            <td><strong>{{ $fieldLabel }}</strong></td>
                                            <td><span class="badge badge-primary">Standaard</span></td>
                                            <td>
                                                <input type="checkbox" name="fields[{{ $fieldName }}][is_enabled]" value="1" 
                                                    {{ $isEnabled ? 'checked' : '' }} class="field-enabled">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="fields[{{ $fieldName }}][is_required]" value="1" 
                                                    {{ $isRequired ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="fields[{{ $fieldName }}][include_in_notification]" value="1" 
                                                    {{ $includeInNotification ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm" 
                                                    name="fields[{{ $fieldName }}][notification_label]" 
                                                    value="{{ $notificationLabel }}" placeholder="{{ $fieldLabel }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary open-conditional-modal" 
                                                    data-field-name="{{ $fieldName }}" 
                                                    data-field-label="{{ $fieldLabel }}">
                                                    <i class="fa fa-code-branch"></i> 
                                                    <span id="logic-indicator-{{ $fieldName }}">
                                                        {{ $conditionalLogic ? 'Bewerken' : 'Instellen' }}
                                                    </span>
                                                </button>
                                                <input type="hidden" name="fields[{{ $fieldName }}][conditional_logic]" 
                                                    id="conditional-logic-{{ $fieldName }}" 
                                                    value='{{ $conditionalLogic ? json_encode($conditionalLogic) : "" }}'>
                                            </td>
                                            <td>
                                                <input type="hidden" name="fields[{{ $fieldName }}][display_order]" value="{{ $field['order'] }}" class="display-order">
                                            </td>
                                        </tr>
                                    @else
                                        @php
                                            $customField = $field['data'];
                                        @endphp
                                        <tr data-field="custom_{{ $customField->field_name }}" data-type="custom" data-id="{{ $customField->id }}" style="cursor: move;">
                                            <td class="text-center" style="cursor: grab;">
                                                <i class="fa fa-bars text-muted"></i>
                                            </td>
                                            <td>
                                                <strong>{{ $customField->field_label }}</strong><br>
                                                <small class="text-muted"><code>{{ $customField->field_name }}</code></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">
                                                    @if($customField->field_type === 'text') Tekst
                                                    @elseif($customField->field_type === 'textarea') Tekstgebied  
                                                    @elseif($customField->field_type === 'select') Selectie
                                                    @elseif($customField->field_type === 'html') HTML
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <input type="checkbox" class="custom-field-enabled" data-id="{{ $customField->id }}" 
                                                    {{ $customField->is_enabled ? 'checked' : '' }}>
                                            </td>
                                            @if($customField->field_type === 'html')
                                                <td colspan="2" class="text-muted">
                                                    <small><em>Alleen voor weergave</em></small>
                                                </td>
                                            @else
                                                <td>
                                                    <input type="checkbox" class="custom-field-required" data-id="{{ $customField->id }}" 
                                                        {{ $customField->is_required ? 'checked' : '' }}>
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="custom-field-notification" data-id="{{ $customField->id }}"
                                                        {{ $customField->include_in_notification ? 'checked' : '' }}>
                                                </td>
                                            @endif
                                            <td>
                                                @if($customField->field_type === 'html')
                                                    <textarea class="form-control form-control-sm" rows="2" disabled>{{ Str::limit(strip_tags($customField->field_label), 50) }}</textarea>
                                                @else
                                                    <input type="text" class="form-control form-control-sm" value="{{ $customField->field_label }}" disabled>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm {{ $customField->conditional_logic ? 'btn-success' : 'btn-secondary' }} open-conditional-modal" 
                                                    data-field-name="custom_{{ $customField->field_name }}" 
                                                    data-field-label="{{ strip_tags($customField->field_label) }}" 
                                                    data-custom-field-id="{{ $customField->id }}">
                                                    <i class="fa fa-code-branch"></i>
                                                    <span id="logic-indicator-custom_{{ $customField->field_name }}">
                                                        {{ $customField->conditional_logic ? 'Bewerken' : 'Instellen' }}
                                                    </span>
                                                </button>
                                                <input type="hidden" id="conditional-logic-custom_{{ $customField->field_name }}" 
                                                    value='{{ $customField->conditional_logic ? json_encode($customField->conditional_logic) : "" }}'>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary edit-custom-field mr-1" 
                                                    data-id="{{ $customField->id }}"
                                                    data-type="{{ $customField->field_type }}"
                                                    data-name="{{ $customField->field_name }}"
                                                    data-label="{{ $customField->field_label }}"
                                                    data-options="{{ $customField->options ? implode("\n", $customField->options) : '' }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete-custom-field" 
                                                    data-id="{{ $customField->id }}"
                                                    data-url="{{ route('admin.company-claim-forms.delete-custom-field', [$company, $customField]) }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-primary">Configuratie Opslaan</button>
                </form>
            </div>
        </div>

        {{-- Notification Recipients Section --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Notificatie Ontvangers</h4>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addNotificationModal">
                    <i class="fa fa-plus"></i> Ontvanger Toevoegen
                </button>
            </div>
            <div class="card-body">
                @if($notifications->isEmpty())
                    <p class="text-muted">Geen notificatie ontvangers ingesteld.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Naam</th>
                                    <th>Email</th>
                                    <th>Acties</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notifications as $notification)
                                <tr>
                                    <td>{{ $notification->name ?? '-' }}</td>
                                    <td>{{ $notification->email }}</td>
                                    <td>
                                        <form action="{{ route('admin.company-claim-forms.delete-notification', [$company, $notification]) }}" method="POST" class="d-inline" onsubmit="return confirm('Weet u zeker dat u deze ontvanger wilt verwijderen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Create Token Modal --}}
<div class="modal fade" id="createTokenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.company-claim-forms.create-token', $company) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nieuwe Token Aanmaken</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="label">Label *</label>
                        <input type="text" class="form-control" id="label" name="label" required 
                            placeholder="Bijv. Chauffeurs, Klanten, etc.">
                        <small class="form-text text-muted">Geef de token een herkenbare naam</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-primary">Token Aanmaken</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Conditional Logic Modal --}}
<div class="modal fade" id="conditionalLogicModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Voorwaardelijke Logica: <span id="modal-field-label"></span></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Stel in wanneer dit veld getoond moet worden. Als leeg, wordt het veld altijd getoond.</p>
                
                <div id="conditions-container">
                    <!-- Conditions will be added here dynamically -->
                </div>
                
                <button type="button" class="btn btn-sm btn-success" onclick="addCondition()">
                    <i class="fa fa-plus"></i> Voorwaarde Toevoegen
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                <button type="button" class="btn btn-danger" onclick="clearConditions()">Alles Wissen</button>
                <button type="button" class="btn btn-primary" onclick="saveConditionalLogic()">Opslaan</button>
            </div>
        </div>
    </div>
</div>

{{-- Add Notification Modal --}}
<div class="modal fade" id="addNotificationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.company-claim-forms.store-notification', $company) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Notificatie Ontvanger Toevoegen</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Naam</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-primary">Toevoegen</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Custom Field Modal --}}
<div class="modal fade" id="addCustomFieldModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.company-claim-forms.store-custom-field', $company) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Aangepast Veld Toevoegen</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="field_type">Veldtype *</label>
                        <select class="form-control" id="field_type" name="field_type" required onchange="toggleOptions()">
                            <option value="">Selecteer...</option>
                            <option value="text">Tekstveld</option>
                            <option value="textarea">Tekstgebied</option>
                            <option value="select">Selectie (dropdown)</option>
                            <option value="html">HTML Inhoud (alleen weergave)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="field_name">Veldnaam (technisch) *</label>
                        <input type="text" class="form-control" id="field_name" name="field_name" required 
                            pattern="[a-z0-9_]+" placeholder="bijv. extra_info">
                        <small class="form-text text-muted">Alleen kleine letters, cijfers en underscores</small>
                    </div>
                    <div class="form-group" id="label_group">
                        <label for="field_label">Label (weergave) *</label>
                        <input type="text" class="form-control" id="field_label" name="field_label" required 
                            placeholder="bijv. Extra Informatie">
                    </div>
                    <div class="form-group" id="html_content_group" style="display:none;">
                        <label for="html_content">HTML Inhoud *</label>
                        <textarea class="form-control" id="html_content" name="html_content" rows="5"
                            placeholder="Voer HTML in voor instructies of notices..."></textarea>
                        <small class="form-text text-muted">Deze inhoud wordt weergegeven in een blauwe notice box</small>
                    </div>
                    <div class="form-group" id="options_group" style="display:none;">
                        <label for="options">Opties *</label>
                        <textarea class="form-control" id="options" name="options" rows="5"
                            placeholder="Eén optie per regel"></textarea>
                        <small class="form-text text-muted">Voor selectie velden: elke optie op een nieuwe regel</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-primary">Toevoegen</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Custom Field Modal --}}
<div class="modal fade" id="editCustomFieldModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCustomFieldForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Aangepast Veld Bewerken</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Veldtype</label>
                        <input type="text" class="form-control" id="edit_field_type_display" disabled>
                    </div>
                    <div class="form-group" id="edit_field_name_group">
                        <label for="edit_field_name">Veldnaam (technisch)</label>
                        <input type="text" class="form-control" id="edit_field_name" disabled>
                        <small class="form-text text-muted">Veldnaam kan niet gewijzigd worden</small>
                    </div>
                    <div class="form-group" id="edit_label_group">
                        <label for="edit_field_label">Label (weergave) *</label>
                        <input type="text" class="form-control" id="edit_field_label" name="field_label" required 
                            placeholder="bijv. Extra Informatie">
                    </div>
                    <div class="form-group" id="edit_html_content_group" style="display:none;">
                        <label for="edit_html_content">HTML Inhoud *</label>
                        <textarea class="form-control" id="edit_html_content" name="html_content" rows="5"
                            placeholder="Voer HTML in voor instructies of notices..."></textarea>
                        <small class="form-text text-muted">Deze inhoud wordt weergegeven in een blauwe notice box</small>
                    </div>
                    <div class="form-group" id="edit_options_group" style="display:none;">
                        <label for="edit_options">Opties *</label>
                        <textarea class="form-control" id="edit_options" name="options" rows="5"
                            placeholder="Eén optie per regel"></textarea>
                        <small class="form-text text-muted">Voor selectie velden: elke optie op een nieuwe regel</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-primary">Opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Copy Fields Modal --}}
<div class="modal fade" id="copyFieldsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.company-claim-forms.copy-from-company', $company) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Velden Kopiëren</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Hiermee kopieert u alle standaard- en aangepaste velden van een ander bedrijf naar dit bedrijf. Notificatie-ontvangers worden <strong>niet</strong> gekopieerd omdat deze bedrijfsspecifiek zijn.
                    </div>
                    <div class="form-group">
                        <label for="source_company_id">Selecteer Bronbedrijf <span class="text-danger">*</span></label>
                        <select name="source_company_id" id="source_company_id" class="form-control" required>
                            <option value="">Selecteer een bedrijf...</option>
                            @foreach(\App\Models\Company::where('id', '!=', $company->id)->orderBy('name')->get() as $otherCompany)
                                <option value="{{ $otherCompany->id }}">{{ $otherCompany->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            Bestaande configuratie wordt overschreven voor standaard velden. Aangepaste velden met dezelfde naam worden overgeslagen.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fa fa-copy"></i> Velden Kopiëren
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
function copyToken(elementId) {
    const input = document.getElementById(elementId);
    input.select();
    document.execCommand('copy');
    alert('Token URL gekopieerd naar klembord!');
}

// Make fields sortable
$(document).ready(function() {
    $('#sortable-all-fields').sortable({
        handle: 'td:first-child',
        cursor: 'move',
        placeholder: 'ui-state-highlight',
        update: function(event, ui) {
            // Update display order for all fields
            $('#sortable-all-fields tr').each(function(index) {
                const $row = $(this);
                const fieldType = $row.data('type');
                
                if (fieldType === 'standard') {
                    // Update hidden input for standard fields (submitted via form)
                    $row.find('.display-order').val(index);
                } else if (fieldType === 'custom') {
                    // Update via AJAX for custom fields
                    const fieldId = $row.data('id');
                    $.ajax({
                        url: '{{ route("admin.company-claim-forms.update-custom-field", [$company, "__ID__"]) }}'.replace('__ID__', fieldId),
                        method: 'PATCH',
                        data: {
                            _token: '{{ csrf_token() }}',
                            display_order: index
                        }
                    });
                }
            });
        }
    });

    // Handle custom field checkbox changes
    $('.custom-field-enabled, .custom-field-required, .custom-field-notification').on('change', function() {
        let fieldId = $(this).data('id');
        let isEnabled = $(`.custom-field-enabled[data-id="${fieldId}"]`).is(':checked');
        let isRequired = $(`.custom-field-required[data-id="${fieldId}"]`).is(':checked');
        let includeInNotification = $(`.custom-field-notification[data-id="${fieldId}"]`).is(':checked');
        
        $.ajax({
            url: '{{ route("admin.company-claim-forms.update-custom-field", [$company, "__ID__"]) }}'.replace('__ID__', fieldId),
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                is_enabled: isEnabled,
                is_required: isRequired,
                include_in_notification: includeInNotification
            }
        });
    });

    // Handle custom field edit
    $('.edit-custom-field').on('click', function(e) {
        e.preventDefault();
        
        const $button = $(this);
        const fieldId = $button.data('id');
        const fieldType = $button.data('type');
        const fieldName = $button.data('name');
        const fieldLabel = $button.data('label');
        const fieldOptions = $button.data('options');
        
        // Set form action URL
        const updateUrl = '{{ route("admin.company-claim-forms.update-custom-field", [$company, ":id"]) }}'.replace(':id', fieldId);
        $('#editCustomFieldForm').attr('action', updateUrl);
        
        // Display field type
        const typeLabels = {
            'text': 'Tekstveld',
            'textarea': 'Tekstgebied',
            'select': 'Selectie (dropdown)',
            'html': 'HTML Inhoud (alleen weergave)'
        };
        $('#edit_field_type_display').val(typeLabels[fieldType] || fieldType);
        
        // Show/hide fields based on type
        if (fieldType === 'html') {
            $('#edit_field_name_group').hide();
            $('#edit_label_group').hide();
            $('#edit_html_content_group').show();
            $('#edit_options_group').hide();
            $('#edit_html_content').val(fieldLabel).prop('required', true);
            $('#edit_field_label').prop('required', false);
            $('#edit_options').prop('required', false);
        } else if (fieldType === 'select') {
            $('#edit_field_name_group').show();
            $('#edit_label_group').show();
            $('#edit_html_content_group').hide();
            $('#edit_options_group').show();
            $('#edit_field_name').val(fieldName);
            $('#edit_field_label').val(fieldLabel).prop('required', true);
            $('#edit_options').val(fieldOptions).prop('required', true);
            $('#edit_html_content').prop('required', false);
        } else {
            $('#edit_field_name_group').show();
            $('#edit_label_group').show();
            $('#edit_html_content_group').hide();
            $('#edit_options_group').hide();
            $('#edit_field_name').val(fieldName);
            $('#edit_field_label').val(fieldLabel).prop('required', true);
            $('#edit_options').prop('required', false);
            $('#edit_html_content').prop('required', false);
        }
        
        $('#editCustomFieldModal').modal('show');
    });

    // Handle custom field delete
    $('.delete-custom-field').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (!confirm('Weet u zeker dat u dit veld wilt verwijderen?')) {
            return;
        }
        
        const $button = $(this);
        const fieldId = $button.data('id');
        const url = $button.data('url');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            },
            success: function() {
                $button.closest('tr').fadeOut(300, function() {
                    $(this).remove();
                });
            },
            error: function() {
                alert('Er is een fout opgetreden bij het verwijderen.');
            }
        });
    });

    // Handle conditional modal opening
    $(document).on('click', '.open-conditional-modal', function(e) {
        e.preventDefault();
        const fieldName = $(this).data('field-name');
        const fieldLabel = $(this).data('field-label');
        const customFieldId = $(this).data('custom-field-id') || null;
        openConditionalModal(fieldName, fieldLabel, customFieldId);
    });
});

let currentFieldName = '';
let currentCustomFieldId = null;
let conditionCounter = 0;
const availableFields = @json($availableFields);
const customFields = @json($customFields->mapWithKeys(function($cf) {
    return ['custom_' . $cf->field_name => $cf->field_label];
})->toArray());
const allFields = {...availableFields, ...customFields};

function toggleOptions() {
    const fieldType = document.getElementById('field_type').value;
    const optionsGroup = document.getElementById('options_group');
    const htmlContentGroup = document.getElementById('html_content_group');
    const labelGroup = document.getElementById('label_group');
    const fieldNameInput = document.getElementById('field_name');
    const fieldLabelInput = document.getElementById('field_label');
    const htmlContentInput = document.getElementById('html_content');
    
    if (fieldType === 'select') {
        optionsGroup.style.display = 'block';
        htmlContentGroup.style.display = 'none';
        labelGroup.style.display = 'block';
        document.getElementById('options').required = true;
        htmlContentInput.required = false;
        fieldNameInput.required = true;
        fieldLabelInput.required = true;
    } else if (fieldType === 'html') {
        optionsGroup.style.display = 'none';
        htmlContentGroup.style.display = 'block';
        labelGroup.style.display = 'none';
        document.getElementById('options').required = false;
        htmlContentInput.required = true;
        fieldNameInput.required = false;
        fieldLabelInput.required = false;
    } else {
        optionsGroup.style.display = 'none';
        htmlContentGroup.style.display = 'none';
        labelGroup.style.display = 'block';
        document.getElementById('options').required = false;
        htmlContentInput.required = false;
        fieldNameInput.required = true;
        fieldLabelInput.required = true;
    }
}

function openConditionalModal(fieldName, fieldLabel, customFieldId = null) {
    currentFieldName = fieldName;
    currentCustomFieldId = customFieldId;
    document.getElementById('modal-field-label').textContent = fieldLabel;
    
    // Load existing conditions
    const existingLogic = document.getElementById('conditional-logic-' + fieldName).value;
    const conditionsContainer = document.getElementById('conditions-container');
    conditionsContainer.innerHTML = '';
    conditionCounter = 0;
    
    if (existingLogic && existingLogic.trim() !== '') {
        try {
            const logic = JSON.parse(existingLogic);
            if (logic.conditions && logic.conditions.length > 0) {
                logic.conditions.forEach(condition => {
                    addCondition(condition);
                });
            }
        } catch (e) {
            console.error('Error parsing conditional logic:', e);
        }
    }
    
    $('#conditionalLogicModal').modal('show');
}

function addCondition(condition = null) {
    const id = conditionCounter++;
    const container = document.getElementById('conditions-container');
    
    const conditionHtml = `
        <div class="card mb-2" id="condition-${id}">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label>Veld</label>
                        <select class="form-control form-control-sm condition-field" data-id="${id}">
                            <option value="">Selecteer veld...</option>
                            ${Object.entries(allFields).map(([name, label]) => 
                                `<option value="${name}" ${condition && condition.field === name ? 'selected' : ''}>${label}</option>`
                            ).join('')}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Operator</label>
                        <select class="form-control form-control-sm condition-operator" data-id="${id}">
                            <option value="equals" ${condition && condition.operator === 'equals' ? 'selected' : ''}>Is gelijk aan</option>
                            <option value="not_equals" ${condition && condition.operator === 'not_equals' ? 'selected' : ''}>Is niet gelijk aan</option>
                            <option value="empty" ${condition && condition.operator === 'empty' ? 'selected' : ''}>Is leeg</option>
                            <option value="not_empty" ${condition && condition.operator === 'not_empty' ? 'selected' : ''}>Is niet leeg</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Waarde</label>
                        <input type="text" class="form-control form-control-sm condition-value" data-id="${id}" 
                            value="${condition && condition.value ? condition.value : ''}" 
                            placeholder="Waarde...">
                    </div>
                    <div class="col-md-1">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-sm btn-danger form-control" onclick="removeCondition(${id})">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', conditionHtml);
}

function removeCondition(id) {
    const element = document.getElementById('condition-' + id);
    if (element) {
        element.remove();
    }
}

function clearConditions() {
    if (confirm('Weet u zeker dat u alle voorwaarden wilt wissen?')) {
        document.getElementById('conditions-container').innerHTML = '';
        conditionCounter = 0;
    }
}

function saveConditionalLogic() {
    const conditions = [];
    const conditionElements = document.querySelectorAll('#conditions-container .card');
    
    conditionElements.forEach(card => {
        const id = card.id.split('-')[1];
        const field = card.querySelector('.condition-field').value;
        const operator = card.querySelector('.condition-operator').value;
        const value = card.querySelector('.condition-value').value;
        
        if (field) {
            conditions.push({ field, operator, value });
        }
    });
    
    let logicObject = null;
    if (conditions.length > 0) {
        logicObject = {
            operator: 'AND',
            conditions: conditions
        };
    }
    
    // Determine if this is a custom field or standard field
    const isCustomField = currentFieldName.startsWith('custom_');
    
    if (isCustomField && currentCustomFieldId) {
        // Save via AJAX for custom fields
        const url = '{{ route("admin.company-claim-forms.update-custom-field", ["company" => $company->id, "customField" => "__FIELD_ID__"]) }}'.replace('__FIELD_ID__', currentCustomFieldId);
        
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PATCH',
                conditional_logic: logicObject ? JSON.stringify(logicObject) : ''
            },
            success: function(response) {
                // Update hidden input
                const hiddenInput = document.getElementById('conditional-logic-' + currentFieldName);
                hiddenInput.value = logicObject ? JSON.stringify(logicObject) : '';
                
                // Update button appearance
                const button = document.querySelector(`button[onclick*="${currentFieldName}"]`);
                if (button) {
                    if (logicObject) {
                        button.classList.remove('btn-secondary');
                        button.classList.add('btn-success');
                    } else {
                        button.classList.remove('btn-success');
                        button.classList.add('btn-secondary');
                    }
                }
                
                $('#conditionalLogicModal').modal('hide');
            },
            error: function() {
                alert('Er is een fout opgetreden bij het opslaan');
            }
        });
    } else {
        // For standard fields, just save to hidden input
        const hiddenInput = document.getElementById('conditional-logic-' + currentFieldName);
        hiddenInput.value = logicObject ? JSON.stringify(logicObject) : '';
        
        // Update button text
        const indicator = document.getElementById('logic-indicator-' + currentFieldName);
        indicator.textContent = logicObject ? 'Bewerken' : 'Instellen';
        
        $('#conditionalLogicModal').modal('hide');
    }
}
</script>
@endsection
