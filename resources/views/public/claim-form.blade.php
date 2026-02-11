@extends('layouts.public')

@section('content')
<div class="card" x-data="claimForm()">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0" x-text="formData.form_type === 'complaint' ? 'Klacht Indienen - {{ $company->name }}' : (formData.form_type === 'claim' ? 'Schademelding Indienen - {{ $company->name }}' : 'Formulier Indienen - {{ $company->name }}')"></h3>
    </div>
    <div class="card-body">
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

            @foreach($allFields as $field)
                @if($field['type'] === 'standard')
                    @php
                        $config = $field['data'];
                        $fieldName = $config->field_name;
                        $isRequired = $config->is_required;
                        $conditionalLogic = $config->conditional_logic;
                        $hasCondition = !empty($conditionalLogic);
                        $fieldLabel = $config->notification_label ?: $availableFields[$fieldName] ?? $fieldName;
                    @endphp

                <div class="form-group" 
                    @if($hasCondition)
                        x-show="evaluateCondition({{ json_encode($conditionalLogic) }})"
                        x-cloak
                        style="display: none;"
                    @endif>
                    
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
                        <input type="text" name="vehicle_plates" class="form-control" 
                            value="{{ old('vehicle_plates') }}" {{ $isRequired ? 'required' : '' }}
                            x-model="formData.vehicle_plates">

                    @elseif($fieldName === 'vehicle_plates_opposite')
                        <label class="{{ $isRequired ? 'required-field' : '' }}">{{ $fieldLabel }}</label>
                        <input type="text" name="vehicle_plates_opposite" class="form-control" 
                            value="{{ old('vehicle_plates_opposite') }}" {{ $isRequired ? 'required' : '' }}
                            x-model="formData.vehicle_plates_opposite">

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
                        <input type="file" name="{{ $fieldName }}[]" class="form-control" multiple 
                            {{ $isRequired ? 'required' : '' }} accept="image/*,application/pdf">
                        <small class="form-text text-muted">U kunt meerdere bestanden selecteren</small>
                    @endif
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
                    @endphp

                <div class="form-group" 
                    @if($hasCondition)
                        x-show="evaluateCondition({{ json_encode($conditionalLogic) }})"
                        x-cloak
                        style="display: none;"
                    @endif>
                    
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
                @endif
            @endforeach

            <hr>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-lg btn-block" :disabled="!formData.form_type">
                    <i class="fa fa-paper-plane"></i> <span x-text="formData.form_type === 'complaint' ? 'Klacht Indienen' : (formData.form_type === 'claim' ? 'Schademelding Indienen' : 'Selecteer Type Formulier')"></span>
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
            vehicle_plates_opposite: '',
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
            @foreach($allFields as $field)
                @if($field['type'] === 'custom' && $field['data']->field_type !== 'html')
                    custom_{{ $field['data']->field_name }}: '',
                @endif
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
        }
    }
}
</script>
@endsection
