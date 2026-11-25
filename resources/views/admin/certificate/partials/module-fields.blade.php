@if(!empty($moduleFieldDefinitions))
    <div class="card mt-3">
        <div class="card-header">Categorie-specifieke velden</div>
        <div class="card-body">
            <div class="row">
                @foreach($moduleFieldDefinitions as $definition)
                    @php
                        $key = $definition['key'];
                        $label = $definition['label'] ?? $key;
                        $type = $definition['type'] ?? 'text';
                        $required = !empty($definition['required']);
                        $value = $moduleFieldValues[$key] ?? null;
                        $inputName = 'module_fields[' . $key . ']';
                        $inputId = 'module_field_' . $key;
                    @endphp

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="{{ $inputId }}" class="{{ $required ? 'required' : '' }}">{{ $label }}</label>

                            @if($type === 'textarea')
                                <textarea
                                    class="form-control {{ $errors->has('module_fields.' . $key) ? 'is-invalid' : '' }}"
                                    id="{{ $inputId }}"
                                    name="{{ $inputName }}"
                                    rows="3"
                                >{{ old('module_fields.' . $key, $value) }}</textarea>
                            @else
                                <input
                                    class="form-control {{ $errors->has('module_fields.' . $key) ? 'is-invalid' : '' }}"
                                    type="{{ $type === 'number' ? 'number' : ($type === 'date' ? 'date' : 'text') }}"
                                    id="{{ $inputId }}"
                                    name="{{ $inputName }}"
                                    value="{{ old('module_fields.' . $key, $value) }}"
                                    @if($required) required @endif
                                    @if(isset($definition['min'])) min="{{ $definition['min'] }}" @endif
                                    @if(isset($definition['max'])) max="{{ $definition['max'] }}" @endif
                                    @if(isset($definition['step'])) step="{{ $definition['step'] }}" @endif
                                >
                            @endif

                            @if($errors->has('module_fields.' . $key))
                                <div class="invalid-feedback">
                                    {{ $errors->first('module_fields.' . $key) }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif