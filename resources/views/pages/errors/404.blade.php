{{--
  Halaman 404 (contoh). Salin ke resources/views/errors/404.blade.php di
  aplikasi consumer agar dipakai otomatis oleh Laravel.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — {{ config('app.name', 'Nexus Admin') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('nexus/assets/css/app.css') }}">
</head>
<body>
    <div class="error-page">
        <div class="error-code">404</div>
        <div class="error-title">Halaman tidak ditemukan</div>
        <div class="error-desc">Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan.</div>
        <x-nexus::button href="{{ url('/') }}">Kembali ke beranda</x-nexus::button>
    </div>
</body>
</html>
