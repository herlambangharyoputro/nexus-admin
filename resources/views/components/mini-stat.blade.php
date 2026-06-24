{{--
  Mini Stat — blok statistik ringkas (label kiri, value kanan).

  Usage:
    <x-nexus::mini-stat label="Hari ini" value="42" />
    <x-nexus::mini-stat label="Pending" value="7" color="amber" />

  Props:
    label, value : teks
    color        : null (default) | primary | amber | green | red | purple → background *-soft
--}}
@props([
    'label' => '',
    'value' => '',
    'color' => null,
])

@php $cls = 'mini-stat' . ($color ? ' bg-' . $color . '-soft' : ''); @endphp

<div {{ $attributes->merge(['class' => $cls]) }}>
    <span class="stat-label">{{ $label }}</span>
    <span class="font-bold">{{ $value }}</span>
</div>
