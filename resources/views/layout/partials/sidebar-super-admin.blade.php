<!-- Super Admin: Restaurants only -->
<div class="two-col-sidebar" id="two-col-sidebar">
    <div class="sidebar sidebar-twocol" id="sidebar">
        <div class="twocol-mini">
            <a href="{{ route('admin.dashboard') }}" class="logo-small">
                <span class="app-brand-logo app-brand-logo--sm"><img src="{{ asset('build/img/global-tea-cafe-logo.png') }}" alt="Global Tea Cafe" style="max-height:32px;width:auto;object-fit:contain;"></span>
            </a>
            <div class="sidebar-left">
                <div class="nav flex-column align-items-center sidebar-nav" id="sidebar-tabs" role="tablist" data-simplebar>
                    <a href="#" class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}" title="Dashboard" data-bs-toggle="tab" data-bs-target="#admin-dashboard-tab">
                        <i class="icon-layout-dashboard"></i>
                    </a>
                    <a href="{{ route('admin.profile.edit') }}" class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" title="Account">
                        <i class="icon-user-cog"></i>
                    </a>
                    <a href="#" class="nav-link {{ Request::is('admin/restaurants*') ? 'active' : '' }}" title="Restaurants" data-bs-toggle="tab" data-bs-target="#admin-restaurants-tab">
                        <i class="icon-warehouse"></i>
                    </a>
                    @if(auth()->user()->isOwner())
                    <a href="#" class="nav-link {{ Request::is('admin/subscription*') ? 'active' : '' }}" title="Subscriptions" data-bs-toggle="tab" data-bs-target="#admin-subscriptions-tab">
                        <i class="icon-credit-card"></i>
                    </a>
                    @endif
                </div>
                <div class="sidebar-profile">
                    @if(false) {{-- Notifications: hidden for now --}}
                    <div class="dropdown dropend">
                        <a href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <i class="icon-bell"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-xl notification-dropdown">
                            <div class="d-flex align-items-center justify-content-between notification-header">
                                <h5 class="mb-0">Notifications</h5>
                            </div>
                            <div class="notification-body p-3">
                                <p class="text-muted small mb-0">No notifications</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="dropdown dropend">
                        <a href="javascript:void(0);" class="avatar avatar-sm" data-bs-toggle="dropdown">
                            @if(auth()->user() && auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="user" class="img-fluid rounded-circle">
                            @else
                                <div class="avatar avatar-sm avatar-rounded bg-primary text-white d-flex align-items-center justify-content-center">{{ auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'A' }}</div>
                            @endif
                        </a>
                        <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-md">
                            <div class="px-3 py-2 border-bottom">
                                <p class="mb-0 fw-medium">{{ auth()->user()->name ?? 'Super Admin' }}</p>
                                <p class="fs-12 text-muted mb-0">{{ auth()->user()->email }}</p>
                            </div>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('admin.profile.edit') }}">
                                <i class="icon-user-cog me-2"></i>Account settings
                            </a>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="icon-log-out me-2"></i>Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sidebar-right">
            <div class="sidebar-logo mb-3">
                <a href="{{ route('admin.dashboard') }}" class="d-inline-flex align-items-center fw-medium">
                    <i class="icon-layout-dashboard me-2"></i>Super Admin
                </a>
                <button class="sidenav-toggle-btn btn border-0 p-0 active" id="toggle_btn" type="button" aria-label="Toggle sidebar">
                    <i class="icon-panel-right-open fs-16"></i>
                </button>
                <button class="sidebar-close"><i class="icon-x align-middle"></i></button>
            </div>
            <div class="sidebar-scroll" data-simplebar>
                <div class="tab-content" id="tab-content">
                    <div class="tab-pane fade {{ Request::is('admin/dashboard') ? 'show active' : '' }}" id="admin-dashboard-tab">
                        <ul>
                            <li class="menu-title"><span>OVERVIEW</span></li>
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
                                    <i class="icon-layout-dashboard"></i><span>Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.profile.edit') }}" class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                                    <i class="icon-user-cog"></i><span>Account</span>
                                </a>
                            </li>
                            <li class="menu-title"><span>RESTAURANTS</span></li>
                            <li>
                                <a href="{{ route('admin.restaurants.index') }}" class="{{ Request::is('admin/restaurants*') ? 'active' : '' }}">
                                    <i class="icon-warehouse"></i><span>Restaurants</span>
                                </a>
                            </li>
                            @if(auth()->user()->isOwner())
                            <li class="menu-title"><span>SUBSCRIPTIONS</span></li>
                            <li>
                                <a href="{{ route('admin.subscription-plans') }}" class="{{ Request::is('admin/subscription-plans*') ? 'active' : '' }}">
                                    <i class="icon-credit-card"></i><span>Plans</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.subscriptions') }}" class="{{ Request::is('admin/subscriptions*') ? 'active' : '' }}">
                                    <i class="icon-list"></i><span>Subscriptions</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                    <div class="tab-pane fade {{ Request::is('admin/restaurants*') ? 'show active' : '' }}" id="admin-restaurants-tab">
                        <ul>
                            <li class="menu-title"><span>RESTAURANTS</span></li>
                            <li>
                                <a href="{{ route('admin.restaurants.index') }}" class="{{ Request::is('admin/restaurants*') ? 'active' : '' }}">
                                    <i class="icon-warehouse"></i><span>Restaurants</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    @if(auth()->user()->isOwner())
                    <div class="tab-pane fade {{ Request::is('admin/subscription*') ? 'show active' : '' }}" id="admin-subscriptions-tab">
                        <ul>
                            <li class="menu-title"><span>SUBSCRIPTIONS</span></li>
                            <li>
                                <a href="{{ route('admin.subscription-plans') }}" class="{{ Request::is('admin/subscription-plans*') ? 'active' : '' }}">
                                    <i class="icon-credit-card"></i><span>Plans</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.subscriptions') }}" class="{{ Request::is('admin/subscriptions*') ? 'active' : '' }}">
                                    <i class="icon-list"></i><span>Subscriptions</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

