{{--
  Demo Pola Form — satu _form untuk create & edit via x-nexus::form-page (tanpa database).
  Daftarkan: Route::view('/nexus-demo/form', 'nexus::pages.demo-form');

  Keadaan (query string):
    (default)      tambah (POST, "Simpan")
    ?mode=edit     edit (PUT, "Simpan Perubahan", nilai dari model, aside pratinjau)
    ?state=error   error validasi: ringkasan di atas + error per field + is-invalid
  Form mengirim POST ke URL ini; tanpa route POST, submit akan 405 (demo tampilan saja).
--}}
@extends('nexus::layouts.app')

@section('title', 'Demo Form')
@section('page-title', 'Demo Pola')
@section('breadcrumb', 'Form')

@section('content')
@php
    $isEdit = request('mode') === 'edit';
    $lokasi = $isEdit ? (object) [
        'exists'        => true,
        'kode'          => 'GD-A-LT1-R01',
        'nama'          => 'Ruang Rapat Lobby',
        'gedung'        => 'Gedung A',
        'lantai'        => 'Lantai 1',
        'kapasitas'     => 12,
        'jenis'         => 'rapat',
        'kondisi'       => 'baik',
        'bisa_dipinjam' => true,
        'deskripsi'     => 'Dekat resepsionis, ada proyektor & papan tulis.',
    ] : null;

    if (request('state') === 'error') {
        app('view')->share('errors', (new \Illuminate\Support\ViewErrorBag)->put('default', new \Illuminate\Support\MessageBag([
            'kode'      => ['Kode sudah dipakai lokasi lain.'],
            'nama'      => ['Nama ruangan wajib diisi.'],
            'kapasitas' => ['Kapasitas minimal 1 orang.'],
            'kondisi'   => ['Pilih kondisi ruangan.'],
            'foto'      => ['Ukuran foto maksimal 3 MB.'],
        ])));
    }
@endphp

<div class="d-flex flex-wrap gap-2 mb-4" aria-label="Keadaan demo">
    <x-nexus::chip :active="!$isEdit && request('state') !== 'error'"><a href="{{ request()->url() }}">Tambah</a></x-nexus::chip>
    <x-nexus::chip :active="$isEdit && request('state') !== 'error'"><a href="{{ request()->url() }}?mode=edit">Edit</a></x-nexus::chip>
    <x-nexus::chip :active="request('state') === 'error'"><a href="{{ request()->url() }}?state=error">Error validasi</a></x-nexus::chip>
</div>

<x-nexus::form-page
    :title="$isEdit ? 'Edit Lokasi / Ruangan' : 'Tambah Lokasi / Ruangan'"
    description="Contoh satu file _form untuk create & edit."
    :action="request()->url()"
    :model="$lokasi"
    cancel="#index"
    size="md" files>

    <x-nexus::form.group label="Kode" name="kode" required hint="Contoh: GD-A-LT1-R01 (maks 20 karakter).">
        <x-nexus::form.input name="kode" maxlength="20" placeholder="GD-A-LT1-R01" />
    </x-nexus::form.group>

    <x-nexus::form.group label="Nama Ruangan" name="nama" required>
        <x-nexus::form.input name="nama" placeholder="Ruang Tamu Lobby" />
    </x-nexus::form.group>

    <x-nexus::form-grid>
        <x-nexus::form.group label="Gedung" name="gedung">
            <x-nexus::form.input name="gedung" placeholder="Gedung A" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Lantai" name="lantai">
            <x-nexus::form.input name="lantai" placeholder="Lantai 1" />
        </x-nexus::form.group>
    </x-nexus::form-grid>

    <x-nexus::form-grid>
        <x-nexus::form.group label="Jenis" name="jenis">
            <x-nexus::form.select name="jenis" placeholder="Pilih jenis"
                :options="['rapat' => 'Ruang Rapat', 'kelas' => 'Kelas', 'gudang' => 'Gudang']" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Kapasitas (orang)" name="kapasitas">
            <x-nexus::form.input name="kapasitas" type="number" min="1" />
        </x-nexus::form.group>
    </x-nexus::form-grid>

    <x-nexus::form.group label="Kondisi" name="kondisi" required>
        <x-nexus::form.radio-group name="kondisi" inline
            :options="['baik' => 'Baik', 'rusak_ringan' => 'Rusak Ringan', 'rusak_berat' => 'Rusak Berat']" />
    </x-nexus::form.group>

    <x-nexus::form.group label="Peminjaman" name="bisa_dipinjam">
        <x-nexus::form.checkbox name="bisa_dipinjam" label="Ya, boleh diajukan peminjaman sebagai ruangan" unchecked="0" />
    </x-nexus::form.group>

    <x-nexus::form.group label="Deskripsi" name="deskripsi">
        <x-nexus::form.textarea name="deskripsi" rows="3" />
    </x-nexus::form.group>

    <x-nexus::form.group label="Foto Ruangan" name="foto">
        <x-nexus::form.file name="foto" accept="image/jpeg,image/png" formats="JPG/PNG" max-size="3 MB" preview />
    </x-nexus::form.group>
</x-nexus::form-page>
@endsection
