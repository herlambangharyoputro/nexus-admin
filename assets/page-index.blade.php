{{--
  SKELETON: Page Index / List
  Gunakan file ini sebagai TITIK AWAL untuk setiap halaman index/list baru.
  JANGAN menyusun struktur dari nol — copy file ini, lalu isi bagian
  yang ditandai {{-- PLACEHOLDER --}}. Jangan ubah urutan/struktur elemen.
--}}
@extends('nexus::layouts.app')

@section('title', '{{-- PLACEHOLDER: judul tab browser, mis. Pengguna --}}')

@section('content')

{{-- 1. PAGE HEADER — WAJIB: breadcrumb + judul + deskripsi + aksi utama --}}
<x-nexus::page-header
    title="{{-- PLACEHOLDER: judul halaman --}}"
    description="{{-- PLACEHOLDER: deskripsi singkat, boleh dihapus prop ini kalau tidak perlu --}}"
    :breadcrumb="[
        ['label' => 'Dashboard', 'url' => url('/')],
        ['label' => '{{-- PLACEHOLDER: label halaman ini --}}'],
    ]">
    <x-slot:actions>
        <x-nexus::button data-modal-open="{{-- PLACEHOLDER: id modal-form tambah, ATAU ganti href ke route create --}}">
            <x-nexus::icons.plus :size="14" /> {{-- PLACEHOLDER: label tombol, mis. "Tambah Pengguna" --}}
        </x-nexus::button>
    </x-slot:actions>
</x-nexus::page-header>

{{-- 2. (OPSIONAL) SUMMARY CARDS — hapus blok ini kalau halaman tidak butuh ringkasan angka --}}
{{--
<div class="stats-grid mb-5">
    <x-nexus::summary-card label="{{-- PLACEHOLDER --}}" value="{{-- PLACEHOLDER --}}" icon-color="primary">
        <x-slot:icon><x-nexus::icons.package /></x-slot:icon>
    </x-nexus::summary-card>
</div>
--}}

{{-- 3. DATATABLE — WAJIB: client-side (semua baris di-render, search/sort di browser via data-table-search & data-sort-table) --}}
<x-nexus::datatable title="{{-- PLACEHOLDER: judul di atas tabel, mis. 'Daftar Pengguna' --}}" sortable class="mb-5">
    {{-- Tombol sekunder di bar kontrol (kanan atas tabel), mis. Export. Hapus slot ini kalau tidak perlu --}}
    <x-slot:actions>
        <x-nexus::button variant="outline" size="sm">
            <x-nexus::icons.download :size="14" /> Export
        </x-nexus::button>
    </x-slot:actions>

    <x-slot:head>
        {{-- PLACEHOLDER: satu <th data-sort="key"> per kolom yang bisa di-sort --}}
        <th data-sort="{{-- PLACEHOLDER --}}">{{-- PLACEHOLDER: nama kolom --}}</th>
        <th>Aksi</th>
    </x-slot:head>

    @foreach($items as $item)
    <tr>
        {{-- PLACEHOLDER: satu <td> per kolom, urutan HARUS sama dengan <th> di atas --}}
        <td>{{ $item->{'PLACEHOLDER'} }}</td>

        <td>
            {{--
              ATURAN: kolom sedikit (≤4 kolom data) → pakai table-actions (icon button).
                      kolom banyak (>4 kolom data)  → pakai dropdown.
              Delete SELALU variant danger — jangan diubah.
            --}}

            {{-- opsi A: sedikit kolom --}}
            <x-nexus::table-actions
                :edit="route('{{-- PLACEHOLDER: name.edit --}}', $item)"
                :delete="route('{{-- PLACEHOLDER: name.destroy --}}', $item)" />

            {{-- opsi B: banyak kolom — hapus opsi A di atas kalau pakai ini
            <x-nexus::dropdown align="right">
                <a class="dropdown-item" href="{{ route('{{-- PLACEHOLDER: name.edit --}}', $item) }}">
                    <x-nexus::icons.edit :size="16" /> Edit
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('{{-- PLACEHOLDER: name.destroy --}}', $item) }}" data-confirm="Hapus item ini?">
                    @csrf @method('DELETE')
                    <button type="submit" class="dropdown-item danger">
                        <x-nexus::icons.trash :size="16" /> Hapus
                    </button>
                </form>
            </x-nexus::dropdown>
            --}}
        </td>
    </tr>
    @endforeach
</x-nexus::datatable>

{{-- 4. EMPTY STATE — tampilkan kalau $items kosong. Bungkus blok datatable di atas dengan @if($items->isNotEmpty()) ... @else ini @endif --}}
{{--
<x-nexus::empty-state title="{{-- PLACEHOLDER: mis. Belum ada pengguna --}}" desc="{{-- PLACEHOLDER --}}">
    <x-nexus::button><x-nexus::icons.plus :size="14" /> Tambah</x-nexus::button>
</x-nexus::empty-state>
--}}

@endsection
