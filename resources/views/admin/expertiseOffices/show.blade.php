@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.expertiseOffice.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.expertise-offices.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.expertiseOffice.fields.id') }}
                        </th>
                        <td>
                            {{ $expertiseOffice->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.expertiseOffice.fields.company') }}
                        </th>
                        <td>
                            {{ $expertiseOffice->company->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.expertiseOffice.fields.identifier') }}
                        </th>
                        <td>
                            {{ $expertiseOffice->identifier }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.expertise-offices.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection