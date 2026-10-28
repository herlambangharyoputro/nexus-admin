# Nexus Admin — Patterns (Cookbook Halaman)

Resep **copy-paste tingkat halaman** yang dirangkai dari komponen `<x-nexus::*>`.
Tujuannya: pola desain konsisten & deterministik — agent (Cowork/Sonnet) cukup
mengikuti resep, bukan menyusun markup dari nol.

Prasyarat: lihat `GUIDELINES.md` (kontrak komponen) & `CHARTS.md` (chart/lib).

> **Riwayat perubahan pola:** v0.5 menjadikan `index-page`, `form-page`, dan
> `detail-page` satu-satunya pola halaman (server-side, responsive-first).
> `datatable` & `table-actions` deprecated (tetap berfungsi). Aturan lengkap:
> `GUIDELINES.md` §9 (anatomi) & §10 (kontrak responsive).

---

## 0. Kerangka halaman

Setiap halaman dalam shell admin meng-`@extends('nexus::layouts.app')`:

```blade
@extends('nexus::layouts.app')
@section('title', 'Judul Tab')
@section('page-title', 'Judul Topbar')
@section('content')
    {{-- index-page | form-page | detail-page --}}
@endsection
```

Layout sudah memasang `<x-nexus::confirm-dialog />` dan `<x-nexus::lightbox />`.
Konten aplikasi di shell (logo, nav, menu user, favicon) lewat titik ekstensi
`resources/views/nexus/*.blade.php` — lihat `GUIDELINES.md` §6.

---

## 1. Halaman List / Index — `index-page`

**Server-side**: cari, filter, sortir, dan paginasi oleh controller.
**Tambah data = halaman terpisah** (`{resource}.create`), bukan modal.

```php
// Controller
public function index(Request $request)
{
    $sortable = ['nama' => 'nama', 'kode' => 'kode_aset', 'harga' => 'harga_total']; // WAJIB whitelist

    $asets = Aset::query()
        ->when($request->search, fn ($q, $s) => $q->where(fn ($w) => $w->where('nama', 'like', "%{$s}%")->orWhere('kode_aset', 'like', "%{$s}%")))
        ->when($request->kategori_id, fn ($q, $id) => $q->where('kategori_id', $id))
        ->orderBy($sortable[$request->sort] ?? 'created_at', $request->dir === 'desc' ? 'desc' : 'asc')
        ->paginate(15);

    return view('aset.index', ['asets' => $asets, 'kategoriOptions' => Kategori::pluck('nama', 'id')]);
}
```

```blade
<x-nexus::index-page
    title="Daftar Aset" :count="true" :paginator="$asets"
    :columns="[
        ['label' => 'Aset', 'sort' => 'nama'],
        ['label' => 'Kode', 'sort' => 'kode'],
        'Lokasi',
        ['label' => 'Harga', 'sort' => 'harga', 'align' => 'end'],
        ['label' => 'Aksi'],
    ]"
    search-placeholder="Nama / kode aset…"
    :create-url="route('aset.create')" create-label="Tambah Aset"
    empty="Belum ada data aset.">

    <x-slot:filters>
        <x-nexus::form.select name="kategori_id" :options="$kategoriOptions"
            placeholder="Semua Kategori" :selected="request('kategori_id')" aria-label="Kategori" />
    </x-slot:filters>

    @foreach($asets as $a)
        <tr>
            <td>{{ $a->nama }}</td>
            <td><code>{{ $a->kode_aset }}</code></td>
            <td>{{ $a->lokasi?->nama }}</td>
            <td class="cell-end">{{ $a->harga_total_format }}</td>
            <td class="cell-end">
                <x-nexus::row-actions :view="route('aset.show', $a)" :edit="route('aset.edit', $a)"
                    :delete="route('aset.destroy', $a)" delete-confirm="Hapus aset {{ $a->kode_aset }}?" />
            </td>
        </tr>
    @endforeach
</x-nexus::index-page>
```

Yang terjadi otomatis: tombol Tambah, toolbar (cari + filter + Cari/Reset), header
sortir dengan `aria-sort`, empty state "belum ada data" vs "tidak cocok", flash
sesi, pagination membawa query string, dan **kartu per baris di HP**.

Pagination `->links()` di tempat lain: `Paginator::defaultView('nexus::pagination.links')`.

---

## 2. Halaman Detail — `detail-page`

```blade
<x-nexus::detail-page :title="$aset->nama" :description="$aset->kode_aset" :back="route('aset.index')">
    <x-slot:actions>
        <x-nexus::button variant="outline" :href="route('aset.edit', $aset)">Edit</x-nexus::button>
        <x-nexus::row-actions label="Aksi lain" :delete="route('aset.destroy', $aset)" />
    </x-slot:actions>

    <x-nexus::description-list>
        <x-nexus::description-item label="Kategori" :value="$aset->kategori?->nama" />
        <x-nexus::description-item label="Kondisi"><x-nexus::badge color="green">Baik</x-nexus::badge></x-nexus::description-item>
        <x-nexus::description-item label="Deskripsi" full :value="$aset->deskripsi" />
    </x-nexus::description-list>

    <x-slot:aside><x-nexus::card title="Foto"><img src="{{ $aset->foto_url }}" alt=""></x-nexus::card></x-slot:aside>
    <x-slot:tabs>
        <x-nexus::tabs :tabs="['riwayat' => 'Riwayat']" active="riwayat">
            <x-nexus::tab-pane name="riwayat" active>…</x-nexus::tab-pane>
        </x-nexus::tabs>
    </x-slot:tabs>
</x-nexus::detail-page>
```

