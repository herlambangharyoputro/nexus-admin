{{--
  Color Picker — input warna (shell Coloris; lib di consumer). Modul Integrations
  memanggil Coloris({el:'[data-coloris]'}) bila window.Coloris ada. Tanpa lib,
  tetap input teks biasa (boleh diisi hex manual).

  Usage:
    x-nexus::colorpicker name="brand_color" value="#0093b4" />

  Props:
    name, id : atribut
    value    : warna awal (hex)
--}}
@props([
    'name'  => 'color',
    'id'    => null,
    'value' => '#0093b4',
])

<input type="text"
    data-coloris
    @if($name) name="{{ $name }}" @endif
    @if($id) id="{{ $id }}" @endif
    value="{{ $value }}"
    {{ $attributes->merge(['class' => 'form-input']) }}>
