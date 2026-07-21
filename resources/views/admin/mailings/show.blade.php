@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.mailings.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.mailings.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>{{ trans('cruds.mailings.fields.id') }}</th>
                        <td>{{ $mailing->id }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.mailings.fields.subject') }}</th>
                        <td>{{ $mailing->subject }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.mailings.fields.recipients') }}</th>
                        <td>
                            @if($mailing->recipients)
                                @foreach($mailing->recipients as $recipient)
                                    <span class="badge badge-info">{{ $recipient }}</span>
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.mailings.fields.cc') }}</th>
                        <td>
                            @if($mailing->cc)
                                @foreach($mailing->cc as $cc)
                                    <span class="badge badge-secondary">{{ $cc }}</span>
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.mailings.fields.bcc') }}</th>
                        <td>
                            @if($mailing->bcc)
                                @foreach($mailing->bcc as $bcc)
                                    <span class="badge badge-secondary">{{ $bcc }}</span>
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.mailings.fields.reply_to') }}</th>
                        <td>{{ $mailing->reply_to }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.mailings.fields.status') }}</th>
                        <td>
                            @if($mailing->status == 'sent')
                                <span class="badge badge-success">{{ $mailing->status }}</span>
                            @elseif($mailing->status == 'failed')
                                <span class="badge badge-danger">{{ $mailing->status }}</span>
                            @elseif($mailing->status == 'scheduled')
                                <span class="badge badge-warning">{{ $mailing->status }}</span>
                            @else
                                <span class="badge badge-secondary">{{ $mailing->status }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.mailings.fields.sent_at') }}</th>
                        <td>{{ $mailing->sent_at ? $mailing->sent_at->format('d-m-Y H:i:s') : '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.mailings.fields.user') }}</th>
                        <td>{{ $mailing->user->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.mailings.fields.mail_template') }}</th>
                        <td>{{ $mailing->mailTemplate->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.mailings.fields.claims') }}</th>
                        <td>
                            @foreach($mailing->claims as $claim)
                                <a href="{{ route('admin.claims.show', $claim->id) }}" class="badge badge-info">{{ $claim->claim_number }}</a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.mailings.fields.body') }}</th>
                        <td>{!! $mailing->body !!}</td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.mailings.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
