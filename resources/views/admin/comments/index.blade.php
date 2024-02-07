@extends('layouts.admin')
@section('content')
@can('comment_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.comments.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.comment.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.comment.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover datatable datatable-Comment">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.comment.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.comment.fields.body') }}
                    </th>
                    <th>
                        {{ trans('cruds.comment.fields.commentable_id') }}
                    </th>
                    <th>
                        {{ trans('cruds.comment.fields.commentable_type') }}
                    </th>
                    <th>
                        &nbsp;
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($comments as $key => $comment)
                    <tr data-entry-url="{{ route('admin.comments.show', $comment->id) }}" data-entry-id="{{ $comment->id }}">
                        <td>

                        </td>
                        <td>
                            {{ $comment->id ?? '' }}
                        </td>
                        <td>
                            {{ $comment->body ?? '' }}
                        </td>
                        <td>
                            {{ $comment->commentable_id ?? '' }}
                        </td>
                        <td>
                            {{ $comment->commentable_type ?? '' }}
                        </td>

                        <td class="edits-td">
                            @can('comment_show')
                                <a id="view-row-datatable" class="btn btn-xs btn-primary" href="{{ route('admin.comments.show', $comment->id) }}">
                                    {{ trans('global.view') }}
                                </a>
                            @endcan
                            

                            @can('comment_edit')
                                <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $comment->id) }}">
                                    {{ trans('global.edit') }}
                                </a>
                            @endcan

                            @can('comment_delete')
                                <form action="{{ route('admin.claims.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('comment_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.comments.massDestroy') }}",
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
  let table = $('.datatable-Comment:not(.ajaxTable)').DataTable({ buttons: dtButtons })
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