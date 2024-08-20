@extends('layouts.admin')
@section('content')

@php

    use Carbon\Carbon;
    $user = auth()->user();
    $isAdminOrAgent = $user->isAdminOrAgent();

@endphp

<div class="top-bar-claims form-group d-flex justify-content-between align-items-center">
    <a class="btn btn-dark" href="{{ route('admin.claims.index') }}">
        {{ trans('global.back_to_list') }}
    </a>

    @if ($isAdminOrAgent)

        @if ($claim->assign_self == true)
            <div class="alert alert-danger" role="alert">
                Let op! Dit schadedossier wordt behandeld door klant zelf.
            </div>
        @endif
    
    @endif

    @unless( !$claim->assign_self && !$isAdminOrAgent )

        <a class="btn btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
            {{ trans('global.edit') }}
        </a>

    @endunless
    
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        Schadedossier overzicht
        
        @if( $claim->assign_self || $isAdminOrAgent)
        <select class="form-control col-md-4" id="current-status" data-claim-id="{{ $claim->id }}">

            @foreach (App\Models\Claim::STATUS_SELECT as $key => $status)

                <option value="{{ $key }}" {{ $claim->status == $key ? 'selected' : '' }}>{{ $status }}</option>

            @endforeach

        </select>

        @else 
        <div class="col-md-3 btn btn-info">
            {{ App\Models\Claim::STATUS_SELECT[$claim->status] }}
        </div>
        @endif
  
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-2">
                <div class="card-title">
                    {{ trans('cruds.claim.fields.claim_number') }}
                </div>
                {{ $claim->claim_number }}
            </div>
            <div class="col-md-3">
                <div class="card-title">
                    {{ trans('cruds.claim.fields.company') }}</div>
                {{ $claim->company->name ?? '' }}
            </div>
            <div class="col-md-3">
                <div class="card-title">
                    {{ trans('cruds.claim.fields.subject') }}</div>
                {{ $claim->subject }}
            </div>
            @if ($claim->opposite_claim_no)
            <div class="col-md-2">
                <div class="card-title">
                    {{ trans('cruds.claim.fields.opposite_claim_no') }}</div>
                {{ $claim->opposite_claim_no }}
            </div>
            @endif
            @if ($claim->assignee_id && isset($assignee_name))
            <div class="col-md-2">
                <div class="card-title">
                    {{ trans('cruds.claim.fields.assignee') }}</div>
                {{ $assignee_name->first_name . ' ' . $assignee_name->last_name }}
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                Schademelding

                @unless( !$claim->assign_self && !$isAdminOrAgent )
                <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                    {{ trans('global.edit') }}
                </a>
                @endunless
            </div>

            <div class="card-body">

                <div class="card-title">
                    {{ trans('cruds.claim.fields.created_at') }}
                </div>
                <p class="card-text">{{ $claim->created_at }}</p>
                
                @if (!empty($claim->date_accident))
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.date_accident') }}
                    </div>
                    <p class="card-text">{{ $claim->date_accident }}</p>
                @endif

                @if (!empty($claim->injury))

                <div class="card-title">
                    {{ trans('cruds.claim.fields.injury') }}
                </div>
                <p class="card-text">{{ App\Models\Claim::INJURY_SELECT[$claim->injury] ?? '' }}</p>
                @endif        
                @if ($claim->injury == 'yes')
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.injury_office') }}
                    </div>
                    <p class="card-text text-capitalize">
                        @if ($claim->injury_office != null)
                        {{ substr($claim->injury_office->identifier, 7) ?? '' }}
                        @endif
                    </p>
                @elseif ( $claim->injury == 'other' )
                <div class="card-title">
                    {{ trans('cruds.claim.fields.injury_other') }}
                </div>
                <p class="card-text">{{ $claim->injury_other }}</p>
                @else

                @endif
                <div class="card-title">
                    {{ trans('cruds.claim.fields.recoverable_claim') }}
                </div>
                <p class="card-text">{{ App\Models\Claim::RECOVERABLE_CLAIM_SELECT[$claim->recoverable_claim] ?? '' }}</p>
                @if (!empty($claim->damage_kind))
                <div class="card-title">
                    Soort schade
                </div>
                <p class="card-text">{{ App\Models\Claim::DAMAGE_KIND[$claim->damage_kind] ?? '' }}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                Contactgegevens

                @unless( !$claim->assign_self && !$isAdminOrAgent )
                <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                    {{ trans('global.edit') }}
                </a>
                @endunless
            </div>
            <div class="card-body">
                @isset($firstContact) 
                <div class="card-title">
                    Naam
                </div>
                <p class="card-text">
                    {{ $firstContact->first_name}} {{ $firstContact->last_name}}   
                </p>
                <div class="card-title">
                    Email
                </div>
                <p class="card-text"><a href="mailto:{{ $firstContact->email}}">{{ $firstContact->email}}   </a> </p>
                @else
                    Nog geen contactpersoon bekend.
                @endisset 

            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        @if (!empty($claim->vehicle->name) || !empty($claim->damaged_part) || !empty($claim->damage_origin) || !empty($claim->damaged_area) || !empty($claim->driver_vehicle ))
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Gegevens wagenpark

                    @unless( !$claim->assign_self && !$isAdminOrAgent )
                    <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                        {{ trans('global.edit') }}
                    </a>
                    @endunless
                </div>

                <div class="card-body">
                    @if (!empty($claim->vehicle->name))
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.vehicle') }}
                    </div>
                    <p class="card-text">{{ $claim->vehicle->name ?? '' }}</p>
                    @endif
                    @if (!empty($claim->damaged_part))
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.damaged_part') }}
                    </div>
                        <p class="card-text">
                            @if ( $claim->damaged_part !== null )
                                @foreach(json_decode( $claim->damaged_part ) as $part)
                                    <span class="badge badge-success">{{ App\Models\Claim::DAMAGED_PART_SELECT[$part] }}</span>
                                @endforeach
                            @endif
                        </p>
                    @endif

                    @if (!empty($claim->damage_origin))
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.damage_origin') }}
                    </div>
                    <p class="card-text">
                        @if ( $claim->damage_origin !== null )
                            @foreach(json_decode( $claim->damage_origin ) as $origin)
                                <span class="badge badge-success">{{ App\Models\Claim::DAMAGE_ORIGIN[$origin] }}</span>
                            @endforeach
                        @endif
                    </p>
                    @endif

                    @if (!empty( $claim->damaged_area ))
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.damaged_area') }}
                    </div>
                    
                    <p class="card-text">
                        @if ( $claim->damaged_area !== null )
                            @foreach(json_decode( $claim->damaged_area ) as $area)
                                <span class="badge badge-success">{{ App\Models\Claim::DAMAGED_AREA_SELECT[$area] }}</span>
                            @endforeach
                        @endif
                    </p>
                    <p class="card-text">{{ App\Models\Claim::DAMAGED_AREA_SELECT[$claim->damaged_area] ?? '' }}</p>
                    @endif

                    @if (!empty($claim->driver_vehicle))
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.driver_vehicle') }}
                    </div>
                    <p class="card-text">{{ App\Models\Driver::find($claim->driver_vehicle)->driver_full_name ?? '' }}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
    <div class="col-md-6">
        @if (!empty($claim->vehicle_opposite) || !empty($claim->damaged_part_opposite) || !empty($claim->damage_origin_opposite) || !empty($claim->damaged_area_opposite) || !empty($claim->driver_vehicle_opposite))

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                Gegevens wederpartij

                @unless( !$claim->assign_self && !$isAdminOrAgent )
                <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                    {{ trans('global.edit') }}
                </a>
                @endunless
            </div>

            <div class="card-body">
                @if ($claim->opposite_type != 'obstacle')
                    @if (!empty($claim->vehicle_opposite) )
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.vehicle_opposite') }}
                        </div>
                        <p class="card-text">{{ $claim->vehicle_opposite->name ?? '' }}</p>
                    @endif
                    @if (!empty($claim->damaged_part_opposite))
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.damaged_part_opposite') }}
                        </div>
                        <p class="card-text">
                            @if ( $claim->damaged_part_opposite !== null )
                                @foreach(json_decode( $claim->damaged_part_opposite ) as $part)
                                    <span class="badge badge-success">{{ App\Models\Claim::DAMAGED_PART_SELECT[$part] }}</span>
                                @endforeach
                            @endif
                        </p>
                    @endif
                    @if(!empty($claim->damage_origin_opposite))
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.damage_origin_opposite') }}
                        </div>
                        <p class="card-text">
                            @if ( $claim->damage_origin_opposite !== null )
                                @foreach(json_decode( $claim->damage_origin_opposite ) as $origin)
                                    <span class="badge badge-success">{{ App\Models\Claim::DAMAGE_ORIGIN_OPPOSITE[$origin] }} </span>
                                @endforeach
                            @endif
                        </p>
                    @endif
                    @if (!empty($claim->damaged_area_opposite))
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.damaged_area_opposite') }}
                        </div>
                        <p class="card-text">
                            @if ( $claim->damaged_area_opposite !== null )
                                @foreach(json_decode( $claim->damaged_area_opposite ) as $area)
                                    <span class="badge badge-success">{{ App\Models\Claim::DAMAGED_AREA_OPPOSITE_SELECT[$area] }} </span>
                                @endforeach
                            @endif
                        </p>
                    @endif
                    @if (!empty($claim->driver_vehicle_opposite))
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.driver_vehicle_opposite') }}
                        </div>
                        <p class="card-text">{{ App\Models\Driver::find($claim->driver_vehicle_opposite)->driver_full_name ?? '' }}</p>
                    @endif

                @else
                <div class="card-title">
                    {{ trans('cruds.claim.fields.obstacle') }}
                </div>
                <p class="card-text">{{ $claim->obstacle }}</p>
                @endif
            </div>
        </div>
        @endif
        @if (!empty($opposite))
            @if (!empty($opposite->name) || !empty($opposite->street) || !empty($opposite->phone) || !empty($opposite->email)   )
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Details wederpartij

                    @unless( !$claim->assign_self && !$isAdminOrAgent )
                    <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                        {{ trans('global.edit') }}
                    </a>
                    @endunless
                </div>

                <div class="card-body">
                    @if (!empty($opposite->name))
                        <div class="card-title">
                            {{ trans('cruds.opposite.fields.name') }}
                        </div>
                        <p class="card-text">{{ $opposite->name ?? '' }}</p>
                    @endif
                    @if (!empty($opposite->street))
                        <div class="card-title">
                            {{ trans('cruds.opposite.fields.street') }}
                        </div>
                        <p class="card-text">{{ $opposite->street ?? '' }}</p>
                    @endif
                    @if (!empty($opposite->zipcode))
                        <div class="card-title">
                            {{ trans('cruds.opposite.fields.zipcode') }} + {{ trans('cruds.opposite.fields.city') }}
                        </div>
                        <p class="card-text">{{ $opposite->zipcode ?? '' }} {{ $opposite->city ?? '' }}</p>
                    @endif
                    @if (!empty($opposite->phone))
                        <div class="card-title">
                            {{ trans('cruds.opposite.fields.phone') }}
                        </div>
                        <p class="card-text">{{ $opposite->phone ?? '' }}</p>
                    @endif
                    @if (!empty($opposite->email))
                        <div class="card-title">
                            {{ trans('cruds.opposite.fields.email') }}
                        </div>
                        <p class="card-text"><a href="mailto:{{ $opposite->email ?? '' }}">{{ $opposite->email ?? '' }}</a></p>
                    @endif
                
                </div>
            </div>
            @endif
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                Bijlages

                @unless( !$claim->assign_self && !$isAdminOrAgent )
                <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                    {{ trans('global.edit') }}
                </a>
                @endunless
            </div>

            <div class="card-body">
                <div class="row">

                    @foreach($parentMediaArray as $name => $mediaArray)
                    

                        <div class="col-md-3">
                            <div class="card-title">
                                {{ $name }}
                            </div>
                            <p class="card-text media-box">

                                @foreach($mediaArray as $key => $media)
                                    
                                    <a href="{{ $media->getUrl() }}" target="_blank">
                                        <img src="{{ $media->getUrl() }}" alt="{{ $media->name }}"/>
                                    </a>

                                @endforeach
                            </p>
                        </div>

                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>

