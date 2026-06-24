{{--
  Accordion — kumpulan item collapsible (modul JS Accordion).

  Usage:
    <x-nexus::accordion>
        <x-nexus::accordion-item title="Bagian 1" open>Isi 1...</x-nexus::accordion-item>
        <x-nexus::accordion-item title="Bagian 2">Isi 2...</x-nexus::accordion-item>
    </x-nexus::accordion>

  Props:
    multi : true → boleh beberapa item terbuka sekaligus (default: hanya satu)
--}}
@props([
    'multi' => false,
])

<div {{ $attributes->merge(['class' => 'accordion']) }} data-accordion @if($multi) data-accordion-multi @endif>
    {{ $slot }}
</div>
