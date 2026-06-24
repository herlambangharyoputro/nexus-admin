{{--
  Divider — garis pemisah.

  Usage:
    x-nexus::divider />              (horizontal)
    x-nexus::divider vertical />     (vertikal, 24px)

  Props:
    vertical : true → .divider-v
--}}
@props(['vertical' => false])

<div {{ $attributes->merge(['class' => $vertical ? 'divider-v' : 'divider']) }}></div>
