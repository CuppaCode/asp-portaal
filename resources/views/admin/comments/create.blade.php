@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.comment.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.comments.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="body">{{ trans('cruds.comment.fields.body') }}</label>
                <textarea class="form-control {{ $errors->has('body') ? 'is-invalid' : '' }}" name="body" id="body">{{ old('body') }}</textarea>
                @if($errors->has('body'))
                    <div class="invalid-feedback">
                        {{ $errors->first('body') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.comment.fields.body_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="commentable">{{ trans('cruds.comment.fields.commentable') }}</label>
                <input class="form-control {{ $errors->has('commentable') ? 'is-invalid' : '' }}" type="number" name="commentable" id="commentable" value="{{ old('commentable', '') }}" step="1" required>
                @if($errors->has('commentable'))
                    <div class="invalid-feedback">
                        {{ $errors->first('commentable') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.comment.fields.commentable_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="commentable_type">{{ trans('cruds.comment.fields.commentable_type') }}</label>
                <input class="form-control {{ $errors->has('commentable_type') ? 'is-invalid' : '' }}" type="text" name="commentable_type" id="commentable_type" value="{{ old('commentable_type', '') }}" required>
                @if($errors->has('commentable_type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('commentable_type') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.comment.fields.commentable_type_helper') }}</span>
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