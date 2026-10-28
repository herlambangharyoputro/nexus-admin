{{--
  Form Grid — baris 2/3 kolom untuk field PENDEK yang berpasangan makna; runtuh di HP.

  Usage:
    x-nexus::form-grid>
        x-nexus::form.group label="Gedung" name="gedung">x-nexus::form.input name="gedung" />/x-nexus::form.group>
        x-nexus::form.group label="Lantai" name="lantai">x-nexus::form.input name="lantai" />/x-nexus::form.group>
    /x-nexus::form-grid>

  Props:
    cols : 2 (default) | 3
           2 → 1 kolom di bawah 600px
           3 → 2 kolom di bawah 900px, 1 kolom di bawah 600px
  Anak: form.group dengan full (span penuh) atau span="2".

  ATURAN (GUIDELINES §9): default form tetap SATU kolom. form-grid hanya untuk field
  pendek yang berkaitan (Gedung+Lantai, Mulai+Selesai, Harga+Jumlah), di form-page
  size md/lg (cols=3 hanya lg), tanpa textarea/file/radio-group di dalamnya.
  Dilarang style="display:grid…" inline.
--}}
@props([
    'cols' => 2,
])

@php
    $cols = in_array((int) $cols, [2, 3], true) ? (int) $cols : 2;
@endphp

<div {{ $attributes->merge(['class' => 'form-grid form-grid-' . $cols]) }}>
    {{ $slot }}
</div>
