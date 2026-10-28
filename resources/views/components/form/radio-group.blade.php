{{--
  Form Radio Group — sekumpulan radio dari :options, sadar $errors & old().

  Usage:
    x-nexus::form.group label="Kondisi" name="kondisi" required>
        x-nexus::form.radio-group name="kondisi"
            :options="['baik'=>'Baik','rusak_ringan'=>'Rusak Ringan','rusak_berat'=>'Rusak Berat']" inline />
    /x-nexus::form.group>

  Props:
    name     : nama input (wajib)
    options  : array [value => label]
    selected : value terpilih eksplisit; null → old(name) → model form-page
    inline   : true → berjajar (runtuh ke bawah di HP); default bertumpuk
    id       : prefix id (default dari name) → tiap opsi "{id}-{index}"
    old      : true (default) → baca old input
  Catatan: label grup ditaruh di form.group; radio-group memberi role="radiogroup"
  + aria-labelledby ke label tersebut bila ada.
--}}
@props([
    'name'     => null,
    'options'  => [],
    'selected' => null,
    'inline'   => false,
    'id'       => null,
    'old'      => true,
])
@aware([
    'model'    => null,
    'label'    => null,
    'hint'     => null,
    'error'    => null,
    'required' => false,
])

@php
    $field   = \Nexus\Admin\Support\Field::class;
    $id      = $id ?: $field::id($name);
    $message = $error === false ? null : ($error ?: $field::error($errors ?? null, $name));
    $current = $field::value($name, $selected, $model, $old);

    $classes = ['form-radio-group'];
    if ($inline)  $classes[] = 'inline';
    if ($message) $classes[] = 'is-invalid';

    $describedBy = $message ? $id . '-error' : ($hint ? $id . '-hint' : null);
@endphp

<div role="radiogroup"
    @if($id) id="{{ $id }}" tabindex="-1" @endif
    @if($label && $id) aria-labelledby="{{ $id }}-label" @endif
    @if($message) aria-invalid="true" @endif
    @if($describedBy && $id) aria-describedby="{{ $describedBy }}" @endif
    @if($required) aria-required="true" @endif
    {{ $attributes->merge(['class' => implode(' ', $classes)]) }}>
    @foreach($options as $val => $text)
        <label class="form-check">
            <input type="radio" class="form-check-input"
                name="{{ $name }}" value="{{ $val }}"
                id="{{ $id }}-{{ $loop->index }}"
                @checked($field::selected($val, $current))>
            <span class="form-check-label">{{ $text }}</span>
        </label>
    @endforeach
    {{ $slot }}
</div>
