{{--
  Form File — input berkas ber-style + hint format/ukuran otomatis + pratinjau gambar opsional.

  Usage:
    x-nexus::form.group label="Foto Ruangan" name="foto">
        x-nexus::form.file name="foto" accept="image/jpeg,image/png"
            formats="JPG/PNG" max-size="3 MB" preview :current="$lokasi?->foto_url" />
    /x-nexus::form.group>

    // form-page wajib diberi prop files (enctype multipart).

  Props:
    name     : nama input (multiple → otomatis name[])
    id       : default diturunkan dari name
    accept   : filter tipe (atribut accept)
    multiple : true → banyak berkas
    formats  : teks format, mis. "JPG/PNG"     ┐ → hint "Format JPG/PNG, maks 3 MB."
    maxSize  : teks ukuran, mis. "3 MB"         ┘   (id "{id}-format")
    preview  : true → pratinjau gambar yang dipilih (JS FilePreview di app.js)
    current  : URL gambar tersimpan (ditampilkan di mode edit)
  Otomatis: is-invalid, aria-invalid, aria-describedby (error + hint format).
  Berkas tidak pernah diisi ulang dari old() (keterbatasan browser).
--}}
@props([
    'name'     => null,
    'id'       => null,
    'accept'   => null,
    'multiple' => false,
    'formats'  => null,
    'maxSize'  => null,
    'preview'  => false,
    'current'  => null,
])
@aware([
    'hint'     => null,
    'error'    => null,
    'required' => false,
])

@php
    $field   = \Nexus\Admin\Support\Field::class;
    $id      = $id ?: $field::id($name);
    $message = $error === false ? null : ($error ?: $field::error($errors ?? null, $name));

    $formatHint = trim(
        ($formats ? 'Format ' . $formats : '') .
        ($formats && $maxSize ? ', maks ' : ($maxSize ? 'Maks ' : '')) .
        ($maxSize ?? '')
    );
    $formatHint = $formatHint !== '' ? $formatHint . '.' : null;

    $describedBy = collect([
        $message ? $id . '-error' : ($hint ? $id . '-hint' : null),
        $formatHint ? $id . '-format' : null,
    ])->filter()->implode(' ');

    $classes = ['form-input', 'form-file'];
    if ($message) $classes[] = 'is-invalid';
@endphp

<div class="form-file-wrap" @if($preview) data-file-preview @endif>
    @if($preview || $current)
        <div class="form-file-preview" @if(!$current) hidden @endif data-file-preview-box>
            <img src="{{ $current }}" alt="Pratinjau" data-file-preview-img>
        </div>
    @endif

    <input type="file"
        @if($name) name="{{ $name }}{{ $multiple && !str_ends_with($name, '[]') ? '[]' : '' }}" @endif
        @if($id) id="{{ $id }}" @endif
        @if($accept) accept="{{ $accept }}" @endif
        @if($multiple) multiple @endif
        @if($message) aria-invalid="true" @endif
        @if($describedBy !== '' && $id) aria-describedby="{{ $describedBy }}" @endif
        @if($required) aria-required="true" @endif
        {{ $attributes->merge(['class' => implode(' ', $classes)]) }}>

    @if($formatHint)
        <p class="form-hint" @if($id) id="{{ $id }}-format" @endif>{{ $formatHint }}</p>
    @endif
</div>
