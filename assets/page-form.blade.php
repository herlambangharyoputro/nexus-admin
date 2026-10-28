{{--
  SKELETON: Page Form (Create & Edit) — v0.5, x-nexus::form-page
  SATU file untuk create DAN edit → resources/views/{resource}/form.blade.php

  Controller:
    public function create() { return view('{resource}.form'); }
    public function edit(Model $item) { return view('{resource}.form', compact('item')); }
  (Bila memakai @include('{resource}._form') dari create/edit yang terpisah, isi
   file _form dengan blok x-nexus::form-page di bawah — tetap satu sumber.)

  Otomatis dari komponen (JANGAN ditulis manual):
    @csrf, @method('PUT') saat edit, nilai old() → model, error per field,
    is-invalid, ringkasan error di atas, tombol "Simpan" / "Simpan Perubahan" + "Batal".
  Layout SATU KOLOM. x-nexus::form-grid HANYA untuk pasangan field pendek yang berkaitan
  (GUIDELINES §9). DILARANG style inline untuk layout & <style> per halaman.
--}}
@extends('nexus::layouts.app')

@section('title', isset($item) ? 'Edit {{-- PLACEHOLDER: Resource --}}' : 'Tambah {{-- PLACEHOLDER: Resource --}}')
@section('page-title', '{{-- PLACEHOLDER: Resource --}}')
@section('breadcrumb', isset($item) ? 'Edit' : 'Tambah')

@section('content')

<x-nexus::form-page
    :title="isset($item) ? 'Edit {{-- PLACEHOLDER --}}' : 'Tambah {{-- PLACEHOLDER --}}'"
    :action="isset($item) ? route('{{-- PLACEHOLDER: name.update --}}', $item) : route('{{-- PLACEHOLDER: name.store --}}')"
    :model="$item ?? null"
    :cancel="route('{{-- PLACEHOLDER: name.index --}}')"
    size="md">  {{-- sm (≤4 field pendek) | md (default) | lg (form-grid cols=3) ; tambah prop files bila ada form.file --}}

    {{-- PLACEHOLDER: ulangi per field. name di group & kontrol HARUS sama. --}}
    <x-nexus::form.group label="{{-- PLACEHOLDER --}}" name="{{-- PLACEHOLDER --}}" required>
        <x-nexus::form.input name="{{-- PLACEHOLDER --}}" placeholder="{{-- PLACEHOLDER --}}" />
    </x-nexus::form.group>

    <x-nexus::form.group label="{{-- PLACEHOLDER --}}" name="{{-- PLACEHOLDER --}}">
        <x-nexus::form.select name="{{-- PLACEHOLDER --}}" :options="{{-- PLACEHOLDER: [value => label] --}}[]" placeholder="Pilih {{-- PLACEHOLDER --}}" />
    </x-nexus::form.group>

    {{-- (OPSIONAL) pasangan field pendek — runtuh ke 1 kolom di HP --}}
    {{--
    <x-nexus::form-grid>
        <x-nexus::form.group label="Mulai" name="mulai"><x-nexus::form.input name="mulai" type="date" /></x-nexus::form.group>
        <x-nexus::form.group label="Selesai" name="selesai"><x-nexus::form.input name="selesai" type="date" /></x-nexus::form.group>
    </x-nexus::form-grid>
    --}}

    <x-nexus::form.group label="{{-- PLACEHOLDER --}}" name="{{-- PLACEHOLDER --}}">
        <x-nexus::form.textarea name="{{-- PLACEHOLDER --}}" rows="4" />
    </x-nexus::form.group>

    {{-- Kontrol lain: form.checkbox, form.radio-group, form.file (lihat GUIDELINES §9) --}}
</x-nexus::form-page>

@endsection
