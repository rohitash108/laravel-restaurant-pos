@if (!Route::is(['pos']))
@auth
@if(auth()->user()->isSuperAdmin())
    @include('layout.partials.sidebar-super-admin')
@else
<!-- Two Col Sidebar (restaurant staff) -->
<div class="two-col-sidebar" id="two-col-sidebar">
    <div class="sidebar sidebar-twocol">
        <div class="twocol-mini">
            <a href="{{ route('dashboard') }}" class="logo-small" title="IT Software">
                {{-- Always platform logo; restaurant logo is shown in the expanded sidebar header dropdown --}}
                <span class="app-brand-logo app-brand-logo--sm"><img src="{{ asset('build/img/global-tea-cafe-logo.png') }}" alt="Global Tea Cafe" style="max-height:32px;width:auto;object-fit:contain;"></span>
            </a>
            <div class="sidebar-left">
                <div class="nav flex-column align-items-center sidebar-nav" id="sidebar-tabs" role="tablist" aria-orientation="vertical" data-simplebar>
                    <a href="#" class="nav-link {{Request::is(['dashboard', '/', 'pos', 'orders', 'kanban-view', 'kitchen', 'reservations']) ? 'active' : '' }}" title="Dashboard" data-bs-toggle="tab" data-bs-target="#dashboard">
                        <i class="icon-layout-dashboard"></i>
                    </a>
                    <a href="#" class="nav-link {{ Request::is(['categories', 'items', 'addons', 'coupons', 'inventory', 'inventory/*']) ? 'active' : '' }}" title="Management" data-bs-toggle="tab" data-bs-target="#menu-management">
                        <i class="icon-layers"></i>
                    </a>
                    <a href="#" class="nav-link {{Request::is(['table', 'customer', 'invoices', 'invoice-details', 'payments']) ? 'active' : '' }}" title="Operations" data-bs-toggle="tab" data-bs-target="#operations">
                        <i class="icon-merge"></i>
                    </a>
                    <a href="#" class="nav-link {{Request::is(['users', 'role-permission', 'earning-report', 'order-report', 'sales-report', 'customer-report', 'audit-report']) ? 'active' : '' }}" title="Administration" data-bs-toggle="tab" data-bs-target="#administration">
                        <i class="icon-user-cog"></i>
                    </a>
                    @if(false)
                    <a href="#" class="nav-link {{Request::is(['login', 'register', 'forgot-password', 'email-verification', 'otp', 'reset-password']) ? 'active' : '' }}" title="Pages" data-bs-toggle="tab" data-bs-target="#pages">
                        <i class="icon-library-big"></i>
                    </a>
                    @endif
                    <a href="#" class="nav-link {{Request::is(['store-settings', 'tax-settings', 'print-settings', 'payment-settings', 'delivery-settings', 'notifications-settings', 'integrations-settings']) ? 'active' : '' }}" title="Settings" data-bs-toggle="tab" data-bs-target="#settings">
                        <i class="icon-cog"></i>
                    </a>
                </div>
                <div class="sidebar-profile">
                    <div class="dropdown dropend">
                        <a href="javascript:void(0);" class="avatar avatar-sm" data-bs-toggle="dropdown">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="user" class="img-fluid rounded-circle">
                            @else
                                <div class="avatar avatar-sm avatar-rounded bg-primary text-white d-flex align-items-center justify-content-center">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                            @endif
                        </a>
                        <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-md">
                            <div class="dropdown-header border-bottom p-3">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg avatar-rounded border border-success">
                                            @if(auth()->user()->avatar)
                                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="rounded-circle" alt="user">
                                            @else
                                                <div class="avatar avatar-lg avatar-rounded bg-primary text-white d-flex align-items-center justify-content-center fs-20">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                                            @endif
                                        </div>
                                        <div class="ms-2">
                                            <h5 class="mb-1 fs-14 fw-semibold">{{ auth()->user()->name }}</h5>
                                            <span class="d-block fs-13">{{ auth()->user()->role ?? 'Staff' }}</span>
                                        </div>
                                    </div>
                                    <span class="badge badge-soft-success">{{ ucfirst(auth()->user()->role ?? 'Staff') }}</span>
                                </div>
                            </div>
                            <div class="p-3">

                                <!-- Item-->
                                <a href="{{ url('store-settings') }}" class="dropdown-item">
                                    <i class="icon-warehouse me-2 fs-16 align-middle"></i>
                                    <span class="align-middle">Store Settings</span>
                                </a>

                                @if(false) {{-- Roles, Audit Logs, Manage Staffs: hidden for now --}}
                                <a href="{{ url('role-permission') }}" class="dropdown-item">
                                    <i class="icon-shield-ellipsis me-2 fs-16 align-middle"></i>
                                    <span class="align-middle">Roles & Permissions</span>
                                </a>
                                <a href="{{ url('audit-report') }}" class="dropdown-item">
                                    <i class="icon-clock-arrow-down me-2 fs-16 align-middle"></i>
                                    <span class="align-middle">Audit Logs</span>
                                </a>
                                <a href="{{ url('users') }}" class="dropdown-item">
                                    <i class="icon-user-pen me-2 fs-16 align-middle"></i>
                                    <span class="align-middle">Manage Staffs</span>
                                </a>
                                @endif

                            </div>
                            <div class="p-3 border-top">
                                <form action="{{ route('logout') }}" method="POST" class="mb-0">
                                    @csrf
                                    <button type="submit" class="btn btn-white btn-sm w-100"><i class="icon-log-out me-1"></i>Logout</button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sidebar-right">
            <div class="sidebar-logo mb-3">
                <div class="dropdown">
                    @php
                        $currentRestaurant = null;
                        $userRestaurants = collect();
                        if (auth()->user()) {
                            $currentRestaurant = auth()->user()->restaurant;
                            // For users with a single restaurant, show that restaurant
                            if ($currentRestaurant) {
                                $userRestaurants = collect([$currentRestaurant]);
                            }
                        }
                    @endphp
                    <a href="javascript:void(0);" class="d-inline-flex align-items-center fw-medium"  data-bs-toggle="dropdown">
                        <div class="avatar avatar-xs avatar-rounded me-1">
                            @if($currentRestaurant && $currentRestaurant->logo)
                                <img src="{{ asset('storage/' . $currentRestaurant->logo) }}" alt="{{ $currentRestaurant->name }}" class="img-fluid" style="object-fit:contain;">
                            @else
                                <img src="{{ asset('build/img/global-tea-cafe-logo.png') }}" alt="store" class="img-fluid" style="object-fit:contain;">
                            @endif
                        </div>
                        {{ $currentRestaurant->name ?? 'Select Restaurant' }} <i class="icon-chevrons-up-down ms-2"></i>
                    </a>
                    <ul class="dropdown-menu p-3 mt-3">
                        @forelse($userRestaurants as $rest)
                        <li>
                            <a class="dropdown-item d-flex align-items-center {{ (int)session('current_restaurant_id') === $rest->id ? 'active' : '' }}" href="{{ url('/?switch_restaurant=' . $rest->id) }}">
                                <div class="avatar avatar-xs avatar-rounded me-2">
                                    @if($rest->logo)
                                        <img src="{{ asset('storage/' . $rest->logo) }}" alt="{{ $rest->name }}" class="img-fluid" style="object-fit:contain;">
                                    @else
                                        <img src="{{ asset('build/img/global-tea-cafe-logo.png') }}" alt="store" class="img-fluid" style="object-fit:contain;">
                                    @endif
                                </div>
                                {{ $rest->name }}
                            </a>
                        </li>
                        @empty
                        <li><span class="dropdown-item text-muted">No restaurants</span></li>
                        @endforelse
                    </ul>
                </div>

                <!-- Sidebar Toggle Button -->
                <button class="sidenav-toggle-btn btn border-0 p-0" id="toggle_btn">
                    <i class="icon-panel-right-open fs-16"></i>
                </button>

                <!-- Sidebar Close Button -->
                <button class="sidebar-close">
                    <i class="icon-x align-middle"></i>
                </button>
            </div>
            <div class="sidebar-scroll">
                <div class="tab-content" id="tab-content">
                    <div class="tab-pane fade {{Request::is(['dashboard', '/', 'pos', 'orders', 'kanban-view', 'kitchen', 'reservations']) ? 'show active' : '' }}" id="dashboard">
                        <ul>
                            <li class="menu-title"><span>MAIN</span></li>
                            <li><a href="{{ route('dashboard') }}" class="{{ Request::is(['dashboard', '/']) ? 'active' : '' }}"><i class="icon-layout-dashboard"></i><span>Dashboard</span></a></li>
                            <li><a href="{{ url('pos') }}" class="{{Request::is(['pos']) ? 'active' : '' }}"><i class="icon-combine"></i><span>POS</span></a></li>
                            <li><a href="{{ url('orders') }}" class="{{Request::is(['orders', 'kanban-view']) ? 'active' : '' }}"><i class="icon-list-todo"></i><span>Orders</span></a></li>
                            <li><a href="{{ url('kitchen') }}" class="{{Request::is(['kitchen']) ? 'active' : '' }}"><i class="icon-drumstick"></i><span>Kitchen (KDS)</span></a></li>
                            <li><a href="{{ url('reservations') }}" class="{{Request::is(['reservations']) ? 'active' : '' }}"><i class="icon-file-clock"></i><span>Reservation</span></a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade {{ Request::is(['categories', 'items', 'coupons', 'inventory', 'inventory/*']) ? 'show active' : '' }}" id="menu-management">
                        <ul>
                            <li class="menu-title"><span>MENU MANAGEMENT</span></li>
                            <li><a href="{{ url('categories') }}" class="{{Request::is(['categories']) ? 'active' : '' }}"><i class="icon-layers"></i><span>Categories</span></a></li>
                            <li><a href="{{ url('items') }}" class="{{Request::is(['items']) ? 'active' : '' }}"><i class="icon-layout-list"></i><span>Items</span></a></li>
                            <li><a href="{{ route('inventory.index') }}" class="{{ Request::is(['inventory', 'inventory/*']) ? 'active' : '' }}"><i class="icon-warehouse"></i><span>Inventory</span></a></li>
                            <li><a href="{{ url('coupons') }}" class="{{Request::is(['coupons']) ? 'active' : '' }}"><i class="icon-badge-percent"></i><span>Coupons</span></a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade {{Request::is(['table', 'customer', 'invoices', 'invoice-details', 'payments']) ? 'show active' : '' }}" id="operations">
                        <ul>
                            <li class="menu-title"><span>OPERATIONS</span></li>
                            <li><a href="{{ url('table') }}" class="{{Request::is(['table']) ? 'active' : '' }}"><i class="icon-concierge-bell"></i><span>Tables</span></a></li>
                            <li><a href="{{ url('customer') }}" class="{{Request::is(['customer']) ? 'active' : '' }}"><i class="icon-user-round"></i><span>Customers</span></a></li>
                            <li><a href="{{ url('invoices') }}" class="{{Request::is(['invoices', 'invoice-details']) ? 'active' : '' }}"><i class="icon-file-spreadsheet"></i><span>Invoices</span></a></li>
                            <li><a href="{{ url('payments') }}" class="{{Request::is(['payments']) ? 'active' : '' }}"><i class="icon-badge-dollar-sign"></i><span>Payments</span></a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade {{Request::is(['users', 'role-permission', 'earning-report', 'order-report', 'sales-report', 'customer-report', 'audit-report', 'admin/restaurants']) ? 'show active' : '' }}" id="administration">
                        <ul>
                            <li class="menu-title"><span>ADMINISTRATION</span></li>
                            @auth
                            @if(auth()->user()->isSuperAdmin())
                            <li><a href="{{ route('admin.restaurants.index') }}" class="{{Request::is('admin/restaurants*') ? 'active' : '' }}"><i class="icon-warehouse"></i><span>Restaurants</span></a></li>
                            @endif
                            @endauth
                            @auth
                            @if(!auth()->user()->isSuperAdmin())
                            <li><a href="{{ url('users') }}" class="{{Request::is(['users']) ? 'active' : '' }}"><i class="icon-users"></i><span>Users</span></a></li>
                            <li><a href="{{ url('role-permission') }}" class="{{Request::is(['role-permission']) ? 'active' : '' }}"><i class="icon-shield"></i><span>Permissions</span></a></li>
                            @endif
                            @endauth
                            <li><a href="{{ url('earning-report') }}" class="{{Request::is(['earning-report', 'order-report', 'sales-report', 'customer-report', 'audit-report']) ? 'active' : '' }}"><i class="icon-file-spreadsheet"></i><span>Reports</span></a></li>
                        </ul>
                    </div>
                    @if(false)
                    <div class="tab-pane fade {{Request::is(['login', 'register', 'forgot-password', 'email-verification', 'otp', 'reset-password']) ? 'show active' : '' }}" id="pages">
                        <ul>
                            <li class="menu-title"><span>Pages</span></li>
                            <li><a href="{{ url('login') }}" class="{{Request::is(['login']) ? 'active' : '' }}"><i class="icon-lock-keyhole"></i><span>Sign In</span></a></li>
                            <li><a href="{{ url('register') }}" class="{{Request::is(['register']) ? 'active' : '' }}"><i class="icon-user-round-plus"></i><span>Sign Up</span></a></li>
                            <li><a href="{{ url('forgot-password') }}" class="{{Request::is(['forgot-password']) ? 'active' : '' }}"><i class="icon-lock-keyhole-open"></i><span>Forgot Password</span></a></li>
                            <li><a href="{{ url('email-verification') }}" class="{{Request::is(['email-verification']) ? 'active' : '' }}"><i class="icon-mail"></i><span>Email Verification</span></a></li>
                            <li><a href="{{ url('otp') }}" class="{{Request::is(['otp']) ? 'active' : '' }}"><i class="icon-blocks"></i><span>OTP</span></a></li>
                            <li><a href="{{ url('reset-password') }}" class="{{Request::is(['reset-password']) ? 'active' : '' }}"><i class="icon-lock-keyhole"></i><span>Reset Password</span></a></li>
                        </ul>
                    </div>
                    @endif
                    <div class="tab-pane fade {{Request::is(['store-settings', 'tax-settings', 'print-settings', 'payment-settings', 'delivery-settings', 'notifications-settings', 'integrations-settings']) ? 'show active' : '' }}" id="settings">
                        <ul>
                            <li class="menu-title"><span>SETTINGS</span></li>
                            <li><a href="{{ url('store-settings') }}" class="{{Request::is(['store-settings']) ? 'active' : '' }}"><i class="icon-warehouse"></i><span>Store Settings</span></a></li>
                            <li><a href="{{ url('tax-settings') }}" class="{{Request::is(['tax-settings']) ? 'active' : '' }}"><i class="icon-diamond-percent"></i><span>Tax</span></a></li>
                            <li><a href="{{ url('print-settings') }}" class="{{Request::is(['print-settings']) ? 'active' : '' }}"><i class="icon-printer"></i><span>Print</span></a></li>
                            <li><a href="{{ url('payment-settings') }}" class="{{Request::is(['payment-settings']) ? 'active' : '' }}"><i class="icon-circle-dollar-sign"></i><span>Payment Types</span></a></li>
                            <li><a href="{{ url('delivery-settings') }}" class="{{Request::is(['delivery-settings']) ? 'active' : '' }}"><i class="icon-bike"></i><span>Delivery</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Two Col Sidebar -->
@endif
@endauth
@endif
