{{--
  Flash — tampilkan pesan sesi (redirect()->with('success', ...)) sebagai alert dismissible.
  Dirender otomatis oleh index-page, form-page, detail-page. Bisa dipakai mandiri.

  Usage:
    x-nexus::flash />
    x-nexus::flash :keys="['success' => 'success', 'gagal' => 'error']" />

  Props:
    keys : [kunci sesi => tipe alert]. Default:
           success→success, error→error, warning→warning, info→info, status→success
           (status = kunci bawaan Laravel Breeze/Fortify)
  Pesan array (mis. with('success', ['a','b'])) dirender satu alert per item.
--}}
@props([
    'keys' => [
        'success' => 'success',
        'error'   => 'error',
        'warning' => 'warning',
        'info'    => 'info',
        'status'  => 'success',
    ],
])

@php
    $messages = [];
    if (request()->hasSession()) {
        foreach ($keys as $key => $type) {
            foreach ((array) session($key) as $msg) {
                if (is_string($msg) && $msg !== '') $messages[] = [$type, $msg];
            }
        }
    }
@endphp

@if(count($messages))
    <div {{ $attributes->merge(['class' => 'flash-stack']) }}>
        @foreach($messages as [$type, $msg])
            <x-nexus::alert :type="$type" dismissible>{{ $msg }}</x-nexus::alert>
        @endforeach
    </div>
@endif
