@php
    use Carbon\Carbon;
@endphp


@foreach ($notesAndTasks as $item)
            @if ($item::class == 'App\Models\Note')
                @php
                    $note = $item;
                @endphp
    
                <div class="item" data-commentable-id="{{ $note->id }}">
                    <div class="row">
                        <div class="col-2 date-holder text-right">
                            <div class="icon"><i class="fa fa-user"></i></div>
                            <div class="date">
                                <span>{{ $note->user->name ?? "Verwijderde gebruiker" }}</span><br>
                                <span class="text-info">{{ $note->created_at }}</span>
                            </div>
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
                    
    
            @elseif ($item::class == 'App\Models\Task')
                @php
                    $task = $item;
                    $deadline = Carbon::parse($task->deadline_at)
                        ->locale('nl_NL')
                        ->format('D d F');
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

                                @php $isAdmin = auth()->user()->isAdmin ?? false; @endphp
                                @if (isset($task->user->id))
                                    @if (auth()->user()->id == $task->user->id || $isAdmin)
                                        <select class="js-task-status badge bg-success border-0 text-white text-center align-middle"
                                            style="width:auto;display:inline-flex;align-items:center;justify-content:center;padding:0.25em 0.6em;font-size:0.85em;font-weight:700;line-height:1.2;border-radius:0.25rem;vertical-align:middle;height:2em;min-height:2em;text-align:center;"
                                            data-task-id="{{ $task->id }}">

                                            @foreach (App\Models\Task::STATUS_SELECT as $key => $status)
                                                <option value="{{ $key }}"
                                                    {{ $task->status == $key ? 'selected' : '' }}>
                                                    {{ $status }}</option>
                                            @endforeach

                                        </select>
                                            <span class="badge bg-warning js-task-edit-btn" data-task-id="{{ $task->id }}" style="cursor:pointer; width:auto; display:inline-flex; align-items:center; justify-content:center; padding:0.25em 0.6em; font-size:0.85em; font-weight:700; line-height:1.2; border-radius:0.25rem; vertical-align:middle; height:2em; min-height:2em; text-align:center;">Bewerk</span>
                                    @else
                                        <span
                                            class="badge bg-success" style="width:auto; display:inline-flex; align-items:center; justify-content:center; padding:0.25em 0.6em; font-size:0.85em; font-weight:700; line-height:1.2; border-radius:0.25rem; vertical-align:middle; height:2em; min-height:2em; text-align:center;">{{ App\Models\Task::STATUS_SELECT[$task->status] }}</span>
                                    @endif
                                    <span class="badge bg-primary" style="width:auto; display:inline-flex; align-items:center; justify-content:center; padding:0.25em 0.6em; font-size:0.85em; font-weight:700; line-height:1.2; border-radius:0.25rem; vertical-align:middle; height:2em; min-height:2em; text-align:center;">{{ $deadline }}</span>
                                    <span class="badge bg-info" style="width:auto; display:inline-flex; align-items:center; justify-content:center; padding:0.25em 0.6em; font-size:0.85em; font-weight:700; line-height:1.2; border-radius:0.25rem; vertical-align:middle; height:2em; min-height:2em; text-align:center;">{{ $task->user->name }}</span>
                                @endif
                                

                            </div>
                            <div class="task-desc">{!! nl2br($task->description) !!}</div>
                                <form class="js-task-edit-form" data-task-id="{{ $task->id }}" style="display:none;">
                                    <div class="mb-2">
                                        <label for="task-desc-{{ $task->id }}">Omschrijving</label>
                                        <textarea class="form-control" id="task-desc-{{ $task->id }}" name="description">{{ $task->description }}</textarea>
                                    </div>
                                    <div class="mb-2">
                                        <label for="task-status-{{ $task->id }}">Status</label>
                                        <select class="form-control" id="task-status-{{ $task->id }}" name="status">
                                            @foreach (App\Models\Task::STATUS_SELECT as $key => $status)
                                                <option value="{{ $key }}" {{ $task->status == $key ? 'selected' : '' }}>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label for="task-user-{{ $task->id }}">Toewijzen aan</label>
                                        <select class="form-control" id="task-user-{{ $task->id }}" name="user_id">
                                            @foreach (\App\Models\User::all() as $user)
                                                <option value="{{ $user->id }}" {{ $task->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Opslaan</button>
                                    <button type="button" class="btn btn-link btn-sm js-task-edit-cancel">Annuleren</button>
                                </form>

                @else

                    <div class="alert-warning">Er is iets verkeerd gegaan...</div>

                @endif
    
                <div class="action-icons">
        
                    <a class="action-icon add-comment" href="javascript:;" data-commentable-id="{{ $item->id }}"
                        data-commentable-type="{{ $item::class }}">
        
                        <i class="fa fa-reply" aria-hidden="true"></i>
        
                    </a>
        
                    <a class="action-icon hide-comment" href="javascript:;" data-commentable-id="{{ $item->id }}"
                        data-commentable-type="{{ $item::class }}">
        
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
            
                                @if ($comment->user)
                                    <span>{{ $comment->user->name }}</span>
                                @endif
                                <br>
                                <span class="text-info">{{ $comment->created_at }}</span>
                            </div>
                        </div>
            
                        <div class="col-7">
                            {{ $comment->body }}
                            {{ $comment->team_id }}
                        </div>
                        
                        <div class="col-1 text-right">
                            @can('comment_delete')
                                <button class="btn btn-danger btn-sm delete-comment-btn" data-comment-id="{{ $comment->id }}">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                            @endcan
                        </div>

                    </div>
            
                </div>
            @endforeach

    @include('partials.claims.show.submitcomment')

    <a class="js-read-more read-more" href="javascript:;">
        <span class="js-read-more-text">Lees meer...</span>
        <span class="comment-total">({{ count($item->comments) }})</span>
    </a>

    </div>
    @endforeach