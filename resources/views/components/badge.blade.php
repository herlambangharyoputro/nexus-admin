{{-- 
  Badge — small status/label pill.

  Usage:
    x-nexus::badge>Default Blue/x-nexus::badge>
    x-nexus::badge color="green">Active/x-nexus::badge>
    x-nexus::badge color="red">Suspended/x-nexus::badge>
    x-nexus::badge color="amber">Pending/x-nexus::badge>
    x-nexus::badge color="purple">VIP/x-nexus::badge>
    x-nexus::badge color="danger">Danger/x-nexus::badge>

  Props:
    color : blue (default) | green | amber | red | purple | danger
            (danger = solid merah, untuk penekanan kuat)
--}}
@props([
    'color' => 'blue',
])

@php
    $classes = 'badge badge-' . $color;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
