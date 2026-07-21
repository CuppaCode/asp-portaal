@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>{{ trans('global.show') }} {{ trans('cruds.contact.title') }}</span>
        @can('contact_edit')
            <a href="{{ route('admin.contacts.edit', $contact->id) }}" class="btn btn-sm btn-primary">
                <i class="fa fa-edit"></i> {{ trans('global.edit') }}
            </a>
        @endcan
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.contacts.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.contact.fields.id') }}
                        </th>
                        <td>
                            {{ $contact->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.contact.fields.company') }}
                        </th>
                        <td>
                            {{ $contact->company->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.contact.fields.user') }}
                        </th>
                        <td>
                            {{ $contact->user->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.contact.fields.first_name') }}
                        </th>
                        <td>
                            {{ $contact->first_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.contact.fields.last_name') }}
                        </th>
                        <td>
                            {{ $contact->last_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.contact.fields.email') }}
                        </th>
                        <td>
                            {{ $contact->email }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.contact.fields.newsletter') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $contact->newsletter ? 'checked' : '' }}>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.contact.fields.phone') }}</th>
                        <td>{{ $contact->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.contact.fields.birthdate') }}</th>
                        <td>{{ $contact->birthdate ? \Carbon\Carbon::parse($contact->birthdate)->format('d-m-Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.contact.fields.driver_license_id') }}</th>
                        <td>{{ $contact->driver_license_id ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.contact.fields.note') }}</th>
                        <td>{{ $contact->note ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.contacts.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection