{{--
  Form Group — wrapper label + kontrol + hint/error. WAJIB membungkus setiap field.

  Usage (v0.5 — error otomatis dari $errors berdasarkan name):
    x-nexus::form.group label="Nama" name="name" required hint="Sesuai KTP">
        x-nexus::form.input name="name" />
    /x-nexus::form.group>

  Usage lama (tetap jalan):
    x-nexus::form.group label="Email" for="email" :error="$errors->first('email')">
        x-nexus::form.input id="email" name="email" type="email" />
    /x-nexus::form.group>

  Props:
    label    : string → teks label
    name     : nama field → error otomatis ($errors->first(name)), for/id otomatis
               ('items[0][qty]' → kunci 'items.0.qty', id 'items-0-qty')
    for      : id kontrol (default: diturunkan dari name)
    hint     : teks bantuan (tampil jika tidak ada error) → id "{id}-hint"
    error    : pesan error manual; MENANG atas error otomatis.
               :error="false" → matikan error otomatis.
    required : true → penanda wajib tunggal (* + "(wajib)" untuk pembaca layar);
               kontrol di dalamnya otomatis mendapat aria-required
    full     : true → span penuh di form-grid
    span     : 2 | 3 → membentang N kolom di form-grid (runtuh di HP)

  Kontrol form.* di dalam group membaca label/hint/error group ini (via @aware)
  untuk aria-invalid, aria-describedby, aria-required dan kelas is-invalid.
--}}
@props([
    'label'    => null,
    'name'     => null,
    'for'      => null,
    'hint'     => null,
    'error'    => null,
    'required' => false,
    'full'     => false,
    'span'     => null,
])

@php
    $field   = \Nexus\Admin\Support\Field::class;
    $id      = $for ?: $field::id($name);
    $message = $error === false ? null : ($error ?: $field::error($errors ?? null, $name));

    $classes = ['form-group'];
    if ($full)    $classes[] = 'full';
    if ($span)    $classes[] = 'form-span-' . (int) $span;
    if ($message) $classes[] = 'has-error';
@endphp

<div {{ $attributes->merge(['class' => implode(' ', $classes)]) }}>
    @if ($label)
        <label class="form-label" @if($id) for="{{ $id }}" id="{{ $id }}-label" @endif>
            {{ $label }}@if($required)<span class="form-required" aria-hidden="true">*</span><span class="sr-only"> (wajib)</span>@endif
        </label>
    @endif

    {{ $slot }}

    @if ($message)
        <p class="form-error" @if($id) id="{{ $id }}-error" @endif>{{ $message }}</p>
    @elseif ($hint)
        <p class="form-hint" @if($id) id="{{ $id }}-hint" @endif>{{ $hint }}</p>
    @endif
</div>
