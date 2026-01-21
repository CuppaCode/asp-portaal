@extends('layouts.admin')
@section('styles')
<style>
    .ui-state-highlight {
        height: 50px;
        background-color: #f0f0f0;
        border: 2px dashed #ccc;
    }
    #sortable-fields tr:hover {
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
        <a href="{{ route('admin.companies.show', $company->id) }}" class="btn btn-secondary mb-3">
            <i class="fa fa-arrow-left"></i> Terug naar bedrijf
        </a>

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
            <div class="card-header">
                <h4 class="mb-0">Formulier Velden Configuratie</h4>
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
                                    <th>Ingeschakeld</th>
                                    <th>Verplicht</th>
                                    <th>In notificatie</th>
                                    <th>Label</th>
                                    <th>Voorwaardelijke logica</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-fields">
                                @php
                                    $existingConfigs = $formConfigs->keyBy('field_name');
                                @endphp
                                @foreach($availableFields as $fieldName => $fieldLabel)
                                    @php
                                        $config = $existingConfigs->get($fieldName);
                                        $isEnabled = $config ? $config->is_enabled : false;
                                        $isRequired = $config ? $config->is_required : false;
                                        $includeInNotification = $config ? $config->include_in_notification : false;
                                        $notificationLabel = $config ? $config->notification_label : $fieldLabel;
                                        $order = $config ? $config->display_order : 0;
                                        $conditionalLogic = $config ? $config->conditional_logic : null;
                                    @endphp
                                    <tr data-field="{{ $fieldName }}" style="cursor: move;">
                                        <td class="text-center" style="cursor: grab;">
                                            <i class="fa fa-bars text-muted"></i>
                                        </td>
                                        <td><strong>{{ $fieldLabel }}</strong></td>
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
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="openConditionalModal('{{ $fieldName }}', '{{ $fieldLabel }}')">
                                                <i class="fa fa-code-branch"></i> 
                                                <span id="logic-indicator-{{ $fieldName }}">
                                                    {{ $conditionalLogic ? 'Bewerken' : 'Instellen' }}
                                                </span>
                                            </button>
                                            <input type="hidden" name="fields[{{ $fieldName }}][conditional_logic]" 
                                                id="conditional-logic-{{ $fieldName }}" 
                                                value='{{ $conditionalLogic ? json_encode($conditionalLogic) : "" }}'>
                                        </td>
                                        <input type="hidden" name="fields[{{ $fieldName }}][display_order]" value="{{ $order }}" class="display-order">
                                    </tr>
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
    $('#sortable-fields').sortable({
        handle: 'td:first-child',
        cursor: 'move',
        placeholder: 'ui-state-highlight',
        update: function(event, ui) {
            // Update display order values
            $('#sortable-fields tr').each(function(index) {
                $(this).find('.display-order').val(index);
            });
        }
    });
});

let currentFieldName = '';
let conditionCounter = 0;
const availableFields = @json($availableFields);

function openConditionalModal(fieldName, fieldLabel) {
    currentFieldName = fieldName;
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
                            ${Object.entries(availableFields).map(([name, label]) => 
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
    
    // Save to hidden input
    const hiddenInput = document.getElementById('conditional-logic-' + currentFieldName);
    hiddenInput.value = logicObject ? JSON.stringify(logicObject) : '';
    
    // Update button text
    const indicator = document.getElementById('logic-indicator-' + currentFieldName);
    indicator.textContent = logicObject ? 'Bewerken' : 'Instellen';
    
    $('#conditionalLogicModal').modal('hide');
}
</script>
@endsection
