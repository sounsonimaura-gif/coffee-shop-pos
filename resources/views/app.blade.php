<!doctype html>
<html lang="{{ app()->getLocale() }}" class="minimal-theme" data-locale="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />

    {{-- Web fonts + icon font (CDN). Bootstrap + the admin layout are bundled via Vite. --}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <title inertia>{{ config('app.name', 'Coffee Shop POS') }}</title>

    @routes
    {{-- jQuery + DataTables must load as classic scripts before the Vite bundle.
         DataTables' UMD source contains bare `window = …` assignments that
         crash in a strict-mode ESM build, so it can't be bundled. --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>window.$ = window.jQuery;</script>
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.bootstrap5.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @inertiaHead
</head>

<body>

    @inertia

    {{-- PHPFlasher will inject session flash toasts here on every full reload. --}}
    @flasher_render

</body>

</html>
