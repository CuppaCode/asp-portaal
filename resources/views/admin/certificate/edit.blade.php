@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.certificate.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.certificate.update', $certificate->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="back_to" value="{{ url()->previous() }}">

            <div class="form-group">
                <label for="category_id">Categorie</label>
                @php $currentCategory = $certificate->category; @endphp
                <select id="category_id" name="category_id" class="form-control select2 {{ $errors->has('category_id') ? 'is-invalid' : '' }}">
                    @if($currentCategory)
                        <option value="{{ $currentCategory->id }}" selected data-duration="{{ $currentCategory->duration }}">{{ $currentCategory->name }}</option>
                    @endif
                </select>
                @if($errors->has('category_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('category_id') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="name">Naam</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $certificate->name) }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label for="notify_date">Melding datum</label>
                <input class="form-control date custom_datepicker {{ $errors->has('notify_date') ? 'is-invalid' : '' }}" type="text" name="notify_date" id="notify_date" value="{{ old('notify_date', $certificate->notify_date) }}">
                @if($errors->has('notify_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('notify_date') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label for="expiry_date">Vervaldatum</label>
                <input class="form-control date custom_datepicker {{ $errors->has('expiry_date') ? 'is-invalid' : '' }}" type="text" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', $certificate->expiry_date) }}">
                @if($errors->has('expiry_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('expiry_date') }}
                    </div>
                @endif
            </div>

            <div class="form-group d-flex gap-2">
                <button class="btn btn-danger btn-sm" type="submit">{{ trans('global.save') }}</button>
                <a href="javascript:history.back()" class="btn btn-secondary btn-sm">Terug</a>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
@parent
<script>
    $(function(){
        // reuse the same select2 quick-create logic as the create form
        $('#category_id').select2({
            placeholder: 'Selecteer of zoek categorie',
            allowClear: true,
            tags: true,
            ajax: {
                url: '{{ route('admin.certificate-categories.search') }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            },
            minimumInputLength: 0
        });

        $('#category_id').on('select2:select', function (e) {
            var selected = e.params.data;
            if (!selected.element) {
                $.post('{{ route('admin.certificate-categories.quickStore') }}', { name: selected.text })
                .done(function (res) {
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
                });
            }
        });
    });
</script>
@endsection
