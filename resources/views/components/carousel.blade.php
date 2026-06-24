{{--
  Carousel — slider scroll-snap (tanpa lib). Isi dengan x-nexus::carousel-item>.
  Tombol prev/next menggeser via modul JS Carousel; swipe/scroll juga jalan.

  Usage:
    x-nexus::carousel>
        x-nexus::carousel-item style="width:280px">x-nexus::card>Slide 1/x-nexus::card>/x-nexus::carousel-item>
        x-nexus::carousel-item style="width:280px">x-nexus::card>Slide 2/x-nexus::card>/x-nexus::carousel-item>
    /x-nexus::carousel>

  Props:
    controls : true (default) → tampilkan tombol prev/next
--}}
@props(['controls' => true])

<div {{ $attributes->merge(['class' => 'carousel']) }} data-carousel>
    @if($controls)
        <button type="button" class="carousel-btn prev" data-carousel-prev aria-label="Sebelumnya"><x-nexus::icons.chevron-left :size="18" /></button>
    @endif

    <div class="carousel-track">{{ $slot }}</div>

    @if($controls)
        <button type="button" class="carousel-btn next" data-carousel-next aria-label="Berikutnya"><x-nexus::icons.chevron-right :size="18" /></button>
    @endif
</div>
