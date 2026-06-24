{{--
  Halaman Registrasi (contoh). Form POST ke /register (konvensi Laravel).
--}}
@extends('nexus::layouts.auth')

@section('title', 'Daftar')

@section('content')
    <div class="auth-title">Buat akun</div>
    <div class="auth-subtitle">Mulai kelola bisnis Anda dalam hitungan menit</div>

    @if($errors->any())
        <x-nexus::alert type="error" class="mb-4">{{ $errors->first() }}</x-nexus::alert>
    @endif

    <form method="POST" action="{{ url('/register') }}">
        @csrf

        <x-nexus::form.group label="Nama lengkap" for="name">
            <x-nexus::form.input id="name" name="name" :value="old('name')" placeholder="Nama Anda" required autofocus />
        </x-nexus::form.group>

        <div class="mt-4">
            <x-nexus::form.group label="Email" for="email">
                <x-nexus::form.input id="email" name="email" type="email" :value="old('email')" placeholder="you@email.com" required />
            </x-nexus::form.group>
        </div>

        <div class="mt-4">
            <x-nexus::form.group label="Kata sandi" for="password">
                <x-nexus::form.input id="password" name="password" type="password" placeholder="Minimal 8 karakter" required />
            </x-nexus::form.group>
        </div>

        <div class="mt-4 mb-5">
            <x-nexus::form.group label="Konfirmasi kata sandi" for="password_confirmation">
                <x-nexus::form.input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi kata sandi" required />
            </x-nexus::form.group>
        </div>

        <x-nexus::button type="submit" full-width>Daftar</x-nexus::button>
    </form>
@endsection

@section('footer')
    Sudah punya akun? <a href="{{ url('/login') }}">Masuk</a>
@endsection
