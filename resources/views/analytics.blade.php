@extends('layouts.admin')
@section('content')
<div class="content">

    {{-- ===== FILTER BAR ===== --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-end">

                        {{-- Company selector --}}
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="a_company_id"><strong>{{ trans('cruds.claim.fields.company') }}</strong></label>
                                <select class="form-control select2" name="a_company_id" id="a_company_id">
                                    @foreach($companies as $id => $entry)
                                        <option value="{{ $id }}">{{ $entry }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Period presets --}}
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label><strong>Periode</strong></label><br>
                                <div class="btn-group btn-group-sm" id="period-presets" role="group">
                                    <button type="button" class="btn btn-outline-primary period-btn active" data-period="monthly">Maandelijks</button>
                                    <button type="button" class="btn btn-outline-primary period-btn" data-period="quarterly">Kwartaal</button>
                                    <button type="button" class="btn btn-outline-primary period-btn" data-period="halfyearly">Halfjaar</button>
                                    <button type="button" class="btn btn-outline-primary period-btn" data-period="yearly">Jaarlijks</button>
                                </div>
                                <input type="hidden" id="selected_period" value="monthly">
                            </div>
                        </div>

                        {{-- Date range --}}
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label><strong>Datumbereik</strong></label>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="input-group" id="datetimepicker1" data-td-target-input="nearest" data-td-target-toggle="nearest">
                                            <input id="datetimepicker1Input" type="text" class="form-control form-control-sm" placeholder="Van" data-td-target="#datetimepicker1" data-td-toggle="datetimepicker"/>
                                            <div class="input-group-append" data-td-target="#datetimepicker1" data-td-toggle="datetimepicker">
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group" id="datetimepicker2" data-td-target-input="nearest" data-td-target-toggle="nearest">
                                            <input id="datetimepicker2Input" type="text" class="form-control form-control-sm" placeholder="Tot" value="{{ $dn }}" data-td-target="#datetimepicker2" data-td-toggle="datetimepicker"/>
                                            <div class="input-group-append" data-td-target="#datetimepicker2" data-td-toggle="datetimepicker">
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="col-md-2">
                            <div class="form-group mb-0 d-flex flex-column" style="gap: 6px;">
                                <button id="btn-load-stats" class="btn btn-success btn-sm">
                                    <i class="fa fa-bar-chart"></i> Toon statistieken
                                </button>
                                <a id="btn-export-csv" href="#" class="btn btn-outline-secondary btn-sm d-none">
                                    <i class="fa fa-download"></i> Exporteer CSV
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== VIEW TOGGLE (shown once data loads) ===== --}}
    <div id="view-toggle-bar" class="row mb-3 d-none">
        <div class="col-md-12 d-flex justify-content-end align-items-center">
            <span class="mr-2 text-muted small">Weergave:</span>
            <div class="btn-group btn-group-sm" role="group" id="view-toggle">
                <button type="button" class="btn btn-primary active" data-view="charts">
                    <i class="fa fa-bar-chart"></i> Grafieken
                </button>
                <button type="button" class="btn btn-outline-primary" data-view="tables">
                    <i class="fa fa-table"></i> Cijfers
                </button>
            </div>
        </div>
    </div>

    {{-- ===== STATISTICS CARDS ===== --}}
    <div id="analytics-area" class="d-none">

        {{-- Row 1: Schadebedrag + Besparing per periode --}}
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header"><strong>Schadebedrag per periode</strong></div>
                    <div class="card-body">
                        <div class="chart-area"><canvas id="chart_damage_period"></canvas></div>
                        <div class="table-area d-none">
                            <div class="table-responsive" id="table_damage_period"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header"><strong>Besparing per periode</strong></div>
                    <div class="card-body">
                        <div class="chart-area"><canvas id="chart_savings_period"></canvas></div>
                        <div class="table-area d-none">
                            <div class="table-responsive" id="table_savings_period"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Per soort + Per activiteit --}}
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header"><strong>Schadebedrag per soort</strong></div>
                    <div class="card-body">
                        <div class="chart-area"><canvas id="chart_kind"></canvas></div>
                        <div class="table-area d-none">
                            <div class="table-responsive" id="table_kind"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header"><strong>Schadebedrag per activiteit <small class="text-muted">(transportschade)</small></strong></div>
                    <div class="card-body">
                        <div class="chart-area"><canvas id="chart_activity"></canvas></div>
                        <div class="table-area d-none">
                            <div class="table-responsive" id="table_activity"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 3: Per medewerker + Per voertuig --}}
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header"><strong>Schade per medewerker</strong></div>
                    <div class="card-body">
                        <div class="chart-area"><canvas id="chart_driver"></canvas></div>
                        <div class="table-area d-none">
                            <div class="table-responsive" id="table_driver"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header"><strong>Schade per voertuig</strong></div>
                    <div class="card-body">
                        <div class="chart-area"><canvas id="chart_vehicle"></canvas></div>
                        <div class="table-area d-none">
                            <div class="table-responsive" id="table_vehicle"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 4: Claims status + Verhaalbaarheid --}}
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header"><strong>Aantal claims</strong> <small class="text-muted">totaal / toegewezen / afgewezen</small></div>
                    <div class="card-body">
                        <div class="chart-area"><canvas id="chart_status"></canvas></div>
                        <div class="table-area d-none">
                            <div class="table-responsive" id="table_status"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header"><strong>Verhaalbare vs niet-verhaalbare schade</strong></div>
                    <div class="card-body">
                        <div class="chart-area"><canvas id="chart_recoverable"></canvas></div>
                        <div class="table-area d-none">
                            <div class="table-responsive" id="table_recoverable"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 5: Verwijtbaarheid + Letselclaims --}}
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header"><strong>Verwijtbaarheid</strong> <small class="text-muted">eigen schuld</small></div>
                    <div class="card-body">
                        <div class="chart-area"><canvas id="chart_verwijtbaar"></canvas></div>
                        <div class="table-area d-none">
                            <div class="table-responsive" id="table_verwijtbaar"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header"><strong>Letselclaims</strong></div>
                    <div class="card-body">
                        <div class="chart-area"><canvas id="chart_injury"></canvas></div>
                        <div class="table-area d-none">
                            <div class="table-responsive" id="table_injury"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 6: Tegenpartij type + Kostensamenstelling --}}
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header"><strong>Tegenpartij type</strong></div>
                    <div class="card-body">
                        <div class="chart-area"><canvas id="chart_opposite"></canvas></div>
                        <div class="table-area d-none">
                            <div class="table-responsive" id="table_opposite"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header"><strong>Kostensamenstelling besparing</strong> <small class="text-muted">per kostenpost</small></div>
                    <div class="card-body">
                        <div class="chart-area"><canvas id="chart_cost_breakdown"></canvas></div>
                        <div class="table-area d-none">
                            <div class="table-responsive" id="table_cost_breakdown"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 7: Gemiddelde afhandelingstijd (full width) --}}
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header"><strong>Gemiddelde afhandelingstijd</strong> <small class="text-muted">dagen van schadedatum tot afsluiting</small></div>
                    <div class="card-body">
                        <div class="chart-area"><canvas id="chart_resolution_time" style="max-height:300px;"></canvas></div>
                        <div class="table-area d-none">
                            <div class="table-responsive" id="table_resolution_time"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /#analytics-area --}}

</div>{{-- /.content --}}
@endsection

@section('scripts')
@parent
@endsection