<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            Bijlages

            @if( $claim->assign_self || $isAdminOrAgent)
                <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}#attachments">
                    {{ trans('global.edit') }}
                </a>
            @endif
        </div>

        <div class="card-body">
            <div class="row">
                
                @foreach($parentMediaArray as $name => $mediaArray)

                    <div class="col-md-3">
                        <div class="card-title">
                            {{ $name }}
                        </div>
                        <p class="card-text media-box">

                            @foreach($mediaArray as $key => $media)
                                
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    <img src="{{ $media->getUrl() }}" alt="{{ $media->name }}"/>
                                </a>
                            @endforeach
                        </p>
                    </div>

                @endforeach
                
            </div>
        </div>
    </div>
</div>