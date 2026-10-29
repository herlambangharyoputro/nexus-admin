{{--
  Form Page — pola halaman form create/edit: kartu ber-size + <form> + ringkasan error + footer aksi baku.
  SATU file _form dipakai create & edit (lihat assets/page-form.blade.php).

  Usage:
    x-nexus::form-page
        :title="isset($lokasi) ? 'Edit Lokasi' : 'Tambah Lokasi'"
        :action="isset($lokasi) ? route('lokasi.update', $lokasi) : route('lokasi.store')"
        :model="$lokasi ?? null"
        :cancel="route('lokasi.index')"
        size="md" files>

        x-nexus::form.group label="Nama" name="nama" required>
            x-nexus::form.input name="nama" />          ← nilai awal otomatis: old() → model
        /x-nexus::form.group>

        x-slot:footerStart>                              ← opsional, kiri footer
            x-nexus::button variant="danger" ...>Hapus/x-nexus::button>
        /x-slot:footerStart>
    /x-nexus::form-page>

  Props:
    action       : URL submit (wajib)
    method       : null (default) → PUT bila model->exists, selain itu POST.
                   POST | PUT | PATCH | DELETE → @csrf + @method spoofing. GET → tanpa csrf.
    model        : model/array sumber nilai awal field form.* (dibaca via @aware)
    title        : judul kartu (h1)
    description  : teks kecil di bawah judul
    size         : sm (560px) | md (760px, default) | lg (1040px) | full
    files        : true → enctype multipart/form-data
    cancel       : URL tombol "Batal" (tidak dirender bila null)
    submitLabel  : default "Simpan" (create) / "Simpan Perubahan" (edit/PUT/PATCH)
    cancelLabel  : default "Batal"
    errorSummary : true (default) → x-nexus::form.errors di atas form
    flash        : true (default) → x-nexus::flash di atas form
  Slots:
    default      : field (dirender bertumpuk satu kolom; pakai form-grid untuk pasangan)
    footer       : ganti seluruh isi footer aksi
    footerStart  : elemen di kiri footer (mis. tombol Hapus)
    aside        : kolom kanan ≥900px (pratinjau, QR, bantuan); di HP pindah ke bawah
  Atribut: class/style → elemen root; atribut lain (id, novalidate, data-*) → <form>.
  Footer: Simpan (primer) paling kanan, Batal di kirinya; di bawah 600px tombol
  selebar layar dengan Simpan paling atas.
--}}
@props([
    'action'       => '#',
    'method'       => null,
    'model'        => null,
    'title'        => null,
    'description'  => null,
    'size'         => 'md',
    'files'        => false,
    'cancel'       => null,
    'submitLabel'  => null,
    'cancelLabel'  => 'Batal',
    'errorSummary' => true,
    'flash'        => true,
])

@php
    $isEdit = is_object($model) && isset($model->exists) && $model->exists === true;
    $method = strtoupper($method ?? ($isEdit ? 'PUT' : 'POST'));
    $isUpdate = $isEdit || in_array($method, ['PUT', 'PATCH'], true);
    $submitLabel = $submitLabel ?? ($isUpdate ? 'Simpan Perubahan' : 'Simpan');

    $size = in_array($size, ['sm', 'md', 'lg', 'full'], true) ? $size : 'md';
    $classes = ['page-pattern', 'form-page', 'page-size-' . $size];
    if (isset($aside)) $classes[] = 'has-aside';
@endphp

<div {{ $attributes->only(['class', 'style'])->merge(['class' => implode(' ', $classes)]) }}>
    <div class="card page-card">
        @if($title || $description)
            <div class="card-header page-card-header">
                <div class="page-card-heading">
                    @if($title)<h1 class="card-title">{{ $title }}</h1>@endif
                    @if($description)<p class="page-card-desc">{{ $description }}</p>@endif
                </div>
            </div>
        @endif

        <div class="card-body">
            @if($flash)<x-nexus::flash class="mb-4" />@endif
            @if($errorSummary)<x-nexus::form.errors class="mb-4" />@endif

            <form method="{{ $method === 'GET' ? 'GET' : 'POST' }}" action="{{ $action }}"
                  @if($files) enctype="multipart/form-data" @endif
                  {{ $attributes->except(['class', 'style']) }}>
                @if($method !== 'GET') @csrf @endif
                @if(!in_array($method, ['GET', 'POST'], true)) @method($method) @endif

                <div class="form-fields">
                    {{ $slot }}
                </div>

                <div class="form-actions">
                    @isset($footer)
                        {{ $footer }}
                    @else
                        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
                        @if($cancel)
                            <a href="{{ $cancel }}" class="btn btn-outline">{{ $cancelLabel }}</a>
                        @endif
                        @isset($footerStart)
                            <div class="form-actions-start">{{ $footerStart }}</div>
                        @endisset
                    @endisset
                </div>
            </form>
        </div>
    </div>

    @isset($aside)
        <aside class="page-aside">{{ $aside }}</aside>
    @endisset
</div>
