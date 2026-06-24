{{--
  Chip — tag/pill kecil (.chip). Cocok untuk filter.

  Usage:
    x-nexus::chip>Semua/x-nexus::chip>
    x-nexus::chip active>Aktif/x-nexus::chip>
    x-nexus::chip>x-nexus::icons.search :size="12" /> Pencarian/x-nexus::chip>

  Props:
    active : true → .chip.active (highlight primary)
--}}
@props(['active' => false])

<span {{ $attributes->merge(['class' => 'chip' . ($active ? ' active' : '')]) }}>
    {{ $slot }}
</span>
