{{--
  Spinner — indikator loading berputar.

  Usage:
    x-nexus::spinner />
    x-nexus::spinner size="lg" />
    x-nexus::button disabled>x-nexus::spinner size="sm" /> Memuat.../x-nexus::button>

  Props:
    size : md (default) | sm | lg
--}}
@props(['size' => 'md'])

@php
    $cls = 'spinner';
    if ($size === 'sm') $cls .= ' spinner-sm';
    if ($size === 'lg') $cls .= ' spinner-lg';
@endphp

<span {{ $attributes->merge(['class' => $cls, 'role' => 'status']) }} aria-label="Memuat"></span>
