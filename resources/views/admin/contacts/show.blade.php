@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.contact.title') }}
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
                            @if($contact->company)
                                <a href="{{ route('admin.companies.show', $contact->company->id) }}">{{ $contact->company->name }}</a>
                            @else
                                -
                            @endif
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