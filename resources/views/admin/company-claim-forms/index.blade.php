@extends('layouts.admin')
@section('styles')
<style>
    /* Drag and drop styling */
    .ui-state-highlight {
        height: 50px;
        background-color: #f0f0f0;
        border: 2px dashed #ccc;
    }
    #sortable-all-fields tr:hover {
        background-color: #f8f9fa;
        cursor: move;
    }
    
    /* Move button styling */
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    /* Vertical drag controls */
    .drag-controls {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
    }
    
    .drag-controls .btn-move {
        padding: 2px 6px;
        font-size: 0.75rem;
        border: none;
        background: transparent;
        color: #6c757d;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .drag-controls .btn-move:hover {
        color: #007bff;
        background-color: #e9ecef;
        border-radius: 3px;
    }
    
    .drag-controls .drag-handle {
        cursor: grab;
        font-size: 1rem;
        color: #6c757d;
        padding: 4px 0;
    }
    
    .drag-controls .drag-handle:hover {
        color: #495057;
    }
    
    /* Smooth transition for row movement */
    #sortable-all-fields tr {
        transition: background-color 0.5s ease;
    }
    
    /* Highlight moved row */
    #sortable-all-fields tr.table-success {
        background-color: #d4edda !important;
    }
    
    /* Improve drag handle visibility */
    #sortable-all-fields tr:hover td:first-child {
        background-color: #f8f9fa;
    }
    
    /* Auto-save indicators */
    .save-indicator {
        display: inline-block;
        margin-left: 5px;
        font-size: 0.75rem;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .save-indicator.show {
        opacity: 1;
    }
    
    .save-indicator.saving {
        color: #ffc107;
    }
    
    .save-indicator.saved {
        color: #28a745;
    }
    
    .save-indicator.error {
        color: #dc3545;
    }

    /* Enhanced table styling */
    .fields-config-table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .fields-config-table thead th {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        font-size: 0.875rem;
        padding: 0.75rem 0.5rem;
        vertical-align: middle;
    }

    .fields-config-table tbody tr {
        border-bottom: 1px solid #e9ecef;
    }

    .fields-config-table tbody tr:nth-child(even) {
        background-color: #fafbfc;
    }

    .fields-config-table tbody tr:hover {
        background-color: #f1f3f5 !important;
    }

    .fields-config-table tbody td {
        padding: 0.625rem 0.5rem;
        vertical-align: middle;
    }

    /* Color-coded badges */
    .badge-type-standard { background-color: #344a9b; color: #ffffff; }
    .badge-type-text { background-color: #17a2b8; color: #ffffff; }
    .badge-type-textarea { background-color: #6c757d; color: #ffffff; }
    .badge-type-select { background-color: #28a745; color: #ffffff; }
    .badge-type-html { background-color: #ffc107; color: #212529; }

    /* Checkbox styling */
    .field-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    /* Bulk operations */
    .bulk-action-bar {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: #344a9b;
        color: white;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        display: none;
        box-shadow: 0 4px 12px rgba(52, 74, 155, 0.3);
    }

    .bulk-action-bar.show {
        display: flex;
        align-items: center;
        justify-content: space-between;
        animation: slideDown 0.3s ease;
    }

    /* Column width optimizations */
    .fields-config-table th:nth-child(11) {
        width: 100px;
        text-align: center;
    }

    .fields-config-table th:nth-child(12) {
        width: 120px;
    }

    .action-buttons {
        display: inline-flex;
        gap: 0.25rem;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .bulk-action-bar .btn {
        margin: 0 0.25rem;
    }

    /* Conditional logic badge */
    .logic-count-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #007bff;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .logic-button-wrapper {
        position: relative;
        display: inline-block;
    }

    /* Responsive improvements */
    @media (max-width: 991px) {
        .fields-config-table th:nth-child(8),
        .fields-config-table td:nth-child(8),
        .fields-config-table th:nth-child(9),
        .fields-config-table td:nth-child(9) {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .drag-controls .btn-move {
            padding: 6px 8px;
            font-size: 1rem;
        }

        .drag-controls .drag-handle {
            font-size: 1.2rem;
            padding: 8px 0;
        }

        .fields-config-table thead th,
        .fields-config-table tbody td {
            font-size: 0.8rem;
            padding: 0.5rem 0.25rem;
        }

        .field-checkbox {
            width: 24px;
            height: 24px;
        }

        .bulk-action-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .bulk-action-bar .btn-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
    }

    /* Touch-friendly on mobile */
    @media (hover: none) {
        .btn {
            min-height: 44px;
        }

        .fields-config-table input[type="text"],
        .fields-config-table select {
            font-size: 16px; /* Prevents iOS zoom */
        }
    }
</style>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        Claim Formulier Configuratie - {{ $company->name }}
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
        <div class="card">
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
                                                <button class="btn btn-sm btn-outline-secondary" onclick="window.open('{{ $token->url }}', '_blank')" title="Open formulier">
                                                    <i class="fas fa-external-link-alt"></i>
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
        <div class="card mb-3">
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
        <div class="card mb-3">
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
                {{-- Bulk Action Bar --}}
                <div class="bulk-action-bar" id="bulk-action-bar">
                    <div>
                        <strong><span id="selected-count">0</span> velden geselecteerd</strong>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-light" data-bulk-action="enable">
                            <i class="fa fa-check"></i> Inschakelen
                        </button>
                        <button type="button" class="btn btn-sm btn-light" data-bulk-action="disable">
                            <i class="fa fa-times"></i> Uitschakelen
                        </button>
                        <button type="button" class="btn btn-sm btn-light" data-bulk-action="require">
                            <i class="fa fa-exclamation-circle"></i> Verplicht Maken
                        </button>
                        <button type="button" class="btn btn-sm btn-light" data-bulk-action="unrequire">
                            <i class="fa fa-circle-o"></i> Niet Verplicht
                        </button>
                        <button type="button" class="btn btn-sm btn-light" data-bulk-action="set_width">
                            <i class="fa fa-arrows-h"></i> Breedte Instellen
                        </button>
                        <button type="button" class="btn btn-sm btn-light" data-bulk-action="set_group">
                            <i class="fa fa-tags"></i> Groep Instellen
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" id="clear-selection">
                            <i class="fa fa-ban"></i> Selectie Wissen
                        </button>
                    </div>
                </div>

                <form action="{{ route('admin.company-claim-forms.update-config', $company) }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table fields-config-table">
                            <thead>
                                <tr>
                                    <th style="width: 30px;"></th>
                                    <th style="width: 40px;">
                                        <input type="checkbox" id="select-all" class="field-checkbox" title="Selecteer alles">
                                    </th>
                                    <th>Veld</th>
                                    <th>Type</th>
                                    <th>Ingeschakeld</th>
                                    <th>Verplicht</th>
                                    <th>In notificatie</th>
                                    <th>Label</th>
                                    <th>Breedte</th>
                                    <th>Groep</th>
                                    <th title="Voorwaardelijke logica"><i class="fa fa-code-branch"></i> Logica</th>
                                    <th style="width: 120px;">Acties</th>
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
                                        <tr data-field="{{ $fieldName }}" data-type="standard" 
                                            data-enabled="{{ $isEnabled ? '1' : '0' }}" 
                                            data-required="{{ $isRequired ? '1' : '0' }}"
                                            data-group="{{ $config ? $config->field_group : '' }}"
                                            style="cursor: move;">
                                            <td class="text-center">
                                                <div class="drag-controls">
                                                    <button type="button" class="btn-move move-to-top" 
                                                            data-field="{{ $fieldName }}" data-type="standard"
                                                            title="Naar boven">
                                                        <i class="fa fa-angle-up"></i>
                                                    </button>
                                                    <div class="drag-handle">
                                                        <i class="fa fa-bars"></i>
                                                    </div>
                                                    <button type="button" class="btn-move move-to-bottom" 
                                                            data-field="{{ $fieldName }}" data-type="standard"
                                                            title="Naar beneden">
                                                        <i class="fa fa-angle-down"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" class="field-checkbox row-select-checkbox" 
                                                       data-field="{{ $fieldName }}" 
                                                       data-type="standard">
                                            </td>
                                            <td><strong>{{ $fieldLabel }}</strong></td>
                                            <td><span class="badge badge-type-standard">Standaard</span></td>
                                            <td>
                                                <input type="checkbox" name="fields[{{ $fieldName }}][is_enabled]" value="1" 
                                                    {{ $isEnabled ? 'checked' : '' }} class="field-checkbox field-enabled standard-field-checkbox" data-field="{{ $fieldName }}" data-config="is_enabled">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="fields[{{ $fieldName }}][is_required]" value="1" 
                                                    {{ $isRequired ? 'checked' : '' }} class="field-checkbox standard-field-checkbox" data-field="{{ $fieldName }}" data-config="is_required">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="fields[{{ $fieldName }}][include_in_notification]" value="1" 
                                                    {{ $includeInNotification ? 'checked' : '' }} class="field-checkbox standard-field-checkbox" data-field="{{ $fieldName }}" data-config="include_in_notification">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm standard-field-input" 
                                                    name="fields[{{ $fieldName }}][notification_label]" 
                                                    value="{{ $notificationLabel }}" placeholder="{{ $fieldLabel }}"
                                                    data-field="{{ $fieldName }}" data-config="notification_label">
                                            </td>
                                            <td>
                                                <select class="form-control form-control-sm standard-field-select" name="fields[{{ $fieldName }}][field_width]"
                                                    data-field="{{ $fieldName }}" data-config="field_width">
                                                    <option value="full" {{ (!$config || $config->field_width === 'full') ? 'selected' : '' }}>Volledig</option>
                                                    <option value="half" {{ $config && $config->field_width === 'half' ? 'selected' : '' }}>Half</option>
                                                    <option value="third" {{ $config && $config->field_width === 'third' ? 'selected' : '' }}>Derde</option>
                                                    <option value="quarter" {{ $config && $config->field_width === 'quarter' ? 'selected' : '' }}>Kwart</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm standard-field-input" 
                                                    name="fields[{{ $fieldName }}][field_group]" 
                                                    value="{{ $config ? $config->field_group : '' }}" 
                                                    placeholder="Optioneel"
                                                    data-field="{{ $fieldName }}" data-config="field_group">
                                            </td>
                                            <td>
                                                @php
                                                    $conditionCount = 0;
                                                    if ($conditionalLogic && isset($conditionalLogic['conditions'])) {
                                                        $conditionCount = count($conditionalLogic['conditions']);
                                                    }
                                                @endphp
                                                <div class="logic-button-wrapper">
                                                    <button type="button" class="btn btn-sm {{ $conditionalLogic ? 'btn-success' : 'btn-outline-secondary' }} open-conditional-modal" 
                                                        data-field-name="{{ $fieldName }}" 
                                                        data-field-label="{{ $fieldLabel }}"
                                                        title="{{ $conditionalLogic ? 'Bewerk voorwaardelijke logica' : 'Stel voorwaardelijke logica in' }}">
                                                        <i class="fa fa-code-branch"></i>
                                                    </button>
                                                    @if($conditionCount > 0)
                                                        <span class="logic-count-badge">{{ $conditionCount }}</span>
                                                    @endif
                                                </div>
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
                                        <tr data-field="custom_{{ $customField->field_name }}" data-type="custom" data-id="{{ $customField->id }}" 
                                            data-enabled="{{ $customField->is_enabled ? '1' : '0' }}"
                                            data-required="{{ $customField->is_required ? '1' : '0' }}"
                                            data-group="{{ $customField->field_group }}"
                                            style="cursor: move;">
                                            <td class="text-center">
                                                <div class="drag-controls">
                                                    <button type="button" class="btn-move move-to-top" 
                                                            data-id="{{ $customField->id }}" data-type="custom"
                                                            title="Naar boven">
                                                        <i class="fa fa-angle-up"></i>
                                                    </button>
                                                    <div class="drag-handle">
                                                        <i class="fa fa-bars"></i>
                                                    </div>
                                                    <button type="button" class="btn-move move-to-bottom" 
                                                            data-id="{{ $customField->id }}" data-type="custom"
                                                            title="Naar beneden">
                                                        <i class="fa fa-angle-down"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" class="field-checkbox row-select-checkbox" 
                                                       data-field="custom_{{ $customField->field_name }}" 
                                                       data-type="custom"
                                                       data-id="{{ $customField->id }}">
                                            </td>
                                            <td>
                                                <strong>{{ strip_tags($customField->field_label) }}</strong><br>
                                                <small class="text-muted"><code>{{ $customField->field_name }}</code></small>
                                            </td>
                                            <td>
                                                <span class="badge {{ $customField->field_type === 'text' ? 'badge-type-text' : ($customField->field_type === 'textarea' ? 'badge-type-textarea' : ($customField->field_type === 'select' ? 'badge-type-select' : 'badge-type-html')) }}">
                                                    @if($customField->field_type === 'text') Tekst
                                                    @elseif($customField->field_type === 'textarea') Tekstgebied  
                                                    @elseif($customField->field_type === 'select') Selectie
                                                    @elseif($customField->field_type === 'html') HTML
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <input type="checkbox" class="field-checkbox custom-field-enabled" data-id="{{ $customField->id }}" 
                                                    {{ $customField->is_enabled ? 'checked' : '' }}>
                                            </td>
                                            @if($customField->field_type === 'html')
                                                <td colspan="2" class="text-muted">
                                                    <small><em>Alleen voor weergave</em></small>
                                                </td>
                                            @else
                                                <td>
                                                    <input type="checkbox" class="field-checkbox custom-field-required" data-id="{{ $customField->id }}" 
                                                        {{ $customField->is_required ? 'checked' : '' }}>
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="field-checkbox custom-field-notification" data-id="{{ $customField->id }}"
                                                        {{ $customField->include_in_notification ? 'checked' : '' }}>
                                                </td>
                                            @endif
                                            <td>
                                                @if($customField->field_type === 'html')
                                                    <textarea class="form-control form-control-sm" rows="2" disabled>{{ Str::limit(strip_tags($customField->field_label), 50) }}</textarea>
                                                @else
                                                    <input type="text" class="form-control form-control-sm" value="{{ strip_tags($customField->field_label) }}" disabled>
                                                @endif
                                            </td>
                                            <td>
                                                <select class="form-control form-control-sm custom-field-width" data-id="{{ $customField->id }}">
                                                    <option value="full" {{ $customField->field_width === 'full' ? 'selected' : '' }}>Volledig</option>
                                                    <option value="half" {{ $customField->field_width === 'half' ? 'selected' : '' }}>Half</option>
                                                    <option value="third" {{ $customField->field_width === 'third' ? 'selected' : '' }}>Derde</option>
                                                    <option value="quarter" {{ $customField->field_width === 'quarter' ? 'selected' : '' }}>Kwart</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm custom-field-group" 
                                                    data-id="{{ $customField->id }}" 
                                                    value="{{ $customField->field_group }}" 
                                                    placeholder="Optioneel">
                                            </td>
                                            <td>
                                                @php
                                                    $customConditionCount = 0;
                                                    if ($customField->conditional_logic && isset($customField->conditional_logic['conditions'])) {
                                                        $customConditionCount = count($customField->conditional_logic['conditions']);
                                                    }
                                                @endphp
                                                <div class="logic-button-wrapper">
                                                    <button type="button" class="btn btn-sm {{ $customField->conditional_logic ? 'btn-success' : 'btn-outline-secondary' }} open-conditional-modal" 
                                                        data-field-name="custom_{{ $customField->field_name }}" 
                                                        data-field-label="{{ strip_tags($customField->field_label) }}" 
                                                        data-custom-field-id="{{ $customField->id }}"
                                                        title="{{ $customField->conditional_logic ? 'Bewerk voorwaardelijke logica' : 'Stel voorwaardelijke logica in' }}">
                                                        <i class="fa fa-code-branch"></i>
                                                    </button>
                                                    @if($customConditionCount > 0)
                                                        <span class="logic-count-badge">{{ $customConditionCount }}</span>
                                                    @endif
                                                </div>
                                                <input type="hidden" id="conditional-logic-custom_{{ $customField->field_name }}" 
                                                    value='{{ $customField->conditional_logic ? json_encode($customField->conditional_logic) : "" }}'>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button type="button" class="btn btn-sm btn-primary edit-custom-field" 
                                                        title="Bewerken"
                                                        data-id="{{ $customField->id }}"
                                                        data-type="{{ $customField->field_type }}"
                                                        data-name="{{ $customField->field_name }}"
                                                        data-label="{{ $customField->field_label }}"
                                                        data-options="{{ $customField->options ? implode("\n", $customField->options) : '' }}">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-custom-field" 
                                                        title="Verwijderen"
                                                        data-id="{{ $customField->id }}"
                                                        data-url="{{ route('admin.company-claim-forms.delete-custom-field', [$company, $customField]) }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info mb-3">
                        <i class="fa fa-info-circle"></i> <strong>Auto-opslaan ingeschakeld:</strong> Alle wijzigingen worden automatisch opgeslagen wanneer u een configuratie aanpast.
                    </div>
                    <button type="submit" class="btn btn-secondary">
                        <i class="fa fa-save"></i> Handmatig Opslaan (optioneel)
                    </button>
                </form>
            </div>
        </div>

        {{-- Notification Recipients Section --}}
        <div class="card mb-3">
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

// Shared function to update all field orders
function updateAllFieldOrders() {
    $('#sortable-all-fields tr').each(function(index) {
        const $row = $(this);
        const fieldType = $row.data('type');
        
        if (fieldType === 'standard') {
            // Update hidden input for standard fields
            $row.find('.display-order').val(index);
            // Also trigger auto-save for standard field order
            const fieldName = $row.data('field');
            updateStandardField(fieldName, 'display_order', index);
        } else if (fieldType === 'custom') {
            // Update via AJAX for custom fields
            const fieldId = $row.data('id');
            $.ajax({
                url: '{{ route("admin.company-claim-forms.update-custom-field", [$company, "__ID__"]) }}'.replace('__ID__', fieldId),
                method: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}',
                    display_order: index
                },
                success: function() {
                    console.log('Order updated for field ' + fieldId);
                }
            });
        }
    });
}

// Debounce function for text inputs
let debounceTimers = {};
function debounce(key, callback, delay = 1000) {
    clearTimeout(debounceTimers[key]);
    debounceTimers[key] = setTimeout(callback, delay);
}

// Show save indicator
function showSaveIndicator($element, status = 'saving') {
    let $indicator = $element.siblings('.save-indicator');
    if ($indicator.length === 0) {
        $indicator = $('<span class="save-indicator"></span>');
        $element.after($indicator);
    }
    
    $indicator.removeClass('saving saved error').addClass(status + ' show');
    
    if (status === 'saving') {
        $indicator.html('<i class="fa fa-spinner fa-spin"></i>');
    } else if (status === 'saved') {
        $indicator.html('<i class="fa fa-check"></i>');
        setTimeout(() => $indicator.removeClass('show'), 2000);
    } else if (status === 'error') {
        $indicator.html('<i class="fa fa-times"></i>');
        setTimeout(() => $indicator.removeClass('show'), 3000);
    }
}

// Update standard field via AJAX
function updateStandardField(fieldName, configKey, value) {
    console.log('Updating field:', fieldName, 'config:', configKey, 'value:', value);
    
    const data = {
        _token: '{{ csrf_token() }}',
        [configKey]: value
    };
    
    $.ajax({
        url: '{{ route("admin.company-claim-forms.update-standard-field", [$company, "__FIELD__"]) }}'.replace('__FIELD__', fieldName),
        method: 'PATCH',
        data: data,
        success: function(response) {
            console.log('✓ Standard field ' + fieldName + ' updated: ' + configKey, response);
        },
        error: function(xhr, status, error) {
            console.error('✗ Error updating standard field:', fieldName, configKey);
            console.error('Status:', status, 'Error:', error);
            console.error('Response:', xhr.responseText);
        }
    });
}

// Make fields sortable
$(document).ready(function() {
    $('#sortable-all-fields').sortable({
        handle: '.drag-handle',
        cursor: 'move',
        placeholder: 'ui-state-highlight',
        update: function(event, ui) {
            updateAllFieldOrders();
        }
    });
    
    // Move to Top functionality
    $(document).on('click', '.move-to-top', function(e) {
        e.preventDefault();
        const $row = $(this).closest('tr');
        const $tbody = $('#sortable-all-fields');
        
        // Move row to top
        $tbody.prepend($row);
        
        // Add visual feedback
        $row.addClass('table-success');
        setTimeout(() => $row.removeClass('table-success'), 500);
        
        // Update orders
        updateAllFieldOrders();
    });
    
    // Move to Bottom functionality
    $(document).on('click', '.move-to-bottom', function(e) {
        e.preventDefault();
        const $row = $(this).closest('tr');
        const $tbody = $('#sortable-all-fields');
        
        // Move row to bottom
        $tbody.append($row);
        
        // Add visual feedback
        $row.addClass('table-success');
        setTimeout(() => $row.removeClass('table-success'), 500);
        
        // Update orders
        updateAllFieldOrders();
    });
    
    // ===== AUTO-SAVE FOR STANDARD FIELDS =====
    
    // Handle standard field checkbox changes
    $(document).on('change', '.standard-field-checkbox', function() {
        const $checkbox = $(this);
        const fieldName = $checkbox.data('field');
        const configKey = $checkbox.data('config');
        const value = $checkbox.is(':checked');
        
        showSaveIndicator($checkbox, 'saving');
        updateStandardField(fieldName, configKey, value);
        setTimeout(() => showSaveIndicator($checkbox, 'saved'), 300);
    });
    
    // Handle standard field select changes
    $(document).on('change', '.standard-field-select', function() {
        const $select = $(this);
        const fieldName = $select.data('field');
        const configKey = $select.data('config');
        const value = $select.val();
        
        showSaveIndicator($select, 'saving');
        updateStandardField(fieldName, configKey, value);
        setTimeout(() => showSaveIndicator($select, 'saved'), 300);
    });
    
    // Handle standard field text input changes (debounced)
    $(document).on('input', '.standard-field-input', function() {
        const $input = $(this);
        const fieldName = $input.data('field');
        const configKey = $input.data('config');
        const value = $input.val();
        
        showSaveIndicator($input, 'saving');
        
        debounce(fieldName + '_' + configKey, function() {
            updateStandardField(fieldName, configKey, value);
            setTimeout(() => showSaveIndicator($input, 'saved'), 300);
        }, 1000);
    });
    
    // ===== AUTO-SAVE FOR CUSTOM FIELDS =====

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
                is_enabled: isEnabled ? 1 : 0,
                is_required: isRequired ? 1 : 0,
                include_in_notification: includeInNotification ? 1 : 0
            }
        });
    });

    // Handle custom field width changes
    $(document).on('change', '.custom-field-width', function() {
        const fieldId = $(this).data('id');
        const width = $(this).val();
        
        $.ajax({
            url: '{{ route("admin.company-claim-forms.update-custom-field", [$company, "__ID__"]) }}'.replace('__ID__', fieldId),
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                field_width: width
            }
        });
    });

    // Handle custom field group changes
    $(document).on('blur', '.custom-field-group', function() {
        const fieldId = $(this).data('id');
        const group = $(this).val();
        
        $.ajax({
            url: '{{ route("admin.company-claim-forms.update-custom-field", [$company, "__ID__"]) }}'.replace('__ID__', fieldId),
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                field_group: group
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

    // ==================== BULK OPERATIONS ====================
    
    // Select All / Deselect All
    $('#select-all').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.row-select-checkbox').prop('checked', isChecked);
        updateBulkActionBar();
    });

    // Individual row selection
    $(document).on('change', '.row-select-checkbox', function() {
        updateBulkActionBar();
        
        // Update select-all checkbox state
        const totalCheckboxes = $('.row-select-checkbox').length;
        const totalSelected = $('.row-select-checkbox:checked').length;
        $('#select-all').prop('checked', totalCheckboxes === totalSelected && totalCheckboxes > 0);
    });

    // Update bulk action bar visibility and count
    function updateBulkActionBar() {
        const selectedCount = $('.row-select-checkbox:checked').length;
        $('#selected-count').text(selectedCount);
        
        if (selectedCount > 0) {
            $('#bulk-action-bar').addClass('show');
        } else {
            $('#bulk-action-bar').removeClass('show');
        }
    }

    // Clear selection
    $('#clear-selection').on('click', function() {
        $('.row-select-checkbox').prop('checked', false);
        $('#select-all').prop('checked', false);
        updateBulkActionBar();
    });

    // Bulk action buttons
    $('[data-bulk-action]').on('click', function() {
        const action = $(this).data('bulk-action');
        const selectedFields = [];
        
        $('.row-select-checkbox:checked').each(function() {
            const $row = $(this).closest('tr');
            const fieldData = {
                field_name: $(this).data('field'),
                type: $(this).data('type')
            };
            
            if (fieldData.type === 'custom') {
                fieldData.id = $(this).data('id');
            }
            
            selectedFields.push(fieldData);
        });

        if (selectedFields.length === 0) {
            alert('Geen velden geselecteerd');
            return;
        }

        // Special handling for set_width and set_group
        let value = null;
        if (action === 'set_width') {
            value = prompt('Selecteer breedte:\n\nfull = Volledig\nhalf = Half\nthird = Derde\nquarter = Kwart\n\nVoer in:', 'full');
            if (!value || !['full', 'half', 'third', 'quarter'].includes(value)) {
                alert('Ongeldige breedte. Gebruik: full, half, third, of quarter');
                return;
            }
        } else if (action === 'set_group') {
            value = prompt('Voer groepsnaam in (of laat leeg om groep te wissen):', '');
            if (value === null) return; // User cancelled
        }

        // Confirmation
        const actionLabels = {
            'enable': 'inschakelen',
            'disable': 'uitschakelen',
            'require': 'verplicht maken',
            'unrequire': 'niet verplicht maken',
            'include_notification': 'opnemen in notificaties',
            'exclude_notification': 'uitsluiten van notificaties',
            'set_width': 'breedte instellen op ' + value,
            'set_group': value ? 'groep instellen op "' + value + '"' : 'groep wissen'
        };

        if (!confirm(`Weet u zeker dat u ${selectedFields.length} veld(en) wilt ${actionLabels[action]}?`)) {
            return;
        }

        // Show loading indicator
        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.html('<i class="fa fa-spinner fa-spin"></i> Bezig...').prop('disabled', true);

        // Send bulk update request
        $.ajax({
            url: '{{ route("admin.company-claim-forms.bulk-update", $company) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                action: action,
                fields: selectedFields,
                value: value
            },
            success: function(response) {
                $btn.html(originalHtml).prop('disabled', false);
                
                if (response.success) {
                    // Update UI for each field
                    selectedFields.forEach(field => {
                        const $row = $(`.row-select-checkbox[data-field="${field.field_name}"]`).closest('tr');
                        
                        // Update data attributes
                        if (action === 'enable') {
                            $row.attr('data-enabled', '1');
                            $row.find('.field-enabled').prop('checked', true);
                        } else if (action === 'disable') {
                            $row.attr('data-enabled', '0');
                            $row.find('.field-enabled').prop('checked', false);
                        } else if (action === 'require') {
                            $row.attr('data-required', '1');
                            $row.attr('data-enabled', '1');
                            $row.find('.standard-field-checkbox[data-config="is_required"], .custom-field-required').prop('checked', true);
                            $row.find('.field-enabled').prop('checked', true);
                        } else if (action === 'unrequire') {
                            $row.attr('data-required', '0');
                            $row.find('.standard-field-checkbox[data-config="is_required"], .custom-field-required').prop('checked', false);
                        } else if (action === 'set_width') {
                            $row.find('.standard-field-select[data-config="field_width"], .custom-field-width').val(value);
                        } else if (action === 'set_group') {
                            $row.attr('data-group', value);
                            $row.find('.standard-field-input[data-config="field_group"], .custom-field-group').val(value);
                        }
                        
                        // Visual feedback
                        $row.addClass('table-success');
                        setTimeout(() => $row.removeClass('table-success'), 1000);
                    });

                    // Clear selection
                    $('.row-select-checkbox').prop('checked', false);
                    $('#select-all').prop('checked', false);
                    updateBulkActionBar();

                    // Show success message
                    alert(`✓ Bulk update voltooid\n\n${response.counts.success} bijgewerkt\n${response.counts.skipped} overgeslagen\n${response.counts.failed} mislukt`);
                } else {
                    alert('Fout: ' + response.message);
                }
            },
            error: function(xhr) {
                $btn.html(originalHtml).prop('disabled', false);
                alert('Er is een fout opgetreden bij het bijwerken van de velden.\n\n' + (xhr.responseJSON ? xhr.responseJSON.message : 'Server error'));
            }
        });
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
