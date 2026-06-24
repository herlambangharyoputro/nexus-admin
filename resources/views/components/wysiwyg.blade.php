{{--
  WYSIWYG — editor teks kaya (shell Quill; lib di consumer). Modul Integrations
  meng-init bila window.Quill ada, dan menyinkronkan HTML ke input tersembunyi.

  Usage:
    <x-nexus::wysiwyg name="body" :value="old('body', $post->body ?? '')" height="240px" />

  Props:
    name        : nama input tersembunyi (berisi HTML hasil edit)
    value       : HTML awal
    height      : tinggi area edit (default 200px)
    placeholder : teks placeholder
  Catatan: $value dirender sebagai HTML mentah — sanitasi konten tepercaya
  adalah tanggung jawab consumer.
--}}
@props([
    'name'        => 'content',
    'value'       => null,
    'height'      => '200px',
    'placeholder' => 'Tulis di sini...',
])

<div class="wysiwyg" data-wysiwyg>
    <div class="wysiwyg-mount" data-quill data-quill-placeholder="{{ $placeholder }}" style="min-height: {{ $height }}">{!! $value !!}</div>
    <input type="hidden" name="{{ $name }}" data-quill-input value="{{ $value }}">
</div>
