@extends('layouts.public')

@section('styles')
<style>
/* Modern Form Grid Layout */
.form-grid-row {
    display: flex;
    flex-wrap: wrap;
    margin-left: -12px;
    margin-right: -12px;
}

.form-field-full,
.form-field-half,
.form-field-third,
.form-field-quarter {
    padding-left: 12px;
    padding-right: 12px;
    margin-bottom: 1.5rem;
}

.form-field-full {
    width: 100%;
}

.form-field-half {
    width: 50%;
}

.form-field-third {
    width: 33.3333%;
}

.form-field-quarter {
    width: 25%;
}

/* Enhanced Card Styling */
.claim-form-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: none;
    overflow: hidden;
}

.claim-form-logo-container {
    background: white;
    padding: 2rem 1.5rem;
    border-radius: 8px 8px 0 0;
    text-align: center;
    border-bottom: 1px solid #e1e8ed;
}

.claim-form-logo {
    display: inline-block;
}

.claim-form-logo img {
    max-height: 80px;
    max-width: 250px;
    height: auto;
    width: auto;
    display: block;
}

.claim-form-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    padding: 2rem 1.5rem;
    border-bottom: none;
}

.claim-form-header h3 {
    color: white;
    font-size: 1.75rem;
    font-weight: 600;
    margin: 0;
    letter-spacing: -0.025em;
    text-align: center;
}

.claim-form-body {
    padding: 2rem 1.5rem;
}

/* Enhanced Form Controls */
.form-group {
    margin-bottom: 0;
}

.form-group label {
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    display: block;
}

.form-control {
    border: 1.5px solid #e1e8ed;
    border-radius: 6px;
    padding: 0.625rem 0.875rem;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    background-color: #ffffff;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.15);
    background-color: #ffffff;
}

.form-control::placeholder {
    color: #95a5a6;
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

select.form-control {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
    padding-right: 2.5rem;
}

/* Radio Buttons */
.form-check-inline {
    margin-right: 1.5rem;
    margin-bottom: 0.5rem;
}

.form-check-input {
    width: 1.125rem;
    height: 1.125rem;
    margin-top: 0.1rem;
    cursor: pointer;
}

.form-check-label {
    font-weight: 500;
    color: #495057;
    cursor: pointer;
    margin-left: 0.5rem;
}

/* Required Field Indicator */
.required-field::after {
    content: " *";
    color: #e74c3c;
    font-weight: 600;
}

/* File Upload Styling */
input[type="file"].form-control {
    padding: 0.5rem 0.875rem;
    border-style: dashed;
    background-color: #f8f9fa;
}

input[type="file"].form-control:hover {
    background-color: #e9ecef;
    border-color: #007bff;
}

/* Alert Styling */
.alert {
    border-radius: 6px;
    border: none;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}

.alert-danger {
    background-color: #fee;
    color: #c33;
}

.alert-info {
    background-color: #e7f3ff;
    color: #0056b3;
    border-left: 4px solid #007bff;
}

.alert ul {
    margin: 0;
    padding-left: 1.25rem;
}

/* Small Text */
.form-text {
    color: #6c757d;
    font-size: 0.85rem;
    margin-top: 0.375rem;
}

/* Submit Button */
.submit-section {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 2px solid #f0f3f5;
}

.btn-submit {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    font-weight: 600;
    font-size: 1.05rem;
    padding: 0.875rem 2rem;
    border-radius: 6px;
    border: none;
    width: 100%;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.25);
    cursor: pointer;
}

