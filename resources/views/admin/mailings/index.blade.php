@extends('layouts.admin')
@section('content')
@can('mailing_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.mailings.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.mailings.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.mailings.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Mailing">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>{{ trans('cruds.mailings.fields.id') }}</th>
                        <th>{{ trans('cruds.mailings.fields.subject') }}</th>
                        <th>{{ trans('cruds.mailings.fields.recipients') }}</th>
                        <th>{{ trans('cruds.mailings.fields.status') }}</th>
                        <th>{{ trans('cruds.mailings.fields.sent_at') }}</th>
                        <th>{{ trans('cruds.mailings.fields.user') }}</th>
                        <th>{{ trans('cruds.mailings.fields.claims') }}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mailings as $mailing)
                        <tr data-entry-id="{{ $mailing->id }}">
                            <td></td>
                            <td>{{ $mailing->id ?? '' }}</td>
                            <td>{{ $mailing->subject ?? '' }}</td>
                            <td>
                                @if($mailing->recipients)
                                    @foreach($mailing->recipients as $recipient)
                                        <span class="badge badge-info">{{ $recipient }}</span>
                                    @endforeach
                                @endif
                            </td>
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
                            <td>{{ $mailing->sent_at ? $mailing->sent_at->format('d-m-Y H:i') : '' }}</td>
                            <td>{{ $mailing->user->name ?? '' }}</td>
                            <td>
                                @foreach($mailing->claims as $claim)
                                    <span class="badge badge-info">{{ $claim->claim_number }}</span>
                                @endforeach
                            </td>
                            <td>
                                @can('mailing_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.mailings.show', $mailing->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('mailing_edit')
                                    @if($mailing->status != 'sent')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.mailings.edit', $mailing->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endif

                                    @if(in_array($mailing->status, ['ready', 'scheduled', 'failed']))
                                        <form action="{{ route('admin.mailings.send', $mailing->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-success" onclick="return confirm('Verzenden?')">
                                                <i class="fas fa-paper-plane"></i> Verzenden
                                            </button>
                                        </form>
                                    @endif
                                @endcan

                                @can('mailing_delete')
                                    <form action="{{ route('admin.mailings.destroy', $mailing->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('mailing_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.mailings.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-Mailing:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
         .columns.adjust();
  });
  
})

</script>
@endsection
