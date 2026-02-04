<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt"></i> {{ trans('cruds.superAdmin.triggers.title') }}
                </h5>
                <small class="text-muted">{{ trans('cruds.superAdmin.triggers.subtitle') }}</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ trans('cruds.superAdmin.triggers.trigger_type') }}</th>
                                <th>{{ trans('cruds.superAdmin.triggers.description') }}</th>
                                <th>{{ trans('cruds.superAdmin.triggers.active_templates') }}</th>
                                <th>{{ trans('cruds.superAdmin.triggers.total_templates') }}</th>
                                <th>{{ trans('cruds.superAdmin.triggers.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $triggers = \App\Services\MailTriggerService::getAvailableTriggers();
                                $templates = \App\Models\MailTemplate::all();
                            @endphp

                            @foreach($triggers as $key => $trigger)
                                @php
                                    $triggerTemplates = $templates->where('trigger_type', $key);
                                    $activeCount = $triggerTemplates->where('is_active', true)->count();
                                    $totalCount = $triggerTemplates->count();
                                    $isAutomatic = !str_starts_with($key, 'MANUAL');
                                    
                                    // Get Dutch translations if available
                                    $triggerName = trans('cruds.superAdmin.triggers.types.' . $key . '.name');
                                    $triggerDesc = trans('cruds.superAdmin.triggers.types.' . $key . '.description');
                                    
                                    // Fallback to English if translation not found
                                    if (str_starts_with($triggerName, 'cruds.superAdmin')) {
                                        $triggerName = $trigger['name'];
                                        $triggerDesc = $trigger['description'];
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $triggerName }}</strong>
                                        @if($isAutomatic)
                                            <span class="badge badge-primary ml-2">{{ trans('cruds.superAdmin.triggers.automatic') }}</span>
                                        @else
                                            <span class="badge badge-secondary ml-2">{{ trans('cruds.superAdmin.triggers.manual') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $triggerDesc }}</td>
                                    <td>
                                        <span class="badge badge-success">{{ $activeCount }}</span>
                                    </td>
                                    <td>{{ $totalCount }}</td>
                                    <td>
                                        <a href="{{ route('admin.mail-templates.index') }}?filter[trigger_type]={{ $key }}" 
                                           class="btn btn-xs btn-info" 
                                           title="{{ trans('cruds.superAdmin.triggers.view_templates') }}">
                                            <i class="fas fa-list"></i> {{ trans('cruds.superAdmin.triggers.view_templates') }}
                                        </a>
                                        <a href="{{ route('admin.mail-templates.create') }}?trigger_type={{ $key }}" 
                                           class="btn btn-xs btn-success" 
                                           title="{{ trans('cruds.superAdmin.triggers.new_template') }}">
                                            <i class="fas fa-plus"></i> {{ trans('cruds.superAdmin.triggers.new_template') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-4">
                    <h6><i class="fas fa-info-circle"></i> {{ trans('cruds.superAdmin.triggers.info_title') }}</h6>
                    <ul class="mb-0">
                        <li><strong>{{ trans('cruds.superAdmin.triggers.automatic') }}:</strong> {{ trans('cruds.superAdmin.triggers.info_automatic') }}</li>
                        <li><strong>{{ trans('cruds.superAdmin.triggers.manual') }}:</strong> {{ trans('cruds.superAdmin.triggers.info_manual') }}</li>
                        <li><strong>{{ trans('cruds.superAdmin.triggers.active_templates') }}:</strong> {{ trans('cruds.superAdmin.triggers.info_active') }}</li>
                    </ul>
                </div>

                <div class="mt-4">
                    <h6>{{ trans('cruds.superAdmin.triggers.statistics') }}</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h3>{{ $templates->where('is_active', true)->count() }}</h3>
                                    <small>{{ trans('cruds.superAdmin.triggers.active_count') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h3>{{ $templates->where('is_automatic', true)->count() }}</h3>
                                    <small>{{ trans('cruds.superAdmin.triggers.automatic_count') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h3>{{ $templates->where('is_automatic', false)->count() }}</h3>
                                    <small>{{ trans('cruds.superAdmin.triggers.manual_count') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-secondary text-white">
                                <div class="card-body">
                                    <h3>{{ $templates->count() }}</h3>
                                    <small>{{ trans('cruds.superAdmin.triggers.total_count') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
