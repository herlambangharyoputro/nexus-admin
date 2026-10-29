# Migrasi ke Nexus Admin 0.5

v0.5 menjadikan paket **satu-satunya sumber** pola halaman index, form, dan detail.
Semua perubahan additive — halaman lama tetap berjalan; migrasi bisa per halaman.
Rincian API: `docs/rfc-0.5.md`, aturan: `GUIDELINES.md` §6, §9, §10.

Consumer acuan dokumen ini: **simantara** (saat ini terkunci di `09e76bb` ≈ v0.1.0
lewat `dev-master` di `composer.lock`).

---

## 1. Naikkan versi & publish ulang aset

```bash
composer require herlambangharyoputro/nexus-admin:^0.5     # ganti constraint dev-master → ^0.5 (setelah tag rilis)
php artisan vendor:publish --tag=nexus-assets --force       # CSS/JS v0.5 → public/nexus/assets
```

Sampai tag `v0.5.0` dibuat, uji dengan `composer require herlambangharyoputro/nexus-admin:dev-<branch>`.

`app/Providers/AppServiceProvider.php`:

```php
// sebelum
Paginator::defaultView('vendor.pagination.nexus');
// sesudah
Paginator::defaultView('nexus::pagination.links');
Paginator::defaultSimpleView('nexus::pagination.links');
```

---

## 2. Override simantara yang kini bisa dihapus

Hapus seluruh `resources/views/vendor/nexus/` dan `resources/views/vendor/pagination/nexus.blade.php`
setelah memindahkan konten aplikasi ke titik ekstensi (kolom terakhir).

| File override simantara | Status | Pengganti |
|---|---|---|
| `components/alert`, `avatar`, `badge`, `button`, `card`, `modal`, `stat-card` | salinan v0.1 tanpa modifikasi — **hapus** | komponen paket (v0.2–v0.5 sudah memperbaikinya) |
| `components/icons/*` (10 file) | salinan v0.1 — **hapus** | ikon paket |
| `components/index-page` | **hapus** | `x-nexus::index-page` paket — format kolom lama `['Label','css','sort_key']` diterima apa adanya |
| `components/row-actions` (+ JS inline) | **hapus** | `x-nexus::row-actions` paket — `primary-href`/`primary-label` tetap diterima |
| `partials/toasts` | salinan — **hapus** | paket |
| `partials/sidebar` | **hapus** | logo → `resources/views/nexus/brand.blade.php`; menu → `resources/views/nexus/sidebar-nav.blade.php` |
| `partials/topbar` | **hapus** | nama & peran dibaca otomatis dari `auth()->user()` (`name`, `role_label`); lonceng → `nexus/topbar-actions.blade.php`; profil/pengaturan/logout → `nexus/user-menu.blade.php` |
| `layouts/app` | **hapus** | favicon → `nexus/head.blade.php`; CSS tambahan responsive (tabel, card-header, topbar dropdown, pagination, `.dropdown-*`) **sudah ada di paket**; `.status-card-link` (kartu statistik klik) pindahkan ke `nexus/head.blade.php` atau ke CSS aplikasi |
| `vendor/pagination/nexus.blade.php` | **hapus** | `nexus::pagination.links` |

Catatan: layout paket memasang `<x-nexus::confirm-dialog />` dan `<x-nexus::lightbox />`.
Override simantara tidak memasangnya, sehingga `data-confirm` sebelumnya tidak aktif —
setelah migrasi, konfirmasi hapus `row-actions` langsung bekerja.

### Contoh isi titik ekstensi (diangkat dari override simantara)

`resources/views/nexus/head.blade.php`
```blade
@php($company = \App\Models\Company::current())
@if($company?->ico_path)
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/'.$company->ico_path) }}">
@endif
```

`resources/views/nexus/brand.blade.php`
```blade
@php($company = \App\Models\Company::current())
@if($company?->logo_path)
    <img src="{{ asset('storage/'.$company->logo_path) }}" class="logo-img" alt="Logo {{ $company->nama_organisasi ?? '' }}">
@else
    <div class="logo-icon">SM</div>
@endif
<div class="logo-text">SIMANTARA</div>
```

`resources/views/nexus/sidebar-nav.blade.php` — salin **isi** `<nav class="sidebar-nav">` dari
override sidebar lama (blok `@auth … @endauth`, `nav-section-label`, `nav-item`) apa adanya.

`resources/views/nexus/topbar-actions.blade.php` — blok lonceng notifikasi dari topbar lama
(`$authUser->unreadNotifications()` …). Ganti `style="…"` inline dengan kelas paket
(`.topbar-btn`, `.notif-dot`) bila memungkinkan.

