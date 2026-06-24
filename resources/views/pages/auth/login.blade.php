{{--
  Halaman Login (contoh). Gunakan: Route::view('/login', 'nexus::pages.auth.login')
  atau publish & sesuaikan. Form POST ke /login (konvensi Laravel).
--}}
@extends('nexus::layouts.auth')

@section('title', 'Masuk')

@section('content')
    <div class="auth-title">Selamat datang kembali</div>
    <div class="auth-subtitle">Masuk untuk melanjutkan ke dashboard</div>

    @if($errors->any())
        <x-nexus::alert type="error" class="mb-4">{{ $errors->first() }}</x-nexus::alert>
    @endif

    <form method="POST" action="{{ url('/login') }}">
        @csrf

        <x-nexus::form.group label="Email" for="email">
            <x-nexus::form.input id="email" name="email" type="email" :value="old('email')" placeholder="you@email.com" required autofocus />
        </x-nexus::form.group>

        <div class="mt-4">
            <x-nexus::form.group label="Kata sandi" for="password">
                <x-nexus::form.input id="password" name="password" type="password" placeholder="••••••••" required />
            </x-nexus::form.group>
        </div>

        <div class="d-flex items-center justify-between mt-4 mb-5">
            <x-nexus::toggle name="remember" label="Ingat saya" />
            <a href="{{ url('/forgot-password') }}" class="text-sm text-primary" style="text-decoration:none">Lupa sandi?</a>
        </div>

        <x-nexus::button type="submit" full-width>Masuk</x-nexus::button>
    </form>
@endsection

@section('footer')
    Belum punya akun? <a href="{{ url('/register') }}">Daftar</a>
@endsection
