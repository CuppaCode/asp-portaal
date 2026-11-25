@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.certificate.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.certificate.store", ['driver' => $driver->id]) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="back_to" value="{{ url()->previous() }}">

            <div class="form-group">
                <label class="required" for="category_id">Certificaattype</label>
                @php $oldCategory = old('category_id') ? \App\Models\CertificateCategory::find(old('category_id')) : null; @endphp
                <select id="category_id" name="category_id" class="form-control select2 {{ $errors->has('category_id') ? 'is-invalid' : '' }}" required>
                    @if($oldCategory)
                        <option value="{{ $oldCategory->id }}" selected data-code="{{ $oldCategory->module_key }}">{{ $oldCategory->name }}</option>
                    @endif
                </select>
                @if($errors->has('category_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('category_id') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="issue_date">Datum afgifte</label>
                <input class="form-control date custom_datepicker {{ $errors->has('issue_date') ? 'is-invalid' : '' }}" type="text" name="issue_date" id="issue_date" value="{{ old('issue_date') }}" required>
                @if($errors->has('issue_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('issue_date') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="expiry_date">Geldig tot</label>
                <input class="form-control date custom_datepicker {{ $errors->has('expiry_date') ? 'is-invalid' : '' }}" type="text" name="expiry_date" id="expiry_date" value="{{ old('expiry_date') }}" required>
                @if($errors->has('expiry_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('expiry_date') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label for="card_number">Kaartnummer</label>
                <input class="form-control {{ $errors->has('card_number') ? 'is-invalid' : '' }}" type="text" name="card_number" id="card_number" value="{{ old('card_number') }}">
                @if($errors->has('card_number'))
                    <div class="invalid-feedback">
                        {{ $errors->first('card_number') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label for="attachments">Certificaat en scans</label>
                <input class="form-control {{ $errors->has('attachments') || $errors->has('attachments.*') ? 'is-invalid' : '' }}"
                       type="file" name="attachments[]" id="attachments" accept=".pdf,.jpg,.jpeg,.png" multiple>
                <div class="form-text">PDF, JPG of PNG. Maximaal 10 MB per bestand, maximaal 10 bestanden.</div>
                @if($errors->has('attachments') || $errors->has('attachments.*'))
                    <div class="invalid-feedback d-block">{{ $errors->first('attachments') ?: $errors->first('attachments.*') }}</div>
                @endif
            </div>

            <div id="module-fields-container">
                @include('admin.certificate.partials.module-fields')
            </div>

            <div class="form-group d-flex gap-2">
                <button class="btn btn-danger btn-sm" type="submit">
                    {{ trans('global.save') }}
                </button>
                <a href="javascript:history.back()" class="btn btn-secondary btn-sm">Terug</a>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
<script>
    $(function(){
        const moduleFieldSchemas = @json($moduleFieldSchemas ?? []);
        const moduleFieldValues = @json($moduleFieldValues ?? []);

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function buildModuleFieldHtml(definitions) {
            if (!definitions || definitions.length === 0) {
                return '';
            }

            const fieldsHtml = definitions.map((definition) => {
                const key = definition.key;
                const label = definition.label || key;
                const type = definition.type || 'text';
                const required = definition.required ? 'required' : '';
                const min = typeof definition.min !== 'undefined' ? `min="${definition.min}"` : '';
                const max = typeof definition.max !== 'undefined' ? `max="${definition.max}"` : '';
                const step = typeof definition.step !== 'undefined' ? `step="${definition.step}"` : '';
                const name = `module_fields[${key}]`;
                const id = `module_field_${key}`;
                const value = Object.prototype.hasOwnProperty.call(moduleFieldValues, key) ? moduleFieldValues[key] : '';
                const escapedValue = escapeHtml(value ?? '');

                if (type === 'textarea') {
                    return `
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="${id}">${label}</label>
                                <textarea class="form-control" id="${id}" name="${name}" rows="3">${escapedValue}</textarea>
                            </div>
                        </div>
                    `;
                }

                const inputType = type === 'number' ? 'number' : (type === 'date' ? 'date' : 'text');

                return `
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="${id}">${label}</label>
                            <input class="form-control" type="${inputType}" id="${id}" name="${name}" value="${escapedValue}" ${required} ${min} ${max} ${step}>
                        </div>
                    </div>
                `;
            }).join('');

            return `
                <div class="card mt-3">
                    <div class="card-header">Type-specifieke velden</div>
                    <div class="card-body">
                        <div class="row">${fieldsHtml}</div>
                    </div>
                </div>
            `;
        }

        function renderModuleFieldsByKey(moduleKey) {
            const container = document.getElementById('module-fields-container');
            if (!container) return;

            const definitions = moduleFieldSchemas[moduleKey] || [];
            const hasServerErrors = container.querySelector('.is-invalid') !== null;

            if (hasServerErrors) {
                return;
            }

            container.innerHTML = buildModuleFieldHtml(definitions);
        }

        // Certificate type select2 with ajax search
        $('#category_id').select2({
            placeholder: 'Selecteer of zoek certificaattype',
            allowClear: true,
                tags: true,
                ajax: {
                    url: '{{ route('admin.certificate-categories.search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                },
            minimumInputLength: 0
        });
        
            // When a selection is made
            $('#category_id').on('select2:select', function (e) {
                var selected = e.params.data;
                // new tag (no underlying element)
                if (!selected.element) {
                    $.post('{{ route('admin.certificate-categories.quickStore') }}', { name: selected.text })
                        .done(function (res) {
                            // try to replace the temporary option
                            var tmpOption = $('#category_id').find('option[value="' + selected.id + '"]');
                            if (tmpOption.length) {
                                tmpOption.attr('value', res.id);
                                tmpOption.attr('data-code', res.code);
                                $('#category_id').trigger('change');
                            } else {
                                var newOption = new Option(res.name, res.id, true, true);
                                $(newOption).attr('data-code', res.code);
                                $('#category_id').append(newOption).trigger('change');
                            }
                            if (typeof sendFlashMessage === 'function') sendFlashMessage('Certificaattype aangemaakt', 'alert-success');
                            renderModuleFieldsByKey(res.code);
                        })
                        .fail(function () {
                            if (typeof sendFlashMessage === 'function') sendFlashMessage('Kon certificaattype niet aanmaken', 'alert-danger');
                        });
                } else {
                    // existing selection
                    var code = selected.code || $('#category_id').find('option[value="' + selected.id + '"]').data('code');
                    renderModuleFieldsByKey(code);
                }
            });

            // Also handle when changed programmatically or on open
            $('#category_id').on('change', function (e) {
                var data = $('#category_id').select2('data')[0];
                if (!data) return;
                var code = data.code || $('#category_id').find('option[value="' + data.id + '"]').data('code');
                renderModuleFieldsByKey(code);
            });

            renderModuleFieldsByKey('{{ $selectedModuleKey ?? '' }}');
    });
</script>
@endsection