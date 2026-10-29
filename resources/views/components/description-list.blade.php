{{--
  Description List — daftar label–nilai (<dl>) responsive untuk halaman detail.

  Usage 1 — item eksplisit:
    x-nexus::description-list cols="2">
        x-nexus::description-item label="Kode" :value="$aset->kode_aset" />
        x-nexus::description-item label="Status">x-nexus::badge color="green">Aktif/x-nexus::badge>/x-nexus::description-item>
        x-nexus::description-item label="Deskripsi" full>{{ $aset->deskripsi }}/x-nexus::description-item>
    /x-nexus::description-list>

  Usage 2 — shorthand :items:
    x-nexus::description-list :items="['Kode' => $aset->kode_aset, 'Lokasi' => $aset->lokasi?->nama]" />

  Props:
    cols   : 1 | 2 (default) | 3 — 3 → 2 kolom di bawah 900px; semua → 1 kolom di bawah 600px
    layout : stacked (default, label di atas nilai) | horizontal (label kiri; stacked di bawah 600px)
    items  : [label => nilai] shorthand (nilai kosong/null → "—")
--}}
@props([
    'cols'   => 2,
    'layout' => 'stacked',
    'items'  => [],
])

@php
    $cols = in_array((int) $cols, [1, 2, 3], true) ? (int) $cols : 2;
    $classes = ['dl', 'dl-cols-' . $cols, 'dl-' . ($layout === 'horizontal' ? 'horizontal' : 'stacked')];
@endphp

<dl {{ $attributes->merge(['class' => implode(' ', $classes)]) }}>
    @foreach($items as $itemLabel => $itemValue)
        <x-nexus::description-item :label="$itemLabel" :value="$itemValue" />
    @endforeach
    {{ $slot }}
</dl>
