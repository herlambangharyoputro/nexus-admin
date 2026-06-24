{{--
  Tooltip — teks bantuan saat hover (CSS-only).

  Usage:
    <x-nexus::tooltip text="Hapus permanen">
        <x-nexus::button variant="danger" icon><x-nexus::icons.trash /></x-nexus::button>
    </x-nexus::tooltip>

  Props:
    text : isi tooltip
--}}
@props(['text' => ''])

<span {{ $attributes->merge(['class' => 'tooltip-wrap']) }}>
    {{ $slot }}
    <span class="tooltip">{{ $text }}</span>
</span>
