{{--
  Form Textarea — <textarea> ber-style .form-textarea.

  Usage:
    <x-nexus::form.textarea name="notes" rows="5" placeholder="Catatan..." />
    <x-nexus::form.textarea name="bio" :value="$bio" />

  Props:
    name, id    : atribut standar
    rows        : default 4
    placeholder : string
    value       : isi awal (boleh juga via slot)
--}}
@props([
    'name'        => null,
    'id'          => null,
    'rows'        => 4,
    'placeholder' => null,
    'value'       => null,
])

<textarea
    @if($name) name="{{ $name }}" @endif
    @if($id) id="{{ $id }}" @endif
    rows="{{ $rows }}"
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    {{ $attributes->merge(['class' => 'form-textarea']) }}
>{{ $value ?? $slot }}</textarea>