---

## 3. Halaman Form — `form-page` (satu file create & edit)

```php
public function create() { return view('lokasi.form'); }
public function edit(Lokasi $lokasi) { return view('lokasi.form', ['item' => $lokasi]); }
```

```blade
<x-nexus::form-page
    :title="isset($item) ? 'Edit Lokasi' : 'Tambah Lokasi'"
    :action="isset($item) ? route('lokasi.update', $item) : route('lokasi.store')"
    :model="$item ?? null"
    :cancel="route('lokasi.index')"
    size="md" files>

    <x-nexus::form.group label="Kode" name="kode" required hint="Contoh: GD-A-LT1-R01">
        <x-nexus::form.input name="kode" maxlength="20" />
    </x-nexus::form.group>

    <x-nexus::form-grid>   {{-- pasangan field pendek, runtuh di HP --}}
        <x-nexus::form.group label="Gedung" name="gedung"><x-nexus::form.input name="gedung" /></x-nexus::form.group>
        <x-nexus::form.group label="Lantai" name="lantai"><x-nexus::form.input name="lantai" /></x-nexus::form.group>
    </x-nexus::form-grid>

    <x-nexus::form.group label="Peminjaman" name="bisa_dipinjam">
        <x-nexus::form.checkbox name="bisa_dipinjam" label="Ya, boleh dipinjam" unchecked="0" />
    </x-nexus::form.group>

    <x-nexus::form.group label="Foto" name="foto">
        <x-nexus::form.file name="foto" accept="image/jpeg,image/png" formats="JPG/PNG" max-size="3 MB"
            preview :current="$item?->foto_url" />
    </x-nexus::form.group>
</x-nexus::form-page>
```

Otomatis: `@csrf`, `@method('PUT')` saat edit, nilai `old()` → model, error per
field + `is-invalid` + `aria-*`, ringkasan semua error (bertaut ke field), footer
**Simpan / Simpan Perubahan** + **Batal** (lebar penuh di HP).

Multi-langkah → `<x-nexus::form-wizard>` di dalam `form-page`.
Form super-singkat (≤3 field) yang sengaja diminta sebagai modal → `modal-form`.

---

## 4. Dashboard

`baris summary → grid: card-chart + card-table → aktivitas`

```blade
@extends('nexus::layouts.app')
@section('title', 'Dashboard')
@section('content')

    <x-nexus::page-header title="Dashboard" description="Ringkasan bisnis hari ini." />

    <div class="stats-grid mb-5">
        <x-nexus::stat-card label="Pendapatan" value="Rp 24,8 jt" change="+12%" change-direction="up">
            <x-slot:icon><x-nexus::icons.shopping-bag /></x-slot:icon>
        </x-nexus::stat-card>
        <x-nexus::stat-card label="Pesanan" value="1.247" change="+8%" change-direction="up">
            <x-slot:icon><x-nexus::icons.package /></x-slot:icon>
        </x-nexus::stat-card>
        {{-- ... --}}
    </div>

    <div class="grid-2 mb-5">
        <x-nexus::card-chart title="Penjualan" subtitle="6 bulan terakhir" type="area"
            :series="[['name'=>'2025','data'=>[31,40,28,51,42,82]]]"
            :options="['xaxis'=>['categories'=>['Jan','Feb','Mar','Apr','Mei','Jun']]]" />

        <x-nexus::card-table title="Transaksi Terbaru">
            <x-slot:action><x-nexus::button variant="ghost" size="sm">Lihat semua</x-nexus::button></x-slot:action>
            <x-slot:head><th>Invoice</th><th>Pelanggan</th><th>Total</th></x-slot:head>
            @foreach($recent as $r)
                <tr><td>{{ $r->no }}</td><td>{{ $r->cust }}</td><td>{{ $r->total }}</td></tr>
            @endforeach
        </x-nexus::card-table>
    </div>

@endsection
```

> `card-chart` butuh ApexCharts dimuat — lihat `CHARTS.md`.

---

## 5. Halaman Auth

Gunakan layout terpusat `nexus::layouts.auth`. Template siap pakai tersedia:
`nexus::pages.auth.login`, `...register`, `...email-confirmation`. Daftarkan:

```php
Route::view('/login', 'nexus::pages.auth.login')->name('login');
Route::view('/register', 'nexus::pages.auth.register')->name('register');
```

Halaman 404: salin `nexus::pages.errors.404` → `resources/views/errors/404.blade.php`.

---

## Catatan grid

Kelas grid yang dipakai resep: `stats-grid` (KPI 4 → 2 kolom < 900px),
`grid-2` (1 kolom < 600px), `grid-3` (2 kolom < 900px, 1 kolom < 600px). Untuk form
pakai komponen `<x-nexus::form-grid cols="2|3">` (bukan kelas/inline grid). Jangan
menulis `style="display:grid…"` — lihat `GUIDELINES.md` §9–10.
