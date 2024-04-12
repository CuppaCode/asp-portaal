@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-6 col-lg-3">
            
            <a href="{{ route("admin.claims.create") }}" class="nounderline" >
                <div class="card bg-success mb-4">
                    <div class="card-header position-relative d-flex justify-content-center align-items-center">
                    <span class="text-white">Schadedossier aanmaken</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        @if (auth()->user()->roles->contains(1))
        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-danger">
                <div class="card-header">
                    Alle openstaande dossiers voor ASP
                </div>
                <div class="card-body">
                    <div class="card-title">{{ $claims_count['claims_asp_open'] }}</div> 
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-info">
                <div class="card-header">
                    Alle openstaande dossiers
                </div>
                <div class="card-body">
                    <div class="card-title">{{ $claims_count['claims_all'] }}</div> 
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-warning">
                <div class="card-header">
                    Meest voorkomende schade
                </div>
                <div class="card-body">
                    <div class="card-title">{{ App\Models\Claim::DAMAGE_ORIGIN[$popular[0]] }}</div> 
                </div>
            </div>
        </div>
        @else 
        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-primary">
                <div class="card-header">
                    Alle openstaande dossiers
                </div>
                <div class="card-body">
                <div class="card-title">{{ $claims_count['company_claims_open'] }}</div> 
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                @if (auth()->user()->roles->contains(1))
                    <div class="card-header">
                        Alle openstaande schadedossiers
                    </div>

                    <div class="card-body">
                        <table class="table table-borderless table-striped table-responsive">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Bedrijf</th>
                                    <th scope="col">Onderwerp</th>
                                    <th scope="col">status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($claims as $claim)
                                <tr class='clickable-row' data-href='{{ route('admin.claims.show', $claim->id) }}'>
                                    <td>{{ $claim->claim_number }}</td>
                                    <td>{{ $claim->company->name }}</td>
                                    <td>{{ $claim->subject }}</td>
                                    <td>{{ App\Models\Claim::STATUS_SELECT[$claim->status] }}</td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                        {{ $claims->links() }}
                    </div>
                @else 
                    <div class="card-header">
                        Alle openstaande schadedossiers
                    </div>

                    <div class="card-body">
                        <table class="table table-borderless table-striped table-responsive">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Onderwerp</th>
                                    <th scope="col">status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($company_claims as $claim)
                                <tr class='clickable-row' data-href='{{ route('admin.claims.show', $claim->id) }}'>
                                    <td>{{ $claim->claim_number }}</td>
                                    <td>{{ $claim->subject }}</td>
                                    <td>{{ App\Models\Claim::STATUS_SELECT[$claim->status] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Openstaande taken (persoonlijk)
                </div>

                <div class="card-body">
                    @isset($personal_tasks)
                    <table class="table table-borderless table-striped table-responsive">
                        <thead>
                            <tr>
                                <th scope="col">Datum</th>
                                <th scope="col">Beschrijving</th>
                                <th scope="col">status</th>
                                <th scope="col">Schadedossier</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($personal_tasks as $task)
                                @php 
                                    $deadline_at = $task->deadline_at;
                                    $today = date('d-m-Y');

                                    // dd($deadline_at);
                                    $deadline_at_time = strtotime($deadline_at);
                                    $today_time = strtotime($today);
                                @endphp

                                @if($today_time <= $deadline_at_time)
                                    @php $overdue = 'overdue-no'; @endphp
                                @else 
                                    @php $overdue = 'overdue-yes'; @endphp
                                @endif
                                <tr class='clickable-row {{ $overdue }}' data-href='{{ route('admin.tasks.show', $task->id) }}'>
                                    <td>{{ date('d-m-Y', strtotime($task->deadline_at)) }}</td>
                                    <td>{!! Str::limit($task->description, 40) !!}</td>
                                    <td>{{ App\Models\TASK::STATUS_SELECT[$task->status] }}</td>
                                    <td>
                                        @isset($task->claim->claim_number) 
                                        {{ $task->claim->claim_number }}
                                        @else 

                                        @endisset
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    {{ $personal_tasks->links() }}

                    @else
                    Geen openstaande taken
                    @endisset
                </div>
            </div>
        </div>
        @if (auth()->user()->roles->contains(1))
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Alle openstaande taken
                    </div>
                    <div class="collapse show" id="collapseTasks">
                        <div class="card-body">
                            @isset($tasks)
                            <table class="table table-borderless table-striped table-responsive">
                                <thead>
                                    <tr>
                                        <th scope="col">Datum</th>
                                        <th scope="col">Beschrijving</th>
                                        <th scope="col">Owner</th>
                                        <th scope="col">status</th>
                                        <th scope="col">Schadedossier</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        @php 
                                            $deadline_at = $task->deadline_at;
                                            $today = date('d-m-Y');

                                            // dd($deadline_at);
                                            $deadline_at_time = strtotime($deadline_at);
                                            $today_time = strtotime($today);
                                        @endphp

                                        @if($today_time <= $deadline_at_time)
                                            @php $overdue = 'overdue-no'; @endphp
                                        @else 
                                            @php $overdue = 'overdue-yes'; @endphp  
                                        @endif
                                        <tr class='clickable-row {{ $overdue }}' data-href='{{ route('admin.tasks.show', $task->id) }}'>
                                            <td>{{ date('d-m-Y', strtotime($task->deadline_at)) }}</td>
                                            <td>{!! Str::limit($task->description, 25) !!}</td>
                                            <td>{{ $task->user->name }}</td>
                                            <td>{{ App\Models\TASK::STATUS_SELECT[$task->status] }}</td>
                                            <td>
                                                @isset($task->claim->claim_number) 
                                                {{ $task->claim->claim_number }}
                                                @else 
                                                ...
                                                @endisset
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                            {{ $tasks->links() }}
                            @else
                            Geen openstaande taken
                            @endisset

                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Nieuwe taak aanmaken
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route("admin.tasks.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="form-group">
                                <label class="required" for="user_id">{{ trans('cruds.task.fields.user') }}</label>
                                <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id" id="user_id" required>
                                    <option selected disabled>{{ trans('global.pleaseSelect') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('user'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('user') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.task.fields.user_helper') }}</span>
                            </div>
                            <label for="description">{{ trans('cruds.task.fields.description') }}</label>
                            <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{!! old('description') !!}</textarea>
                            @if($errors->has('description'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('description') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.task.fields.description_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="claim_id">{{ trans('cruds.task.fields.claim') }}</label>
                            <select class="form-control select2 {{ $errors->has('claim') ? 'is-invalid' : '' }}" name="claim_id" id="claim_id">
                                <option selected disabled>{{ trans('global.pleaseSelect') }}</option>
                                @foreach($claims as $claim)
                                    <option value="{{ $claim->id }}">{{ $claim->claim_number }}</option>
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
                            <input class="form-control date {{ $errors->has('deadline_at') ? 'is-invalid' : '' }}" type="text" name="deadline_at" id="deadline_at" value="{{ old('deadline_at') }}" required>
                            @if($errors->has('deadline_at'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('deadline_at') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.task.fields.deadline_at_helper') }}</span>
                        </div>
                        <div class="form-group d-none">
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
                            <button class="btn btn-danger" type="submit" name="add-task-dashboard" value='true'>
                                {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent

@endsection