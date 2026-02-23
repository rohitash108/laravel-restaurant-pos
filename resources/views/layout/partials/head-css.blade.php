    <!-- Favicon -->
    <link rel="shortcut icon" href="{{URL::asset('build/img/favicon.png')}}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{URL::asset('build/img/apple-icon.png')}}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{URL::asset('build/css/bootstrap.min.css')}}">

@if (!Route::is(['login', 'register', 'email-verification', 'forgot-password', 'otp', 'reset-password']))
    <!-- Theme Script -->
    <script src="{{URL::asset('build/js/theme-script.js')}}"></script>
@endif

    <!-- Lucide Icon CSS -->
    <link rel="stylesheet" href="{{URL::asset('build/plugins/lucide/lucide.css')}}">

@if (!Route::is(['login', 'register', 'email-verification', 'forgot-password', 'otp', 'reset-password']))
    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{URL::asset('build/plugins/simplebar/simplebar.min.css')}}">
@endif

@if (Route::is(['addons', 'coupons', 'dashboard', 'index', '/', 'invoices', 'kanban-view', 'orders', 'payments', 'role-permission', 'users']))
    <!-- Daterangepikcer CSS -->
	<link rel="stylesheet" href="{{URL::asset('build/plugins/daterangepicker/daterangepicker.css')}}">
@endif

@if (Route::is(['addons', 'audit-report', 'categories', 'coupons', 'customer-report', 'customer', 'delivery-settings', 'earning-report', 'integrations-settings', 'invoices', 'items', 'notifications-settings', 'order-report', 'payment-settings', 'payments', 'print-settings', 'role-permission', 'sales-report', 'store-settings', 'table', 'users']))
    <!-- Datatable CSS -->
	<link rel="stylesheet" href="{{URL::asset('build/plugins/datatables/dataTables.bootstrap5.min.css')}}">
@endif

@if (Route::is(['audit-report', 'coupons', 'customer-report', 'customer', 'delivery-settings', 'earning-report', 'integrations-settings', 'kitchen', 'notifications-settings', 'order-report', 'orders', 'payment-settings', 'print-settings', 'pos', 'reservations', 'sales-report', 'store-settings', 'table']))
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="{{URL::asset('build/plugins/flatpickr/flatpickr.min.css')}}">
@endif

@if (Route::is(['pos']))
    <!-- Slick CSS -->
    <link rel="stylesheet" href="{{URL::asset('build/plugins/slick/slick.css')}}">
    <link rel="stylesheet" href="{{URL::asset('build/plugins/slick/slick-theme.css')}}">
@endif

 @if (Route::is(['addons', 'audit-report', 'categories', 'coupons', 'customer-report', 'customer', 'delivery-settings', 'earning-report', 'index', '/', 'integrations-settings', 'invoices', 'items', 'kitchen', 'pos', 'notifications-settings', 'order-report', 'orders', 'payment-settings', 'payments', 'print-settings', 'reservations', 'role-permission', 'sales-report', 'store-settings', 'table', 'tax-settings', 'users']) || request()->routeIs('admin.*'))
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{URL::asset('build/plugins/select2/css/select2.min.css')}}">
@endif

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{URL::asset('build/css/style.css')}}">
    <!-- Responsive overrides -->
    <link rel="stylesheet" href="{{URL::asset('build/css/responsive.css')}}">