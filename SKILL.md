---
name: nexus-admin-page-builder
description: Gunakan skill ini setiap kali membuat, mengedit, atau menambah halaman admin (index/list, create, edit, show/detail) pada aplikasi Laravel yang memakai package nexus-admin (namespace view "nexus::", komponen <x-nexus::...>) — meskipun user tidak menyebut kata "nexus-admin" secara eksplisit. Trigger juga saat user minta "buat halaman CRUD", "buat halaman admin untuk [resource]", "tabel data", "form tambah/edit", atau menyebut komponen seperti page-header, datatable, card, badge. Skill ini memastikan halaman baru konsisten secara struktur dengan halaman admin yang sudah ada, bukan disusun ulang dari nol tiap kali.
---

# Nexus Admin — Page Builder

Package presentation-layer Laravel (Blade views + CSS pre-compiled) dengan
namespace komponen `<x-nexus::...>` (didaftarkan via `loadViewsFrom(..., 'nexus')`).
Semua komponen di `resources/views/components/`.

## Aturan #1 — JANGAN susun struktur halaman dari nol

Untuk 3 tipe halaman yang berulang (index/list, form create-edit, show/detail),
**SELALU mulai dari skeleton di `assets/`**, bukan menyusun sendiri dari deskripsi
komponen di bawah. Ini yang membuat halaman konsisten lintas resource dan lintas
proyek, walau dibuat di sesi/percakapan yang berbeda.

| Kebutuhan halaman | Skeleton |
|---|---|
| Daftar/tabel data (index) | `assets/page-index.blade.php` |
| Tambah / edit data | `assets/page-form.blade.php` |
| Detail read-only (show) | `assets/page-show.blade.php` |

Cara pakai: copy file skeleton ke `resources/views/{resource}/{nama}.blade.php`,
lalu isi HANYA bagian yang ditandai `{{-- PLACEHOLDER --}}`. Jangan mengubah
urutan elemen, jangan hapus wrapper komponen (page-header, card, dll), jangan
mengganti komponen dengan HTML mentah kecuali skeleton sendiri menyediakan opsi itu.

Keputusan struktural yang SUDAH DIFIKS di skeleton (jangan diubah per-halaman):
- Breadcrumb + judul halaman selalu via `<x-nexus::page-header>`, di LUAR card, di atas konten.
- Tombol aksi utama (Tambah/Simpan) selalu di kanan atas / kanan bawah — lihat skeleton masing-masing.
- Tabel index selalu **client-side** (semua baris di-render ke HTML, search & sort ditangani JS
  di browser via `data-table-search` / `data-sort-table` — BUKAN AJAX server-side).
- Kolom aksi tabel: ≤4 kolom data → `<x-nexus::table-actions>` (icon button).
  \>4 kolom data → `<x-nexus::dropdown>`. Tombol Delete SELALU `variant="danger"`.
- Form create/edit: SATU FILE untuk keduanya (`isset($item)`), layout SATU KOLOM
  (field berurutan ke bawah, bukan `.form-grid` 2 kolom).

## Aturan #2 — Kosakata komponen (dipakai MENGISI skeleton, bukan menyusun halaman baru)

### Layout & struktur
- `<x-nexus::page-header title description :breadcrumb>` — judul+breadcrumb+aksi, slot `actions`.
- `<x-nexus::breadcrumb :items="[['label'=>..,'url'=>..]]">` — item terakhir otomatis jadi current.
- `<x-nexus::card title noBody>` — container utama, slot `header`/`headerAction`, body via `card-body`.
- `<x-nexus::card-table title sortable>` — card + tabel tanpa search/pagination, untuk ringkasan pendek.

### Data / tabel
- `<x-nexus::datatable title search sortable :paginator>` — composite: card + search + table + pagination.
  Slot: `head` (isi `<tr>` thead), default (baris tbody), `actions` (tombol kanan bar kontrol).
- `<x-nexus::table sortable>` — wrapper `<table class="data-table">` polos, dipakai internal oleh datatable/card-table.
- `<x-nexus::table-actions :view :edit :delete deleteConfirm>` — grup tombol aksi baris.
- `<x-nexus::dropdown label align>` — menu klik, slot `trigger` untuk custom trigger (butuh `data-dropdown-trigger`).
- `<x-nexus::pagination :paginator>`, `<x-nexus::empty-state icon title desc>`.

### Form
- `<x-nexus::form.group label for hint error required full>` — WAJIB membungkus setiap field.
- `<x-nexus::form.input type name id value placeholder>`
- `<x-nexus::form.select name id :options :selected placeholder>` — options = `[value => label]`.
- `<x-nexus::form.textarea name id rows value>`
- `<x-nexus::modal-form id title action submit-label width method>` — untuk form via modal (bukan pola default,
  lihat "Kapan pakai modal" di bawah).

### Feedback & status
- `<x-nexus::badge color>` — color: blue (default) | green | amber | red | purple.
- `<x-nexus::alert type>`, `<x-nexus::confirm-dialog>` (pasang sekali di layout, trigger via `data-confirm="pesan"`
  pada form/button), Toast via JS global `window.Toast.show(msg, type)`.

### Ringkasan/dashboard
- `<x-nexus::summary-card label value icon-color change change-direction>`, `<x-nexus::stat-card>`,
  `<x-nexus::mini-stat>`, `<x-nexus::chart>`, `<x-nexus::card-chart>`.

### Ikon
Semua ikon adalah komponen: `<x-nexus::icons.{nama} :size="14" />` — cek `resources/views/components/icons/`
untuk daftar lengkap (edit, trash, eye, plus, search, download, chevron-*, dll). Jangan pakai SVG inline
kalau ikonnya sudah ada di folder ini.

## Aturan #3 — Kapan pakai modal vs halaman terpisah

Default proyek ini: **form create/edit = halaman terpisah** (`page-form.blade.php`), BUKAN modal.
`<x-nexus::modal-form>` tetap tersedia dan valid dipakai untuk kasus form super-singkat (1-3 field)
yang secara sengaja user minta sebagai modal — tapi jangan jadi default tanpa diminta.

## Aturan #4 — Design tokens (jangan hardcode warna/spacing)

Semua styling lewat CSS variable di `resources/assets/css/tokens.css`, JANGAN hardcode hex/px baru:
- Warna brand: `var(--primary)`, `var(--green)`, `var(--amber)`, `var(--red)`, `var(--purple)` (+ varian `-soft`/`-hover`).
- Spacing scale: `var(--s1)` (4px) sampai `var(--s8)` (32px) — atau pakai utility class `.gap-{1-6}`, `.mt-{0-6}`.
- Radius: `var(--radius-sm|--radius|--radius-lg|--radius-xl|--radius-full)`.
- Tema alternatif diatur via `[data-theme="indigo|emerald|violet|rose|..."]` di `themes.css` — hanya override
  `--primary`, jangan bikin sistem warna paralel.
- Layout grid utility: `.d-flex`, `.d-grid`, `.grid-cols-{1-4}`, `.gap-x-*`, `.gap-y-*`, `.col-span-full`.

## Referensi

- `references/full-page-example.md` — contoh halaman index lengkap (produk) sebagai referensi pola nyata,
  termasuk summary cards + datatable + modal edit (kasus modal yang valid).
