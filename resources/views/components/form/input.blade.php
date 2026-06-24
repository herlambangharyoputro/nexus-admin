{{--
  Form Input — <input> ber-style .form-input.

  Usage:
    <x-nexus::form.input name="email" type="email" placeholder="you@mail.com" />
    <x-nexus::form.input name="qty" type="number" :value="$qty" />

  Props:
    type        : text (default) | email | password | number | date | ...
    name, id    : atribut standar
    value       : nilai awal
    placeholder : string
  Semua atribut lain (required, min, max, dst.) diteruskan via $attributes.
--}}
@props([
    'type'        => 'text',
    'name'        => null,
    'id'          => null,
    'value'       => null,
    'placeholder' => null,
])

<input
    type="{{ $type }}"
    @if($name) name="{{ $name }}" @endif
    @if($id) id="{{ $id }}" @endif
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @if(!is_null($value)) value="{{ $value }}" @endif
    {{ $attributes->merge(['class' => 'form-input']) }}
>
