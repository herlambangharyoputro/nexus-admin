{{--
  Halaman Verifikasi Email (contoh). Tombol kirim ulang POST ke
  /email/verification-notification (konvensi Laravel).
--}}
@extends('nexus::layouts.auth')

@section('title', 'Verifikasi Email')

@section('content')
    <div style="text-align:center; font-size:40px; margin-bottom:8px">📩</div>
    <div class="auth-title">Cek email Anda</div>
    <div class="auth-subtitle">
        Kami telah mengirim tautan verifikasi ke alamat email Anda.
        Klik tautan tersebut untuk mengaktifkan akun.
    </div>

    @if(session('status') === 'verification-link-sent')
        <x-nexus::alert type="success" class="mb-4">Tautan verifikasi baru telah dikirim.</x-nexus::alert>
    @endif

    <form method="POST" action="{{ url('/email/verification-notification') }}">
        @csrf
        <x-nexus::button type="submit" variant="outline" full-width>Kirim ulang email</x-nexus::button>
    </form>
@endsection

@section('footer')
    <form method="POST" action="{{ url('/logout') }}" style="display:inline">
        @csrf
        <button type="submit" style="background:none;border:none;color:var(--primary);cursor:pointer;font-family:var(--font);font-size:13px">Keluar</button>
    </form>
@endsection
