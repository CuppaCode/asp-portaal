@extends('layouts.admin')
@section('content')

    @php

        use Carbon\Carbon;
        $user = auth()->user();
        $isAdminOrAgent = $user->isAdminOrAgent();

    @endphp

    @include('partials.claims.show.header')

    @include('partials.claims.show.eventModal')

    <div class="row">
        
        @include('partials.claims.show.overview')

        @include('partials.claims.show.claim')

        @include('partials.claims.show.contactdetails')

    </div>

    <div class="row">
        
        @include('partials.claims.show.cardetails')

        @include('partials.claims.show.opponentdetails')

    </div>

    <div class="row">
        
        @include('partials.claims.show.attachments')

    </div>

    <div class="card recent-activities">
        <div class="card-header d-flex justify-content-between align-items-center">
            Notities / Activiteiten
        </div>
    
        @include('partials.claims.show.activities')

    <div class="item last-form">
        <div class="row">
            <div class="col-2 date-holder text-right">
                <div class="icon"><i class="fa fa-plus"></i></div>
                <div class="date">Nieuwe notitie<span></span><br><span class="text-info"></span></div>
            </div>
            <div class="col-10 content">
                <ul class="nav nav-tabs" id="notes" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="note-tab" data-toggle="tab" href="#noteSection" role="tab"
                            aria-controls="note-tab" aria-selected="true">Notitie</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="task-tab" data-toggle="tab" href="#taskSection" role="tab"
                            aria-controls="task-tab" aria-selected="false">Taak</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="task-tab" data-toggle="tab" href="#mailSection" role="tab"
                            aria-controls="task-tab" aria-selected="false">Mail</a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    @include('partials.claims.show.createnote')

                    @include('partials.claims.show.createtask')

                    @include('partials.claims.show.createmail')

                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>


    @include('partials.claims.show.financial')

@endsection