<div class="card recent-activities">
    <div class="card-header d-flex justify-content-between align-items-center">
        Notities / Activiteiten
    </div>

    @foreach ($notesAndTasks as $item)

        @if ($item::class == 'App\Models\Note')

            @php 
                $note = $item;
            @endphp

            <div class="item" data-commentable-id="{{ $note->id }}">
                <div class="row">
                    <div class="col-2 date-holder text-right">
                        <div class="icon"><i class="fa fa-user"></i></div>
                        <div class="date"> <span>{{ $note->user->name }}</span><br><span class="text-info">{{ $note->created_at }}</span></div>
                        </div>
                        <div class="col-10 content">
                        <h5> {{ $note->title }}</h5>
                        {!! nl2br($note->description) !!}
                        
                        @if($item->hasMedia('attachments'))

                            <div class="note-imagewrapper">

                                <strong>Bijlage(s):</strong><br/><br/>

                                @foreach($item->getMedia('attachments') as $image)

                                    <a download href="{{ $image->getUrl() }}">
                                        <img src="{{ $image->getUrl('thumb') }}" alt="Image">
                                    </a>
                                @endforeach

                            </div>

                        @endif
                      
                    {{-- </div>
                </div>
            </div> --}}

        @elseif ($item::class == 'App\Models\Task')

            @php
                $task = $item;
                $deadline = Carbon::parse($task->deadline_at)->locale('nl_NL')->format('D d F');
            @endphp

            

            <div class="item task" data-commentable-id="{{ $task->id }}">
                <div class="row">
                    <div class="col-2 date-holder text-right">
                        <div class="icon"><i class="fa fa-calendar-check-o"></i></div>

                        <div class="date">

                            <span class="text-info">{{ $task->created_at }}</span>

                        </div>
                    </div>

                    <div class="col-10 content">
                        <div class="status">

                            @if (auth()->user()->id == $task->user->id)

                                <select class="js-task-status badge bg-success" data-task-id="{{ $task->id }}">

                                    @foreach (App\Models\Task::STATUS_SELECT as $key => $status)
                        
                                        <option value="{{ $key }}" {{ $task->status == $key ? 'selected' : '' }}>{{ $status }}</option>
                        
                                    @endforeach
                        
                                </select>
                            
                            @else

                                <span class="badge bg-success">{{ App\Models\Task::STATUS_SELECT[$task->status] }}</span>

                            @endif

                            <span class="badge bg-primary">{{ $deadline }}</span>
                            <span class="badge bg-info">{{ $task->user->name }}</span>
                            
                        </div>
                        
                        {!! nl2br($task->description) !!}
                        
                    
                    {{-- </div>
                </div>
            </div> --}}

        @else

            <div class="alert-warning">Er is iets verkeerd gegaan...</div>

        @endif

                    <div class="action-icons">

                        <a class="action-icon add-comment" href="javascript:;" data-commentable-id="{{ $item->id }}" data-commentable-type="{{ $item::class }}">

                            <i class="fa fa-reply" aria-hidden="true"></i>

                        </a>

                        <a class="action-icon hide-comment" href="javascript:;" data-commentable-id="{{ $item->id }}" data-commentable-type="{{ $item::class }}">

                            <i class="fa fa-chevron-up" aria-hidden="true"></i>

                        </a>
                    </div>

                </div>
            </div>

            @foreach ($item->comments as $comment)
            
                <div class="item comment">
                    <div class="row">

                        <div class="col-2 p-0"></div>

                        <div class="col-2 date-holder text-right">
                            <div class="icon"><i class="fa fa-commenting-o"></i></div>
                            <div class="date">

                                @if ( $comment->user )

                                    <span>{{ $comment->user->name }}</span>

                                @endif
                                <br>
                                <span class="text-info">{{ $comment->created_at }}</span>
                            </div>
                        </div>

                        <div class="col-8">

                            {{ $comment->body }}
                            {{ $comment->team_id }}

                        </div>

                    </div>

                </div>
            @endforeach

            <div class="item form">
                <div class="row">

                    <div class="col-2 p-0"></div>

                    <div class="col-10">
                        <form method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="body">{{ trans('cruds.comment.fields.body') }}</label>
                                <textarea class="form-control {{ $errors->has('body') ? 'is-invalid' : '' }}" name="body" id="body" required>{{ old('body') }}</textarea>
                                @if($errors->has('body'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('body') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.comment.fields.body_helper') }}</span>
                            </div>

                            <input type="hidden" name="commentable" data-id="commentable_{{ $item->id }}" value="{{ $item->id }}"/>
                            <input type="hidden" name="commentable_type" data-id="commentable_type_{{ $item->id }}" value="{{ $item::class }}"/>
                            <input type="hidden" name="user_id" data-id="user_id_{{ $item->id }}" value="{{ auth()->user()->id }}" />
                            <input type="hidden" name="team_id" data-id="team_id_{{ $item->id }}" value="{{ auth()->user()->team->id }}" />
                        
                            <div class="form-group">
                                <button class="btn btn-danger" type="submit" data-submit-comment>
                                    {{ trans('global.save') }}
                                </button>

                                <div class="action-icons action-icons-bottom">

                                    <a class="action-icon hide-comment" href="javascript:;" data-commentable-id="{{ $item->id }}" data-commentable-type="{{ $item::class }}">

                                        <i class="fa fa-chevron-up" aria-hidden="true"></i>
            
                                    </a>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <a class="js-read-more read-more" href="javascript:;">
                <span class="js-read-more-text">Lees meer...</span>
                    
                @if (count($item->comments) > 0)

                    <span class="comment-total">
                        ({{ count($item->comments) }})
                    </span>

                @endif
                
            </a>

        </div>
    @endforeach
    
    <div class="item last-form">
        <div class="row">
            <div class="col-2 date-holder text-right">
                <div class="icon"><i class="fa fa-plus"></i></div>
                <div class="date">Nieuwe notitie<span></span><br><span class="text-info"></span></div>
                </div>
                <div class="col-10 content">
                    <ul class="nav nav-tabs" id="notes" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active" id="note-tab" data-toggle="tab" href="#noteSection" role="tab" aria-controls="note-tab" aria-selected="true">Notitie</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="task-tab" data-toggle="tab" href="#taskSection" role="tab" aria-controls="task-tab" aria-selected="false">Taak</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="task-tab" data-toggle="tab" href="#mailSection" role="tab" aria-controls="task-tab" aria-selected="false">Mail</a>
                        </li>
                      </ul>
                      
                      <!-- Tab panes -->
                      <div class="tab-content">
                        <div class="tab-pane active pt-3" id="noteSection" role="tabpanel" aria-labelledby="note-tab">
                            <form method="POST" action="{{ route("admin.notes.store") }}" enctype="multipart/form-data">
                            @csrf
                                <div class="form-group">
                                    <label class="required" for="title">{{ trans('cruds.note.fields.title') }}</label>
                                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', '') }}" required>
                                    @if($errors->has('title'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('title') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.note.fields.title_helper') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="description">{{ trans('cruds.note.fields.description') }}</label>
                                    <textarea class="form-control ckeditor {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{!! old('description') !!}</textarea>
                                    @if($errors->has('description'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('description') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.note.fields.description_helper') }}</span>
                                </div>
                                <div class="form-group d-none">
                                    <select class="form-control select2 {{ $errors->has('claims') ? 'is-invalid' : '' }}" name="claims[]" id="claims" multiple required>
                                        <option value="{{ $claim->id }}" selected>{{ $claim->id }}</option>
                                    </select>
                                </div>
                        
                                @if (auth()->user()->isAdminOrAgent())
                                <div class="form-group d-none">
                                    <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id" id="user_id" required>
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
                        
                        <div class="tab-pane pt-3" id="taskSection" role="tabpanel" aria-labelledby="task-tab">
                            <form method="POST" action="{{ route("admin.tasks.store") }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <div class="form-group">
                                        <label class="required" for="user_id">Toewijzen aan</label>
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
                                <div class="form-group d-none">
                                    <label for="claim_id">{{ trans('cruds.task.fields.claim') }}</label>
                                    <select class="form-control select2 {{ $errors->has('claim') ? 'is-invalid' : '' }}" name="claim_id" id="claim_id">
                                            <option value="{{ $claim->id }}">{{ $claim->claim_number }}</option>
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
                                    <input class="form-control date custom_datepicker {{ $errors->has('deadline_at') ? 'is-invalid' : '' }}" type="text" name="deadline_at" id="deadline_at" value="{{ old('deadline_at') }}" required>
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

                        <div class="tab-pane pt-3" id="mailSection" role="tabpanel" aria-labelledby="mail-tab">
                            <form method="POST" action="{{ route("admin.claims.sendMail") }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <div class="form-group">
                                        <label class="required" for="mailReceiver">Ontvanger</label>
                                        <select class="form-control select2" name="mailReceiver[]" id="mailReceiver" required multiple="multiple">

                                            @foreach($allContactsInCompany as $id => $entry)
                                                <option value="{{ $entry->email }}" {{ old('mailReceiver') ? 'selected' : '' }}>{{ $entry->first_name ?? '' }} {{ $entry->last_name ?? '' }} - {{ $entry->email }}</option>
                                            @endforeach
                                        </select>
                                
                                    </div>
                                    <div class="form-group">
                                        <label for="template">Template</label>
                                        <select class="form-control select2" name="mailTemplate" id="mailTemplate">

                                            <option selected disabled>{{ trans('global.pleaseSelect') }}</option>

                                            @foreach($mailTemplates as $id => $entry)
                                                <option value="{{ $entry->body }}" data-subject="{{ $entry->subject ?? '' }}">{{ $entry->name ?? '' }}</option>
                                            @endforeach
                                        </select>
                                
                                    </div>
                                    <div class="form-group">

                                        <label for="mailSubject" class="required">Onderwerp</label>
                                        <input type="text" class="form-control" name="mailSubject" id="mailSubject" value="" required>

                                    </div>

                                    <div class="form-group">

                                        <label class="required" for="mailBody">Bericht</label>
                                        <textarea class="form-control" name="mailBody" id="mailBody">{!! old('mailBody') !!}</textarea>

                                    </div>


                                    <div class="form-group">

                                        <label for="mailAttachments">Bijlage</label>
                                        <input type="file" name="mailAttachments[]" id="mailAttachments" multiple>

                                    </div>

                                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                                    <div class="form-group d-none">
                                        <select class="form-control select2 {{ $errors->has('claims') ? 'is-invalid' : '' }}" name="claims[]" id="claims" multiple required>
                                            <option value="{{ $claim->id }}" selected>{{ $claim->id }}</option>
                                        </select>
                                    </div>
                                    <div class="d-none" id="claimJson">{{ json_encode($claim) }}</div>

                                    @if ($firstContact)

                                        <div class="d-none" id="contactJson">{{ json_encode($firstContact) }}</div>

                                    @endif

                                    @if (isset($claim->recovery_office))

                                        @php

                                            $recoveryOffice = App\Models\Company::find($claim->recovery_office->company_id);

                                        @endphp

                                        <div class="d-none" id="recoveryJson">{{ json_encode($recoveryOffice) }}</div>

                                        @if (!$recoveryOffice->contacts->isEmpty())

                                            <div class="d-none" id="recoveryContactJson">{{ json_encode($recoveryOffice->contacts) }}</div>

                                        @endif

                                    @endif

                                    @if (isset($claim->driver_vehicle))

                                        @php

                                            $driver = App\Models\Driver::find($claim->driver_vehicle);

                                            if(isset($driver)) {

                                                $driverContact = App\Models\Contact::find($driver->contact_id);

                                            }

                                        @endphp

                                        <div class="d-none" id="driverJson">{{ json_encode($driverContact) }}</div>

                                    @endif

                                    @if (isset($opposite))

                                        <div class="d-none" id="oppositeJson">{{ json_encode($opposite) }}</div>

                                    @endif

                                    <div class="d-none" id="statusSelectJson">{{ json_encode(App\Models\Claim::STATUS_SELECT) }}</div>
                                    <div class="d-none" id="damagePartSelectJson">{{ json_encode(App\Models\Claim::DAMAGED_PART_SELECT) }}</div>
                                    <div class="d-none" id="damageAreaSelectJson">{{ json_encode(App\Models\Claim::DAMAGED_AREA_SELECT) }}</div>
                                    <div class="d-none" id="damageOriginJson">{{ json_encode(App\Models\Claim::DAMAGE_ORIGIN) }}</div>
                                    <div class="d-none" id="damageKindJson">{{ json_encode(App\Models\Claim::DAMAGE_KIND) }}</div>
                                    <div class="d-none" id="recoverableClaimJson">{{ json_encode(App\Models\Claim::RECOVERABLE_CLAIM_SELECT) }}</div>

                                </div>
                                <div class="form-group">
                                    <button class="btn btn-danger" type="submit" name="add-task-dashboard" value='true'>
                                        {{ trans('global.send') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    </div>

    

@can ('financial_access')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Kosten Schadedossier

                    @unless( !$claim->assign_self && !$isAdminOrAgent )
                    <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                        {{ trans('global.edit') }}
                    </a>
                    @endunless
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card-title">
                                {{ trans('cruds.claim.fields.damage_costs') }}
                            </div>
                            <p class="card-text">&euro; {{ $claim->damage_costs }}</p>
                        
                            <div class="card-title">
                                {{ trans('cruds.claim.fields.recovery_costs') }}
                            </div>
                            <p class="card-text">&euro; {{ $claim->recovery_costs }}</p>
                        
                            <div class="card-title">
                                {{ trans('cruds.claim.fields.replacement_vehicle_costs') }}
                            </div>
                            <p class="card-text">&euro; {{ $claim->replacement_vehicle_costs }}</p>
                            
                            <div class="card-title">
                                {{ trans('cruds.claim.fields.expert_costs') }}
                            </div>
                            <p class="card-text">&euro; {{ $claim->expert_costs }}</p>
                        </div>
                        <div class="col-md-6">    
                            <div class="card-title">
                                {{ trans('cruds.claim.fields.other_costs') }}
                            </div>
                            <p class="card-text">&euro; {{ $claim->other_costs }}</p>
                            
                            <div class="card-title">
                                {{ trans('cruds.claim.fields.deductible_excess_costs') }}
                            </div>
                            <p class="card-text">&euro; {{ $claim->deductible_excess_costs }}</p>
                            
                            <div class="card-title">
                                {{ trans('cruds.claim.fields.insurance_costs') }}
                            </div>
                            <p class="card-text">&euro; {{ $claim->insurance_costs }}</p>
                        </div>
                    </div>
                
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    ASP Financieel
                    
                    @unless( !$claim->assign_self && !$isAdminOrAgent )
                    <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                        {{ trans('global.edit') }}
                    </a>
                    @endunless
                </div>

                <div class="card-body">
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.invoice_settlement') }}
                    </div>
                    <p class="card-text">
                        @if ($claim->invoice_settlement_asp == 1)
                        Ja
                        @else
                        Nee
                        @endif
                    </p>

                    @if ($claim->invoice_comment)
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.invoice_comment') }}
                    </div>
                    <p class="card-text">
                        {{ $claim->invoice_comment }}
                    </p>
                    @endif

                    @if ($claim->invoice_amount)
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.invoice_amount') }}
                    </div>
                    <p class="card-text">
                        &euro; {{ $claim->invoice_amount }}
                    </p>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endcan

@endsection