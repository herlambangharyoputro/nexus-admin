{{--
  Map — kontainer peta (shell Leaflet; lib di consumer). Bila window.L tersedia
  DAN lat/lng diberikan, modul Integrations meng-init peta OSM + marker dasar.
  Untuk kontrol penuh (layer GIS, GeoJSON, dll), init Leaflet sendiri di scriptmu
  menargetkan #id — lihat CHARTS.md (bagian Map).

  Usage (auto):
    x-nexus::map :lat="-7.2575" :lng="112.7521" :zoom="13" height="400px" />

  Usage (manual):
    x-nexus::map id="petaWalida" height="480px" />
    @push('scripts') <script> /* L.map('petaWalida')... */ </script> @endpush

  Props:
    id     : id elemen (default acak)
    lat,lng: koordinat pusat (untuk auto-init)
    zoom   : level zoom (default 13)
    height : tinggi (default 360px)
--}}
@props([
    'id'     => null,
    'lat'    => null,
    'lng'    => null,
    'zoom'   => 13,
    'height' => '360px',
])

@php $mapId = $id ?? 'map-' . uniqid(); @endphp

<div id="{{ $mapId }}" class="map-mount" data-map
    @if(!is_null($lat)) data-map-lat="{{ $lat }}" @endif
    @if(!is_null($lng)) data-map-lng="{{ $lng }}" @endif
    data-map-zoom="{{ $zoom }}"
    style="height: {{ $height }}"
    {{ $attributes }}></div>
