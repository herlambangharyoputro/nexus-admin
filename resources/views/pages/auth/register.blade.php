{{--
  Halaman Registrasi. Form POST ke /register (konvensi Laravel).
  Errors (per-field): name, email, password, password_confirmation
--}}
@extends('nexus::layouts.auth')

@section('title', 'Daftar')

@section('content')
    <div class="auth-title">Buat akun baru</div>
    <div class="auth-subtitle">Mulai kelola bisnis Anda dalam hitungan menit</div>

    @if ($errors->any() && !$errors->has('name') && !$errors->has('email') && !$errors->has('password'))
        <x-nexus::alert type="error" class="mb-4">{{ $errors->first() }}</x-nexus::alert>
    @endif

    <form method="POST" action="{{ url('/register') }}">
        @csrf

        <x-nexus::form.group label="Nama lengkap" for="name" :error="$errors->first('name')">
            <x-nexus::form.input
                id="name"
                name="name"
                :value="old('name')"
                placeholder="Nama Anda"
                required
                autofocus
            />
        </x-nexus::form.group>

        <div class="mt-4">
            <x-nexus::form.group label="Email" for="email" :error="$errors->first('email')">
                <x-nexus::form.input
                    id="email"
                    name="email"
                    type="email"
                    :value="old('email')"
                    placeholder="you@email.com"
                    required
                />
            </x-nexus::form.group>
        </div>

        <div class="mt-4">
            <x-nexus::form.group label="Kata sandi" for="password" :error="$errors->first('password')">
                <div class="input-pw-wrap">
                    <x-nexus::form.input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Minimal 8 karakter"
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
                {{-- strength meter --}}
                <div class="strength-bars" id="strength-bars" aria-hidden="true">
                    <div class="strength-bar" data-bar="1"></div>
                    <div class="strength-bar" data-bar="2"></div>
                    <div class="strength-bar" data-bar="3"></div>
                    <div class="strength-bar" data-bar="4"></div>
                </div>
                <div class="strength-label" id="strength-label"></div>
            </x-nexus::form.group>
        </div>

        <div class="mt-4 mb-5">
            <x-nexus::form.group label="Konfirmasi kata sandi" for="password_confirmation" :error="$errors->first('password_confirmation')">
                <div class="input-pw-wrap">
                    <x-nexus::form.input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        placeholder="Ulangi kata sandi"
                        required
                    />
                    <button type="button" class="pw-toggle" data-pw-toggle="password_confirmation" aria-label="Tampilkan sandi">
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

        <x-nexus::button type="submit" full-width>Buat akun</x-nexus::button>
    </form>
@endsection

@section('footer')
    Sudah punya akun? <a href="{{ url('/login') }}">Masuk</a>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* ── show/hide toggle ── */
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

    /* ── strength meter ── */
    var pwInp    = document.getElementById('password');
    var bars     = document.querySelectorAll('#strength-bars .strength-bar');
    var label    = document.getElementById('strength-label');
    var levels   = ['', 'Lemah', 'Cukup', 'Bagus', 'Kuat'];
    var classes  = ['', 'weak',  'fair',  'good',  'strong'];

    function score(val) {
        if (!val) return 0;
        var s = 0;
        if (val.length >= 8)            s++;
        if (/[A-Z]/.test(val))          s++;
        if (/[0-9]/.test(val))          s++;
        if (/[^A-Za-z0-9]/.test(val))   s++;
        return s;
    }

    function update() {
        var s   = score(pwInp.value);
        var cls = classes[s];
        bars.forEach(function (bar, i) {
            bar.className = 'strength-bar' + (i < s ? ' ' + cls : '');
        });
        label.textContent = s > 0 ? levels[s] : '';
        label.style.color = s === 1 ? 'var(--red)'
                          : s === 2 ? 'var(--amber)'
                          : s === 3 ? 'var(--primary)'
                          : s === 4 ? 'var(--green)'
                          : 'var(--muted)';
    }

    if (pwInp) {
        pwInp.addEventListener('input', update);
    }
}());
</script>
@endpush
