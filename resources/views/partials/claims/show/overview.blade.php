<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            Schadedossier overzicht

            @if( $claim->assign_self || $isAdminOrAgent)
                <select class="form-control col-md-4" id="current-status" data-claim-id="{{ $claim->id }}">

                    @foreach (App\Models\Claim::STATUS_SELECT as $key => $status)
                        <option value="{{ $key }}" {{ $claim->status == $key ? 'selected' : '' }}>
                            {{ $status }}</option>
                    @endforeach

                </select>
            @else
                <div class="col-md-3 btn btn-info">
                    {{ App\Models\Claim::STATUS_SELECT[$claim->status] }}
                </div>
            @endif

        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.claim_number') }}
                    </div>
                    {{ $claim->claim_number }}
                </div>
                <div class="col-md-3">
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.company') }}</div>
                    {{ $claim->company->name ?? '' }}
                </div>
                <div class="col-md-3">
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.subject') }}</div>
                    {{ $claim->subject }}
                </div>
                @if ($claim->opposite_claim_no)
                    <div class="col-md-2">
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.opposite_claim_no') }}</div>
                        {{ $claim->opposite_claim_no }}
                    </div>
                @endif
                @if ($assignee_name)
                    <div class="col-md-2">
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.assignee') }}</div>
                        {{ $assignee_name->first_name ?? 'Verwijderde gebruiker' . ' ' . $assignee_name->last_name ?? '' }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>