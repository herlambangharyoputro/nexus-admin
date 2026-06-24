{{--
  Kbd — penanda tombol keyboard.

  Usage:
    x-nexus::kbd>Ctrl/x-nexus::kbd> + x-nexus::kbd>K/x-nexus::kbd>
--}}
<kbd {{ $attributes->merge(['class' => 'kbd']) }}>{{ $slot }}</kbd>