`resources/views/nexus/user-menu.blade.php`
```blade
<a href="{{ route('profil.edit') }}" class="dropdown-item" role="menuitem">
    <x-nexus::icons.user :size="16" /> <span>Profil &amp; Password</span>
</a>
@if(auth()->user()->isAdmin() && Route::has('pengaturan.index'))
    <a href="{{ route('pengaturan.index') }}" class="dropdown-item" role="menuitem">
        <x-nexus::icons.settings :size="16" /> <span>Pengaturan</span>
    </a>
@endif
<div class="dropdown-divider"></div>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="dropdown-item danger" role="menuitem">
        <x-nexus::icons.log-out /> <span>Keluar</span>
    </button>
</form>
```
(Header nama/peran di dropdown sudah dirender paket.)

---

## 3. Contoh: `lokasi/create` + `lokasi/edit` → satu `lokasi/form`

### Sebelum — `lokasi/create.blade.php` (dan `edit.blade.php` hampir identik, 60 baris)

```blade
@extends('nexus::layouts.app')
@section('title', 'Tambah Lokasi')
@section('page-title', 'Lokasi / Ruangan')
@section('breadcrumb', 'Tambah')

@section('content')
<x-nexus::card title="Tambah Lokasi / Ruangan" style="max-width:600px;">
    @if($errors->any())
        <x-nexus::alert type="error" class="mb-3">{{ $errors->first() }}</x-nexus::alert>
    @endif
    <form method="POST" action="{{ route('lokasi.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label class="form-label">Kode <span style="color:var(--red)">*</span></label>
            <input type="text" name="kode" class="form-input" value="{{ old('kode') }}"
                   placeholder="Contoh: GD-A-LT1-R01" maxlength="20" style="text-transform:uppercase" required>
        </div>
        …
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">   {{-- tidak runtuh di HP --}}
            <div class="form-group">
                <label class="form-label">Gedung</label>
                <input type="text" name="gedung" class="form-input" value="{{ old('gedung') }}">
            </div>
            …
        </div>
        …
        <div class="form-group">
            <label class="form-label">Foto Ruangan</label>
            <input type="file" name="foto" class="form-input" accept="image/jpeg,image/png">
            <small style="color:var(--muted)">Format JPG/PNG, maks 3 MB.</small>
        </div>
        <div style="display:flex;gap:.75rem;margin-top:1rem;">
            <x-nexus::button type="submit">Simpan</x-nexus::button>
            <x-nexus::button href="{{ route('lokasi.index') }}" variant="outline">Batal</x-nexus::button>
        </div>
    </form>
</x-nexus::card>
@endsection
```

Masalah: hanya error pertama yang tampil, field tidak ditandai, 7× `style` inline,
grid 2 kolom tidak runtuh di HP, create/edit diduplikasi.

### Sesudah — `lokasi/form.blade.php` (satu file untuk keduanya)

```blade
@extends('nexus::layouts.app')
@section('title', isset($lokasi) ? 'Edit Lokasi' : 'Tambah Lokasi')
@section('page-title', 'Lokasi / Ruangan')
@section('breadcrumb', isset($lokasi) ? 'Edit' : 'Tambah')

@section('content')
<x-nexus::form-page
    :title="isset($lokasi) ? 'Edit Lokasi / Ruangan' : 'Tambah Lokasi / Ruangan'"
    :action="isset($lokasi) ? route('lokasi.update', $lokasi) : route('lokasi.store')"
    :model="$lokasi ?? null"
    :cancel="route('lokasi.index')"
    size="md" files>

    <x-nexus::form.group label="Kode" name="kode" required>
        <x-nexus::form.input name="kode" maxlength="20" class="uppercase" placeholder="Contoh: GD-A-LT1-R01" required />
    </x-nexus::form.group>

    <x-nexus::form.group label="Nama Ruangan" name="nama" required>
        <x-nexus::form.input name="nama" placeholder="Contoh: Ruang Tamu Lobby" required />
    </x-nexus::form.group>

    <x-nexus::form-grid>
        <x-nexus::form.group label="Gedung" name="gedung"><x-nexus::form.input name="gedung" placeholder="Contoh: Gedung A" /></x-nexus::form.group>
        <x-nexus::form.group label="Lantai" name="lantai"><x-nexus::form.input name="lantai" placeholder="Contoh: Lantai 1" /></x-nexus::form.group>
    </x-nexus::form-grid>

    <x-nexus::form.group label="Bisa Dipinjam sebagai Ruangan" name="bisa_dipinjam">
        <x-nexus::form.checkbox name="bisa_dipinjam" label="Ya, boleh diajukan peminjaman" />
    </x-nexus::form.group>

    <x-nexus::form.group label="Kapasitas (orang)" name="kapasitas">
        <x-nexus::form.input name="kapasitas" type="number" min="1" />
    </x-nexus::form.group>

    <x-nexus::form.group label="Deskripsi" name="deskripsi">
        <x-nexus::form.textarea name="deskripsi" rows="3" />
    </x-nexus::form.group>

    <x-nexus::form.group label="Foto Ruangan" name="foto">
        <x-nexus::form.file name="foto" accept="image/jpeg,image/png" formats="JPG/PNG" max-size="3 MB"
            preview :current="$lokasi->foto_url ?? null" />
    </x-nexus::form.group>
</x-nexus::form-page>
@endsection
```

