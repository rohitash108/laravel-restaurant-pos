{{-- Site-wide footer: copyright + brand logo. Hidden on POS (full-screen terminal). --}}
<footer class="app-site-footer" role="contentinfo">
    <div class="container-fluid px-3">
        <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 gap-md-3 py-1">
            <p class="mb-0 text-muted small text-center">
                © {{ date('Y') }} All Rights Reserved <span class="text-body-secondary mx-1">|</span> IT Softwar
            </p>
            <span class="app-brand-logo app-brand-logo--footer" aria-hidden="true">
                <img src="{{ asset('build/img/logo-small.svg') }}" alt="" width="32" height="29" decoding="async">
            </span>
        </div>
    </div>
</footer>
