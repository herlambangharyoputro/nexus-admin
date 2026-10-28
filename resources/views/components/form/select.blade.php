{{--
  Form Select — <select> ber-style .form-select, sadar $errors & old().

  Usage 1 — via :options (key => label):
    x-nexus::form.select name="role" :options="['admin'=>'Admin','user'=>'User']" placeholder="Pilih peran" />

  Usage 2 — via slot:
    x-nexus::form.select name="city">
        <option value="sby">Surabaya</option>
        <option value="mlg">Malang</option>
    /x-nexus::form.select>

  Usage 3 — multiple:
    x-nexus::form.select name="tags[]" multiple :options="$tags" :selected="$item->tag_ids" />

  Props:
    name, id    : atribut standar (id default: diturunkan dari name)
    options     : array [value => label]
    selected    : value terpilih eksplisit (fallback setelah old()); array untuk multiple
    placeholder : opsi pertama bernilai "" (kosongkan untuk tanpa placeholder)
    old         : true (default) → isi otomatis: old(name) → selected → model form-page
  Otomatis (v0.5): is-invalid, aria-invalid, aria-describedby, aria-required
  (lihat form.input).
--}}
@props([
    'name'        => null,
    'id'          => null,
    'options'     => [],
    'selected'    => null,
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
    $current = $field::value($name, $selected, $model, $old);
    if ($current instanceof \Illuminate\Support\Collection) $current = $current->all();

    $classes = ['form-select'];
    if ($message) $classes[] = 'is-invalid';

    $describedBy = $message ? $id . '-error' : ($hint ? $id . '-hint' : null);
@endphp

<select
    @if($name) name="{{ $name }}" @endif
    @if($id) id="{{ $id }}" @endif
    @if($message) aria-invalid="true" @endif
    @if($describedBy && $id) aria-describedby="{{ $describedBy }}" @endif
    @if($required) aria-required="true" @endif
    {{ $attributes->merge(['class' => implode(' ', $classes)]) }}
>
    @if($placeholder)
        <option value="" @selected($current === null || $current === '' || $current === [])>{{ $placeholder }}</option>
    @endif

    @foreach($options as $val => $label)
        <option value="{{ $val }}" @selected($field::selected($val, $current))>{{ $label }}</option>
    @endforeach

    {{ $slot }}
</select>
