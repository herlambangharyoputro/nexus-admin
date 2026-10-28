{{--
  Form Input — <input> ber-style .form-input, sadar $errors & old().

  Usage:
    x-nexus::form.input name="email" type="email" placeholder="you@mail.com" />
    x-nexus::form.input name="qty" type="number" :value="$qty" />

    // di dalam x-nexus::form-page :model="$item"> nilai awal otomatis dari model:
    x-nexus::form.input name="nama" />

  Props:
    type        : text (default) | email | password | number | date | ...
    name, id    : atribut standar (id default: diturunkan dari name)
    value       : nilai awal eksplisit (fallback setelah old())
    placeholder : string
    old         : true (default) → isi otomatis: old(name) → value → model form-page.
                  false → hanya pakai value.
  Otomatis (v0.5): kelas is-invalid + aria-invalid bila ada error untuk name,
  aria-describedby ke error/hint form.group, aria-required bila group required.
  type password & file tidak pernah diisi ulang.
  Semua atribut lain (required, min, max, dst.) diteruskan via $attributes.
--}}
@props([
    'type'        => 'text',
    'name'        => null,
    'id'          => null,
    'value'       => null,
    'placeholder' => null,
    'old'         => true,
])
@aware([
    'model'    => null,
    'hint'     => null,
    'error'    => null,
    'required' => false,
])

@php
    $field   = \Nexus\Admin\Support\Field::class;
    $id      = $id ?: $field::id($name);
    $message = $error === false ? null : ($error ?: $field::error($errors ?? null, $name));
    $fillable = ! in_array($type, ['password', 'file'], true);
    $current  = $fillable ? $field::scalar($field::value($name, $value, $model, $old), $type) : null;

    $classes = ['form-input'];
    if ($message) $classes[] = 'is-invalid';

    $describedBy = $message ? $id . '-error' : ($hint ? $id . '-hint' : null);
@endphp

<input
    type="{{ $type }}"
    @if($name) name="{{ $name }}" @endif
    @if($id) id="{{ $id }}" @endif
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @if(!is_null($current)) value="{{ $current }}" @endif
    @if($message) aria-invalid="true" @endif
    @if($describedBy && $id) aria-describedby="{{ $describedBy }}" @endif
    @if($required) aria-required="true" @endif
    {{ $attributes->merge(['class' => implode(' ', $classes)]) }}
>
