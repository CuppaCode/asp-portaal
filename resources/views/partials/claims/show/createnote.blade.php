<div class="tab-pane active pt-3" id="noteSection" role="tabpanel" aria-labelledby="note-tab">
    <form method="POST" action="{{ route('admin.notes.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label class="required" for="title">{{ trans('cruds.note.fields.title') }}</label>
            <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                type="text" name="title" id="title" value="{{ old('title', '') }}"
                required>
            @if ($errors->has('title'))
                <div class="invalid-feedback">
                    {{ $errors->first('title') }}
                </div>
            @endif
            <span class="help-block">{{ trans('cruds.note.fields.title_helper') }}</span>
        </div>
        <div class="form-group">
            <label for="description">{{ trans('cruds.note.fields.description') }}</label>
            <textarea class="form-control ckeditor {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description"
                id="description">{!! old('description') !!}</textarea>
            @if ($errors->has('description'))
                <div class="invalid-feedback">
                    {{ $errors->first('description') }}
                </div>
            @endif
            <span class="help-block">{{ trans('cruds.note.fields.description_helper') }}</span>
        </div>
        <div class="form-group d-none">
            <select class="form-control select2 {{ $errors->has('claims') ? 'is-invalid' : '' }}"
                name="claims[]" id="claims" multiple required>
                <option value="{{ $claim->id }}" selected>{{ $claim->id }}</option>
            </select>
        </div>

        @if ($isAdminOrAgent)
            <div class="form-group d-none">
                <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}"
                    name="user_id" id="user_id" required>
                    <option value="{{ auth()->user()->id }}">{{ auth()->user()->name }}</option>
                </select>
            </div>
        @else
            <input type="hidden" name="user_id" id="user_id" value="{{ auth()->user()->id }}">
        @endif

        <div class="form-group">
            <button class="btn btn-danger" type="submit" name="add-new-note" value='true'>
                {{ trans('global.save') }}
            </button>
        </div>
    </form>
</div>