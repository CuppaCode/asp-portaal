<div class="tab-pane pt-3" id="taskSection" role="tabpanel" aria-labelledby="task-tab">
    <form method="POST" action="{{ route('admin.tasks.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <div class="form-group">
                <label class="required" for="user_id">Toewijzen aan</label>
                <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}"
                    name="user_id" id="user_id" required>
                    <option selected disabled>{{ trans('global.pleaseSelect') }}</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('user'))
                    <div class="invalid-feedback">
                        {{ $errors->first('user') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.task.fields.user_helper') }}</span>
            </div>

            <label for="description">{{ trans('cruds.task.fields.description') }}</label>
            <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description"
                id="description">{!! old('description') !!}</textarea>
            @if ($errors->has('description'))
                <div class="invalid-feedback">
                    {{ $errors->first('description') }}
                </div>
            @endif
            <span class="help-block">{{ trans('cruds.task.fields.description_helper') }}</span>
        </div>
        <div class="form-group d-none">
            <label for="claim_id">{{ trans('cruds.task.fields.claim') }}</label>
            <select class="form-control select2 {{ $errors->has('claim') ? 'is-invalid' : '' }}"
                name="claim_id" id="claim_id">
                <option value="{{ $claim->id }}">{{ $claim->claim_number }}</option>
            </select>
            @if ($errors->has('claim'))
                <div class="invalid-feedback">
                    {{ $errors->first('claim') }}
                </div>
            @endif
            <span class="help-block">{{ trans('cruds.task.fields.claim_helper') }}</span>
        </div>
        <div class="form-group">
            <label class="required"
                for="deadline_at">{{ trans('cruds.task.fields.deadline_at') }}</label>
            <input
                class="form-control date custom_datepicker {{ $errors->has('deadline_at') ? 'is-invalid' : '' }}"
                type="text" name="deadline_at" id="deadline_at"
                value="{{ old('deadline_at') }}" required>
            @if ($errors->has('deadline_at'))
                <div class="invalid-feedback">
                    {{ $errors->first('deadline_at') }}
                </div>
            @endif
            <span class="help-block">{{ trans('cruds.task.fields.deadline_at_helper') }}</span>
        </div>
        <div class="form-group d-none">
            <label class="required">{{ trans('cruds.task.fields.status') }}</label>
            <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}"
                name="status" id="status" required>
                <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>
                    {{ trans('global.pleaseSelect') }}</option>
                @foreach (App\Models\Task::STATUS_SELECT as $key => $label)
                    <option value="{{ $key }}"
                        {{ old('status', 'new') === (string) $key ? 'selected' : '' }}>
                        {{ $label }}</option>
                @endforeach
            </select>
            @if ($errors->has('status'))
                <div class="invalid-feedback">
                    {{ $errors->first('status') }}
                </div>
            @endif
            <span class="help-block">{{ trans('cruds.task.fields.status_helper') }}</span>
        </div>
        <div class="form-group">
            <button class="btn btn-danger" type="submit" name="add-task-dashboard" value='true'>
                {{ trans('global.save') }}
            </button>
        </div>
    </form>
</div>