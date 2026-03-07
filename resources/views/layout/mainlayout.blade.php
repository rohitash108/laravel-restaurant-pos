<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.partials.title-meta')

    @include('layout.partials.head-css')
    @stack('styles')
</head>

@if (Route::is(['login', 'register', 'email-verification', 'forgot-password', 'otp', 'reset-password']))
    <body class="bg-white">
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
    <div class="main-wrapper">
    @endif

    @if (!Route::is(['login', 'register', 'email-verification', 'forgot-password', 'otp', 'reset-password']))
        @include('layout.partials.topbar')
        @include('layout.partials.sidebar')
    @endif

        @yield('content')

    </div>
    <!-- /Main Wrapper -->

    @include('components.modal-popup')

    <script>window.currencySymbol = @json(config('app.currency_symbol', '₹'));</script>
@include('layout.partials.vendor-scripts')
@stack('scripts')

</body>
</html>
