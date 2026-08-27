{{--
  SKELETON: Page Show / Detail
  Halaman read-only untuk melihat satu record. Layout: label di atas, value di bawah,
  grid 2 kolom via .grid-cols-2 (utility class), field penuh pakai .col-span-full.
--}}
@extends('nexus::layouts.app')

@section('title', '{{-- PLACEHOLDER: mis. Detail Pengguna --}}')

@section('content')

{{-- 1. PAGE HEADER — aksi utama di sini biasanya "Edit" --}}
<x-nexus::page-header
    title="{{-- PLACEHOLDER: mis. Detail Pengguna --}}"
    :breadcrumb="[
        ['label' => 'Dashboard', 'url' => url('/')],
        ['label' => '{{-- PLACEHOLDER: label index --}}', 'url' => route('{{-- PLACEHOLDER: name.index --}}')],
        ['label' => 'Detail'],
    ]">
    <x-slot:actions>
        <x-nexus::button variant="outline" href="{{ route('{{-- PLACEHOLDER: name.index --}}') }}">
            Kembali
        </x-nexus::button>
        <x-nexus::button href="{{ route('{{-- PLACEHOLDER: name.edit --}}', $item) }}">
            <x-nexus::icons.edit :size="14" /> Edit
        </x-nexus::button>
    </x-slot:actions>
</x-nexus::page-header>

{{-- 2. DETAIL CARD — grid 2 kolom, field label+value --}}
<x-nexus::card>
    <div class="d-grid grid-cols-2 gap-y-4 gap-x-3">

        {{-- PLACEHOLDER: ulangi blok ini per field. Field panjang (alamat, deskripsi) tambahkan class="col-span-full" --}}
        <div>
            <div class="form-label">{{-- PLACEHOLDER: label field --}}</div>
            <div>{{ $item->{{-- PLACEHOLDER: nama field --}} }}</div>
        </div>

        <div>
            <div class="form-label">Status</div>
            <div><x-nexus::badge color="green">{{-- PLACEHOLDER --}}</x-nexus::badge></div>
        </div>

    </div>
</x-nexus::card>

@endsection
