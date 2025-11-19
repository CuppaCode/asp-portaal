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
                <label for="category_id">Categorie</label>
                @php $oldCategory = old('category_id') ? \App\Models\CertificateCategory::find(old('category_id')) : null; @endphp
                <select id="category_id" name="category_id" class="form-control select2 {{ $errors->has('category_id') ? 'is-invalid' : '' }}">
                    @if($oldCategory)
                        <option value="{{ $oldCategory->id }}" selected data-duration="{{ $oldCategory->duration }}">{{ $oldCategory->name }}</option>
                    @endif
                </select>
                @if($errors->has('category_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('category_id') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.contact.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('first_name', '') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                
            </div>

            <div class="form-group">
                <label for="notify_date">{{ trans('cruds.claim.fields.notify_date') }}</label>
                <input class="form-control date custom_datepicker {{ $errors->has('notify_date') ? 'is-invalid' : '' }}" type="text" name="notify_date" id="notify_date" value="{{ old('notify_date') }}">
                @if($errors->has('notify_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('notify_date') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label for="expiry_date">{{ trans('cruds.claim.fields.expiry_date') }}</label>
                <input class="form-control date custom_datepicker {{ $errors->has('expiry_date') ? 'is-invalid' : '' }}" type="text" name="expiry_date" id="expiry_date" value="{{ old('expiry_date') }}">
                @if($errors->has('expiry_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('expiry_date') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
<script>
    $(function(){
        // Category select2 with ajax search
        function setExpiryFromDuration(duration) {
            if (!duration) return;
            var now = new Date();
            var target = new Date(now.getFullYear(), now.getMonth() + parseInt(duration), now.getDate());
            var dd = String(target.getDate()).padStart(2, '0');
            var mm = String(target.getMonth() + 1).padStart(2, '0');
            var yyyy = target.getFullYear();
            var formatted = dd + '-' + mm + '-' + yyyy;
            $('#expiry_date').val(formatted);
        }
        $('#category_id').select2({
            placeholder: 'Selecteer of zoek categorie',
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
                                tmpOption.attr('data-duration', res.duration);
                                $('#category_id').trigger('change');
                            } else {
                                var newOption = new Option(res.name, res.id, true, true);
                                $(newOption).attr('data-duration', res.duration);
                                $('#category_id').append(newOption).trigger('change');
                            }
                            if (typeof sendFlashMessage === 'function') sendFlashMessage('Categorie aangemaakt', 'alert-success');
                            setExpiryFromDuration(res.duration);
                        })
                        .fail(function () {
                            if (typeof sendFlashMessage === 'function') sendFlashMessage('Kon categorie niet aanmaken', 'alert-danger');
                        });
                } else {
                    // existing selection
                    var duration = selected.duration || $('#category_id').find('option[value="' + selected.id + '"]').data('duration');
                    setExpiryFromDuration(duration);
                }
            });

            // Also handle when changed programmatically or on open
            $('#category_id').on('change', function (e) {
                var data = $('#category_id').select2('data')[0];
                if (!data) return;
                var duration = data.duration || $('#category_id').find('option[value="' + data.id + '"]').data('duration');
                setExpiryFromDuration(duration);
            });

            // if there's already a selected option on load (old value), set expiry
            var initial = $('#category_id').find('option:selected');
            if (initial.length && initial.data('duration')) {
                setExpiryFromDuration(initial.data('duration'));
            }
    });
</script>
@endsection