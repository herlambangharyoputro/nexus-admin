# Nexus Admin — Patterns (Cookbook Halaman)

Resep **copy-paste tingkat halaman** yang dirangkai dari komponen `<x-nexus::*>`.
Tujuannya: pola desain konsisten & deterministik — agent (Cowork/Sonnet) cukup
mengikuti resep, bukan menyusun markup dari nol.

Prasyarat: lihat `GUIDELINES.md` (kontrak komponen) & `CHARTS.md` (chart/lib).

> **Riwayat perubahan pola:** List/Index & Form direvisi — datatable jadi
> client-side (tanpa `:paginator`), form jadi satu file create+edit dengan
> layout satu kolom, dan "Tambah" jadi halaman terpisah (bukan modal).
> Pola Detail & Dashboard belum berubah.

---

## 0. Kerangka halaman

Setiap halaman dalam shell admin meng-`@extends('nexus::layouts.app')`:

```blade
@extends('nexus::layouts.app')
@section('title', 'Judul Tab')
@section('content')
    {{-- pola halaman di sini --}}
@endsection
```

Pasang sekali di layout (untuk dialog global): `<x-nexus::confirm-dialog />` dan
`<x-nexus::lightbox />` sebelum `</body>`.

---

## 1. Halaman List / Index

`page-header → baris summary → datatable + table-actions`

**Datatable = client-side.** Semua baris di-render langsung ke HTML (tanpa
`:paginator`) — search & sort ditangani JS di browser lewat hook
`data-table-search` / `data-sort-table` bawaan komponen `datatable`
(prop `paginator` opsional, default `null`; kalau di-isi baru muncul
pagination server-side — TIDAK dipakai untuk pola default ini).

**Tambah data = halaman terpisah** (`{resource}.create`), BUKAN modal.
`<x-nexus::modal-form>` tetap valid untuk kasus form super-singkat (≤3 field)
yang sengaja diminta sebagai modal, tapi bukan default.

```blade
@extends('nexus::layouts.app')
@section('title', 'Pengguna')
@section('content')

    <x-nexus::page-header
        title="Pengguna"
        description="Kelola akun & hak akses."
        :breadcrumb="[['label'=>'Dashboard','url'=>url('/')], ['label'=>'Pengguna']]">
        <x-slot:actions>
            <x-nexus::button :href="route('users.create')">
                <x-nexus::icons.plus :size="14" /> Tambah
            </x-nexus::button>
        </x-slot:actions>
    </x-nexus::page-header>

    <div class="stats-grid mb-5">
        <x-nexus::summary-card label="Total" value="1.247" icon-color="primary">
            <x-slot:icon><x-nexus::icons.users /></x-slot:icon>
        </x-nexus::summary-card>
        <x-nexus::summary-card label="Aktif" value="980" icon-color="green" change="+4%" change-direction="up">
            <x-slot:icon><x-nexus::icons.user /></x-slot:icon>
        </x-nexus::summary-card>
    </div>

    <x-nexus::datatable title="Daftar Pengguna" sortable>
        <x-slot:head>
            <th data-sort="name">Nama</th>
            <th data-sort="email">Email</th>
            <th>Peran</th>
            <th style="text-align:right">Aksi</th>
        </x-slot:head>

        @foreach($users as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td><x-nexus::badge>{{ $u->role }}</x-nexus::badge></td>
                <td>
                    <x-nexus::table-actions
                        :view="route('users.show', $u)"
                        :edit="route('users.edit', $u)"
                        :delete="route('users.destroy', $u)" />
                </td>
            </tr>
        @endforeach
    </x-nexus::datatable>

@endsection
```

> Kalau `$users` besar (ribuan baris) dan render-semua-baris jadi berat,
> pertimbangkan kembali ke `:paginator` server-side untuk resource itu saja —
> ini pengecualian per-kasus, bukan mengubah default.

---

## 2. Halaman Detail

`breadcrumb + page-header → grid: kartu info + kartu aktivitas`

