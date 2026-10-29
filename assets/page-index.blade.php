{{--
  SKELETON: Page Index / List (v0.5 — x-nexus::index-page, server-side)
  Copy file ini per resource → resources/views/{resource}/index.blade.php,
  lalu isi HANYA bagian {{-- PLACEHOLDER --}}. Jangan ubah urutan/struktur.
  DILARANG style inline untuk layout & <style> per halaman.

  Controller (contoh, WAJIB whitelist kolom sortir):
    $sortable = ['nama' => 'nama', 'kode' => 'kode'];
    $items = Model::query()
        ->when($request->search, fn ($q, $s) => $q->where('nama', 'like', "%{$s}%"))
        ->when($request->kategori_id, fn ($q, $k) => $q->where('kategori_id', $k))
        ->orderBy($sortable[$request->sort] ?? 'created_at', $request->dir === 'desc' ? 'desc' : 'asc')
        ->paginate(15);
--}}
@extends('nexus::layouts.app')

@section('title', '{{-- PLACEHOLDER: judul tab, mis. Aset --}}')
@section('page-title', '{{-- PLACEHOLDER: judul topbar, mis. Aset --}}')

@section('content')

{{-- 1. (OPSIONAL) RINGKASAN KPI — hapus bila tidak perlu --}}
{{--
<div class="stats-grid">
    <x-nexus::stat-card label="{{-- PLACEHOLDER --}}" value="{{-- PLACEHOLDER --}}" icon-color="primary">
        <x-slot:icon><x-nexus::icons.package /></x-slot:icon>
    </x-nexus::stat-card>
</div>
--}}

{{-- 2. INDEX PAGE — header + toolbar + tabel + empty state + flash + pagination --}}
<x-nexus::index-page
    title="{{-- PLACEHOLDER: mis. Daftar Aset --}}"
    :count="true"
    :paginator="$items"
    :columns="[
        ['label' => '{{-- PLACEHOLDER: kolom utama --}}', 'sort' => '{{-- PLACEHOLDER: kunci sortir --}}'],
        '{{-- PLACEHOLDER: kolom biasa --}}',
        ['label' => 'Aksi'],
    ]"
    search-placeholder="{{-- PLACEHOLDER: mis. Nama / kode… --}}"
    :create-url="route('{{-- PLACEHOLDER: name.create --}}')"
    create-label="Tambah {{-- PLACEHOLDER: Resource --}}"
    empty="Belum ada data {{-- PLACEHOLDER: resource --}}.">

    {{-- (OPSIONAL) filter tambahan — name = parameter query --}}
    <x-slot:filters>
        <x-nexus::form.select name="{{-- PLACEHOLDER --}}" :options="{{-- PLACEHOLDER: [value => label] --}}[]"
            placeholder="Semua {{-- PLACEHOLDER --}}" :selected="request('{{-- PLACEHOLDER --}}')" aria-label="{{-- PLACEHOLDER --}}" />
    </x-slot:filters>

    {{-- (OPSIONAL) aksi sekunder di header (Export/Impor) — hapus bila tidak perlu --}}
    {{--
    <x-slot:actions>
        <x-nexus::row-actions label="Lainnya" :icon="false">
            <a class="dropdown-item" role="menuitem" href="{{ route('{{-- PLACEHOLDER --}}') }}">Export Excel</a>
        </x-nexus::row-actions>
    </x-slot:actions>
    --}}

    @foreach($items as $item)
        <tr>
            {{-- PLACEHOLDER: satu <td> per kolom, urutan SAMA dengan :columns. Kolom pertama = judul kartu di HP --}}
            <td>{{ $item->{{-- PLACEHOLDER --}} }}</td>
            <td>{{ $item->{{-- PLACEHOLDER --}} }}</td>
            <td class="cell-end">
                {{-- Kolom aksi SELALU row-actions. Hapus selalu lewat prop delete (danger + konfirmasi). --}}
                <x-nexus::row-actions
                    :view="route('{{-- PLACEHOLDER: name.show --}}', $item)"
                    :edit="route('{{-- PLACEHOLDER: name.edit --}}', $item)"
                    :delete="route('{{-- PLACEHOLDER: name.destroy --}}', $item)"
                    delete-confirm="Hapus {{-- PLACEHOLDER --}} ini?" />
            </td>
        </tr>
    @endforeach
</x-nexus::index-page>

@endsection
