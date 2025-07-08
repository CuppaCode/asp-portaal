@extends('layouts.admin')
@section('content')

@php
    
    $user = auth()->user();
    $isAdmin = $user->can('financial_access');
    $isAdminOrAgent = $user->isAdminOrAgent();
    
@endphp

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
                            {{ trans('cruds.claim.fields.claim_feature') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.claim_number') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.date_accident') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.assignee') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.driver_vehicle')}}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.damage_kind')}}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.damaged_area')}}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.damage_origin')}}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.damaged_part')}}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.vehicle_opposite')}}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.opposite_claim_no') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.damaged_area_opposite')}}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.damage_origin_opposite')}}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.damaged_part_opposite')}}
                        </th>
                        <th>
                            {{ trans('cruds.opposite.fields.name') }} WP
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.injury') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.damage_costs') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.recovery_costs') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.recovery_office') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.expertise_office') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.recoverable_claim') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.loading_photos') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.unloading_photos') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.waybill_signed_at_loading') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.waybill_signed_at_unloading') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.invoice_amount') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.closed_at') }}
                        </th>
                        <th>
                            {{ trans('cruds.claim.fields.created_at') }}
                        </th>
                        <th style="width: 250px;">
                            
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
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
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
                                {{ $claim->vehicle->plates ?? '' }}
                            </td>
                            <td>
                                {{ $claim->claim_number ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Claim::STATUS_SELECT[$claim->status] ?? '' }}
                            </td>
                            <td>
                                {{ $claim->date_accident ?? ''}}
                            </td>
                            <td>
                                {{ $claim->assignee ?? ''}}
                            </td>
                            <td>
                                {{ App\Models\Driver::find($claim->driver_vehicle)->driver_name ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Claim::DAMAGE_KIND[$claim->damage_kind] ?? '' }}
                            </td>
                            <td>
                                {{ $claim->damaged_area_x ?? '' }}
                            </td>
                            <td>
                                {{ $claim->damaged_origin_x ?? '' }}
                            </td>
                            <td>
                                {{ $claim->damaged_part_x ?? '' }}
                            </td>
                            <td>
                                {{ $claim->opposite_driver_plate->plates ?? ''}}
                            </td>
                            <td>
                                {{ $claim->opposite_claim_no ?? '' }}
                            </td>
                            <td>
                                {{ $claim->damaged_area_opposite_x ?? '' }}
                            </td>
                            <td>
                                {{ $claim->damaged_origin_opposite_x ?? '' }}
                            </td>
                            <td>
                                {{ $claim->damaged_part_opposite_x ?? '' }}
                            </td>
                            <td>
                                {{ $claim->opposite->name ?? ''}}
                            </td>
                            <td>
                                {{ App\Models\Claim::INJURY_SELECT[$claim->injury] ?? '' }}
                            </td>
                            <td>
                                &euro; {{ dot_to_comma($claim->damage_costs) ?? ''}}
                            </td>
                            <td>
                                &euro; {{ dot_to_comma($claim->recovery_costs) ?? '' }}
                            </td>
                            <td>
                                {{ $claim->recovery_office_x ?? '' }}
                            </td>
                            <td>
                                {{ $claim->expertise_office_x ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Claim::RECOVERABLE_CLAIM_SELECT[$claim->recoverable_claim] ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Claim::WAYBILL_SELECT[$claim->loading_photos] ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Claim::WAYBILL_SELECT[$claim->unloading_photos] ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Claim::WAYBILL_SELECT[$claim->waybill_signed_at_loading] ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Claim::WAYBILL_SELECT[$claim->waybill_signed_at_unloading] ?? '' }}
                            </td>
                            <td>
                                &euro; {{ dot_to_comma($claim->invoice_amount) ?? '' }}
                            </td>
                            <td>
                                {{ $claim->closed_at ?? '' }}
                            </td>
                            <td>
                                {{ $claim->created_at ? $claim->created_at->format('d-m-Y') : '' }}
                            </td>
                            
                            <td class="edits-td">
                                @can('claim_show')
                                    <a id="view-row-datatable" class="btn btn-xs btn-primary" href="{{ route('admin.claims.show', $claim->id) }}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                @endcan
                                    
                                @unless( !$claim->assign_self && !$isAdminOrAgent )

                                    @can('claim_edit')
                                        <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                        </a>
                                    @endcan

                                @can('claim_delete')
                                    <form action="{{ route('admin.claims.destroy', $claim->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="btn btn-xs btn-danger">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                @endcan

                                @endunless
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
    order: [[ 5, 'desc' ]],
    pageLength: 50,
    stateSave: false,
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
        table.column( 8 ).visible( false );  
        table.column( 9 ).visible( false );  
        table.column( 10 ).visible( false );  
        table.column( 11 ).visible( false );  
        table.column( 12 ).visible( false );  
        table.column( 13 ).visible( false );  
        table.column( 14 ).visible( false );  
        table.column( 15 ).visible( false );  
        table.column( 16 ).visible( false );  
        table.column( 17 ).visible( false );  
        table.column( 18 ).visible( false );  
        table.column( 19 ).visible( false );  
        table.column( 20 ).visible( false );  
        table.column( 21 ).visible( false );  
        table.column( 22 ).visible( false );
        table.column( 23 ).visible( false );
        table.column( 24 ).visible( false );
        table.column( 25 ).visible( false );
        table.column( 26 ).visible( false );
        table.column( 27 ).visible( false );
        table.column( 28 ).visible( false );
        table.column( 29 ).visible( false );
        table.column( 30 ).visible( false );
        table.column( 31 ).visible( false );
        table.column( 32 ).visible( false );
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
