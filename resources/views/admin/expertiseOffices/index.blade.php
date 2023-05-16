@extends('layouts.admin')
@section('content')
@can('expertise_office_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.expertise-offices.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.expertiseOffice.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.expertiseOffice.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-ExpertiseOffice">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.expertiseOffice.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.expertiseOffice.fields.company') }}
                        </th>
                        <th>
                            {{ trans('cruds.expertiseOffice.fields.identifier') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expertiseOffices as $key => $expertiseOffice)
                        <tr data-entry-id="{{ $expertiseOffice->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $expertiseOffice->id ?? '' }}
                            </td>
                            <td>
                                {{ $expertiseOffice->company->name ?? '' }}
                            </td>
                            <td>
                                {{ $expertiseOffice->identifier ?? '' }}
                            </td>
                            <td>

                                @can('expertise_office_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.expertise-offices.edit', $expertiseOffice->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('expertise_office_delete')
                                    <form action="{{ route('admin.expertise-offices.destroy', $expertiseOffice->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('expertise_office_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.expertise-offices.massDestroy') }}",
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
  let table = $('.datatable-ExpertiseOffice:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection