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
    $icons = [
        'info'    => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
        'success' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
        'warning' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        'error'   => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
    ];
    $icon = $icons[$type] ?? $icons['info'];
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="alert">
    <span class="alert-icon">{!! $icon !!}</span>
    <span class="alert-msg">{{ $slot }}</span>
    @if ($dismissible)
        <button type="button" class="alert-close"
                onclick="this.closest('.alert').remove()"
                aria-label="Close">&times;</button>
    @endif
</div>
