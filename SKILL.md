---
name: nexus-admin-page-builder
description: Gunakan skill ini setiap kali membuat, mengedit, atau menambah halaman admin (index/list, create, edit, show/detail) pada aplikasi Laravel yang memakai package nexus-admin (namespace view "nexus::", komponen <x-nexus::...>) — meskipun user tidak menyebut kata "nexus-admin" secara eksplisit. Trigger juga saat user minta "buat halaman CRUD", "buat halaman admin untuk [resource]", "tabel data", "form tambah/edit", atau menyebut komponen seperti index-page, form-page, detail-page, row-actions, page-header, card, badge. Skill ini memastikan halaman baru konsisten secara struktur dengan halaman admin yang sudah ada, bukan disusun ulang dari nol tiap kali.
---

# Nexus Admin — Page Builder

Package presentation-layer Laravel (Blade views + CSS pre-compiled) dengan
namespace komponen `<x-nexus::...>` (didaftarkan via `loadViewsFrom(..., 'nexus')`).
Semua komponen di `resources/views/components/`.

## Aturan #0 — WAJIB (v0.5)

1. **Halaman index/form/detail baru WAJIB memakai `<x-nexus::index-page>`,
   `<x-nexus::form-page>`, `<x-nexus::detail-page>`.** Tidak ada pengecualian
   "cuma halaman kecil".
2. **DILARANG `style="…"` untuk layout** (grid, flex, lebar/`max-width`, margin,
   padding) dan **DILARANG blok `<style>` per halaman.** Butuh pola yang belum ada →
   tambahkan ke paket nexus-admin, jangan di view aplikasi.
3. **DILARANG menulis manual** yang sudah otomatis: `@csrf`/`@method` di form-page,
   `$errors->first()`, `@error`, `old()`, kelas `is-invalid`, penanda `*`, alert flash,
   tombol Simpan/Batal, empty state, pagination.
4. **DILARANG** meng-override view paket (`resources/views/vendor/nexus/...`). Konten
   shell aplikasi (logo, nav, menu user, favicon) → `resources/views/nexus/*.blade.php`
   (GUIDELINES §6).
5. `datatable` dan `table-actions` **deprecated** — jangan dipakai di halaman baru.

## Aturan #1 — JANGAN susun struktur halaman dari nol

**SELALU mulai dari skeleton di `assets/`**, isi HANYA bagian `{{-- PLACEHOLDER --}}`.

| Kebutuhan halaman | Skeleton | Komponen inti |
|---|---|---|
| Daftar/tabel data (index) | `assets/page-index.blade.php` | `index-page` + `row-actions` |
| Tambah / edit data (SATU file) | `assets/page-form.blade.php` | `form-page` + `form.*` |
| Detail read-only (show) | `assets/page-show.blade.php` | `detail-page` + `description-list` |

Keputusan struktural yang SUDAH DIFIKS (jangan diubah per-halaman):
- Judul halaman (h1) ada di kartu komponen halaman; topbar diisi `@section('page-title')`
  (+ `@section('breadcrumb')`).
- Index **server-side**: `?search=`, filter, `?sort=&dir=` (controller whitelist kolom), paginate.
- Kolom aksi tabel **selalu** `<x-nexus::row-actions :view :edit :delete>` (menu ⋮).
- Form create/edit: SATU FILE, `form-page :model="$item ?? null"`, layout SATU KOLOM.
  `form-grid` hanya untuk pasangan field pendek berkaitan (GUIDELINES §9.2).
- Label tombol: **Tambah {Resource}**, **Simpan**, **Simpan Perubahan**, **Batal**,
  **Hapus**, **Kembali**.
- Di HP (< 600px) tabel otomatis jadi kartu, form & modal penuh lebar — jangan
  menambal dengan CSS sendiri.

## Aturan #2 — Kosakata komponen

### Pola halaman (v0.5)
- `<x-nexus::index-page title :count :paginator :columns search searchName searchPlaceholder createUrl createLabel empty emptyFiltered filtered flash mobile>`
  — slot default (baris `<tr>`), `filters`, `actions`, `emptyActions`, `toolbar`, `footer`.
  Kolom: `'Label'` atau `['label','sort','align'=>start|center|end,'class','width','actions']`.
