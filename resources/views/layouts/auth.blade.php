{{--
  Layout Auth — halaman terpusat tanpa sidebar (login, register, dll).
  Extend: @extends('nexus::layouts.auth')
  Sections: title, brand, content, footer ; stacks: styles, scripts
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masuk') — {{ config('app.name', 'Nexus Admin') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('nexus/assets/css/app.css') }}">
    @stack('styles')
</head>
<body class="auth-body">

    <div class="auth-shell">
        <div class="auth-card">
            <div class="auth-logo">
                <div class="logo-icon">@yield('logo', 'N')</div>
                <span class="auth-brand">@yield('brand', config('app.name', 'Nexus Admin'))</span>
            </div>

            @yield('content')
        </div>

        @hasSection('footer')
            <div class="auth-footer">@yield('footer')</div>
        @endif
    </div>

    <script src="{{ asset('nexus/assets/js/vendor/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('nexus/assets/js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
