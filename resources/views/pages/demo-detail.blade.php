{{--
  Demo Pola Detail — x-nexus::detail-page + description-list (tanpa database).
  Daftarkan: Route::view('/nexus-demo/detail', 'nexus::pages.demo-detail');

  Keadaan (query string):
    (default)      data lengkap + aside + tab riwayat
    ?state=empty   nilai kosong ("—") & riwayat kosong
--}}
@extends('nexus::layouts.app')

@section('title', 'Demo Detail')
@section('page-title', 'Demo Pola')
@section('breadcrumb', 'Detail')

@section('content')
@php
    $empty = request('state') === 'empty';
    $riwayat = $empty ? [] : [
        ['2026-10-21', 'Perawatan rutin', 'Servis kipas & pembersihan', 'green'],
        ['2026-08-02', 'Mutasi', 'Gedung B · Lt 3 → Gedung A · Lt 1 · Lobby', 'primary'],
        ['2026-03-14', 'Perbaikan', 'Ganti baterai CMOS', 'amber'],
    ];
@endphp

<div class="d-flex flex-wrap gap-2 mb-4" aria-label="Keadaan demo">
    <x-nexus::chip :active="!$empty"><a href="{{ request()->url() }}">Lengkap</a></x-nexus::chip>
    <x-nexus::chip :active="$empty"><a href="{{ request()->url() }}?state=empty">Kosong</a></x-nexus::chip>
</div>

<x-nexus::detail-page title="Laptop Dell Latitude 5420" description="AST-2026-0001 · Elektronik" back="#index">
    <x-slot:actions>
        <x-nexus::button variant="outline" href="#edit"><x-nexus::icons.edit :size="14" /> Edit</x-nexus::button>
        <x-nexus::row-actions label="Aksi lain" delete="#hapus" delete-confirm="Hapus aset AST-2026-0001?">
            <a class="dropdown-item" role="menuitem" href="#qr">Cetak label QR</a>
        </x-nexus::row-actions>
    </x-slot:actions>

    <x-nexus::description-list>
        <x-nexus::description-item label="Kategori" :value="$empty ? null : 'Elektronik'" />
        <x-nexus::description-item label="Lokasi" :value="$empty ? null : 'Gedung A · Lt 1 · Lobby'" />
        <x-nexus::description-item label="Kondisi">
            @unless($empty)<x-nexus::badge color="green">Baik</x-nexus::badge>@endunless
        </x-nexus::description-item>
        <x-nexus::description-item label="Penanggung Jawab" :value="$empty ? null : 'Siti Aminah'" />
        <x-nexus::description-item label="Tanggal Perolehan" :value="$empty ? null : \Illuminate\Support\Carbon::parse('2024-02-12')" />
        <x-nexus::description-item label="Harga" :value="$empty ? null : 'Rp 14.750.000'" />
        <x-nexus::description-item label="Dapat Dipinjam" :value="$empty ? null : true" />
        <x-nexus::description-item label="Nomor Seri" :value="$empty ? null : 'DL5420-7HX2-9Q1K-PLM3-ZZ81-AB04'" />
        <x-nexus::description-item label="Deskripsi" full :value="$empty ? null : 'Laptop operasional staf keuangan; termasuk tas, charger 65W, dan dok USB-C. Garansi resmi berakhir Februari 2027.'" />
    </x-nexus::description-list>

    <x-slot:aside>
        <x-nexus::card title="Label QR">
            <div class="demo-qr" aria-hidden="true"></div>
            <x-nexus::description-list cols="1" layout="horizontal" class="mt-4">
                <x-nexus::description-item label="Dicetak" value="3 kali" />
                <x-nexus::description-item label="Terakhir" value="21/10/2026" />
            </x-nexus::description-list>
        </x-nexus::card>
    </x-slot:aside>

    <x-slot:tabs>
        <x-nexus::tabs :tabs="['riwayat' => 'Riwayat', 'dokumen' => 'Dokumen']" active="riwayat">
            <x-nexus::tab-pane name="riwayat" active>
                @if(count($riwayat))
                    <x-nexus::activity>
                        @foreach($riwayat as [$tgl, $judul, $ket, $warna])
                            <x-nexus::activity-item :color="$warna" :time="\Illuminate\Support\Carbon::parse($tgl)->format('d/m/Y')"><strong>{{ $judul }}</strong> — {{ $ket }}</x-nexus::activity-item>
                        @endforeach
                    </x-nexus::activity>
                @else
                    <x-nexus::empty-state icon="🗂️" title="Belum ada riwayat" desc="Perawatan, mutasi, dan peminjaman akan tercatat di sini." />
                @endif
            </x-nexus::tab-pane>
            <x-nexus::tab-pane name="dokumen">
                <x-nexus::empty-state icon="📄" title="Belum ada dokumen" />
            </x-nexus::tab-pane>
        </x-nexus::tabs>
    </x-slot:tabs>
</x-nexus::detail-page>

@push('styles')
<style>
    /* Hanya untuk demo: placeholder gambar QR. */
    .demo-qr { aspect-ratio: 1; max-width: 180px; margin: 0 auto; border-radius: 8px;
        background: repeating-conic-gradient(var(--text) 0 25%, var(--card) 0 50%) 0 0 / 22px 22px; }
</style>
@endpush
@endsection