- `<x-nexus::row-actions :view :edit :delete deleteConfirm label :icon align>` — slot = item tambahan
  (`<a class="dropdown-item" role="menuitem">`).
- `<x-nexus::form-page :action method :model title description size files :cancel submitLabel cancelLabel errorSummary flash>`
  — slot default (field), `footer`, `footerStart`, `aside`. `size`: sm|md|lg|full.
- `<x-nexus::detail-page title description :back backLabel flash>` — slot default, `actions`, `aside`, `tabs`.
- `<x-nexus::description-list cols layout :items>` + `<x-nexus::description-item label :value full empty>`.
- `<x-nexus::flash />`, `<x-nexus::pagination :paginator>` (otomatis di index-page).

### Form (sadar `$errors`/`old()`/model — cukup `name`)
- `<x-nexus::form.group label name required hint full span>` — WAJIB membungkus setiap field.
- `<x-nexus::form.input name type placeholder>` · `<x-nexus::form.select name :options placeholder multiple>`
  · `<x-nexus::form.textarea name rows>`
- `<x-nexus::form.checkbox name label unchecked="0">` · `<x-nexus::form.radio-group name :options inline>`
  · `<x-nexus::form.file name accept formats maxSize preview :current>`
- `<x-nexus::form-grid cols="2|3">` · `<x-nexus::form.errors />` (otomatis di form-page)
- `<x-nexus::modal-form>` hanya untuk form ≤3 field yang sengaja diminta sebagai modal.

### Layout & lainnya
- `<x-nexus::page-header>` — hanya untuk halaman non-pola (dashboard/laporan).
- `<x-nexus::card title noBody>`, `<x-nexus::card-table>` (ringkasan dashboard, BUKAN index).
- `<x-nexus::badge color>`, `<x-nexus::alert type>`, `<x-nexus::empty-state>`, `<x-nexus::tabs>`.
- Ringkasan: `<x-nexus::stat-card>` di dalam `.stats-grid`.

### Ikon
Semua ikon adalah komponen: `<x-nexus::icons.{nama} :size="14" />` — cek `resources/views/components/icons/`.
Jangan pakai SVG inline kalau ikonnya sudah ada.

## Aturan #3 — Kapan pakai modal vs halaman terpisah

Default: **form create/edit = halaman terpisah** (`form-page`), BUKAN modal.
`modal-form` hanya untuk form super-singkat (1–3 field) yang sengaja diminta sebagai modal.

## Aturan #4 — Design tokens (jangan hardcode warna/spacing)

Semua styling lewat CSS variable di `resources/assets/css/tokens.css`, JANGAN hardcode hex/px baru:
- Warna brand: `var(--primary)`, `var(--green)`, `var(--amber)`, `var(--red)`, `var(--purple)` (+ varian `-soft`/`-hover`).
- Spacing scale: `var(--s1)` (4px) sampai `var(--s8)` (32px) — atau pakai utility class `.gap-{1-6}`, `.mt-{0-6}`.
- Radius: `var(--radius-sm|--radius|--radius-lg|--radius-xl|--radius-full)`.
- Tema alternatif diatur via `[data-theme="indigo|emerald|violet|rose|..."]` di `themes.css` — hanya override
  `--primary`, jangan bikin sistem warna paralel.
- Utility (`.d-flex`, `.gap-*`, `.mt-*`) boleh untuk penataan kecil di dalam sel/slot; layout halaman
  & form selalu lewat komponen (`form-grid`, `description-list`, `.stats-grid`, `.grid-2/3`).
- Breakpoint & kontrak responsive: `GUIDELINES.md` §10 (600 / 900 / 1200).

## Referensi

- Halaman demo hidup: `nexus::pages.demo-index`, `nexus::pages.demo-form`, `nexus::pages.demo-detail`.
- `MIGRATION-0.5.md` — contoh sebelum/sesudah (lokasi/create) & pemetaan pola lama → baru.
- `references/full-page-example.md` — contoh lama (datatable, pra-0.5); jangan ditiru untuk halaman baru.
