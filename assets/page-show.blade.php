{{--
  SKELETON: Page Show / Detail — v0.5, x-nexus::detail-page + description-list
  Copy → resources/views/{resource}/show.blade.php, isi bagian {{-- PLACEHOLDER --}}.
  Nilai kosong otomatis tampil "—". DILARANG style inline untuk layout.
--}}
@extends('nexus::layouts.app')

@section('title', '{{-- PLACEHOLDER: mis. Detail Aset --}}')
@section('page-title', '{{-- PLACEHOLDER: Resource --}}')
@section('breadcrumb', 'Detail')

@section('content')

<x-nexus::detail-page
    :title="$item->{{-- PLACEHOLDER: field judul --}}"
    :description="$item->{{-- PLACEHOLDER: kode/sub-judul, boleh dihapus --}}"
    :back="route('{{-- PLACEHOLDER: name.index --}}')">

    <x-slot:actions>
        <x-nexus::button variant="outline" :href="route('{{-- PLACEHOLDER: name.edit --}}', $item)">
            <x-nexus::icons.edit :size="14" /> Edit
        </x-nexus::button>
        <x-nexus::row-actions label="Aksi lain"
            :delete="route('{{-- PLACEHOLDER: name.destroy --}}', $item)"
            delete-confirm="Hapus {{-- PLACEHOLDER --}} ini?" />
    </x-slot:actions>

    <x-nexus::description-list>
        {{-- PLACEHOLDER: ulangi per field. Nilai panjang (alamat, deskripsi) → full --}}
        <x-nexus::description-item label="{{-- PLACEHOLDER --}}" :value="$item->{{-- PLACEHOLDER --}}" />
        <x-nexus::description-item label="Status">
            <x-nexus::badge color="green">{{-- PLACEHOLDER --}}</x-nexus::badge>
        </x-nexus::description-item>
        <x-nexus::description-item label="{{-- PLACEHOLDER --}}" full :value="$item->{{-- PLACEHOLDER --}}" />
    </x-nexus::description-list>

    {{-- (OPSIONAL) kolom samping ≥ 900px: foto, QR, ringkasan --}}
    {{--
    <x-slot:aside>
        <x-nexus::card title="Foto"><img src="{{ $item->foto_url }}" alt=""></x-nexus::card>
    </x-slot:aside>
    --}}

    {{-- (OPSIONAL) riwayat / tab di kartu bawah --}}
    {{--
    <x-slot:tabs>
        <x-nexus::tabs :tabs="['riwayat' => 'Riwayat']" active="riwayat">
            <x-nexus::tab-pane name="riwayat" active>…</x-nexus::tab-pane>
        </x-nexus::tabs>
    </x-slot:tabs>
    --}}
</x-nexus::detail-page>

@endsection
