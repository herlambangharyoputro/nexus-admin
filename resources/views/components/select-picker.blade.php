{{--
  Select Picker — select multi/live-search (shell Tom Select; lib di consumer).
  Modul JS Integrations meng-init bila window.TomSelect tersedia; tanpa lib,
  fallback ke <select> biasa.

  Usage:
    <x-nexus::select-picker name="tags" multiple
        :options="['laravel'=>'Laravel','php'=>'PHP','js'=>'JavaScript']"
        :selected="['laravel','php']" placeholder="Pilih tag" />

  Props:
    name, id    : atribut
    options     : array [value => label]
    selected    : value terpilih (string atau array)
    multiple    : true → multi-select
    placeholder : teks placeholder
--}}
@props([
    'name'        => null,
    'id'          => null,
    'options'     => [],
    'selected'    => [],
    'multiple'    => false,
    'placeholder' => 'Pilih...',
])

@php $selected = array_map('strval', (array) $selected); @endphp

<select
    @if($name) name="{{ $name }}{{ $multiple ? '[]' : '' }}" @endif
    @if($id) id="{{ $id }}" @endif
    @if($multiple) multiple @endif
    data-tomselect
    data-placeholder="{{ $placeholder }}"
    {{ $attributes->merge(['class' => 'form-select']) }}>

    @unless($multiple)<option value="">{{ $placeholder }}</option>@endunless

    @foreach($options as $val => $label)
        <option value="{{ $val }}" @selected(in_array((string) $val, $selected))>{{ $label }}</option>
    @endforeach

    {{ $slot }}
</select>