.btn-submit:hover:not(:disabled) {
    background: linear-gradient(135deg, #0069d9 0%, #004494 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.35);
    color: white;
}

.btn-submit:disabled {
    background: #6c757d;
    cursor: not-allowed;
    opacity: 0.65;
    box-shadow: none;
}

.btn-submit i {
    margin-right: 0.5rem;
}

/* Responsive breakpoints */
@media (max-width: 767px) {
    .form-field-half,
    .form-field-third,
    .form-field-quarter {
        width: 100%;
    }
    
    .claim-form-logo-container {
        padding: 1.5rem 1rem;
    }
    
    .claim-form-logo img {
        max-height: 60px;
        max-width: 200px;
    }
    
    .claim-form-header {
        padding: 1.5rem 1rem;
    }
    
    .claim-form-header h3 {
        font-size: 1.5rem;
    }
    
    .claim-form-body {
        padding: 1.5rem 1rem;
    }
    
    .form-field-full,
    .form-field-half,
    .form-field-third,
    .form-field-quarter {
        margin-bottom: 1.25rem;
    }
}

@media (min-width: 768px) and (max-width: 991px) {
    .form-field-third,
    .form-field-quarter {
        width: 50%;
    }
}

/* Loading state */
.btn-submit:disabled::after {
    content: "...";
    animation: dots 1.5s steps(4, end) infinite;
}

@keyframes dots {
    0%, 20% { content: ""; }
    40% { content: "."; }
    60% { content: ".."; }
    80%, 100% { content: "..."; }
}
</style>
@endsection

@section('content')
<div class="claim-form-card card" x-data="claimForm()">
    @if($company->logo)
    <div class="claim-form-logo-container">
        <div class="claim-form-logo">
            <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }} Logo">
        </div>
    </div>
    @endif
    <div class="claim-form-header card-header">
        <h3 x-text="formData.form_type === 'complaint' ? 'Klacht Indienen - {{ $company->name }}' : (formData.form_type === 'claim' ? 'Schademelding Indienen - {{ $company->name }}' : 'Formulier Indienen - {{ $company->name }}')"></h3>
    </div>
    <div class="claim-form-body card-body">
        <form action="{{ route('public.claim-form.store', $claimToken->token) }}" method="POST" enctype="multipart/form-data">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @foreach($groupedFields as $groupKey => $groupFields)
                {{-- Start flex row for this group --}}
                <div class="form-grid-row">
                    @foreach($groupFields as $field)
                        @if($field['type'] === 'standard')
                            @php
                                $config = $field['data'];
                                $fieldName = $config->field_name;
                                // vehicle_plates_opposite is always required if enabled
                                $isRequired = ($fieldName === 'vehicle_plates_opposite') ? true : $config->is_required;
                                $conditionalLogic = $config->conditional_logic;
                                $hasCondition = !empty($conditionalLogic);
                                $fieldLabel = $config->notification_label ?: $availableFields[$fieldName] ?? $fieldName;
                                $fieldWidth = $config->field_width ?? 'full';
                                $fieldsForBothTypes = ['op_name', 'op_street', 'op_zipcode', 'op_city', 'op_phone', 'op_email'];
                                $showForBothTypes = in_array($fieldName, $fieldsForBothTypes) ? 'true' : 'false';
                            @endphp

                        <div class="form-field-{{ $fieldWidth }}" 
                            @if($hasCondition)
                                x-show="evaluateCondition({{ json_encode($conditionalLogic) }}) && (formData.form_type === 'claim' || {{ $showForBothTypes }} || '{{ $fieldName }}' === 'complaint_description' || '{{ $fieldName }}' === 'form_type')"
                                x-cloak
                                style="display: none;"
                            @else
                                x-show="formData.form_type === 'claim' || {{ $showForBothTypes }} || '{{ $fieldName }}' === 'complaint_description' || '{{ $fieldName }}' === 'form_type'"
                                x-cloak
                                style="display: none;"
                            @endif>
                            <div class="form-group">
                                @if($fieldName === 'form_type')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="form_type" id="form_type_claim" 
                                                value="claim" {{ old('form_type', 'claim') === 'claim' ? 'checked' : '' }} 
                                                {{ $isRequired ? 'required' : '' }} x-model="formData.form_type">
                                            <label class="form-check-label" for="form_type_claim">
                                                Schademelding
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="form_type" id="form_type_complaint" 
                                                value="complaint" {{ old('form_type') === 'complaint' ? 'checked' : '' }}
                                                {{ $isRequired ? 'required' : '' }} x-model="formData.form_type">
                                            <label class="form-check-label" for="form_type_complaint">
                                                Klacht
                                            </label>
                                        </div>
                                    </div>

                                @elseif($fieldName === 'complaint_description')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <textarea name="complaint_description" class="form-control" rows="6"
                                        {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.complaint_description" placeholder="Beschrijf uw klacht in detail...">{{ old('complaint_description') }}</textarea>

                                @elseif($fieldName === 'subject')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <input type="text" name="subject" class="form-control" 
                                        value="{{ old('subject') }}" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.subject">

                                @elseif($fieldName === 'date_accident')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <input type="text" name="date_accident" class="form-control" 
                                        value="{{ old('date_accident') }}" placeholder="dd-mm-jjjj" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.date_accident">

                                @elseif($fieldName === 'injury')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <select name="injury" class="form-control" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.injury">
                                        <option value="">Selecteer...</option>
                                        @foreach(\App\Models\Claim::INJURY_SELECT as $key => $value)
                                            <option value="{{ $key }}" {{ old('injury') === $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>

                                @elseif($fieldName === 'injury_other')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <input type="text" name="injury_other" class="form-control" 
                                        value="{{ old('injury_other') }}" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.injury_other">

                                @elseif($fieldName === 'damage_kind')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <select name="damage_kind" class="form-control" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.damage_kind">
                                        <option value="">Selecteer...</option>
                                        @foreach(\App\Models\Claim::DAMAGE_KIND as $key => $value)
                                            <option value="{{ $key }}" {{ old('damage_kind') === $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>

                                @elseif($fieldName === 'recoverable_claim')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <select name="recoverable_claim" class="form-control" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.recoverable_claim">
                                        <option value="">Selecteer...</option>
                                        @foreach(\App\Models\Claim::RECOVERABLE_CLAIM_SELECT as $key => $value)
                                            <option value="{{ $key }}" {{ old('recoverable_claim') === $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>

                                @elseif($fieldName === 'vehicle_plates')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <input type="hidden" name="vehicle_plates_foreign" :value="formData.vehicle_plates_foreign ? '1' : '0'">
                                    <input type="text" name="vehicle_plates" class="form-control text-uppercase" 
                                        value="{{ old('vehicle_plates') }}" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.vehicle_plates"
                                        @input="formData.vehicle_plates = formData.vehicle_plates_foreign ? $event.target.value.toUpperCase() : formatLicensePlate($event.target.value)"
                                        :placeholder="formData.vehicle_plates_foreign ? 'Buitenlands kenteken' : 'XX-99-XX'"
                                        :maxlength="formData.vehicle_plates_foreign ? 20 : 8">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="vehicle_plates_foreign_check"
                                            x-model="formData.vehicle_plates_foreign"
                                            @change="formData.vehicle_plates = ''">
                                        <label class="form-check-label text-muted small" for="vehicle_plates_foreign_check">
                                            Buitenlands kenteken
                                        </label>
                                    </div>

                                @elseif($fieldName === 'vehicle_plates_opposite')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <input type="hidden" name="vehicle_plates_opposite_foreign" :value="formData.vehicle_plates_opposite_foreign ? '1' : '0'">
                                    <input type="text" name="vehicle_plates_opposite" class="form-control text-uppercase" 
                                        value="{{ old('vehicle_plates_opposite') }}" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.vehicle_plates_opposite"
                                        @input="formData.vehicle_plates_opposite = formData.vehicle_plates_opposite_foreign ? $event.target.value.toUpperCase() : formatLicensePlate($event.target.value)"
                                        :placeholder="formData.vehicle_plates_opposite_foreign ? 'Buitenlands kenteken' : 'XX-99-XX'"
                                        :maxlength="formData.vehicle_plates_opposite_foreign ? 20 : 8">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="vehicle_plates_opposite_foreign_check"
                                            x-model="formData.vehicle_plates_opposite_foreign"
                                            @change="formData.vehicle_plates_opposite = ''">
                                        <label class="form-check-label text-muted small" for="vehicle_plates_opposite_foreign_check">
                                            Buitenlands kenteken
                                        </label>
                                    </div>

                                @elseif($fieldName === 'vehicle_brand_opposite')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <input type="text" name="vehicle_brand_opposite" class="form-control"
                                        value="{{ old('vehicle_brand_opposite') }}" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.vehicle_brand_opposite"
                                        placeholder="bijv. Mercedes Atego">

                                @elseif($fieldName === 'vehicle_chassis_number_opposite')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <input type="text" name="vehicle_chassis_number_opposite" class="form-control"
                                        value="{{ old('vehicle_chassis_number_opposite') }}" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.vehicle_chassis_number_opposite">

                                @elseif($fieldName === 'damaged_part')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <select name="damaged_part[]" class="form-control" multiple {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.damaged_part">
                                        @foreach(\App\Models\Claim::DAMAGED_PART_SELECT as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Houd Ctrl ingedrukt voor meerdere selecties</small>

                                @elseif($fieldName === 'damaged_area')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <select name="damaged_area[]" class="form-control" multiple {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.damaged_area">
                                        @foreach(\App\Models\Claim::DAMAGED_AREA_SELECT as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Houd Ctrl ingedrukt voor meerdere selecties</small>

                                @elseif($fieldName === 'opposite_type')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <select name="opposite_type" class="form-control" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.opposite_type">
                                        <option value="">Selecteer...</option>
                                        @foreach(\App\Models\Claim::OPPOSITE_TYPE_SELECT as $key => $value)
                                            <option value="{{ $key }}" {{ old('opposite_type') === $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>

                                @elseif($fieldName === 'obstacle')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <input type="text" name="obstacle" class="form-control" 
                                        value="{{ old('obstacle') }}" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.obstacle">

                                @elseif($fieldName === 'op_name')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <input type="text" name="op_name" class="form-control" 
                                        value="{{ old('op_name') }}" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.op_name">

                                @elseif($fieldName === 'op_street')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <input type="text" name="op_street" class="form-control" 
                                        value="{{ old('op_street') }}" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.op_street">

                                @elseif($fieldName === 'op_zipcode')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <input type="text" name="op_zipcode" class="form-control" 
                                        value="{{ old('op_zipcode') }}" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.op_zipcode">

                                @elseif($fieldName === 'op_city')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <input type="text" name="op_city" class="form-control" 
                                        value="{{ old('op_city') }}" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.op_city">

                                @elseif($fieldName === 'op_phone')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <input type="text" name="op_phone" class="form-control" 
                                        value="{{ old('op_phone') }}" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.op_phone">

                                @elseif($fieldName === 'op_email')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <input type="email" name="op_email" class="form-control" 
                                        value="{{ old('op_email') }}" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.op_email">

                                @elseif(in_array($fieldName, ['loading_photos', 'unloading_photos', 'waybill_signed_at_loading', 'waybill_signed_at_unloading']))
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <select name="{{ $fieldName }}" class="form-control" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.{{ $fieldName }}">
                                        <option value="">Selecteer...</option>
                                        @foreach(\App\Models\Claim::WAYBILL_SELECT as $key => $value)
                                            <option value="{{ $key }}" {{ old($fieldName) === $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>

                                @elseif(in_array($fieldName, ['damage_files', 'report_files', 'financial_files', 'other_files']))
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <input type="file" name="{{ $fieldName }}[]" class="form-control file-input" 
                                        data-collection="{{ $fieldName }}"
                                        {{ $isRequired ? 'required' : '' }} 
                                        accept="image/jpeg,image/png,image/gif,application/pdf,.doc,.docx,.xls,.xlsx"
                                        @change="validateFiles($event, '{{ $fieldName }}')">
                                    <div class="file-error-message text-danger" style="display: none;" data-collection="{{ $fieldName }}"></div>
                                    <div class="file-success-message text-success" style="display: none;" data-collection="{{ $fieldName }}"></div>
                                    <small class="form-text text-muted d-block mt-2">
                                        U kunt meerdere bestanden selecteren (max 10 per categorie). 
                                        Ondersteunde formaten: JPG, PNG, GIF, PDF, DOC, DOCX, XLS, XLSX. 
                                        Maximale bestandsgrootte: <strong>10 MB per bestand</strong>.
                                    </small>
                                @endif
                            </div>
                        </div>
                        
                        @else
                            {{-- Custom Field --}}
                            @php
                                $customField = $field['data'];
                                $fieldKey = 'custom_' . $customField->field_name;
                                $isRequired = $customField->is_required;
                                $conditionalLogic = $customField->conditional_logic;
                                $hasCondition = !empty($conditionalLogic);
                                $fieldLabel = $customField->field_label;
                                $fieldWidth = $customField->field_width ?? 'full';
                            @endphp

                        <div class="form-field-{{ $fieldWidth }}" 
                            @if($hasCondition)
                                x-show="evaluateCondition({{ json_encode($conditionalLogic) }})"
                                x-cloak
                                style="display: none;"
                            @endif>
                            <div class="form-group">
                                @if($customField->field_type === 'html')
                                    <div class="alert alert-info">
                                        {!! $fieldLabel !!}
                                    </div>

                                @elseif($customField->field_type === 'text')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <input type="text" name="{{ $fieldKey }}" class="form-control" 
                                        value="{{ old($fieldKey) }}" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.{{ $fieldKey }}">

                                @elseif($customField->field_type === 'textarea')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <textarea name="{{ $fieldKey }}" class="form-control" rows="4" 
                                        {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.{{ $fieldKey }}">{{ old($fieldKey) }}</textarea>

                                @elseif($customField->field_type === 'select')
                                    <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                                    <select name="{{ $fieldKey }}" class="form-control" {{ $isRequired ? 'required' : '' }}
                                        x-model="formData.{{ $fieldKey }}">
                                        <option value="">Selecteer...</option>
                                        @if(!empty($customField->options))
                                            @foreach($customField->options as $option)
                                                <option value="{{ $option }}" {{ old($fieldKey) === $option ? 'selected' : '' }}>{{ $option }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                @endif
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            @endforeach

            <div class="submit-section">
                <button type="submit" class="btn btn-submit" :disabled="!formData.form_type">
                    <i class="fa fa-paper-plane"></i>
                    <span x-text="formData.form_type === 'complaint' ? 'Klacht Indienen' : (formData.form_type === 'claim' ? 'Schademelding Indienen' : 'Selecteer Type Formulier')"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function claimForm() {
    return {
        formData: {
            form_type: '',
            complaint_description: '',
            subject: '',
            date_accident: '',
            injury: '',
            injury_other: '',
            damage_kind: '',
            recoverable_claim: '',
            vehicle_plates: '',
            vehicle_plates_foreign: {{ old('vehicle_plates_foreign') ? 'true' : 'false' }},
            vehicle_plates_opposite: '',
            vehicle_plates_opposite_foreign: {{ old('vehicle_plates_opposite_foreign') ? 'true' : 'false' }},
            vehicle_brand_opposite: '',
            vehicle_chassis_number_opposite: '',
            damaged_part: [],
            damaged_area: [],
            opposite_type: '',
            obstacle: '',
            op_name: '',
            op_street: '',
            op_zipcode: '',
            op_city: '',
            op_phone: '',
            op_email: '',
            loading_photos: '',
            unloading_photos: '',
            waybill_signed_at_loading: '',
            waybill_signed_at_unloading: '',
            @foreach($groupedFields as $groupFields)
                @foreach($groupFields as $field)
                    @if($field['type'] === 'custom' && $field['data']->field_type !== 'html')
                        custom_{{ $field['data']->field_name }}: '',
                    @endif
                @endforeach
            @endforeach
        },
        
        evaluateCondition(logic) {
            if (!logic || !logic.conditions) {
                return true;
            }
            
            return this.evaluateNode(logic);
        },
        
        evaluateNode(node) {
            const operator = node.operator || 'AND';
            const conditions = node.conditions || [];
            
            if (conditions.length === 0) {
                return true;
            }
            
            const results = conditions.map(condition => {
                // Check if this is a nested node (has conditions array) vs simple condition (has field)
                if (condition.conditions) {
                    return this.evaluateNode(condition);
                } else {
                    return this.evaluateSimpleCondition(condition);
                }
            });
            
            if (operator === 'OR') {
                return results.some(r => r === true);
            } else {
                return results.every(r => r === true);
            }
        },
        
        evaluateSimpleCondition(condition) {
            const field = condition.field;
            const op = condition.operator || 'equals';
            const value = condition.value;
            const fieldValue = this.formData[field];
            
            switch (op) {
                case 'equals':
                    return fieldValue == value;
                case 'not_equals':
                    return fieldValue != value;
                case 'contains':
                    return Array.isArray(fieldValue) && fieldValue.includes(value);
                case 'not_contains':
                    return !Array.isArray(fieldValue) || !fieldValue.includes(value);
                case 'empty':
                    return !fieldValue || fieldValue.length === 0;
                case 'not_empty':
                    return !!fieldValue && fieldValue.length > 0;
                default:
                    return false;
            }
        },
        
        formatLicensePlate(plate) {
            if (!plate) return '';
            
            // Remove all non-alphanumeric characters and convert to uppercase
            let cleaned = plate.toUpperCase().replace(/[^A-Z0-9]/g, '');
            
            if (cleaned.length < 4 || cleaned.length > 8) {
                return cleaned;
            }
            
            // Dutch license plate patterns
            const patterns = [
                { regex: /^([A-Z]{2})(\d{2})(\d{2})$/, format: '$1-$2-$3' },     // XX-99-99
                { regex: /^(\d{2})(\d{2})([A-Z]{2})$/, format: '$1-$2-$3' },     // 99-99-XX
                { regex: /^(\d{2})([A-Z]{2})(\d{2})$/, format: '$1-$2-$3' },     // 99-XX-99
                { regex: /^([A-Z]{2})(\d{2})([A-Z]{2})$/, format: '$1-$2-$3' },  // XX-99-XX
                { regex: /^([A-Z]{2})([A-Z]{2})(\d{2})$/, format: '$1-$2-$3' },  // XX-XX-99
                { regex: /^(\d{2})([A-Z]{2})([A-Z]{2})$/, format: '$1-$2-$3' },  // 99-XX-XX
                { regex: /^(\d{2})([A-Z]{3})(\d{1})$/, format: '$1-$2-$3' },     // 99-XXX-9
                { regex: /^(\d{1})([A-Z]{3})(\d{2})$/, format: '$1-$2-$3' },     // 9-XXX-99
                { regex: /^([A-Z]{2})(\d{3})([A-Z]{1})$/, format: '$1-$2-$3' },  // XX-999-X
                { regex: /^([A-Z]{1})(\d{3})([A-Z]{2})$/, format: '$1-$2-$3' },  // X-999-XX
                { regex: /^([A-Z]{3})(\d{2})([A-Z]{1})$/, format: '$1-$2-$3' },  // XXX-99-X
                { regex: /^([A-Z]{1})(\d{2})([A-Z]{3})$/, format: '$1-$2-$3' },  // X-99-XXX
                { regex: /^(\d{1})([A-Z]{2})(\d{3})$/, format: '$1-$2-$3' },     // 9-XX-999
                { regex: /^(\d{3})([A-Z]{2})(\d{1})$/, format: '$1-$2-$3' }      // 999-XX-9
            ];
            
            for (let pattern of patterns) {
                if (pattern.regex.test(cleaned)) {
                    return cleaned.replace(pattern.regex, pattern.format);
                }
            }
            
            return cleaned;
        },

        validateFiles(event, collection) {
            const files = event.target.files;
            const maxFileSize = 10 * 1024 * 1024; // 10MB in bytes
            const maxFiles = 10;
            const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
            
            const errorContainer = document.querySelector(`[data-collection="${collection}"].file-error-message`);
            const successContainer = document.querySelector(`[data-collection="${collection}"].file-success-message`);
            const fileInput = event.target;
            
            // Reset messages
            errorContainer.style.display = 'none';
            successContainer.style.display = 'none';
            errorContainer.innerHTML = '';
            successContainer.innerHTML = '';
            
            // Check number of files
            if (files.length > maxFiles) {
                const errorMsg = `U kunt maximaal ${maxFiles} bestanden selecteren. U heeft ${files.length} bestanden geselecteerd.`;
                errorContainer.innerHTML = errorMsg;
                errorContainer.style.display = 'block';
                fileInput.value = '';
                return false;
            }
            
            let errors = [];
            let validFiles = [];
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileName = file.name;
                const fileSize = file.size;
                const fileExtension = fileName.split('.').pop().toLowerCase();
                
                // Check file extension
                if (!allowedExtensions.includes(fileExtension)) {
                    errors.push(`"${fileName}": Bestandstype niet ondersteund (.${fileExtension})`);
                    continue;
                }
                
                // Check file size
                if (fileSize > maxFileSize) {
                    const fileSizeMB = (fileSize / 1024 / 1024).toFixed(2);
                    errors.push(`"${fileName}": Bestand is te groot (${fileSizeMB} MB, max. 10 MB)`);
                    continue;
                }
                
                validFiles.push(fileName);
            }
            
            // Display errors if any
            if (errors.length > 0) {
                const errorList = '<strong>Fouten gevonden:</strong><ul style="margin-top: 8px; padding-left: 20px;">' + 
                    errors.map(error => `<li>${error}</li>`).join('') + 
                    '</ul>';
                errorContainer.innerHTML = errorList;
                errorContainer.style.display = 'block';
                
                // Clear the input if all files have errors
                if (validFiles.length === 0) {
                    fileInput.value = '';
                    return false;
                }
            }
            
            // Display success message
            if (validFiles.length > 0) {
                const successMsg = `${validFiles.length} bestand(en) klaar voor upload: ${validFiles.join(', ')}`;
                successContainer.innerHTML = successMsg;
                successContainer.style.display = 'block';
            }
            
            return true;
        }
    }
}
</script>
@endsection