```blade
@extends('nexus::layouts.app')
@section('title', $user->name)
@section('content')

    <x-nexus::page-header
        :title="$user->name"
        description="Detail & riwayat pengguna."
        :breadcrumb="[
            ['label'=>'Dashboard','url'=>url('/')],
            ['label'=>'Pengguna','url'=>route('users.index')],
            ['label'=>$user->name],
        ]">
        <x-slot:actions>
            <x-nexus::button variant="outline" :href="route('users.edit',$user)">Edit</x-nexus::button>
        </x-slot:actions>
    </x-nexus::page-header>

    <div class="grid-2">
        <x-nexus::card title="Informasi">
            <div class="d-flex items-center gap-4 mb-4">
                <x-nexus::avatar :name="$user->name" size="lg" />
                <div>
                    <div class="font-bold">{{ $user->name }}</div>
                    <div class="text-sm text-muted">{{ $user->email }}</div>
                </div>
            </div>
            <x-nexus::divider />
            <x-nexus::mini-stat label="Bergabung" :value="$user->created_at->format('d M Y')" />
            <x-nexus::mini-stat label="Status" value="Aktif" color="green" />
        </x-nexus::card>

        <x-nexus::card title="Aktivitas Terbaru">
            <x-nexus::activity>
                <x-nexus::activity-item color="primary" time="2 menit lalu">Membuat invoice <strong>#INV-021</strong>.</x-nexus::activity-item>
                <x-nexus::activity-item color="green" time="1 jam lalu">Pembayaran diterima.</x-nexus::activity-item>
            </x-nexus::activity>
        </x-nexus::card>
    </div>

@endsection
```

---

## 3. Halaman Form

`page-header → card berisi form-group` (atau `form-wizard` untuk multi-langkah)

**Satu file untuk create & edit** (dibedakan `isset($item)`), **layout satu
kolom** — field berurutan ke bawah, BUKAN `.form-grid` 2 kolom. Route create
= `{resource}.store`, edit = `{resource}.update`.

```blade
@extends('nexus::layouts.app')
@section('title', isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('content')

    <x-nexus::page-header
        :title="isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna'"
        :breadcrumb="[
            ['label'=>'Pengguna','url'=>route('users.index')],
            ['label'=>isset($user) ? 'Edit' : 'Tambah'],
        ]" />

    <x-nexus::card>
        <form method="POST"
              action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}">
            @csrf
            @if(isset($user)) @method('PUT') @endif

            <x-nexus::form.group label="Nama" for="name" required :error="$errors->first('name')">
                <x-nexus::form.input id="name" name="name" :value="old('name', $user->name ?? null)" />
            </x-nexus::form.group>

            <div class="mt-4">
                <x-nexus::form.group label="Email" for="email" required :error="$errors->first('email')">
                    <x-nexus::form.input id="email" name="email" type="email" :value="old('email', $user->email ?? null)" />
                </x-nexus::form.group>
            </div>

            <div class="mt-4">
                <x-nexus::form.group label="Peran" for="role" :error="$errors->first('role')">
                    <x-nexus::form.select id="role" name="role"
                        :options="['admin'=>'Admin','staff'=>'Staff']"
                        :selected="old('role', $user->role ?? null)" />
                </x-nexus::form.group>
            </div>

            <div class="mt-4">
                <x-nexus::form.group label="Catatan" for="notes">
                    <x-nexus::form.textarea id="notes" name="notes" rows="4" :value="old('notes', $user->notes ?? null)" />
                </x-nexus::form.group>
            </div>

            <div class="d-flex justify-end gap-2 mt-5">
                <x-nexus::button variant="outline" :href="route('users.index')">Batal</x-nexus::button>
                <x-nexus::button type="submit">{{ isset($user) ? 'Perbarui' : 'Simpan' }}</x-nexus::button>
            </div>
        </form>
    </x-nexus::card>

@endsection
```

Multi-langkah → ganti isi card dengan `<x-nexus::form-wizard>` (lihat komponen).
`.form-grid` (2 kolom) masih tersedia di CSS untuk kasus khusus, tapi bukan
lagi default form.

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

Kelas grid yang dipakai resep (sudah ada di utilities/layout): `stats-grid`
(KPI 4-kolom), `grid-2` (2 kolom responsif), `form-grid` (form 2-kolom; item
`.full` span penuh). Bila kelas berbeda di versi CSS-mu, sesuaikan.
