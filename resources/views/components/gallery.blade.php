{{--
  Gallery — grid galeri gambar. Isi dengan x-nexus::gallery-item>.
  Klik item membuka lightbox (pasang x-nexus::lightbox /> sekali di layout).

  Usage:
    x-nexus::gallery>
        x-nexus::gallery-item thumb="{{ asset('1.jpg') }}" title="Foto 1" meta="2 MB" />
        x-nexus::gallery-item thumb="{{ asset('2.jpg') }}" title="Foto 2" meta="1.4 MB" />
    /x-nexus::gallery>
--}}
<div {{ $attributes->merge(['class' => 'gallery-grid']) }} data-gallery>
    {{ $slot }}
</div>
