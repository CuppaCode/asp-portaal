@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.view') }} {{ trans('cruds.certificateCategory.title_singular') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.certificate-categories.index') }}">{{ trans('global.back_to_list') ?? 'Back to list' }}</a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>{{ trans('cruds.certificateCategory.fields.id') }}</th>
                        <td>{{ $certificateCategory->id }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.certificateCategory.fields.name') }}</th>
                        <td>{{ $certificateCategory->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.certificateCategory.fields.duration') }}</th>
                        <td>{{ $certificateCategory->duration }} maanden</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
