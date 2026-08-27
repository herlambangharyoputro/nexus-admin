{{--
  SKELETON: Page Form (Create & Edit)
  Satu file untuk create DAN edit — dibedakan lewat pengecekan isset($item).
  Copy file ini per resource, isi bagian {{-- PLACEHOLDER --}}.
  Layout SATU KOLOM — field berurutan ke bawah, JANGAN pakai .form-grid.
--}}
@extends('nexus::layouts.app')

@section('title', isset($item) ? 'Edit {{-- PLACEHOLDER: nama resource --}}' : 'Tambah {{-- PLACEHOLDER: nama resource --}}')

@section('content')

{{-- 1. PAGE HEADER --}}
<x-nexus::page-header
    :title="isset($item) ? 'Edit {{-- PLACEHOLDER --}}' : 'Tambah {{-- PLACEHOLDER --}}'"
    :breadcrumb="[
        ['label' => 'Dashboard', 'url' => url('/')],
        ['label' => '{{-- PLACEHOLDER: label index, mis. Pengguna --}}', 'url' => route('{{-- PLACEHOLDER: name.index --}}')],
        ['label' => isset($item) ? 'Edit' : 'Tambah'],
    ]" />

{{-- 2. FORM CARD — satu kolom, field berurutan ke bawah --}}
<x-nexus::card>
    <form method="POST"
          action="{{ isset($item) ? route('{{-- PLACEHOLDER: name.update --}}', $item) : route('{{-- PLACEHOLDER: name.store --}}') }}">
        @csrf
        @if(isset($item)) @method('PUT') @endif

        {{-- PLACEHOLDER: ulangi form.group ini per field, urutan sesuai kebutuhan resource --}}
        <x-nexus::form.group label="{{-- PLACEHOLDER: label field --}}" for="{{-- PLACEHOLDER: name --}}"
            required :error="$errors->first('{{-- PLACEHOLDER: name --}}')">
            <x-nexus::form.input
                id="{{-- PLACEHOLDER: name --}}"
                name="{{-- PLACEHOLDER: name --}}"
                :value="old('{{-- PLACEHOLDER: name --}}', $item->{{-- PLACEHOLDER: name --}} ?? null)"
                placeholder="{{-- PLACEHOLDER --}}" />
        </x-nexus::form.group>

        <div class="mt-4">
            {{-- PLACEHOLDER: contoh field select — hapus kalau tidak perlu --}}
            <x-nexus::form.group label="{{-- PLACEHOLDER --}}" for="{{-- PLACEHOLDER --}}"
                :error="$errors->first('{{-- PLACEHOLDER --}}')">
                <x-nexus::form.select
                    id="{{-- PLACEHOLDER --}}" name="{{-- PLACEHOLDER --}}"
                    :options="['{{-- PLACEHOLDER: value --}}' => '{{-- PLACEHOLDER: label --}}']"
                    :selected="old('{{-- PLACEHOLDER --}}', $item->{{-- PLACEHOLDER --}} ?? null)"
                    placeholder="Pilih {{-- PLACEHOLDER --}}" />
            </x-nexus::form.group>
        </div>

        <div class="mt-4">
            {{-- PLACEHOLDER: contoh field textarea — hapus kalau tidak perlu --}}
            <x-nexus::form.group label="{{-- PLACEHOLDER --}}" for="{{-- PLACEHOLDER --}}"
                :error="$errors->first('{{-- PLACEHOLDER --}}')">
                <x-nexus::form.textarea
                    id="{{-- PLACEHOLDER --}}" name="{{-- PLACEHOLDER --}}" rows="4"
                    :value="old('{{-- PLACEHOLDER --}}', $item->{{-- PLACEHOLDER --}} ?? null)" />
            </x-nexus::form.group>
        </div>

        {{-- 3. FORM ACTIONS — selalu di bawah, rata kanan. Cancel kembali ke index --}}
        <div class="d-flex justify-end gap-2 mt-5">
            <x-nexus::button variant="outline" href="{{ route('{{-- PLACEHOLDER: name.index --}}') }}">
                Batal
            </x-nexus::button>
            <x-nexus::button type="submit">
                {{ isset($item) ? 'Perbarui' : 'Simpan' }}
            </x-nexus::button>
        </div>
    </form>
</x-nexus::card>

@endsection
