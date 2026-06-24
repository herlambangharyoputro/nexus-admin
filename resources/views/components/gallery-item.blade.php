{{--
  Gallery Item — satu kartu galeri (thumbnail + info + overlay).

  Props:
    thumb : URL gambar thumbnail
    src   : URL gambar penuh untuk lightbox (default = thumb)
    title : judul
    meta  : info kecil (mis. ukuran/tanggal)
    emoji : placeholder bila tanpa thumb (default 🖼️)
--}}
@props([
    'thumb' => null,
    'src'   => null,
    'title' => null,
    'meta'  => null,
    'emoji' => '🖼️',
])

<div class="gallery-item"
     data-lightbox
     data-lb-src="{{ $src ?? $thumb }}"
     data-lb-title="{{ $title }}"
     data-lb-sub="{{ $meta }}">
    <div class="gallery-thumb">
        @if($thumb)
            <img src="{{ $thumb }}" alt="{{ $title }}" style="width:100%;height:100%;object-fit:cover">
        @else
            <div class="gallery-thumb-inner">{{ $emoji }}</div>
        @endif
        <div class="gallery-overlay"><x-nexus::icons.eye :size="22" class="text-white" /></div>
    </div>
    @if($title || $meta)
        <div class="gallery-info">
            @if($title)<div class="gallery-title">{{ $title }}</div>@endif
            @if($meta)<div class="gallery-meta">{{ $meta }}</div>@endif
        </div>
    @endif
</div>
