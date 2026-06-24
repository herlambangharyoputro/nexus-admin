{{--
  Form Group — wrapper label + kontrol + hint/error.

  Usage:
    x-nexus::form.group label="Nama" for="name" required>
        x-nexus::form.input id="name" name="name" />
    /x-nexus::form.group>

    x-nexus::form.group label="Email" for="email" error="Email wajib diisi">
        x-nexus::form.input id="email" type="email" />
    /x-nexus::form.group>

  Props:
    label    : string → teks label
    for      : string → id kontrol (atribut for)
    hint     : string → teks bantuan (tampil jika tidak ada error)
    error    : string → pesan error (mengalahkan hint)
    required : true   → tampilkan penanda *
    full     : true   → .form-group.full (span penuh di .form-grid)
--}}
@props([
    'label'    => null,
    'for'      => null,
    'hint'     => null,
    'error'    => null,
    'required' => false,
    'full'     => false,
])

@php $classes = 'form-group' . ($full ? ' full' : ''); @endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if ($label)
        <label class="form-label" @if($for) for="{{ $for }}" @endif>
            {{ $label }}@if($required)<span class="text-red"> *</span>@endif
        </label>
    @endif

    {{ $slot }}

    @if ($error)
        <span class="form-error">{{ $error }}</span>
    @elseif ($hint)
        <span class="form-hint">{{ $hint }}</span>
    @endif
</div>
