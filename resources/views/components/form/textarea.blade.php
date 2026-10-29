{{--
  Form Textarea — <textarea> ber-style .form-textarea, sadar $errors & old().

  Usage:
    x-nexus::form.textarea name="notes" rows="5" placeholder="Catatan..." />
    x-nexus::form.textarea name="bio" :value="$bio" />

  Props:
    name, id    : atribut standar (id default: diturunkan dari name)
    rows        : default 4
    placeholder : string
    value       : isi awal eksplisit (boleh juga via slot); fallback setelah old()
    old         : true (default) → isi otomatis: old(name) → value/slot → model form-page
  Otomatis (v0.5): is-invalid, aria-invalid, aria-describedby, aria-required
  (lihat form.input).
--}}
@props([
    'name'        => null,
    'id'          => null,
    'rows'        => 4,
    'placeholder' => null,
    'value'       => null,
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
    // Isi via slot (pola lama) dirender apa adanya — kecuali ada old input.
    $useSlot = $value === null && ! $slot->isEmpty() && ! ($old && $field::hasOld($name));
    $current = $useSlot ? $slot : $field::scalar($field::value($name, $value, $model, $old));

    $classes = ['form-textarea'];
    if ($message) $classes[] = 'is-invalid';

    $describedBy = $message ? $id . '-error' : ($hint ? $id . '-hint' : null);
@endphp

<textarea
    @if($name) name="{{ $name }}" @endif
    @if($id) id="{{ $id }}" @endif
    rows="{{ $rows }}"
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @if($message) aria-invalid="true" @endif
    @if($describedBy && $id) aria-describedby="{{ $describedBy }}" @endif
    @if($required) aria-required="true" @endif
    {{ $attributes->merge(['class' => implode(' ', $classes)]) }}
>{{ $current }}</textarea>
