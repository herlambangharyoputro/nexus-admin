{{--
  Detail Page — pola halaman show/detail: kartu utama berjudul + aksi, kolom samping opsional,
  dan kartu tab/riwayat di bawah. Isi kartu utama biasanya x-nexus::description-list.

  Usage:
    x-nexus::detail-page :title="$aset->nama" :description="$aset->kode_aset" :back="route('aset.index')">
        x-slot:actions>
            x-nexus::button variant="outline" :href="route('aset.edit', $aset)">Edit/x-nexus::button>
            x-nexus::row-actions :delete="route('aset.destroy', $aset)" label="Aksi lain" />
        /x-slot:actions>

        x-nexus::description-list>
            x-nexus::description-item label="Kategori">{{ $aset->kategori->nama }}/x-nexus::description-item>
            x-nexus::description-item label="Deskripsi" full>{{ $aset->deskripsi }}/x-nexus::description-item>
        /x-nexus::description-list>

        x-slot:aside>
            x-nexus::card title="Foto"><img src="{{ $aset->foto_url }}" alt="">/x-nexus::card>
        /x-slot:aside>

        x-slot:tabs>
            x-nexus::tabs :tabs="['riwayat' => 'Riwayat', 'mutasi' => 'Mutasi']" active="riwayat">…/x-nexus::tabs>
        /x-slot:tabs>
    /x-nexus::detail-page>

  Props:
    title, description : judul (h1) & sub-judul kartu utama
    back               : URL tombol "Kembali" (paling kiri di grup aksi)
    backLabel          : default "Kembali"
    flash              : true (default) → x-nexus::flash di atas kartu
  Slots:
    default : isi kartu utama
    actions : tombol di header kanan (di bawah 600px turun ke bawah judul, lebar penuh)
    aside   : kolom kanan ≥900px (foto, QR, ringkasan); di layar kecil pindah ke bawah
    tabs    : kartu kedua di bawah (riwayat/aktivitas/tab)
--}}
@props([
    'title'       => null,
    'description' => null,
    'back'        => null,
    'backLabel'   => 'Kembali',
    'flash'       => true,
])

@php
    $classes = ['page-pattern', 'detail-page'];
    if (isset($aside)) $classes[] = 'has-aside';
@endphp

<div {{ $attributes->merge(['class' => implode(' ', $classes)]) }}>
    @if($flash)<x-nexus::flash class="page-flash" />@endif

    <div class="page-split">
        <div class="page-main">
            <div class="card page-card">
                @if($title || $description || $back || isset($actions))
                    <div class="card-header page-card-header">
                        <div class="page-card-heading">
                            @if($title)<h1 class="card-title">{{ $title }}</h1>@endif
                            @if($description)<p class="page-card-desc">{{ $description }}</p>@endif
                        </div>
                        @if($back || isset($actions))
                            <div class="page-card-actions">
                                @if($back)<a href="{{ $back }}" class="btn btn-outline">{{ $backLabel }}</a>@endif
                                {{ $actions ?? '' }}
                            </div>
                        @endif
                    </div>
                @endif
                <div class="card-body">{{ $slot }}</div>
            </div>

            @isset($tabs)
                <div class="card page-card page-tabs">
                    <div class="card-body">{{ $tabs }}</div>
                </div>
            @endisset
        </div>

        @isset($aside)
            <aside class="page-aside">{{ $aside }}</aside>
        @endisset
    </div>
</div>
