<div id="sidebar" class="c-sidebar c-sidebar-fixed c-sidebar-lg-show">

    <div class="c-sidebar-brand d-md-down-none">
        <a class="c-sidebar-brand-full" href="{{ route("admin.home") }}">
            <img class="asp-logo" src="/images/logo-asp.png">
        </a>
    </div>

    <ul class="c-sidebar-nav">
        <li class="c-sidebar-nav-item">
            <a href="{{ route("admin.home") }}" class="c-sidebar-nav-link">
                <i class="c-sidebar-nav-icon fas fa-fw fa-tachometer-alt">

                </i>
                {{ trans('global.dashboard') }}
            </a>
        </li>
        @can('business_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("admin/companies*") ? "c-show" : "" }} {{ request()->is("admin/injury-offices*") ? "c-show" : "" }} {{ request()->is("admin/expertise-offices*") ? "c-show" : "" }} {{ request()->is("admin/recovery-offices*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.business.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('company_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.companies.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/companies") || request()->is("admin/companies/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-building c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.company.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('injury_office_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.injury-offices.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/injury-offices") || request()->is("admin/injury-offices/*") ? "c-active" : "" }}">
                                <i class="fa-fw fab fa-accessible-icon c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.injuryOffice.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('expertise_office_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.expertise-offices.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/expertise-offices") || request()->is("admin/expertise-offices/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-graduation-cap c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.expertiseOffice.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('recovery_office_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.recovery-offices.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/recovery-offices") || request()->is("admin/recovery-offices/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-wrench c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.recoveryOffice.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can('contact_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.contacts.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/contacts") || request()->is("admin/contacts/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-user-tie c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.contact.title') }}
                </a>
            </li>
        @endcan
        @can('claim_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.claims.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/claims") || request()->is("admin/claims/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-address-book c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.claim.title') }}
                </a>
            </li>
        @endcan
        @can('task_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.tasks.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/tasks") || request()->is("admin/tasks/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-tasks c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.task.title') }}
                </a>
            </li>
        @endcan
        @can('vehicle_information_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("admin/vehicles*") ? "c-show" : "" }} {{ request()->is("admin/vehicle-opposites*") ? "c-show" : "" }} {{ request()->is("admin/drivers*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-car c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.vehicleInformation.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('vehicle_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.vehicles.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/vehicles") || request()->is("admin/vehicles/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-car c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.vehicle.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('vehicle_opposite_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.vehicle-opposites.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/vehicle-opposites") || request()->is("admin/vehicle-opposites/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-taxi c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.vehicleOpposite.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('driver_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.drivers.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/drivers") || request()->is("admin/drivers/*") ? "c-active" : "" }}">
                                <i class="fa-fw far fa-address-card c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.driver.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can('user_management_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("admin/permissions*") ? "c-show" : "" }} {{ request()->is("admin/roles*") ? "c-show" : "" }} {{ request()->is("admin/users*") ? "c-show" : "" }} {{ request()->is("admin/teams*") ? "c-show" : "" }} {{ request()->is("admin/audit-logs*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.userManagement.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('permission_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.permissions.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/permissions") || request()->is("admin/permissions/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-unlock-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.permission.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('role_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.roles.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/roles") || request()->is("admin/roles/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.role.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('user_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.users.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/users") || request()->is("admin/users/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.user.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('team_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.teams.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/teams") || request()->is("admin/teams/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.team.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('audit_log_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.audit-logs.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/audit-logs") || request()->is("admin/audit-logs/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-file-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.auditLog.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can('note_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.notes.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/notes") || request()->is("admin/notes/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-paperclip c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.note.title') }}
                </a>
            </li>
        @endcan
        @if(\Illuminate\Support\Facades\Schema::hasColumn('teams', 'owner_id') && \App\Models\Team::where('owner_id', auth()->user()->id)->exists())
            <li class="c-sidebar-nav-item">
                <a class="{{ request()->is("admin/team-members") || request()->is("admin/team-members/*") ? "c-active" : "" }} c-sidebar-nav-link" href="{{ route("admin.team-members.index") }}">
                    <i class="c-sidebar-nav-icon fa-fw fa fa-users">
                    </i>
                    <span>{{ trans("global.team-members") }}</span>
                </a>
            </li>
        @endif
    </ul>

</div>