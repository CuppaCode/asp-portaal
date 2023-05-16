<aside class="main-sidebar sidebar-dark-primary elevation-4" style="min-height: 917px;">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">{{ trans('panel.site_title') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs("admin.home") ? "active" : "" }}" href="{{ route("admin.home") }}">
                        <i class="fas fa-fw fa-tachometer-alt nav-icon">
                        </i>
                        <p>
                            {{ trans('global.dashboard') }}
                        </p>
                    </a>
                </li>
                @can('business_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/companies*") ? "menu-open" : "" }} {{ request()->is("admin/injury-offices*") ? "menu-open" : "" }} {{ request()->is("admin/expertise-offices*") ? "menu-open" : "" }} {{ request()->is("admin/recovery-offices*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/companies*") ? "active" : "" }} {{ request()->is("admin/injury-offices*") ? "active" : "" }} {{ request()->is("admin/expertise-offices*") ? "active" : "" }} {{ request()->is("admin/recovery-offices*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-briefcase">

                            </i>
                            <p>
                                {{ trans('cruds.business.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('company_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.companies.index") }}" class="nav-link {{ request()->is("admin/companies") || request()->is("admin/companies/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-building">

                                        </i>
                                        <p>
                                            {{ trans('cruds.company.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('injury_office_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.injury-offices.index") }}" class="nav-link {{ request()->is("admin/injury-offices") || request()->is("admin/injury-offices/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fab fa-accessible-icon">

                                        </i>
                                        <p>
                                            {{ trans('cruds.injuryOffice.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('expertise_office_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.expertise-offices.index") }}" class="nav-link {{ request()->is("admin/expertise-offices") || request()->is("admin/expertise-offices/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-graduation-cap">

                                        </i>
                                        <p>
                                            {{ trans('cruds.expertiseOffice.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('recovery_office_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.recovery-offices.index") }}" class="nav-link {{ request()->is("admin/recovery-offices") || request()->is("admin/recovery-offices/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-wrench">

                                        </i>
                                        <p>
                                            {{ trans('cruds.recoveryOffice.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('contact_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.contacts.index") }}" class="nav-link {{ request()->is("admin/contacts") || request()->is("admin/contacts/*") ? "active" : "" }}">
                            <i class="fa-fw nav-icon fas fa-user-tie">

                            </i>
                            <p>
                                {{ trans('cruds.contact.title') }}
                            </p>
                        </a>
                    </li>
                @endcan
                @can('claim_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.claims.index") }}" class="nav-link {{ request()->is("admin/claims") || request()->is("admin/claims/*") ? "active" : "" }}">
                            <i class="fa-fw nav-icon fas fa-address-book">

                            </i>
                            <p>
                                {{ trans('cruds.claim.title') }}
                            </p>
                        </a>
                    </li>
                @endcan
                @can('task_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.tasks.index") }}" class="nav-link {{ request()->is("admin/tasks") || request()->is("admin/tasks/*") ? "active" : "" }}">
                            <i class="fa-fw nav-icon fas fa-tasks">

                            </i>
                            <p>
                                {{ trans('cruds.task.title') }}
                            </p>
                        </a>
                    </li>
                @endcan
                @can('vehicle_information_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/vehicles*") ? "menu-open" : "" }} {{ request()->is("admin/vehicle-opposites*") ? "menu-open" : "" }} {{ request()->is("admin/drivers*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/vehicles*") ? "active" : "" }} {{ request()->is("admin/vehicle-opposites*") ? "active" : "" }} {{ request()->is("admin/drivers*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-car">

                            </i>
                            <p>
                                {{ trans('cruds.vehicleInformation.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('vehicle_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.vehicles.index") }}" class="nav-link {{ request()->is("admin/vehicles") || request()->is("admin/vehicles/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-car">

                                        </i>
                                        <p>
                                            {{ trans('cruds.vehicle.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('vehicle_opposite_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.vehicle-opposites.index") }}" class="nav-link {{ request()->is("admin/vehicle-opposites") || request()->is("admin/vehicle-opposites/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-taxi">

                                        </i>
                                        <p>
                                            {{ trans('cruds.vehicleOpposite.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('driver_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.drivers.index") }}" class="nav-link {{ request()->is("admin/drivers") || request()->is("admin/drivers/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon far fa-address-card">

                                        </i>
                                        <p>
                                            {{ trans('cruds.driver.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('user_management_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/permissions*") ? "menu-open" : "" }} {{ request()->is("admin/roles*") ? "menu-open" : "" }} {{ request()->is("admin/users*") ? "menu-open" : "" }} {{ request()->is("admin/teams*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/permissions*") ? "active" : "" }} {{ request()->is("admin/roles*") ? "active" : "" }} {{ request()->is("admin/users*") ? "active" : "" }} {{ request()->is("admin/teams*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-users">

                            </i>
                            <p>
                                {{ trans('cruds.userManagement.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('permission_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.permissions.index") }}" class="nav-link {{ request()->is("admin/permissions") || request()->is("admin/permissions/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-unlock-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.permission.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('role_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.roles.index") }}" class="nav-link {{ request()->is("admin/roles") || request()->is("admin/roles/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-briefcase">

                                        </i>
                                        <p>
                                            {{ trans('cruds.role.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('user_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.users.index") }}" class="nav-link {{ request()->is("admin/users") || request()->is("admin/users/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-user">

                                        </i>
                                        <p>
                                            {{ trans('cruds.user.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('team_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.teams.index") }}" class="nav-link {{ request()->is("admin/teams") || request()->is("admin/teams/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-users">

                                        </i>
                                        <p>
                                            {{ trans('cruds.team.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @if(\Illuminate\Support\Facades\Schema::hasColumn('teams', 'owner_id') && \App\Models\Team::where('owner_id', auth()->user()->id)->exists())
                    <li class="nav-item">
                        <a class="{{ request()->is("admin/team-members") || request()->is("admin/team-members/*") ? "active" : "" }} nav-link" href="{{ route("admin.team-members.index") }}">
                            <i class="fa-fw fa fa-users nav-icon">
                            </i>
                            <p>
                                {{ trans("global.team-members") }}
                            </p>
                        </a>
                    </li>
                @endif
                @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
                    @can('profile_password_edit')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'active' : '' }}" href="{{ route('profile.password.edit') }}">
                                <i class="fa-fw fas fa-key nav-icon">
                                </i>
                                <p>
                                    {{ trans('global.change_password') }}
                                </p>
                            </a>
                        </li>
                    @endcan
                @endif
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                        <p>
                            <i class="fas fa-fw fa-sign-out-alt nav-icon">

                            </i>
                            <p>{{ trans('global.logout') }}</p>
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>