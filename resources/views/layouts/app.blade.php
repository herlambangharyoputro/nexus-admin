<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Nexus Admin') }}</title>

    {{-- Favicon (optional, ganti kalau ada) --}}
    {{-- <link rel="icon" type="image/png" href="{{ asset('nexus/assets/images/favicon.png') }}"> --}}

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    {{-- App CSS (master, imports tokens/base/utilities/layout/components) --}}
    <link rel="stylesheet" href="{{ asset('nexus/assets/css/app.css') }}">

    {{-- Per-page extra styles --}}
    @stack('styles')
</head>
<body>

<div class="layout">

    {{-- Sidebar --}}
    @include('nexus::partials.sidebar')

    {{-- Mobile sidebar overlay (clicking closes sidebar on mobile) --}}
    <div class="sidebar-overlay"></div>

    {{-- Main area --}}
    <main class="main">

        {{-- Topbar --}}
        @include('nexus::partials.topbar')

        {{-- Content --}}
        <div class="content">
            @yield('content')
        </div>

    </main>

</div>

{{-- Toast container (auto-injected by JS if missing, but defined here for SSR-safe markup) --}}
@include('nexus::partials.toasts')

{{-- Global dialogs (pasang sekali) --}}
<x-nexus::confirm-dialog />
<x-nexus::lightbox />

{{-- jQuery + App JS --}}
<script src="{{ asset('nexus/assets/js/vendor/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('nexus/assets/js/app.js') }}"></script>

{{-- Per-page extra scripts --}}
@stack('scripts')

</body>
</html>
