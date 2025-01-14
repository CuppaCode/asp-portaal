@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-12">
            <h4>{{ trans('cruds.superAdmin.title') }}</h4>
        </div>

        @include('partials.super-admin.migrate-status')

    </div>
</div>
@endsection
@section('scripts')
@parent

@endsection