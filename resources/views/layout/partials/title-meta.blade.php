@php
    $filename = Route::currentRouteName(); // get current route name

    // Acronyms to force uppercase
    $acronyms = ['pos', 'otp'];

    if ($filename === 'index') {
        $title = 'Dashboard';
    } else {

        // convert route name to words
        $parts = explode('-', strtolower($filename));

        // format words
        $formatted_parts = array_map(function ($word) use ($acronyms) {
            return in_array($word, $acronyms)
                ? strtoupper($word)
                : ucfirst($word);
        }, $parts);

        // final title
        $title = implode(' ', $formatted_parts);
    }
@endphp

<!-- Meta Tags -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title> {{ $title }} | POS Food - Bootstrap 5 Admin Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Dreams POS is a powerful Bootstrap based Inventory Management Admin Template designed for businesses, offering seamless invoicing, project tracking, and estimates.">
<meta name="keywords" content="inventory management, admin dashboard, bootstrap template, invoicing, estimates, business management, responsive admin, POS system">
<meta name="author" content="Dreams Technologies">
