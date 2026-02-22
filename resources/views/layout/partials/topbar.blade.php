@if (Route::is(['pos']))
    <!-- Navbar Header -->
    @php $posStoreLogo = auth()->user()->restaurant?->logo ?? null; @endphp
    <div class="header">
        <div class="container-fluid">
            <div class="header-menu">
                <div class="header-logo">
                    @if($posStoreLogo)
                        <a href="{{ route('dashboard') }}" class="logo-dark"><img src="{{ asset('storage/' . $posStoreLogo) }}" alt="logo" class="img-fluid" style="max-height: 36px; width: auto; object-fit: contain;"></a>
                        <a href="{{ route('dashboard') }}" class="logo-light"><img src="{{ asset('storage/' . $posStoreLogo) }}" alt="logo" class="img-fluid" style="max-height: 36px; width: auto; object-fit: contain;"></a>
                    @else
                        <a href="{{ route('dashboard') }}" class="logo-dark"> <img src="{{ URL::asset('build/img/logo.svg') }}" alt="logo" class="img-fluid"></a>
                        <a href="{{ route('dashboard') }}" class="logo-light"> <img src="{{ URL::asset('build/img/logo-white.svg') }}" alt="logo" class="img-fluid"></a>
                    @endif
                </div>
                <div class="navbar-header">
                    <a href="{{ route('dashboard') }}" class="toggle-btn me-2"><i class="icon-grip"></i></a>

                    <!-- Search -->
                    <div class="header-links d-lg-flex d-none">
                        <a href="{{ url('pos') }}" class="d-inline-flex align-items-center {{Request::is(['pos']) ? 'active' : '' }}"><i class="icon-hand-platter me-1"></i>POS</a>
                        <a href="{{ url('orders') }}" class="d-inline-flex align-items-center {{Request::is(['orders', 'kanban-view']) ? 'active' : '' }}"><i class="icon-list-todo me-1"></i>Orders</a>
                        <a href="{{ url('kitchen') }}" class="d-inline-flex align-items-center {{Request::is(['kitchen']) ? 'active' : '' }}"><i class="icon-drumstick me-1"></i>Kitchen</a>
                        <a href="{{ url('reservations') }}" class="d-inline-flex align-items-center {{Request::is(['reservations']) ? 'active' : '' }}"><i class="icon-file-clock me-1"></i>Reservation</a>
                        <a href="{{ url('table') }}" class="d-inline-flex align-items-center {{Request::is(['table']) ? 'active' : '' }}"><i class="icon-concierge-bell me-1"></i>Table</a>
                    </div>

                    <ul class="header-notification">
                        <li class="d-none d-sm-flex">
                            <a href="{{ url('customer-report') }}" class="btn btn-icon"><i class="icon-chart-column-stacked"></i></a>
                        </li>

                        <!-- Light/Dark Mode Button -->
                        <li class="header-item d-flex">
                            <button class="btn btn-icon light-dark-mode" type="button" aria-label="light/dark mode">
                                <i class="icon-moon fs-16"></i>
                            </button>
                        </li>

                        @if(false) {{-- Notifications: hidden for now, enable when needed --}}
                        <li>
                            <div>
                                <button class="btn-icon btn notification" type="button" data-bs-toggle="dropdown"  data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false" aria-label="Notifications"><i class="icon-bell"></i></button>
                                <div class="dropdown-menu dropdown-menu-xl notification-dropdown">
                                <div class="d-flex align-items-center justify-content-between notification-header">
                                        <h5 class="mb-0">Notifications</h5>
                                        <a href="#" class="link-primary">Mark all as unread</a>
                                    </div>
                                    <div class="notification-body" data-simplebar>

                                        <ul class="nav nav-tabs p-1 bg-light rounded border-0 nav-solid-white mb-3">
                                            <li class="nav-item">
                                                <a href="#all-notification" data-bs-toggle="tab" aria-expanded="true" class="nav-link active d-flex align-items-center py-1 px-2">
                                                    All
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#unread-notification" data-bs-toggle="tab" aria-expanded="false" class="nav-link d-flex align-items-center py-1 px-2">
                                                    Unread <span class="badge-icon ms-1">4</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#inbox-notification" data-bs-toggle="tab" aria-expanded="false" class="nav-link d-flex align-items-center py-1 px-2">
                                                    Inbox
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#kitchen-notification" data-bs-toggle="tab" aria-expanded="false" class="nav-link d-flex align-items-center py-1 px-2">
                                                    Kitchen <span class="badge-icon ms-1">5</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#order-notification" data-bs-toggle="tab" aria-expanded="false" class="nav-link d-flex align-items-center py-1 px-2">
                                                    Orders
                                                </a>
                                            </li>
                                        </ul>

                                        <div class="tab-content">
                                            <div class="tab-pane fade show active" id="all-notification">

                                                <div class="notification-list">
                                                    <h6 class="fs-14 fw-semibold mb-3">Today</h6>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-secondary border border-secondary">
                                                                <i class="icon-cooking-pot"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1">New order from <span class="text-dark fw-medium">Table #12</span>  (3 items) pending.</p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>20 Min Ago</p>
                                                            </div>
                                                        </div>
                                                        <div class="notification-action">
                                                            <a href="javascript:void(0);" class="notification-read rounded-circle bg-success" data-bs-toggle="tooltip" title="" data-bs-original-title="Make as Read" aria-label="Make as Read"></a>
                                                        </div>
                                                    </div>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-orange border border-orange">
                                                                <i class="icon-shopping-cart"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1"><span class="text-dark fw-medium">Order #124</span> confirmed and sent to the kitchen.</p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>35 Min Ago</p>
                                                            </div>
                                                        </div>
                                                        <div class="notification-action">
                                                            <a href="javascript:void(0);" class="notification-read rounded-circle bg-success" data-bs-toggle="tooltip" title="" data-bs-original-title="Make as Read" aria-label="Make as Read"></a>
                                                        </div>
                                                    </div>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-success border border-success">
                                                                <i class="icon-badge-dollar-sign"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1"><span class="text-dark fw-medium">{{ $currency_symbol }}850</span> received via UPI for <span class="text-dark fw-medium">Order #124.</span></p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>40 Min Ago</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-success border border-success">
                                                                <i class="icon-square-pen"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1">New  order has been created <span class="text-dark fw-medium">Dine</span> in  for <span class="text-dark fw-medium">Table 1</span> total <span class="text-dark fw-medium">20 Items</span></p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>45 Min Ago</p>
                                                                <div class="d-flex align-items-center gap-2 mt-2">
                                                                    <button type="button" class="btn btn-sm btn-primary">Accept</button>
                                                                    <button type="button" class="btn btn-sm btn-white">Decline</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="notification-list">
                                                    <h6 class="fs-14 fw-semibold mb-3">Yesterday</h6>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-danger border border-danger">
                                                                <i class="icon-info"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1">Low stock: Cheese <span class="text-dark fw-medium">(5 units left).</span></p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>10 Hrs Ago</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-indigo border border-indigo">
                                                                <i class="icon-calendar-fold"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1">Table reservation for Andrew Merkel at <span class="text-dark fw-medium">7:30 PM.</span></p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>40 Hrs Ago</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="tab-pane fade" id="unread-notification">

                                                <div class="notification-list">
                                                    <h6 class="fs-14 fw-semibold mb-3">Today</h6>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-secondary border border-secondary">
                                                                <i class="icon-cooking-pot"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1">New order from <span class="text-dark fw-medium">Table #12</span>  (3 items) pending.</p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>20 Min Ago</p>
                                                            </div>
                                                        </div>
                                                        <div class="notification-action">
                                                            <a href="javascript:void(0);" class="notification-read rounded-circle bg-success" data-bs-toggle="tooltip" title="" data-bs-original-title="Make as Read" aria-label="Make as Read"></a>
                                                        </div>
                                                    </div>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-orange border border-orange">
                                                                <i class="icon-shopping-cart"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1"><span class="text-dark fw-medium">Order #124</span> confirmed and sent to the kitchen.</p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>35 Min Ago</p>
                                                            </div>
                                                        </div>
                                                        <div class="notification-action">
                                                            <a href="javascript:void(0);" class="notification-read rounded-circle bg-success" data-bs-toggle="tooltip" title="" data-bs-original-title="Make as Read" aria-label="Make as Read"></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="notification-list">
                                                    <h6 class="fs-14 fw-semibold mb-3">Yesterday</h6>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-danger border border-danger">
                                                                <i class="icon-info"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1">Low stock: Cheese <span class="text-dark fw-medium">(5 units left).</span></p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>10 Hrs Ago</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-indigo border border-indigo">
                                                                <i class="icon-calendar-fold"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1">Table reservation for Andrew Merkel at <span class="text-dark fw-medium">7:30 PM.</span></p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>40 Hrs Ago</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="tab-pane fade" id="inbox-notification">

                                                <div class="notification-list">
                                                    <h6 class="fs-14 fw-semibold mb-3">Today</h6>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-success border border-success">
                                                                <i class="icon-badge-dollar-sign"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1"><span class="text-dark fw-medium">{{ $currency_symbol }}850</span> received via UPI for <span class="text-dark fw-medium">Order #124.</span></p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>40 Min Ago</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-success border border-success">
                                                                <i class="icon-square-pen"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1">New  order has been created <span class="text-dark fw-medium">Dine</span> in  for <span class="text-dark fw-medium">Table 1</span> total <span class="text-dark fw-medium">20 Items</span></p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>45 Min Ago</p>
                                                                <div class="d-flex align-items-center gap-2 mt-2">
                                                                    <button type="button" class="btn btn-sm btn-primary">Accept</button>
                                                                    <button type="button" class="btn btn-sm btn-white">Decline</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="tab-pane fade" id="kitchen-notification">

                                                <div class="notification-list">
                                                    <h6 class="fs-14 fw-semibold mb-3">Today</h6>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-orange border border-orange">
                                                                <i class="icon-shopping-cart"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1"><span class="text-dark fw-medium">Order #124</span> confirmed and sent to the kitchen.</p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>35 Min Ago</p>
                                                            </div>
                                                        </div>
                                                        <div class="notification-action">
                                                            <a href="javascript:void(0);" class="notification-read rounded-circle bg-success" data-bs-toggle="tooltip" title="" data-bs-original-title="Make as Read" aria-label="Make as Read"></a>
                                                        </div>
                                                    </div>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-secondary border border-secondary">
                                                                <i class="icon-cooking-pot"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1">New order from <span class="text-dark fw-medium">Table #12</span>  (3 items) pending.</p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>20 Min Ago</p>
                                                            </div>
                                                        </div>
                                                        <div class="notification-action">
                                                            <a href="javascript:void(0);" class="notification-read rounded-circle bg-success" data-bs-toggle="tooltip" title="" data-bs-original-title="Make as Read" aria-label="Make as Read"></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="notification-list">
                                                    <h6 class="fs-14 fw-semibold mb-3">Yesterday</h6>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-indigo border border-indigo">
                                                                <i class="icon-calendar-fold"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1">Table reservation for Andrew Merkel at <span class="text-dark fw-medium">7:30 PM.</span></p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>40 Hrs Ago</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="tab-pane fade" id="order-notification">

                                                <div class="notification-list">
                                                    <h6 class="fs-14 fw-semibold mb-3">Today</h6>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-secondary border border-secondary">
                                                                <i class="icon-cooking-pot"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1">New order from <span class="text-dark fw-medium">Table #12</span>  (3 items) pending.</p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>20 Min Ago</p>
                                                            </div>
                                                        </div>
                                                        <div class="notification-action">
                                                            <a href="javascript:void(0);" class="notification-read rounded-circle bg-success" data-bs-toggle="tooltip" title="" data-bs-original-title="Make as Read" aria-label="Make as Read"></a>
                                                        </div>
                                                    </div>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-orange border border-orange">
                                                                <i class="icon-shopping-cart"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1"><span class="text-dark fw-medium">Order #124</span> confirmed and sent to the kitchen.</p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>35 Min Ago</p>
                                                            </div>
                                                        </div>
                                                        <div class="notification-action">
                                                            <a href="javascript:void(0);" class="notification-read rounded-circle bg-success" data-bs-toggle="tooltip" title="" data-bs-original-title="Make as Read" aria-label="Make as Read"></a>
                                                        </div>
                                                    </div>

                                                    <!-- Item-->
                                                    <div class="notification-item">
                                                        <div class="d-flex">
                                                            <div class="me-2 avatar avatar-rounded flex-shrink-0 badge-soft-success border border-success">
                                                                <i class="icon-square-pen"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <p class="mb-1">New  order has been created <span class="text-dark fw-medium">Dine</span> in  for <span class="text-dark fw-medium">Table 1</span> total <span class="text-dark fw-medium">20 Items</span></p>
                                                                <p class="fs-13 mb-0 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>45 Min Ago</p>
                                                                <div class="d-flex align-items-center gap-2 mt-2">
                                                                    <button type="button" class="btn btn-sm btn-primary">Accept</button>
                                                                    <button type="button" class="btn btn-sm btn-white">Decline</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </li>
                        @endif
                        <li>
                            <a href="{{ url('tax-settings') }}" class="btn btn-icon"><i class="icon-cog"></i></a>
                        </li>
                        <li>
                            <div>
                                <button class="btn-icon profile-icon" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Actions">
                                    @if(auth()->user() && auth()->user()->avatar)
                                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="img-fluid img-1">
                                    @else
                                        <div class="avatar avatar-sm avatar-rounded bg-primary text-white d-flex align-items-center justify-content-center">{{ auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 1)) : '?' }}</div>
                                    @endif
                                </button>
                                <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-md mt-2">
                                    <div class="dropdown-header border-bottom p-3">
                                        <div class="d-flex align-items-center justify-content-between gap-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-lg avatar-rounded border border-success">
                                                    @if(auth()->user() && auth()->user()->avatar)
                                                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="rounded-circle" alt="user">
                                                    @else
                                                        <div class="avatar avatar-lg avatar-rounded bg-primary text-white d-flex align-items-center justify-content-center fs-20">{{ auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 1)) : '?' }}</div>
                                                    @endif
                                                </div>
                                                <div class="ms-2">
                                                    <h5 class="mb-1 fs-14 fw-semibold">{{ auth()->user()->name ?? 'User' }}</h5>
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

                                        @if(false) {{-- Roles, Audit Logs & Manage Staffs: hidden for now --}}
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
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Navbar Header -->
@else
    <!-- Topbar Start -->
    <header class="navbar-header">
        <div class="topbar-menu">
            <div class="d-flex align-items-center gap-2">

                @if(auth()->user() && auth()->user()->isSuperAdmin())
                <!-- Super Admin: single logo to avoid overlap -->
                <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center text-decoration-none">
                    <img src="{{ asset('build/img/logo.svg') }}" alt="Logo" class="img-fluid" style="max-height: 32px; max-width: 140px;">
                </a>
                @else
                <!-- Logo (light/dark variants); use store logo from settings when set -->
                @php $storeLogo = auth()->user()->restaurant?->logo ?? null; @endphp
                <a href="{{ route('dashboard') }}" class="logo">
                    <span class="logo-light">
                        @if($storeLogo)
                            <span class="logo-lg"><img src="{{ asset('storage/' . $storeLogo) }}" alt="logo" class="img-fluid" style="max-height: 32px; width: auto; object-fit: contain;"></span>
                            <span class="logo-sm"><img src="{{ asset('storage/' . $storeLogo) }}" alt="small logo" class="img-fluid" style="max-height: 28px; width: auto; object-fit: contain;"></span>
                        @else
                            <span class="logo-lg"><img src="{{ URL::asset('build/img/logo.svg') }}" alt="logo"></span>
                            <span class="logo-sm"><img src="{{ URL::asset('build/img/logo-small.svg') }}" alt="small logo"></span>
                        @endif
                    </span>
                    <span class="logo-dark">
                        @if($storeLogo)
                            <span class="logo-lg"><img src="{{ asset('storage/' . $storeLogo) }}" alt="dark logo" class="img-fluid" style="max-height: 32px; width: auto; object-fit: contain;"></span>
                        @else
                            <span class="logo-lg"><img src="{{ URL::asset('build/img/logo-white.svg') }}" alt="dark logo"></span>
                        @endif
                    </span>
                </a>
                @endif

                <!-- Sidebar Mobile Button -->
                <a id="mobile_btn" class="mobile-btn" href="#sidebar">
                    <i class="icon-menu fs-24"></i>
                </a>

                <!-- Search (hidden for super admin – they only see Restaurants) -->
                @if(auth()->user() && !auth()->user()->isSuperAdmin())
                <div class="header-links d-lg-flex d-none">
                    <a href="{{ url('pos') }}" class="d-inline-flex align-items-center {{Request::is(['pos']) ? 'active' : '' }}"><i class="icon-hand-platter me-1"></i>POS</a>
                    <a href="{{ url('orders') }}" class="d-inline-flex align-items-center {{Request::is(['orders', 'kanban-view']) ? 'active' : '' }}"><i class="icon-list-todo me-1"></i>Orders</a>
                    <a href="{{ url('kitchen') }}" class="d-inline-flex align-items-center {{Request::is(['kitchen']) ? 'active' : '' }}"><i class="icon-drumstick me-1"></i>Kitchen</a>
                    <a href="{{ url('reservations') }}" class="d-inline-flex align-items-center {{Request::is(['reservations']) ? 'active' : '' }}"><i class="icon-file-clock me-1"></i>Reservation</a>
                    <a href="{{ url('table') }}" class="d-inline-flex align-items-center {{Request::is(['table']) ? 'active' : '' }}"><i class="icon-concierge-bell me-1"></i>Table</a>
                </div>
                @endif

            </div>

            <div class="d-flex align-items-center header-list">

                @if(!auth()->user() || !auth()->user()->isSuperAdmin())
                    <!-- Upgrade Button -->
                <div class="header-item d-none d-sm-flex">
                    <a href="#" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                        <i class="icon-crown me-1"></i>Upgrade
                    </a>
                </div>

                <!-- Search Button -->
                <div class="header-item d-flex">
                    <button class="topbar-link btn btn-icon" data-bs-toggle="modal" data-bs-target="#searchModal" type="button" aria-label="search">
                        <i class="icon-search fs-16"></i>
                    </button>
                </div>

                <!-- Report Button -->
                <div class="header-item d-none d-sm-flex">
                    <a href="{{ url('earning-report') }}" class="topbar-link btn btn-icon" aria-label="report">
                        <i class="icon-chart-column-stacked fs-16"></i><span class="position-absolute report-badge bg-success"></span>
                    </a>
                </div>
                @endif

                <!-- Light/Dark Mode Button -->
                <div class="header-item d-flex">
                    <button class="topbar-link btn btn-icon light-dark-mode" type="button" aria-label="light/dark mode">
                        <i class="icon-moon fs-16"></i>
                    </button>
                </div>

            </div>
        </div>
    </header>
    <!-- Topbar End -->

    <!-- Search Modal -->
    <div class="modal fade" id="searchModal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="mb-0">Search</h5>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <div class="modal-body">
                    <div class="input-group input-group-flat mb-4">
                        <input type="text" class="form-control form-control-lg fs-14" placeholder="Search your keyword">
                        <span class="input-group-text">
                            <i class="icon-search text-dark"></i>
                        </span>
                    </div>
                    <ul class="nav nav-tabs nav-bordered nav-bordered-primary mb-4">
                        <li class="nav-item">
                            <a href="#customer-tab" data-bs-toggle="tab" aria-expanded="false" class="nav-link active d-flex align-items-center">
                                <i class="icon-badge-dollar-sign me-2"></i>Customer
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#orders-tab" data-bs-toggle="tab" aria-expanded="true" class="nav-link d-flex align-items-center">
                                    <i class="icon-list-todo me-2"></i>Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#kitchen-tab" data-bs-toggle="tab" aria-expanded="false" class="nav-link d-flex align-items-center">
                                    <i class="icon-shopping-bag me-2"></i>Kitchen
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content pt-1">
                        <div class="tab-pane fade show active" id="customer-tab">
                            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-rounded me-2">
                                        <img src="{{URL::asset('build/img/profiles/avatar-32.jpg')}}" alt="customer">
                                    </div>
                                    <div>
                                        <h6 class="fs-14 fw-semibold mb-1">Adrian James</h6>
                                        <p class="fs-13 mb-0">Male</p>
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark">#CR6569</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-rounded me-2">
                                        <img src="{{URL::asset('build/img/profiles/avatar-33.jpg')}}" alt="customer">
                                    </div>
                                    <div>
                                        <h6 class="fs-14 fw-semibold mb-1">Sue Allen</h6>
                                        <p class="fs-13 mb-0">Female</p>
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark">#CR6569</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-rounded me-2">
                                        <img src="{{URL::asset('build/img/profiles/avatar-31.jpg')}}" alt="customer">
                                    </div>
                                    <div>
                                        <h6 class="fs-14 fw-semibold mb-1">Frank Barrett</h6>
                                        <p class="fs-13 mb-0">Male</p>
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark">#CR4824</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-rounded bg-light text-dark border me-2">
                                        <i class="icon-user fs-20"></i>
                                    </div>
                                    <div>
                                        <h6 class="fs-14 fw-semibold mb-1">Walkin Customer</h6>
                                        <p class="fs-13 mb-0">Male</p>
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark">#CR8238</span>
                            </div>
                            <a href="{{ url('customer') }}" class="btn btn-sm btn-white d-inline-flex align-items-center w-100 mt-1">View All<i class="icon-arrow-right ms-1"></i></a>
                        </div>
                        <div class="tab-pane fade" id="orders-tab">
                            <div class="d-flex align-items-sm-center justify-content-between flex-column gap-2 flex-sm-row pb-3 mb-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-rounded bg-light text-dark me-2">
                                        <i class="icon-shopping-bag fs-24"></i>
                                    </div>
                                    <div>
                                        <h6 class="fs-14 fw-semibold mb-1">#56998</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <p class="mb-0">Dine In</p>
                                            <span class="even-line"></span>
                                                <p class="mb-0">Table No : 3</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <span class="badge bg-light text-dark">Token No : 27</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-sm-center justify-content-between flex-column gap-2 flex-sm-row pb-3 mb-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-rounded bg-light text-dark me-2">
                                        <i class="icon-shopping-bag fs-24"></i>
                                    </div>
                                    <div>
                                        <h6 class="fs-14 fw-semibold mb-1">#57001</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <p class="mb-0">Take Away</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <span class="badge bg-light text-dark">Token No : 26</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-sm-center justify-content-between flex-column gap-2 flex-sm-row pb-3 mb-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-rounded bg-light text-dark me-2">
                                        <i class="icon-shopping-bag fs-24"></i>
                                    </div>
                                    <div>
                                        <h6 class="fs-14 fw-semibold mb-1">#56998</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <p class="mb-0">Dine In</p>
                                            <span class="even-line"></span>
                                                <p class="mb-0">Table No : 3</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <span class="badge bg-light text-dark">Token No : 27</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-sm-center justify-content-between flex-column gap-2 flex-sm-row pb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-rounded bg-light text-dark me-2">
                                        <i class="icon-shopping-bag fs-24"></i>
                                    </div>
                                    <div>
                                        <h6 class="fs-14 fw-semibold mb-1">#57002</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <p class="mb-0">Delivery</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <span class="badge bg-light text-dark">Token No : 25</span>
                                </div>
                            </div>
                            <a href="{{ url('orders') }}" class="btn btn-sm btn-white d-inline-flex align-items-center w-100 mt-1">View All<i class="icon-arrow-right ms-1"></i></a>
                            <div class="text-center d-none">
                                <img src="{{URL::asset('build/img/icons/search.svg')}}" alt="search icon" class="img-fluid mb-4">
                                <h4 class="mb-1">No Results Found</h4>
                                <p class="mb-0">Refine your query to discover relevant items</p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="kitchen-tab">
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-rounded bg-light text-dark me-2">
                                        <i class="icon-hand-platter fs-24"></i>
                                    </div>
                                    <div>
                                        <h6 class="fs-14 fw-semibold mb-1">Andrew Brooks</h6>
                                        <p class="fs-13 mb-0">#14751</p>
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark">T#23896</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-rounded bg-light text-dark me-2">
                                        <i class="icon-hand-platter fs-24"></i>
                                    </div>
                                    <div>
                                        <h6 class="fs-14 fw-semibold mb-1">Walk in Customer</h6>
                                        <p class="fs-13 mb-0">Take Away</p>
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark">#14547</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-rounded bg-light text-dark me-2">
                                        <i class="icon-hand-platter fs-24"></i>
                                    </div>
                                    <div>
                                        <h6 class="fs-14 fw-semibold mb-1">Elijah Thompson</h6>
                                        <p class="fs-13 mb-0">Take Away</p>
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark">#98765</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-rounded bg-light text-dark me-2">
                                        <i class="icon-hand-platter fs-24"></i>
                                    </div>
                                    <div>
                                        <h6 class="fs-14 fw-semibold mb-1">Jennifer Brooks</h6>
                                        <p class="fs-13 mb-0">DineIn</p>
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark">#23896</span>
                            </div>
                            <a href="{{ url('kitchen') }}" class="btn btn-sm btn-white d-inline-flex align-items-center w-100 mt-1">View All<i class="icon-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
