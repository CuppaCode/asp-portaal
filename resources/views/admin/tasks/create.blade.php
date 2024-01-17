@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.task.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.tasks.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="task_number">{{ trans('cruds.task.fields.task_number') }}</label>
                <input class="form-control {{ $errors->has('task_number') ? 'is-invalid' : '' }}" type="text" name="task_number" id="task_number" value="{{ old('task_number', '') }}" required>
                @if($errors->has('task_number'))
                    <div class="invalid-feedback">
                        {{ $errors->first('task_number') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.task.fields.task_number_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="description">{{ trans('cruds.task.fields.description') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{!! old('description') !!}</textarea>
                @if($errors->has('description'))
                    <div class="invalid-feedback">
                        {{ $errors->first('description') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.task.fields.description_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="user_id">{{ trans('cruds.task.fields.user') }}</label>
                <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id" id="user_id" required>
                    @foreach($users as $id => $entry)
                        <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('user'))
                    <div class="invalid-feedback">
                        {{ $errors->first('user') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.task.fields.user_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="claim_id">{{ trans('cruds.task.fields.claim') }}</label>
                <select class="form-control select2 {{ $errors->has('claim') ? 'is-invalid' : '' }}" name="claim_id" id="claim_id">
                    @foreach($claims as $id => $entry)
                        <option value="{{ $id }}" {{ old('claim_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('claim'))
                    <div class="invalid-feedback">
                        {{ $errors->first('claim') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.task.fields.claim_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="deadline_at">{{ trans('cruds.task.fields.deadline_at') }}</label>
                <input class="form-control datetime {{ $errors->has('deadline_at') ? 'is-invalid' : '' }}" type="text" name="deadline_at" id="deadline_at" value="{{ old('deadline_at') }}" required>
                @if($errors->has('deadline_at'))
                    <div class="invalid-feedback">
                        {{ $errors->first('deadline_at') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.task.fields.deadline_at_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.task.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Task::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', 'new') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <div class="invalid-feedback">
                        {{ $errors->first('status') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.task.fields.status_helper') }}</span>
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
    $(document).ready(function () {
  function SimpleUploadAdapter(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
      return {
        upload: function() {
          return loader.file
            .then(function (file) {
              return new Promise(function(resolve, reject) {
                // Init request
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('admin.tasks.storeCKEditorImages') }}', true);
                xhr.setRequestHeader('x-csrf-token', window._token);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.responseType = 'json';

                // Init listeners
                var genericErrorText = `Couldn't upload file: ${ file.name }.`;
                xhr.addEventListener('error', function() { reject(genericErrorText) });
                xhr.addEventListener('abort', function() { reject() });
                xhr.addEventListener('load', function() {
                  var response = xhr.response;

                  if (!response || xhr.status !== 201) {
                    return reject(response && response.message ? `${genericErrorText}\n${xhr.status} ${response.message}` : `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`);
                  }

                  $('form').append('<input type="hidden" name="ck-media[]" value="' + response.id + '">');

                  resolve({ default: response.url });
                });

                if (xhr.upload) {
                  xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                      loader.uploadTotal = e.total;
                      loader.uploaded = e.loaded;
                    }
                  });
                }

                // Send request
                var data = new FormData();
                data.append('upload', file);
                data.append('crud_id', '{{ $task->id ?? 0 }}');
                xhr.send(data);
              });
            })
        }
      };
    }
  }

  var allEditors = document.querySelectorAll('.ckeditor');
  for (var i = 0; i < allEditors.length; ++i) {
    ClassicEditor.create(
      allEditors[i], {
        extraPlugins: [SimpleUploadAdapter]
      }
    );
  }
});
</script>

@endsection