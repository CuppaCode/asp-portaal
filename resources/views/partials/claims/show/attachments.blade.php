<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            Bijlages

            @if( $claim->assign_self || $isAdminOrAgent)
                <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                    {{ trans('global.edit') }}
                </a>
            @endif
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.damage_files') }}
                    </div>
                    <p class="card-text media-box">

                        @foreach ($claim->damage_files as $key => $media)
                            <a href="{{ $media->getUrl() }}" target="_blank">
                                <img src="{{ $media->getUrl('thumb') }}" alt="{{ $media->name }}" />
                            </a>
                        @endforeach
                    </p>
                </div>
                <div class="col-md-3">
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.report_files') }}
                    </div>
                    <p class="card-text media-box">
                        @foreach ($claim->report_files as $key => $media)
                            <a href="{{ $media->getUrl() }}" target="_blank">
                                <img src="{{ $media->getUrl('thumb') }}" alt="{{ $media->name }}" />
                            </a>
                        @endforeach
                    </p>
                </div>
                <div class="col-md-3">
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.financial_files') }}
                    </div>
                    <p class="card-text media-box">
                        @foreach ($claim->financial_files as $key => $media)
                            <a href="{{ $media->getUrl() }}" target="_blank">
                                <img src="{{ $media->getUrl('thumb') }}" alt="{{ $media->name }}" />
                            </a>
                        @endforeach
                    </p>
                </div>
                <div class="col-md-3">
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.other_files') }}
                    </div>
                    <p class="card-text media-box">
                        @foreach ($claim->other_files as $key => $media)
                            <a href="{{ $media->getUrl() }}" target="_blank">
                                <img src="{{ $media->getUrl('thumb') }}" alt="{{ $media->name }}" />
                            </a>
                        @endforeach
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>