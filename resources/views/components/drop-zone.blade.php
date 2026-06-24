{{--
  Drop Zone — area unggah berkas (klik untuk pilih; input tersembunyi di dalam).

  Usage:
    x-nexus::drop-zone name="document" accept=".pdf,.jpg" hint="PDF atau JPG, maks 2MB" />
    x-nexus::drop-zone name="photos" multiple text="Lepas foto di sini" />

  Props:
    name     : nama input file
    accept   : filter tipe (atribut accept)
    multiple : true → boleh banyak berkas (name jadi name[])
    text     : teks utama
    hint     : teks bantuan kecil
  Catatan: penanganan unggah/drag-drop di-wire oleh consumer (presentasi saja).
--}}
@props([
    'name'     => 'file',
    'accept'   => null,
    'multiple' => false,
    'text'     => 'Seret berkas ke sini atau klik untuk memilih',
    'hint'     => null,
])

<label {{ $attributes->merge(['class' => 'drop-zone d-block']) }}>
    <input type="file"
        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        @if($accept) accept="{{ $accept }}" @endif
        @if($multiple) multiple @endif
        style="display:none">
    <div class="text-muted">{{ $text }}</div>
    @if($hint)<div class="text-xs text-muted mt-1">{{ $hint }}</div>@endif
    {{ $slot }}
</label>
