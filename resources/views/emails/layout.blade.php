{{--
  Email Layout — master template email HTML (table-based, inline-style).
  Email client TIDAK mendukung CSS eksternal / variabel, jadi semua warna
  di-hardcode (brand teal #0093b4) dan layout pakai <table>.

  Extend:
    @extends('nexus::emails.layout')
    @section('content') ... @endsection

  Sections: title (subject visual), content, footer, brand
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name', 'Nexus Admin'))</title>
</head>
<body style="margin:0; padding:0; background:#f7f8fa; -webkit-text-size-adjust:100%;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f7f8fa; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:560px; width:100%; background:#ffffff; border:1px solid #e8eaed; border-radius:12px; overflow:hidden; font-family:'Segoe UI',Arial,Helvetica,sans-serif;">

                    {{-- Header --}}
                    <tr>
                        <td style="background:#0093b4; padding:22px 32px;">
                            <span style="color:#ffffff; font-size:18px; font-weight:bold; letter-spacing:-0.3px;">@yield('brand', config('app.name', 'Nexus Admin'))</span>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px; color:#111827; font-size:15px; line-height:1.65;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:20px 32px; background:#f7f8fa; border-top:1px solid #e8eaed; color:#6b7280; font-size:12px; line-height:1.5; text-align:center;">
                            @hasSection('footer')
                                @yield('footer')
                            @else
                                &copy; {{ date('Y') }} {{ config('app.name', 'Nexus Admin') }}. Semua hak dilindungi.<br>
                                Email ini dikirim otomatis, mohon tidak membalas.
                            @endif
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
