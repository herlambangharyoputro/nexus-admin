{{--
  Demo Pola Index — x-nexus::index-page dengan data contoh (tanpa database).
  Daftarkan: Route::view('/nexus-demo/index', 'nexus::pages.demo-index');

  Keadaan (query string):
    (default)      normal, 23 baris, 8 per halaman
    ?search=…      cari (cocok / tidak cocok)
    ?state=empty   belum ada data sama sekali
    ?state=flash   pesan flash sukses
    ?sort=&dir=    sortir kolom
--}}
@extends('nexus::layouts.app')

@section('title', 'Demo Index')
@section('page-title', 'Demo Pola')
@section('breadcrumb', 'Index')

@section('content')
@php
    $state = request('state');
    $names = ['Laptop Dell Latitude 5420', 'Proyektor Epson EB-X51', 'Meja Rapat Jati 2,4 m', 'Kursi Ergonomis', 'AC Daikin 1 PK', 'Printer HP LaserJet M404dn'];
    $kategori = ['elektronik' => 'Elektronik', 'mebel' => 'Mebel', 'kendaraan' => 'Kendaraan'];
    $lokasi = ['Gedung A · Lt 1 · Lobby', 'Gedung B · Lt 3 · Ruang Rapat Utama', 'Gudang'];
    $kondisi = [['Baik', 'green'], ['Rusak Ringan', 'amber'], ['Rusak Berat', 'red']];

    $rows = collect(range(1, 23))->map(fn ($i) => [
        'id'       => $i,
        'kode'     => sprintf('AST-2026-%04d', $i),
        'nama'     => $names[$i % count($names)],
        'kategori' => array_keys($kategori)[$i % 3],
        'lokasi'   => $lokasi[$i % 3],
        'kondisi'  => $kondisi[$i % 7 === 0 ? 2 : ($i % 4 === 0 ? 1 : 0)],
        'harga'    => 1250000 * (($i % 9) + 1),
    ]);
    if ($state === 'empty') $rows = collect();
    if ($state === 'flash') session()->now('success', 'Aset "Laptop Dell Latitude 5420" berhasil disimpan.');

    if ($q = trim((string) request('search'))) {
        $rows = $rows->filter(fn ($r) => str_contains(mb_strtolower($r['nama'] . ' ' . $r['kode']), mb_strtolower($q)));
    }
    if ($k = request('kategori')) $rows = $rows->where('kategori', $k);

    $sortable = ['nama' => 'nama', 'kode' => 'kode', 'harga' => 'harga'];   // whitelist
    if (isset($sortable[request('sort')])) {
        $rows = $rows->sortBy($sortable[request('sort')], SORT_NATURAL, request('dir') === 'desc');
    }

    $page = max(1, (int) request('page', 1));
    $asets = new \Illuminate\Pagination\LengthAwarePaginator(
        $rows->forPage($page, 8)->values(), $rows->count(), 8, $page, ['path' => request()->url()]
    );
@endphp

<div class="d-flex flex-wrap gap-2 mb-4" aria-label="Keadaan demo">
    <x-nexus::chip :active="!$state && !request('search')"><a href="{{ request()->url() }}">Normal</a></x-nexus::chip>
    <x-nexus::chip :active="request('search') === 'zzz'"><a href="{{ request()->url() }}?search=zzz">Tidak cocok</a></x-nexus::chip>
    <x-nexus::chip :active="$state === 'empty'"><a href="{{ request()->url() }}?state=empty">Kosong</a></x-nexus::chip>
    <x-nexus::chip :active="$state === 'flash'"><a href="{{ request()->url() }}?state=flash">Flash</a></x-nexus::chip>
</div>

<div class="stats-grid">
    <x-nexus::stat-card label="Total Aset" value="23" icon-color="primary"><x-slot:icon><x-nexus::icons.package /></x-slot:icon></x-nexus::stat-card>
    <x-nexus::stat-card label="Total Nilai" value="Rp 143,7 jt" icon-color="green"><x-slot:icon><x-nexus::icons.shopping-bag /></x-slot:icon></x-nexus::stat-card>
    <x-nexus::stat-card label="Kondisi Baik" value="17" icon-color="green"><x-slot:icon><x-nexus::icons.home /></x-slot:icon></x-nexus::stat-card>
    <x-nexus::stat-card label="Perlu Perhatian" value="6" icon-color="amber"><x-slot:icon><x-nexus::icons.settings /></x-slot:icon></x-nexus::stat-card>
</div>

<x-nexus::index-page
    title="Daftar Aset" :count="true" :paginator="$asets"
    :columns="[
        ['label' => 'Aset', 'sort' => 'nama'],
        ['label' => 'Kode', 'sort' => 'kode'],
        'Kategori',
        'Lokasi',
        'Kondisi',
        ['label' => 'Harga', 'sort' => 'harga', 'align' => 'end'],
        ['label' => 'Aksi'],
    ]"
    search-placeholder="Nama / kode aset…"
    create-url="#tambah" create-label="Tambah Aset"
    empty="Belum ada data aset.">

    <x-slot:filters>
        <x-nexus::form.select name="kategori" :options="$kategori" placeholder="Semua Kategori"
            :selected="request('kategori')" aria-label="Kategori" />
    </x-slot:filters>

    <x-slot:actions>
        <x-nexus::row-actions label="Lainnya" :icon="false">
            <a class="dropdown-item" role="menuitem" href="#export"><x-nexus::icons.download :size="16" /> Export Excel</a>
            <button type="button" class="dropdown-item" role="menuitem" data-modal-open="demoImpor"><x-nexus::icons.plus :size="16" /> Impor cepat</button>
        </x-nexus::row-actions>
    </x-slot:actions>

    @foreach($asets as $a)
        <tr>
            <td>{{ $a['nama'] }}</td>
            <td><code>{{ $a['kode'] }}</code></td>
            <td><x-nexus::badge>{{ $kategori[$a['kategori']] }}</x-nexus::badge></td>
            <td>{{ $a['lokasi'] }}</td>
            <td><x-nexus::badge :color="$a['kondisi'][1]">{{ $a['kondisi'][0] }}</x-nexus::badge></td>
            <td class="cell-end">Rp {{ number_format($a['harga'], 0, ',', '.') }}</td>
            <td class="cell-end">
                <x-nexus::row-actions view="#detail" edit="#edit" delete="#hapus"
                    delete-confirm="Hapus aset {{ $a['kode'] }}?" />
            </td>
        </tr>
    @endforeach
</x-nexus::index-page>

<x-nexus::modal-form id="demoImpor" title="Impor Cepat" action="#" submit-label="Impor" width="480px">
    <x-nexus::form.group label="Berkas Excel" name="berkas" required>
        <x-nexus::form.file name="berkas" accept=".xlsx" formats="XLSX" max-size="5 MB" />
    </x-nexus::form.group>
</x-nexus::modal-form>
@endsection
