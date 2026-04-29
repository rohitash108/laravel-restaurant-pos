<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.partials.title-meta')

    @include('layout.partials.head-css')
    @stack('styles')
</head>

@if (Route::is(['login', 'register', 'email-verification', 'forgot-password', 'otp', 'reset-password']))
    <body class="bg-white{{ Route::is('login') ? ' login-page' : '' }}">
@elseif (Route::is('pos'))
    <body class="pos-page">
@elseif(auth()->check() && auth()->user()->isSuperAdmin() && request()->routeIs('admin.*'))
    {{-- Super admin on admin area: sidebar expanded, correct topbar/content margin --}}
    <body class="mini-sidebar expand-menu">
    <style>.mini-sidebar.expand-menu .page-wrapper { margin-left: var(--sidenav-width); }</style>
@else
    <body>
@endif

    <!-- Main Wrapper -->
    @if (Route::is(['pos']))
    <div class="main-wrapper pos-wrapper">
    @else
    <div class="main-wrapper main-wrapper--with-footer">
    @endif

    @if (!Route::is(['login', 'register', 'email-verification', 'forgot-password', 'otp', 'reset-password']))
        @include('layout.partials.topbar')
        @include('layout.partials.sidebar')
    @endif

    @if(session('impersonating_super_admin_id'))
    <div style="position:fixed;top:0;left:0;right:0;z-index:9999;background:#f59e0b;color:#1a1a1a;padding:8px 16px;display:flex;align-items:center;justify-content:space-between;font-size:13px;font-weight:600;box-shadow:0 2px 6px rgba(0,0,0,.2);">
        <span><i class="icon-shield-alert me-2"></i>Super Admin viewing as: <strong>{{ session('impersonating_restaurant_name') }}</strong></span>
        <form action="{{ route('return-to-super-admin') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" style="background:#1a1a1a;color:#f59e0b;border:none;border-radius:6px;padding:4px 14px;font-size:12px;font-weight:700;cursor:pointer;">
                &#8617; Return to Super Admin
            </button>
        </form>
    </div>
    <div style="height:40px;"></div>
    @endif

        @yield('content')

        @if (!Route::is(['pos']))
            @include('layout.partials.footer')
        @endif

    </div>
    <!-- /Main Wrapper -->

    @include('components.modal-popup')

    <script>window.currencySymbol = @json($currency_symbol ?? '₹');</script>
@include('layout.partials.vendor-scripts')
@stack('scripts')

</body>
</html>
