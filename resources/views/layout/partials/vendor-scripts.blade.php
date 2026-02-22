    <!-- jQuery -->
    <script src="{{URL::asset('build/js/jquery-3.7.1.min.js')}}"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{URL::asset('build/js/bootstrap.bundle.min.js')}}"></script>

@if (!Route::is(['login', 'register', 'email-verification', 'forgot-password', 'otp', 'reset-password']))
	<!-- Simplebar JS -->
	<script src="{{URL::asset('build/plugins/simplebar/simplebar.min.js')}}"></script>
@endif

@if (Route::is(['addons', 'coupons', 'dashboard', '/', 'invoices', 'kanban-view', 'orders', 'payments', 'role-permission', 'users']))
    <!-- Daterangepikcer JS -->
    <script src="{{URL::asset('build/js/moment.min.js')}}"></script>
    <script src="{{URL::asset('build/plugins/daterangepicker/daterangepicker.js')}}"></script>
@endif

@if (Route::is(['addons', 'audit-report', 'categories', 'coupons', 'customer-report', 'customer', 'delivery-settings', 'earning-report', 'integrations-settings', 'invoices', 'items', 'notifications-settings', 'order-report', 'payment-settings', 'payments', 'print-settings', 'role-permission', 'sales-report', 'store-settings', 'table', 'users']) || request()->routeIs('admin.restaurants.*'))
	<!-- Datatable JS -->
    <script src="{{URL::asset('build/plugins/datatables/js/jquery.dataTables.min.js')}}"></script>
	<script src="{{URL::asset('build/plugins/datatables/dataTables.bootstrap5.min.js')}}"></script>
@endif

@if (Route::is(['audit-report', 'coupons', 'customer-report', 'customer', 'delivery-settings', 'earning-report', 'integrations-settings', 'kitchen', 'notifications-settings', 'order-report', 'orders', 'payment-settings', 'print-settings', 'pos', 'reservations', 'sales-report', 'store-settings', 'table']))
    <!-- Flatpickr JS -->
	<script src="{{URL::asset('build/plugins/flatpickr/flatpickr.min.js')}}"></script>
@endif

@if (Route::is(['pos']))
    <!-- Slick Js -->
    <script src="{{URL::asset('build/plugins/slick/slick.min.js')}}"></script>
@endif

@if (Route::is(['dashboard', '/', 'admin.dashboard']))
    <!-- ApexChart JS -->
    <script src="{{URL::asset('build/plugins/apexchart/apexcharts.min.js')}}"></script>
    <script src="{{URL::asset('build/plugins/apexchart/chart-data.js')}}"></script>
@endif

@if (Route::is(['kanban-view']))
    <!-- Dragula Js-->
    <script src="{{URL::asset('build/plugins/dragula/dragula.min.js')}}"></script>
    <script src="{{URL::asset('build/js/dragula.js')}}"></script>
@endif

@if (Route::is(['orders', 'kanban-view']))
    <!-- Calculator JS -->
    <script src="{{URL::asset('build/js/calculator.js')}}"></script>
@endif

@if (Route::is(['otp']))
	<!-- OTP JS -->
	<script src="{{URL::asset('build/js/otp.js')}}"></script>
@endif

@if (Route::is(['addons', 'audit-report', 'categories', 'coupons', 'customer-report', 'customer', 'delivery-settings', 'earning-report', 'dashboard', '/', 'integrations-settings', 'invoices', 'items', 'kitchen', 'pos', 'notifications-settings', 'order-report', 'orders', 'payment-settings', 'payments', 'print-settings', 'reservations', 'role-permission', 'sales-report', 'store-settings', 'table', 'tax-settings', 'users']) || request()->routeIs('admin.*'))
    <!-- Select2 Js -->
    <script src="{{URL::asset('build/plugins/select2/js/select2.min.js')}}"></script>
@endif

    <!-- Main JS -->
    <script src="{{URL::asset('build/js/script.js')}}"></script>
