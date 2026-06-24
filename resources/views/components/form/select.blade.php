{{--
  Form Select — <select> ber-style .form-select.

  Usage 1 — via :options (key => label):
    <x-nexus::form.select name="role" :options="['admin'=>'Admin','user'=>'User']" :selected="$role" placeholder="Pilih peran" />

  Usage 2 — via slot:
    <x-nexus::form.select name="city">
        <option value="sby">Surabaya</option>
        <option value="mlg">Malang</option>
    </x-nexus::form.select>

  Props:
    name, id    : atribut standar
    options     : array [value => label]
    selected    : value yang terpilih
    placeholder : opsi pertama disabled (kosongkan untuk tanpa placeholder)
--}}
@props([
    'name'        => null,
    'id'          => null,
    'options'     => [],
    'selected'    => null,
    'placeholder' => null,
])

<select
    @if($name) name="{{ $name }}" @endif
    @if($id) id="{{ $id }}" @endif
    {{ $attributes->merge(['class' => 'form-select']) }}
>
    @if($placeholder)
        <option value="" disabled @selected(is_null($selected))>{{ $placeholder }}</option>
    @endif

    @foreach($options as $val => $label)
        <option value="{{ $val }}" @selected((string) $selected === (string) $val)>{{ $label }}</option>
    @endforeach

    {{ $slot }}
</select>
