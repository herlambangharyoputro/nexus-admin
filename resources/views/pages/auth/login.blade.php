{{--
  Halaman Login. Gunakan: Route::view('/login', 'nexus::pages.auth.login')
  atau publish & sesuaikan.

  Flash session yang didukung:
    session('status')  → pesan sukses (misal setelah reset sandi)
  Errors (per-field): email, password
--}}
@extends('nexus::layouts.auth')

@section('title', 'Masuk')

@section('content')
    <div class="auth-title">Selamat datang kembali</div>
    <div class="auth-subtitle">Masuk untuk melanjutkan ke dashboard</div>

    @if (session('status'))
        <x-nexus::alert type="success" class="mb-4">{{ session('status') }}</x-nexus::alert>
    @endif

    @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
        <x-nexus::alert type="error" class="mb-4">{{ $errors->first() }}</x-nexus::alert>
    @endif

    <form method="POST" action="{{ url('/login') }}">
        @csrf

        <x-nexus::form.group label="Email" for="email" :error="$errors->first('email')">
            <x-nexus::form.input
                id="email"
                name="email"
                type="email"
                :value="old('email')"
                placeholder="you@email.com"
                required
                autofocus
            />
        </x-nexus::form.group>

        <div class="mt-4">
            <x-nexus::form.group label="Kata sandi" for="password" :error="$errors->first('password')">
                <div class="input-pw-wrap">
                    <x-nexus::form.input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="••••••••"
                        required
                    />
                    <button type="button" class="pw-toggle" data-pw-toggle="password" aria-label="Tampilkan sandi">
                        <svg class="pw-icon-show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg class="pw-icon-hide" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </x-nexus::form.group>
        </div>

        <div class="d-flex items-center justify-between mt-4 mb-5">
            <x-nexus::toggle name="remember" label="Ingat saya" />
            <a href="{{ url('/forgot-password') }}" class="text-sm" style="color:var(--primary);text-decoration:none">Lupa sandi?</a>
        </div>

        <x-nexus::button type="submit" full-width>Masuk</x-nexus::button>
    </form>
@endsection

@section('footer')
    Belum punya akun? <a href="{{ url('/register') }}">Daftar sekarang</a>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';
    document.querySelectorAll('[data-pw-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var inp = document.getElementById(btn.getAttribute('data-pw-toggle'));
            if (!inp) return;
            var show = inp.type === 'password';
            inp.type = show ? 'text' : 'password';
            btn.querySelector('.pw-icon-show').style.display = show ? 'none' : '';
            btn.querySelector('.pw-icon-hide').style.display = show ? ''     : 'none';
        });
    });
}());
</script>
@endpush