Catatan: `size` menggantikan `max-width` bebas. Di sini `md` (760px) karena form memakai
`form-grid` untuk pasangan Gedung+Lantai — `form-grid` hanya boleh di `md/lg`
(GUIDELINES §9.2). Bila ingin kartu sempit `sm` (560px), hapus `form-grid` (satu kolom).
"Bisa dipinjam" + "Kapasitas" sengaja tidak digabung dalam grid: checkbox berlabel
panjang tidak boleh di dalam grid dan keduanya bukan pasangan makna.

`LokasiController`:
```php
public function create()             { return view('lokasi.form'); }
public function edit(Lokasi $lokasi) { return view('lokasi.form', compact('lokasi')); }
```
Lalu hapus `lokasi/create.blade.php` dan `lokasi/edit.blade.php`.

Didapat otomatis: `@csrf` + `@method('PUT')` saat edit, `old()` → nilai model (field yang
dikosongkan user tidak terisi ulang), ringkasan **semua** error bertaut ke field, error per
field + `is-invalid` + `aria-*`, penanda wajib tunggal, tombol **Simpan** / **Simpan
Perubahan** + **Batal** (lebar penuh di HP), pratinjau foto saat edit, flash sesi.

---

## 4. Pemetaan pola lama → baru

| Pola lama di simantara | Ganti dengan |
|---|---|
| `@if($errors->any()) <x-nexus::alert>{{ $errors->first() }}` | dihapus — `form-page` merender `form.errors` |
| `@error('x') <div class="text-red" style=…>` · `pm-err` · `is-invalid` manual | `form.group name="x"` |
| `<span class="text-red">*</span>` · `<span style="color:var(--red)">*</span>` | `form.group required` |
| `value="{{ old('x', $model?->x) }}"` | `form.input name="x"` di dalam `form-page :model` |
| `@selected(old('x', $m?->x) == $id)` dalam `@foreach` | `form.select name="x" :options="$opsi"` |
| `style="max-width:…"` pada kartu (±20 nilai) | `form-page size="sm\|md\|lg"` |
| `style="display:grid;grid-template-columns:1fr 1fr"` (19×) | `form-grid` (atau satu kolom) |
| `class="d-grid grid-cols-3"` di form | `form-grid cols="3"` + `form-page size="lg"` |
| `@include('x._form', ['action'=>…, 'method'=>…, 'submitLabel'=>…])` | `form-page` di dalam `_form` (props `action`, `method`, `submit-label`, `cancel`) |
| `<style>` per view (17 view) | hapus; bila ada pola yang hilang, laporkan ke paket |
| `x-nexus::row-actions` + tombol hapus `onclick` + modal hapus per halaman | `row-actions :delete="…" delete-confirm="…"` |
| `request()->query()` untuk deteksi "hasil pencarian" | otomatis (`index-page filtered`) |
| `x-nexus::index-page title="Daftar ({{ $x->total() }} item)"` | `title="Daftar" :count="true"` |
| Halaman show dengan `div` label/nilai manual | `detail-page` + `description-list` |

---

## 5. Risiko & cek setelah upgrade

- `form.select` dengan `placeholder`: opsi placeholder kini **bisa dipilih** (nilai `""`,
  tidak lagi `disabled`) agar dapat dipakai sebagai filter "Semua …". Validasi `required`
  di server tetap menolak nilai kosong.
- `form.group`: `hint`/`error` kini `<p>` (dulu `<span>`), ukuran teks 11px → 12px.
- Sidebar off-canvas kini di bawah **900px** (dulu 768px); `.grid-2` baru runtuh di bawah
  600px (dulu 768px), `.grid-3` → 2 kolom di bawah 900px.
- Topbar default: label menu "Profile/Setting/Logout" → "Profil/Pengaturan/Keluar"; logout
  memakai POST bila route `logout` ada.
- Jalankan `php artisan view:clear` setelah menghapus override.
- Uji di 360px: tidak ada scroll horizontal, tabel index tampil sebagai kartu.
