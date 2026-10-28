{{--
  Icon Download — arrow-down into tray.

  Usage:
    x-nexus::icons.download />
    x-nexus::icons.download :size="18" class="text-primary" />

  Props:
    size  : 16 (default)
    class : tambahan class CSS
--}}
@props(['size' => 16])

<svg {{ $attributes->merge(['class' => 'icon']) }}
    xmlns="http://www.w3.org/2000/svg"
    width="{{ $size }}" height="{{ $size }}"
    viewBox="0 0 24 24" fill="none"
    stroke="currentColor" stroke-width="2"
    stroke-linecap="round" stroke-linejoin="round">
    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
    <polyline points="7 10 12 15 17 10"/>
    <line x1="12" y1="15" x2="12" y2="3"/>
</svg>
