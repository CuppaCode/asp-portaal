@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">  
                <div class="card-header">
                    Nog te factureren
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Claim ID</th>
                                    <th scope="col">Bedrijf</th>
                                    <th scope="col">Gefactureerd</th>
                                    <th scope="col">Opmerking</th>
                                    <th scope="col">Bedrag</th>
                                    <th scope="col">status</th>
                                </tr>
                            </thead>
                        @foreach($claims as $claim)
                            <tr class='clickable-row' data-href='{{ route('admin.claims.edit', $claim->id) }}'>
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
                            </tr>
                        @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
@parent

@endsection