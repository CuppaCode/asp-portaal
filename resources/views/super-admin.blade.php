@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-12">
            <h4>{{ trans('cruds.superAdmin.title') }}</h4>

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mt-3" id="superadmin-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="migrations-tab" data-toggle="tab" href="#migrations" role="tab" aria-controls="migrations" aria-selected="true">
                        <i class="fas fa-database"></i> Data Migrations
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="triggers-tab" data-toggle="tab" href="#triggers" role="tab" aria-controls="triggers" aria-selected="false">
                        <i class="fas fa-envelope"></i> Email Triggers
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content mt-3" id="superadmin-tab-content">
                <!-- Data Migrations Tab -->
                <div class="tab-pane fade show active" id="migrations" role="tabpanel" aria-labelledby="migrations-tab">
                    <div class="row">
                        @include('partials.super-admin.migrate-status')
                        @include('partials.super-admin.migrate-opposite-type')
                        @include('partials.super-admin.migrate-damaged-part')
                        @include('partials.super-admin.migrate-damaged-part-opposite')
                        @include('partials.super-admin.migrate-damage-origin')
                        @include('partials.super-admin.migrate-damage-origin-opposite')
                        @include('partials.super-admin.migrate-damaged-area')
                        @include('partials.super-admin.migrate-damaged-area-opposite')
                    </div>
                </div>

                <!-- Email Triggers Tab -->
                <div class="tab-pane fade" id="triggers" role="tabpanel" aria-labelledby="triggers-tab">
                    @include('partials.super-admin.manage-triggers')
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent

@endsection