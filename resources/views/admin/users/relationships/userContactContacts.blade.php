<div class="m-3">
    @can('contact_contact_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.contact-contacts.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.contactContact.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.contactContact.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-userContactContacts">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.contactContact.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.contactContact.fields.company') }}
                            </th>
                            <th>
                                {{ trans('cruds.contactContact.fields.user') }}
                            </th>
                            <th>
                                {{ trans('cruds.contactContact.fields.contact_phone_1') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contactContacts as $key => $contactContact)
                            <tr data-entry-id="{{ $contactContact->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $contactContact->id ?? '' }}
                                </td>
                                <td>
                                    {{ $contactContact->company->company_name ?? '' }}
                                </td>
                                <td>
                                    {{ $contactContact->user->name ?? '' }}
                                </td>
                                <td>
                                    {{ $contactContact->contact_phone_1 ?? '' }}
                                </td>
                                <td>
                                    @can('contact_contact_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.contact-contacts.show', $contactContact->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('contact_contact_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.contact-contacts.edit', $contactContact->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('contact_contact_delete')
                                        <form action="{{ route('admin.contact-contacts.destroy', $contactContact->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
</div>
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('contact_contact_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.contact-contacts.massDestroy') }}",
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
    order: [[ 2, 'asc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-userContactContacts:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection