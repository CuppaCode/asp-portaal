@extends('layouts.admin')
@section('content')
@can('claim_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.claims.create') }}">
                {{ trans('cruds.claim.title_singular') }} aanmaken
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.claim.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="Claim-Dtable" class="table table-bordered table-striped table-hover datatable datatable-Claim">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.company') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.subject') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.claim_number') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.status') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <select class="search">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach($companies as $key => $item)
                                    <option value="{{ $item->name }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <select class="search" strict="true">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach(App\Models\Claim::STATUS_SELECT as $key => $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($claims as $key => $claim)
                        <tr data-entry-url="{{ route('admin.claims.show', $claim->id) }}" data-entry-id="{{ $claim->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $claim->id ?? '' }}
                            </td>
                            <td>
                                {{ $claim->company->name ?? '' }}
                            </td>
                            <td>
                                {{ $claim->subject ?? '' }}
                            </td>
                            <td>
                                {{ $claim->claim_number ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Claim::STATUS_SELECT[$claim->status] ?? '' }}
                            </td>
                            <td class="edits-td">
                                @can('claim_show')
                                    <a id="view-row-datatable" class="btn btn-xs btn-primary" href="{{ route('admin.claims.show', $claim->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('claim_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.claims.edit', $claim->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('claim_delete')
                                    <form action="{{ route('admin.claims.destroy', $claim->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('claim_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.claims.massDestroy') }}",
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
    order: [[ 1, 'asc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-Claim:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });

    $( function() {
        $.urlParam = function(name){
            var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
            if (results!=null){
                return results[1] || 0;
            }
        }
        
        if ($.urlParam('status') != null) {
            param = $.urlParam('status')
            table.column(5).search(param, 'strict').draw();
        }

        table.column( 1 ).visible( false );
    });
  
let visibleColumnsIndexes = null;
$('.datatable thead').on('input', '.search', function () {
      let strict = $(this).attr('strict') || false
      let value = strict && this.value ? "^" + this.value + "$" : this.value

      let index = $(this).parent().index()
      console.log(index);
      if (visibleColumnsIndexes !== null) {
        index = visibleColumnsIndexes[index]
      }

      table
        .column(index)
        .search(value, strict)
        .draw()
    });
    table.on('column-visibility.dt', function(e, settings, column, state) {
        visibleColumnsIndexes = []
        table.columns(":visible").every(function(colIdx) {
            visibleColumnsIndexes.push(colIdx);
        });
    })
    
})

</script>
@endsection