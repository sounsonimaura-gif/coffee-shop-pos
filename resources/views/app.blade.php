<!doctype html>
<html lang="{{ app()->getLocale() }}" class="minimal-theme" data-locale="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/assets/images/favicon-32x32.png') }}" type="image/png" />

    {{--
      Vendor "Skodash" theme CSS. Devs can drop the vendor backend
      assets at public/assets/backend/* later; the bundled Vite app.scss
      already includes a working Bootstrap 5 + plugin stylesheet so the
      app renders even without the legacy vendor assets.
    --}}
    <link href="{{ asset('assets/backend/assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/backend/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/backend/assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/backend/assets/css/bootstrap-extended.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/backend/assets/css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/backend/assets/css/icons.css') }}" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/backend/assets/plugins/bootstrap-icons/font/bootstrap-icons.css') }}">
    <link href="{{ asset('assets/backend/assets/css/dark-theme.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/backend/assets/css/light-theme.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/backend/assets/css/semi-dark.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/backend/assets/css/header-colors.css') }}" rel="stylesheet" />

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

    {{-- Legacy vendor scripts (Bootstrap bundle is also bundled by Vite, but the
         Skodash theme scripts power animations / pace loader / metisMenu). --}}
    <script src="{{ asset('assets/backend/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/backend/assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/backend/assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/backend/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/backend/assets/js/pace.min.js') }}"></script>
    <script src="{{ asset('assets/backend/assets/js/app.js') }}"></script>
</body>

</html>
