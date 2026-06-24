{{--
  Contoh email (extend nexus::emails.layout). Tunjukkan heading, paragraf,
  dan tombol "bulletproof" (table-based) agar tampil baik di Outlook dkk.

  Variabel contoh: $name, $actionUrl, $actionText
--}}
@extends('nexus::emails.layout')

@section('title', 'Konfirmasi Akun')

@section('content')
    <h1 style="margin:0 0 16px; font-size:20px; font-weight:bold; color:#111827;">
        Halo {{ $name ?? 'Pengguna' }},
    </h1>

    <p style="margin:0 0 16px;">
        Terima kasih telah mendaftar. Untuk mengaktifkan akun Anda, silakan klik
        tombol di bawah ini.
    </p>

    {{-- Bulletproof button (table-based) --}}
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:24px 0;">
        <tr>
            <td align="center" bgcolor="#0093b4" style="border-radius:8px;">
                <a href="{{ $actionUrl ?? '#' }}"
                   style="display:inline-block; padding:12px 28px; font-size:14px; font-weight:bold; color:#ffffff; text-decoration:none; border-radius:8px;">
                    {{ $actionText ?? 'Aktifkan Akun' }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 8px; color:#6b7280; font-size:13px;">
        Jika tombol tidak berfungsi, salin tautan berikut ke browser Anda:
    </p>
    <p style="margin:0; font-size:13px; word-break:break-all;">
        <a href="{{ $actionUrl ?? '#' }}" style="color:#0093b4;">{{ $actionUrl ?? '#' }}</a>
    </p>
@endsection
