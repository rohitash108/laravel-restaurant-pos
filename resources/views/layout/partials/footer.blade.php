{{-- Site-wide footer: copyright + brand logo. Hidden on POS (full-screen terminal). --}}
<footer class="app-site-footer" role="contentinfo">
    <div class="container-fluid px-3">
        <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 gap-md-3 py-1">
            <p class="mb-0 small text-center app-site-footer__text">
                © {{ date('Y') }} IT Software. All Rights Reserved. <span class="app-site-footer__sep mx-1">|</span> Designed by IT Software
            </p>
            <span class="app-brand-logo app-brand-logo--footer app-site-footer__logo-light" aria-hidden="true">
                <img src="{{ asset('build/img/logo-small.svg') }}" alt="" width="32" height="29" decoding="async">
            </span>
            <span class="app-brand-logo app-brand-logo--footer app-site-footer__logo-dark" aria-hidden="true">
                <img src="{{ asset('build/img/logo-white.svg') }}" alt="" width="32" height="29" decoding="async">
            </span>
        </div>
    </div>
</footer>
