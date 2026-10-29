{{--
  Form Errors — ringkasan SEMUA error validasi di atas form; tiap pesan tautan ke field-nya.
  Dirender otomatis oleh form-page (prop errorSummary). Bisa dipakai mandiri.

  Usage:
    x-nexus::form.errors />
    x-nexus::form.errors title="Data belum bisa disimpan:" bag="login" />

  Props:
    title : judul ringkasan (default "Periksa kembali isian berikut:")
    bag   : nama error bag (default "default")
  Tautan menuju "#{id}" dengan id = Field::id(kunci error) — sama dengan id
  otomatis form.* ('items.0.qty' → #items-0-qty). Saat halaman dimuat, app.js
  memfokuskan ringkasan ini ([data-error-summary]) agar pembaca layar membacanya.
--}}
@props([
    'title' => 'Periksa kembali isian berikut:',
    'bag'   => 'default',
])

@php
    $bagErrors = isset($errors) && method_exists($errors, 'getBag') ? $errors->getBag($bag) : null;
@endphp

@if($bagErrors && $bagErrors->any())
    <div {{ $attributes->merge(['class' => 'alert alert-error form-errors']) }}
         role="alert" tabindex="-1" data-error-summary>
        <span class="alert-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></span>
        <div class="alert-msg">
            <p class="form-errors-title">{{ $title }}</p>
            <ul class="form-errors-list">
                @foreach($bagErrors->messages() as $key => $messages)
                    @foreach($messages as $msg)
                        <li><a href="#{{ \Nexus\Admin\Support\Field::id($key) }}">{{ $msg }}</a></li>
                    @endforeach
                @endforeach
            </ul>
        </div>
    </div>
@endif
