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

                                @if (auth()->user()->id == $task->user->id)
                                    <select class="js-task-status badge bg-success"
                                        data-task-id="{{ $task->id }}">

                                        @foreach (App\Models\Task::STATUS_SELECT as $key => $status)
                                            <option value="{{ $key }}"
                                                {{ $task->status == $key ? 'selected' : '' }}>
                                                {{ $status }}</option>
                                        @endforeach

                                    </select>
                                @else
                                    <span
                                        class="badge bg-success">{{ App\Models\Task::STATUS_SELECT[$task->status] }}</span>
                                @endif

                                <span class="badge bg-primary">{{ $deadline }}</span>
                                <span class="badge bg-info">{{ $task->user->name }}</span>

                            </div>
                            {!! nl2br($task->description) !!}

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
            
                        <div class="col-8">
                            {{ $comment->body }}
                            {{ $comment->team_id }}
                        </div>
            
                    </div>
            
                </div>
            @endforeach

    @include('partials.claims.show.submitcomment')

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