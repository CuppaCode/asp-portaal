<div class="col-md-12">
    <!-- Fancybox CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
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
                    <div class="col-md-12 pb-3">
                        <div class="card-title">
                            {{ $name }}
                        </div>
                        <div class="card-text media-box row">
                                @foreach($mediaArray as $key => $media)
                                    @php
                                        $extension = strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION));
                                    @endphp
                                    <div class="mb-2 col-md" style="max-width:140px;width:100%;">
                                        @php $cleanName = preg_replace('/^\d+_/', '', $media->file_name); @endphp
                                        @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                            <a data-fancybox="gallery-{{ $name }}" href="{{ $media->getUrl() }}" data-caption="{{ $cleanName }}" title="{{ $cleanName }}">
                                                <img src="{{ $media->getUrl() }}" alt="{{ $cleanName }}" class="w-[140px] h-[140px] object-cover rounded border" style="width:140px;height:140px;object-fit:cover;display:inline-block;" onerror="this.style.display='none';this.parentNode.innerHTML='<div class=\'flex items-center justify-center rounded border bg-gray-100\' style=\'width:140px;height:140px;display:flex;align-items:center;justify-content:center;\'><i class=\'fa fa-file\' style=\'font-size:100px;color:#9ca3af;\'></i></div>'" />
                                            </a>
                                        @elseif($extension === 'pdf')
                                            <a data-fancybox="gallery-{{ $name }}" href="{{ $media->getUrl() }}" data-caption="{{ $cleanName }}" data-type="iframe" data-width="1200" data-height="800" style="max-width:100vw;max-height:100vh;" title="{{ $cleanName }}">
                                                <div class="flex items-center justify-center rounded border bg-gray-100" style="width:140px;height:140px;display:flex;align-items:center;justify-content:center;">
                                                    <i class="fa fa-file-pdf-o" style="font-size:100px;color:#9ca3af;"></i>
                                                </div>
                                            </a>
                                        @else
                                            <a href="{{ $media->getUrl() }}" target="_blank" title="{{ $cleanName }}">
                                                <div class="flex items-center justify-center rounded border bg-gray-100" style="width:140px;height:140px;">
                                                        <i class="fa fa-file" style="font-size:100px;color:#9ca3af;display:flex;align-items:center;justify-content:center;width:140px;height:140px;"></i>
                                                </div>
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                                </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>