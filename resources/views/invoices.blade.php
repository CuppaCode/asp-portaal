@extends('layouts.admin')
@section('content')
            
<div class="card">  
    <div class="card-header">
        Nog te factureren
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable datatable-Invoices">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>Claim ID</th>
                        <th>Bedrijf</th>
                        <th>Gefactureerd</th>
                        <th>Opmerking</th>
                        <th>Bedrag</th>
                        <th>status</th>
                        <th>&nbsp;</th>
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                            <select class="search" strict="true">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach($companies as $key => $item)
                                    <option value="{{ $item->name }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            
                        </td>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($claims as $key => $claim)
                        <tr data-entry-url="{{ route('admin.claims.show', $claim->id) }}" data-entry-id="{{ $claim->id }}">
                            <td></td>
                            <td>{{ $claim->claim_number }}</td>
                            <td>{{ $claim->company->name }}</td>
                            <td>
                                @if ($claim->invoice_settlement_asp == 1)
                                Ja
                                @else
                                Nee
                                @endif
                            </td>
                            <td>{{ $claim->invoice_comment }}</td>
                            <td>&euro; {{ $claim->invoice_amount }}</td>
                            <td>{{ App\Models\Claim::STATUS_SELECT[$claim->status] }}</td>
                            <td></td>
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

    $.extend(true, $.fn.dataTable.defaults, {
        orderCellsTop: true,
        order: [[ 2, 'asc' ]],
        pageLength: 100,
    });
    let table = $('.datatable-Invoices:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
    
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
})

</script>
@endsection