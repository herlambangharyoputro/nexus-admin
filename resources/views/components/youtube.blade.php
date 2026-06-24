{{--
  YouTube — embed video responsif 16:9 (youtube-nocookie, lazy). Tanpa lib.

  Usage:
    x-nexus::youtube id="dQw4w9WgXcQ" />
    x-nexus::youtube url="https://youtu.be/dQw4w9WgXcQ" title="Demo produk" />

  Props:
    id    : ID video YouTube (11 karakter)
    url   : URL YouTube (id diekstrak otomatis bila id kosong)
    title : judul iframe (aksesibilitas)
--}}
@props([
    'id'    => null,
    'url'   => null,
    'title' => 'YouTube video',
])

@php
    $vid = $id;
    if (!$vid && $url && preg_match('~(?:youtu\.be/|v=|embed/|shorts/)([\w-]{11})~', $url, $m)) {
        $vid = $m[1];
    }
@endphp

<div {{ $attributes->merge(['class' => 'video-embed']) }}>
    @if($vid)
        <iframe
            src="https://www.youtube-nocookie.com/embed/{{ $vid }}"
            title="{{ $title }}"
            loading="lazy"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen></iframe>
    @endif
</div>
