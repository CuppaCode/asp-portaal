@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.recoveryOffice.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.recovery-offices.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.recoveryOffice.fields.id') }}
                        </th>
                        <td>
                            {{ $recoveryOffice->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.recoveryOffice.fields.company') }}
                        </th>
                        <td>
                            {{ $recoveryOffice->company->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.recoveryOffice.fields.identifier') }}
                        </th>
                        <td>
                            {{ $recoveryOffice->identifier }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.recovery-offices.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection