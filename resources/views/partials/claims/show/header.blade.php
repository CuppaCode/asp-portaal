<div class="top-bar-claims form-group d-flex justify-content-between align-items-center">
    <a class="btn btn-dark" href="{{ route('admin.claims.index') }}">
        {{ trans('global.back_to_list') }}
    </a>

    @if ($isAdminOrAgent)

        @if ($claim->assign_self == true)
            <div class="alert alert-danger" role="alert">
                Let op! Dit schadedossier wordt behandeld door klant zelf.
            </div>
        @endif

    @endif

    {{-- Draft Status Badge --}}
    @if(in_array($claim->status, ['draft', 'draft_denied']))
        <div class="alert alert-{{ $claim->status === 'draft' ? 'warning' : 'danger' }}" role="alert">
            @if($claim->status === 'draft')
                <strong>Concept:</strong> Wacht op goedkeuring
                @if($claim->draft_expires_at)
                    <br><small>Verloopt over {{ now()->diffInDays($claim->draft_expires_at, false) }} dagen</small>
                @endif
            @else
                <strong>Concept afgewezen:</strong> {{ $claim->denied_reason }}
            @endif
        </div>
    @endif

    <div>
        <!-- Company Details Toggle Button -->
        <button id="company-details-toggle" type="button" class="btn" style="background-color: #D32F2F; color: white; border: none;">
            <i class="fa fa-arrow-down"></i> Bedrijfsgegevens
        </button>

        <!-- Contact Person Toggle Button -->
        @if($firstContact)
        <button id="contact-details-toggle" type="button" class="btn" style="background-color: #689F38; color: white; border: none;">
            <i class="fa fa-arrow-down"></i> Contactpersoon
        </button>
        @endif

        <!-- Insurance Company Toggle Button (if exists) -->
        @if($claim->insurance_company_id)
        <button id="insurance-details-toggle" type="button" class="btn" style="background-color: #344A9B; color: white; border: none;">
            <i class="fa fa-arrow-down"></i> Verzekeraar
        </button>
        @endif

        @if($sla)
            @can('sla_access')
                <button id="sla-toggle" type="button" class="btn btn-secondary">
                    <i class="fa fa-arrow-down"></i> SLA details
                </button>
            @endcan
        @endif

        {{-- Draft Actions --}}
        @if($claim->status === 'draft')
            @can('approve_draft_claim', $claim)
                <form action="{{ route('admin.claims.resubmit', $claim) }}" method="POST" class="d-inline" onsubmit="return confirm('Wilt u deze concept claim goedkeuren?')">
                    @csrf
                    <button type="button" class="btn btn-success" onclick="approveDraft({{ $claim->id }})">
                        <i class="fa fa-check"></i> Goedkeuren
                    </button>
                </form>
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#denyDraftModal">
                    <i class="fa fa-times"></i> Afwijzen
                </button>
            @endcan
        @elseif($claim->status === 'draft_denied')
            @can('approve_draft_claim', $claim)
                <form action="{{ route('admin.claims.resubmit', $claim) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-redo"></i> Opnieuw Indienen
                    </button>
                </form>
            @endcan
        @endif

        @if( $claim->assign_self || $isAdminOrAgent)
            <a class="btn btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                {{ trans('global.edit') }}
            </a>
        @endif
    </div>
</div>

{{-- Deny Draft Modal --}}
@if($claim->status === 'draft')
<div class="modal fade" id="denyDraftModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.claims.deny', $claim->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Concept Claim Afwijzen</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reason">Reden voor afwijzing *</label>
                        <textarea name="reason" id="reason" class="form-control" rows="5" required 
                            placeholder="Geef aan waarom deze claim wordt afgewezen..."></textarea>
                        <small class="form-text text-muted">Minimaal 10 karakters vereist</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-times"></i> Claim Afwijzen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveDraft(claimId) {
    if(confirm('Wilt u deze concept claim goedkeuren en omzetten naar een actieve claim?')) {
        $.ajax({
            url: '/admin/claims/' + claimId + '/approve',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('Er is een fout opgetreden. Probeer het opnieuw.');
            }
        });
    }
}
</script>

<script>
$(function() {
    $('#company-details-toggle').on('click', function () {
        $('.sla-show').slideUp();
        $('.contact-details-show, .insurance-details-show').slideUp();
        $('.company-details-show').slideToggle();
    });
    $('#contact-details-toggle').on('click', function () {
        $('.sla-show').slideUp();
        $('.company-details-show, .insurance-details-show').slideUp();
        $('.contact-details-show').slideToggle();
    });
    $('#insurance-details-toggle').on('click', function () {
        $('.sla-show').slideUp();
        $('.company-details-show, .contact-details-show').slideUp();
        $('.insurance-details-show').slideToggle();
    });
});
</script>
@endif

<div class="row">
    <div class="col-md-6 offset-md-6" style="position: relative;">
        <div class="card card-sla text-white bg-blue-asp sla-show" style="display:none;">
            <div class="card-header">
                SLA Details
            </div>
            <div class="card-body">
                @if ($sla)
                <table class="table table-sm text-white">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.sla.fields.startdate') }}
                            </th>
                            <td>
                                {{ $sla->startdate ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.sla.fields.enddate') }}
                            </th>
                            <td>
                                {{ $sla->enddate ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.sla.fields.label') }}
                            </th>
                            <td>
                                @if ($sla->label)
                                    {{ App\Models\SLA::LABEL_SELECT[$sla->label] }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.sla.fields.amount_users') }}
                            </th>
                            <td>
                                {{ $sla->amount_users }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.sla.fields.max_amount') }}
                            </th>
                            <td>
                                &euro; {{ $sla->max_amount }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.sla.fields.reports') }}
                            </th>
                            <td>
                                @if ($sla->reports !== null)
                                    {{ App\Models\SLA::REPORT_SELECT[$sla->reports] }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.sla.fields.analytics') }}
                            </th>
                            <td>
                                @if ($sla->analytics_options !== null)
                                    @foreach (json_decode($sla->analytics_options) as $options)
                                        {{ App\Models\SLA::ANALYTICS_SELECT[$options] }},
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.sla.fields.other') }}
                            </th>
                            <td>
                                {{ $sla->other }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Company Details Card (Hidden by default) -->
<div class="row">
    <div class="col-md-6 offset-md-6">
        <div class="card card-company text-white bg-red-asp company-details-show" style="display:none;">
            <div class="card-header d-flex justify-content-between align-items-center">
                Bedrijfsgegevens
                <a href="{{ route('admin.companies.show', $claim->company->id) }}" class="btn btn-sm btn-light">Ga naar details</a>
            </div>
            <div class="card-body">
                @if ($claim->company)
                    <table class="table table-sm text-white">
                        <tbody>
                            <tr>
                                <th>{{ trans('cruds.company.fields.name') }}</th>
                                <td>{{ $claim->company->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('cruds.company.fields.email') }}</th>
                                <td>
                                    @if($claim->company->email)
                                        <a href="mailto:{{ $claim->company->email }}" class="text-white">{{ $claim->company->email }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ trans('cruds.company.fields.phone') }}</th>
                                <td>
                                    @if($claim->company->phone)
                                        <a href="tel:{{ $claim->company->phone }}" class="text-white">{{ $claim->company->phone }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ trans('cruds.company.fields.street') }}</th>
                                <td>{{ $claim->company->street ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('cruds.company.fields.city') }}</th>
                                <td>{{ $claim->company->city ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <p class="text-white">Geen bedrijfsgegevens beschikbaar</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Contact Person Details Card (Hidden by default) -->
@if($firstContact)
    <div class="row">
        <div class="col-md-6 offset-md-6">
            <div class="card card-contact text-white bg-green-asp contact-details-show" style="display:none;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Contactpersoon
                    <a href="{{ route('admin.contacts.show', $firstContact->id) }}" class="btn btn-sm btn-light">Ga naar details</a>
                </div>
                <div class="card-body">
                    <table class="table table-sm text-white">
                        <tbody>
                            <tr>
                                <th>{{ trans('cruds.contact.fields.first_name') }}</th>
                                <td>{{ $firstContact->first_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('cruds.contact.fields.last_name') }}</th>
                                <td>{{ $firstContact->last_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('cruds.contact.fields.email') }}</th>
                                <td>
                                    @if($firstContact->email)
                                        <a href="mailto:{{ $firstContact->email }}" class="text-white">{{ $firstContact->email }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ trans('cruds.contact.fields.phone') }}</th>
                                <td>
                                    @if($firstContact->phone)
                                        <a href="tel:{{ $firstContact->phone }}" class="text-white">{{ $firstContact->phone }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Insurance Company Details Card (Hidden by default, only if insurance company exists) -->
@if($claim->insurance_company_id && $claim->insuranceCompany)
    <div class="row">
        <div class="col-md-6 offset-md-6">
            <div class="card card-insurance text-white bg-blue-asp insurance-details-show" style="display:none;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Verzekeraar
                    <a href="{{ route('admin.companies.show', $claim->insuranceCompany->id) }}" class="btn btn-sm btn-light">Ga naar details</a>
                </div>
                <div class="card-body">
                    <table class="table table-sm text-white">
                        <tbody>
                            <tr>
                                <th>{{ trans('cruds.company.fields.name') }}</th>
                                <td>{{ $claim->insuranceCompany->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('cruds.company.fields.email') }}</th>
                                <td>
                                    @if($claim->insuranceCompany->email)
                                        <a href="mailto:{{ $claim->insuranceCompany->email }}" class="text-white">{{ $claim->insuranceCompany->email }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ trans('cruds.company.fields.phone') }}</th>
                                <td>
                                    @if($claim->insuranceCompany->phone)
                                        <a href="tel:{{ $claim->insuranceCompany->phone }}" class="text-white">{{ $claim->insuranceCompany->phone }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ trans('cruds.company.fields.street') }}</th>
                                <td>{{ $claim->insuranceCompany->street ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('cruds.company.fields.city') }}</th>
                                <td>{{ $claim->insuranceCompany->city ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif