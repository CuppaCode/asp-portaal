<div class="item form">
    <div class="row">

        <div class="col-2 p-0"></div>

        <div class="col-10">
            <form method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="body">{{ trans('cruds.comment.fields.body') }}</label>
                    <textarea class="form-control {{ $errors->has('body') ? 'is-invalid' : '' }}" name="body" id="body"
                        required>{{ old('body') }}</textarea>
                    @if ($errors->has('body'))
                        <div class="invalid-feedback">
                            {{ $errors->first('body') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.comment.fields.body_helper') }}</span>
                </div>

                <input type="hidden" name="commentable" data-id="commentable_{{ $item->id }}"
                    value="{{ $item->id }}" />
                <input type="hidden" name="commentable_type" data-id="commentable_type_{{ $item->id }}"
                    value="{{ $item::class }}" />
                <input type="hidden" name="user_id" data-id="user_id_{{ $item->id }}"
                    value="{{ auth()->user()->id }}" />
                <input type="hidden" name="team_id" data-id="team_id_{{ $item->id }}"
                    value="{{ auth()->user()->team->id }}" />

                <div class="form-group">
                    <button class="btn btn-danger" type="submit" data-submit-comment>
                        {{ trans('global.save') }}
                    </button>

                    <div class="action-icons action-icons-bottom">

                        <a class="action-icon hide-comment" href="javascript:;"
                            data-commentable-id="{{ $item->id }}" data-commentable-type="{{ $item::class }}">

                            <i class="fa fa-chevron-up" aria-hidden="true"></i>

                        </a>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>