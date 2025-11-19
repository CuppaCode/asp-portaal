@extends('layouts.admin')
@section('content')
@can('certificate_category_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.certificate-categories.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.certificateCategory.title_singular') }}
            </a>
        </div>
    </div>
@endcan

<div class="card">
    <div class="card-header">
        {{ trans('cruds.certificateCategory.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-CertificateCategory">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>{{ trans('cruds.certificateCategory.fields.id') }}</th>
                        <th>{{ trans('cruds.certificateCategory.fields.name') }}</th>
                        <th>{{ trans('cruds.certificateCategory.fields.duration') }}</th>
                        <th>&nbsp;</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input class="search" type="text" placeholder="{{ trans('global.search') }}"></td>
                        <td><input class="search" type="text" placeholder="{{ trans('global.search') }}"></td>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $key => $category)
                        <tr data-entry-url="{{ route('admin.certificate-categories.show', $category->id) }}" data-entry-id="{{ $category->id }}">
                            <td></td>
                            <td>{{ $category->id ?? '' }}</td>
                            <td>{{ $category->name ?? '' }}</td>
                            <td>{{ $category->duration ?? '' }} maanden</td>
                            <td>
                                @can('certificate_category_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.certificate-categories.show', $category->id) }}">{{ trans('global.view') }}</a>
                                @endcan

                                @can('certificate_category_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.certificate-categories.edit', $category->id) }}">{{ trans('global.edit') }}</a>
                                @endcan

                                @can('certificate_category_delete')
                                    <form action="{{ route('admin.certificate-categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
  let table = $('.datatable-CertificateCategory:not(.ajaxTable)').DataTable();

  let visibleColumnsIndexes = null;
  $('.datatable thead').on('input', '.search', function () {
      let strict = $(this).attr('strict') || false
      let value = strict && this.value ? "^" + this.value + "$" : this.value

      let index = $(this).parent().index()
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
});
</script>
@endsection
