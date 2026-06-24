{{--
  Page Header — judul halaman + deskripsi + breadcrumb + aksi.
  Pola standar di atas konten setiap halaman.

  Usage:
    x-nexus::page-header
        title="Pengguna"
        description="Kelola akun & hak akses pengguna."
        :breadcrumb="[['label'=>'Dashboard','url'=>url('/')], ['label'=>'Pengguna']]">
        x-slot:actions>
            x-nexus::button>x-nexus::icons.plus :size="14" /> Tambah/x-nexus::button>
        /x-slot:actions>
    /x-nexus::page-header>

  Props:
    title       : judul halaman
    description : deskripsi singkat (opsional)
    breadcrumb  : array item untuk x-nexus::breadcrumb> (opsional)
  Slot:
    actions     : tombol di kanan (opsional)
--}}
@props([
    'title'       => '',
    'description' => null,
    'breadcrumb'  => [],
])

<div {{ $attributes->merge(['class' => 'page-header']) }}>
    <div>
        @if(!empty($breadcrumb))
            <x-nexus::breadcrumb :items="$breadcrumb" class="mb-2" />
        @endif
        <div class="page-header-title">{{ $title }}</div>
        @if($description)<div class="page-header-desc">{{ $description }}</div>@endif
    </div>

    @isset($actions)
        <div class="page-header-actions">{{ $actions }}</div>
    @endisset
</div>
