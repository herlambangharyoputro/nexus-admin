# Nexus Admin — Guidelines

Kontrak konvensi untuk package ini. **Wajib dibaca sebelum menambah/mengubah
komponen** agar API tetap konsisten dan agent (Cowork/Sonnet) bisa
mengeksekusinya tanpa improvisasi markup.

---

## 1. Filosofi package

- **Presentation layer only.** Hanya Blade views + asset CSS/JS *pre-compiled*.
  Tidak ada controller, model, migration, atau config aplikasi.
- **No build step.** Asset dikirim sudah jadi (tidak ada Vite/Webpack di sisi
  consumer). Jangan menambah dependency JS berat yang butuh kompilasi.
- **Library, bukan starter kit.** Consumer memasang via Composer, lalu
  `vendor:publish` asset & (opsional) view.

> Konsekuensi: untuk fitur yang butuh lib pihak ketiga (peta, kalender interaktif,
> chart) → kirim **shell + resep integrasi** (lihat `PATTERNS.md`), **jangan**
> bundel lib-nya.

---

## 2. Namespace & registrasi

| Hal | Nilai |
|-----|-------|
| View namespace | `nexus::` (mis. `nexus::layouts.app`) |
| Component prefix | `<x-nexus::...>` (anonymous components) |
| PSR-4 | `Nexus\Admin\` → `src/` |
| Publish asset | `php artisan vendor:publish --tag=nexus-assets` → `public/nexus/assets` |
| Publish view (override) | `php artisan vendor:publish --tag=nexus-views` → `resources/views/vendor/nexus` |

Auto-discovery lewat `extra.laravel.providers`. Tidak ada config file (v0.x).

---

## 3. Konvensi menulis komponen

Semua komponen adalah **anonymous component** (`resources/views/components/*.blade.php`).
Pola wajib (lihat `button.blade.php` sebagai rujukan):

```blade
{{--
  Nama — deskripsi singkat 1 baris.

  Usage:
    <x-nexus::nama>...</x-nexus::nama>
    <x-nexus::nama variant="outline" size="sm">...</x-nexus::nama>

  Props:
    variant : primary (default) | outline | ...
    size    : md (default) | sm | lg
--}}
@props([
    'variant' => 'primary',
    'size'    => 'md',
])

@php
    $classes = ['blok-css-utama', 'blok-css-utama-' . $variant];
    if ($size === 'sm') $classes[] = 'blok-css-utama-sm';
    $classAttr = implode(' ', $classes);
@endphp

<div {{ $attributes->merge(['class' => $classAttr]) }}>
    {{ $slot }}
</div>
```

Aturan:

1. **Header dokumentasi wajib** — blok komentar berisi *Usage* + *Props* di atas
   tiap komponen. Ini sumber kebenaran yang dibaca manusia & agent.
2. **`@props([...])`** mendeklarasikan semua input + default-nya.
3. **Builder class** via array `$classes` → `implode`. Jangan hardcode string
   class yang panjang di markup.
4. **`$attributes->merge([...])`** selalu dipakai di elemen root agar consumer
   bisa menambah `class`, `id`, `data-*`, dll.
5. **Gunakan CSS yang sudah ada** di `components.css`/`utilities.css`. Komponen
   baru idealnya = pembungkus CSS eksisting, bukan CSS baru. Kalau butuh CSS
   baru, tambahkan di file yang tepat (lihat §5) dengan token dari `tokens.css`.
6. **Sub-namespace** untuk grup: `icons.*`, `form.*` (mis. `<x-nexus::form.input>`).
   File diletakkan di sub-folder yang sama (`components/form/input.blade.php`).
7. **Tanpa state PHP/DB.** Komponen murni presentasi; data datang dari props.

---

## 4. Kontrak markup shell (PENTING untuk JS)

Modul JS (sidebar, modal, toast, dst.) bergantung pada class & atribut berikut.
**Jika consumer meng-override layout, struktur ini harus dipertahankan**, atau
toggle sidebar / interaksi lain akan rusak.

| Elemen | Wajib |
|--------|-------|
| Wrapper | `.layout` |
| Sidebar | `.sidebar` → di dalamnya `.sidebar-nav` |
| Konten utama | `<main class="main">` → di dalamnya `.content` |
| Overlay mobile | `.sidebar-overlay` |
| Tombol toggle | atribut `data-sidebar-toggle` (atau class `.topbar-toggle`) |

Kelas state yang dikelola JS (jangan dipakai manual): `.collapsed`, `.expanded`,
`.no-margin`, `.mobile-hidden`, `.is-open` (sidebar terbuka < 900px), `.show`,
`.is-floating` (dropdown mengambang).

**Pemuatan asset:**

- Muat `app.js` **sekali saja**, di **akhir `<body>`**, **setelah jQuery**.
  (Memuat di `<head>` atau dua kali adalah penyebab umum bug toggle.)
- Modul Sidebar bersifat *idempotent* dan re-query DOM, tapi tetap ikuti aturan
  ini untuk hasil yang pasti.

API JS publik: `window.Sidebar.{toggle, collapse, expand, closeMobile}`,
`window.Modal.{open, close}`, `window.Toast.show(msg, type)`,
`window.Notif.{markRead, markAllRead}`, `window.NexusBreakpoint.{sm, md, lg}()`,
`window.StackTable.init(root)` (panggil ulang setelah menyisipkan baris via AJAX).

---

## 5. Urutan import CSS (terkunci)

`app.css` meng-`@import` dengan urutan ini — **jangan diubah**:

```
tokens → themes → base → utilities → layout → components
```

- `tokens.css` — CSS variables (warna, spacing, radius, shadow, dimensi).
  Semua nilai desain mengacu ke sini; jangan hardcode warna/ukuran.
- `themes.css` — palet alternatif via `[data-theme="..."]` (indigo, emerald,
  violet, rose, amber). Override brand-token setelah `tokens.css`. Aktifkan
  dengan `<html data-theme="indigo">`. Sidebar tetap navy untuk semua tema.
- `base.css` — reset + elemen dasar.
- `utilities.css` — utility class (`.d-flex`, `.gap-3`, `.text-muted`, …).
- `layout.css` — shell (sidebar, topbar, main, content).
- `components.css` — semua komponen ber-style.

---

## 6. Override oleh consumer → pakai titik ekstensi

**Jangan** publish & mengedit `layouts/app`, `partials/*` atau komponen paket untuk
hal yang memang milik aplikasi. Salinan hasil `vendor:publish --tag=nexus-views`
membayangi semua perbaikan versi berikutnya. Sejak v0.5 layout memanggil file
**milik aplikasi** (opsional) di `resources/views/nexus/`:

| File aplikasi | Isi | Default paket |
|---|---|---|
| `nexus/head.blade.php` | favicon, meta, CSS aplikasi (juga `@push('head')`) | — |
| `nexus/brand.blade.php` | logo + nama di header sidebar | `partials/brand-default` |
| `nexus/sidebar-nav.blade.php` | `.nav-section-label` + `a.nav-item` | `partials/sidebar-nav-default` |
| `nexus/topbar-actions.blade.php` | tombol kanan topbar (mis. lonceng notifikasi) | `partials/topbar-actions-default` |
| `nexus/user-menu.blade.php` | isi dropdown user (profil, logout POST) | `partials/user-menu-default` |

Topbar membaca `auth()->user()` (`name`, peran dari `role_label ?? role`) atau
variabel `$userName`/`$userRole` dari `View::composer('nexus::partials.topbar', …)`.
Override view paket hanya sebagai jalan terakhir — dan karena itu **jaga kestabilan
API props & class** antar versi minor.

---

## 7. Versioning (SemVer)

- **PATCH** (0.1.x) — perbaikan bug, tanpa perubahan API.
- **MINOR** (0.x.0) — komponen/fitur baru yang *backward-compatible*.
- **MAJOR** — perubahan yang memutus kompatibilitas (rename/ubah props/class).

Catat semua di `CHANGELOG.md` (format Keep a Changelog).

---

## 8. Aksesibilitas (baseline)

- Komponen interaktif: sediakan `aria-*` yang relevan (lihat `modal.blade.php`:
  `role`, `aria-modal`, `aria-labelledby`).
- Tombol icon-only wajib `aria-label`.
- Fokus keyboard tidak boleh hilang; overlay/modal mendukung `Esc` untuk tutup.

---

## 9. Anatomi halaman (WAJIB untuk halaman baru)

Tiga pola halaman punya satu komponen kanonik masing-masing. Halaman baru dimulai
dari skeleton `assets/page-*.blade.php`. **Dilarang** `style="…"` untuk layout
(grid, lebar, jarak) dan blok `<style>` per halaman — kalau komponen kurang,
tambahkan ke paket.

```
topbar: @section('page-title') / @section('breadcrumb')
└─ (opsional) .stats-grid berisi stat-card
└─ index-page | form-page | detail-page      ← judul halaman (h1) ada di kartu ini
```

| Pola | Komponen | Skeleton | Menggantikan (deprecated) |
|---|---|---|---|
| Index / daftar | `index-page` + `row-actions` | `page-index` | `datatable`, `table-actions` |
| Form create/edit | `form-page` + `form.*` (+ `form-grid`) | `page-form` | `card` + `<form>` manual |
| Detail / show | `detail-page` + `description-list` | `page-show` | `d-grid grid-cols-2` manual |

`page-header` tetap tersedia untuk halaman non-pola (dashboard, laporan) yang
butuh breadcrumb + deskripsi panjang.

### 9.1 Index

- **Server-side**: cari `?search=`, filter (parameter bebas), sortir `?sort=&dir=`,
  `?page=`. Controller WAJIB whitelist kolom sortir.
- Empty state otomatis: "belum ada data" (`empty`) vs "tidak cocok" (`emptyFiltered`
  + tautan Reset) — dibedakan dari query selain `page/sort/dir/per_page`.
- Kolom aksi **selalu** `row-actions` (⋮). Hapus selalu lewat prop `delete`
  (form DELETE + `data-confirm`, butuh `confirm-dialog` di layout).
- Flash `session('success'|'error'|'warning'|'info'|'status')` tampil otomatis.

### 9.2 Form

- **Satu file** untuk create & edit: `form-page :model="$item ?? null"`. Method
  (`POST`/`PUT`), `@csrf`, label tombol, ringkasan error otomatis.
- Setiap field: `form.group name="x"` + kontrol `name="x"` (nama sama). Error,
  `old()`, nilai model, `is-invalid`, `aria-*` otomatis — **jangan** menulis
  `:error="$errors->first(…)"` atau `:value="old(…)"` lagi (masih didukung).
- Penanda wajib hanya lewat `required` di `form.group`.
- Ukuran kartu: `size="sm|md|lg|full"` — bukan `max-width` bebas.
- **Satu kolom adalah default.** `form-grid` (cols 2|3) **boleh** hanya bila
  semua terpenuhi: (1) field pendek & berpasangan makna (Gedung+Lantai,
  Mulai+Selesai, Harga+Jumlah+Satuan); (2) `form-page size` md/lg — cols=3 hanya
  lg, tidak pernah sm; (3) textarea, file, radio-group, checkbox berlabel panjang
  tidak di dalam grid (atau pakai `full`); (4) urutan kiri→kanan = urutan isian,
  karena di HP runtuh menjadi atas→bawah.

**Label tombol baku**

| Konteks | Label |
|---|---|
| Tombol buat di index | **Tambah {Resource}** |
| Submit create | **Simpan** |
| Submit edit | **Simpan Perubahan** |
| Batal | **Batal** (ke index; dari detail → ke detail) |
| Hapus | **Hapus** (danger + konfirmasi) |
| Kembali (detail) | **Kembali** |

Label submit khusus boleh bila aksinya memang berbeda, wajib diawali "Simpan"
(mis. "Simpan & Buat QR").

### 9.3 Detail

- `detail-page` (judul, `back`, slot `actions`, `aside`, `tabs`) berisi
  `description-list` (`cols` 1|2|3, `layout` stacked|horizontal). Nilai kosong → "—".

---

## 10. Kontrak responsive

Breakpoint adalah **token tunggal** di `tokens.css`; CSS tidak bisa memakai
`var()` di `@media`, jadi media query hanya boleh memakai nilai literal ini:

| Token | Nilai | Media query | Yang terjadi |
|---|---|---|---|
| `--bp-sm` | 600px | `max-width: 599.98px` | HP: semua grid 1 kolom, tabel index → **kartu per baris**, modal penuh layar, footer form & aksi header lebar penuh |
| `--bp-md` | 900px | `max-width: 899.98px` | Tablet: sidebar off-canvas, grid 3 → 2, target sentuh ≥ 40px, tabel di-scroll **di dalam** kontainer dengan kolom aksi menempel kanan |
| `--bp-lg` | 1200px | `max-width: 1199.98px` | Kolom samping (`aside`) menyempit 320 → 280px |

JS membaca token yang sama (`NexusBreakpoint.md()`), jadi sidebar & CSS tidak bisa
berselisih.

**Janji paket (diuji Playwright di 360 / 768 / 1280 — `tests/browser/responsive.cjs`):**

1. Tidak ada scroll horizontal halaman: `document.documentElement.scrollWidth <= innerWidth`.
   Konten lebar (tabel) di-scroll di dalam kontainernya sendiri.
2. Target sentuh ≥ 40px di bawah 900px: `.btn`, `.pg-btn`, `.form-input/select`,
   `.search-box`, `.dropdown-item`, `.form-check`, `.topbar-btn/toggle`, `.modal-close`.
3. Modal (`modal`, `modal-form`, `confirm-dialog`) penuh layar di bawah 600px.
4. Dropdown aksi (`row-actions`, `data-dropdown="float"`) mengambang: tidak terpotong
   kontainer overflow, dijepit ke viewport, membalik ke atas bila ruang bawah kurang.
5. Sidebar off-canvas + overlay di bawah 900px (juga sebelum `app.js` berjalan);
   topbar: judul terpotong elipsis, dropdown user/notifikasi selebar layar di HP.

**Tabel di HP — keputusan: kartu per baris** (`index-page mobile="cards"`, default).
Kolom pertama jadi judul kartu, kolom aksi mengapung kanan atas, kolom lain tampil
"LABEL nilai" (label disalin otomatis dari `<th>` oleh `StackTable`; boleh menimpa
dengan `data-label`). Kolom bersortir tampil sebagai chip di atas kartu.
`mobile="scroll"` hanya untuk tabel angka/matriks (laporan).

Aturan menulis CSS baru: mobile tidak boleh mengandalkan `white-space: nowrap`
pada teks bebas, beri `min-width: 0` pada anak grid/flex yang berisi teks, dan
gunakan `overflow-wrap: anywhere` untuk nilai panjang (kode, nomor seri, URL).
