{{-- 
  Alert — inline notification box.
  
  Usage:
    x-nexus::alert>Default info message/x-nexus::alert>
    x-nexus::alert type="success">Data berhasil disimpan/x-nexus::alert>
    x-nexus::alert type="error">Gagal koneksi ke server/x-nexus::alert>
    x-nexus::alert type="warning" :dismissible="true">Sesi akan berakhir/x-nexus::alert>

  Props:
    type        : info (default) | success | warning | error
    dismissible : true → tambahin tombol close yang langsung remove element
--}}
@props([
    'type'        => 'info',
    'dismissible' => false,
])

@php
    $classes = 'alert alert-' . $type;
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="alert">
    <span class="flex-1">{{ $slot }}</span>
    @if ($dismissible)
        <button type="button" 
                onclick="this.closest('.alert').remove()" 
                style="background:none;border:none;color:inherit;cursor:pointer;opacity:.7;font-size:18px;line-height:1;padding:0 0 0 8px;"
                aria-label="Close">&times;</button>
    @endif
</div>
