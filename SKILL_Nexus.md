---
name: nexus-admin
description: >
  Bangun UI admin Laravel dengan tema Nexus Admin memakai komponen Blade
  <x-nexus::*>. Gunakan saat membuat halaman dashboard, list/index (index-page),
  form, detail, atau auth di proyek yang memakai package nexus-admin. Berisi
  quick-reference semua komponen, pola halaman (lihat PATTERNS_Nexus.md), dan
  aturan markup/asset yang wajib dipatuhi agar JS (sidebar, modal, dll) bekerja.
---

# Nexus Admin — Skill

Package presentation-layer untuk admin Laravel: komponen Blade `<x-nexus::*>` +
asset CSS/JS pre-compiled. Saat membangun UI di proyek ber-nexus-admin, **pakai
komponen ini, jangan tulis markup/CSS dari nol.**

Dokumen pendukung: `GUIDELINES.md` (kontrak & cara bikin komponen),
`PATTERNS_Nexus.md` (resep halaman copy-paste), `CHARTS.md` (chart & lib eksternal).

## Setup (sekali per proyek)

```bash
composer require herlambangharyoputro/nexus-admin
php artisan vendor:publish --tag=nexus-assets   # → public/nexus/assets
php artisan vendor:publish --tag=nexus-views     # opsional, untuk override
```

Halaman shell admin: `@extends('nexus::layouts.app')`. Halaman terpusat
(login dll): `@extends('nexus::layouts.auth')`.

## Aturan kritis (JANGAN dilanggar)

1. **Kontrak markup shell** — saat override layout, pertahankan kelas: `.layout`,
   `.sidebar` > `.sidebar-nav`, `<main class="main">` > `.content`,
   `.sidebar-overlay`, dan tombol toggle ber-`data-sidebar-toggle`.
2. **Muat `app.js` SEKALI, di akhir `<body>`, setelah jQuery.** Dua kali / di
   `<head>` = bug toggle sidebar.
3. **Pasang sekali di layout** (sebelum `</body>`): `<x-nexus::confirm-dialog />`
   (mengaktifkan konfirmasi hapus) dan `<x-nexus::lightbox />` (galeri).
4. **Jangan hardcode warna/ukuran** — pakai token di `tokens.css`. Ganti tema via
   `<html data-theme="indigo|emerald|violet|rose|amber">`.
5. **Lib eksternal (chart/peta/editor) TIDAK dibundel** — muat sendiri lalu
   komponen shell auto-init. Lihat `CHARTS.md`.
6. Komponen baru harus ikut pola di `GUIDELINES.md` (`@props` + builder `$classes`
   + `$attributes->merge` + header dokumentasi).
7. **Halaman baru WAJIB memakai `index-page` / `form-page` / `detail-page`**;
   **dilarang `style` inline untuk layout** dan `<style>` per halaman (GUIDELINES §9).
8. **Jangan override view paket.** Logo/nav/menu user/favicon → file aplikasi di
   `resources/views/nexus/*.blade.php` (GUIDELINES §6).
9. Responsive mengikuti kontrak 600/900/1200 (GUIDELINES §10) — jangan menambal
   breakpoint sendiri.

## Pola halaman → lihat `PATTERNS_Nexus.md`

- **List/Index**: `index-page` (server-side: cari/filter/sortir/paginate, empty state, flash, kartu di HP) + `row-actions`. Tambah data = halaman terpisah.
- **Form**: `form-page` + `form.group`/`form.*` (error, `old()`, model otomatis), satu kolom, satu file create & edit. `form-grid` hanya untuk pasangan field pendek. `form-wizard` untuk multi-langkah.
- **Detail**: `detail-page` + `description-list` (+ slot `aside`, `tabs`).
- **Dashboard**: `stat-card` (baris) → `card-chart` + `card-table` → `activity`
- **Auth**: layout `nexus::layouts.auth` + template `nexus::pages.auth.*`

## Quick-reference komponen

**Aksi & tampilan dasar**
| Komponen | Pakai untuk |
|---|---|
| `<x-nexus::button variant size full-width href>` | Tombol/anchor |
| `<x-nexus::badge>` `<x-nexus::chip active>` | Label/tag |
| `<x-nexus::avatar name src size>` `<x-nexus::avatar-group :items :max>` | Avatar |
| `<x-nexus::alert type dismissible>` | Pesan inline |
| `<x-nexus::card title noBody>` `<x-nexus::widget title subtitle>` | Kontainer |
| `<x-nexus::divider vertical>` `<x-nexus::kbd>` `<x-nexus::spinner size>` | Misc |
| `<x-nexus::tooltip text>` `<x-nexus::progress value color label>` | Misc |
| `<x-nexus::icons.* size class>` | Ikon (lihat folder icons) |

**Data & tabel**
| Komponen | Pakai untuk |
|---|---|
| `<x-nexus::index-page title :paginator :columns :create-url>` | **Halaman index** (kanonik v0.5) |
| `<x-nexus::row-actions :view :edit :delete>` | **Aksi baris** (menu ⋮, kanonik v0.5) |
| `<x-nexus::detail-page title :back>` + `<x-nexus::description-list>` | **Halaman detail** |
| `<x-nexus::card-table title sortable>` | Kartu + tabel polos (dashboard) |
| `<x-nexus::table sortable>` + `<x-slot:head>` | Tabel mentah |
| `<x-nexus::pagination :paginator>` · view `nexus::pagination.links` | Pagination |
| `<x-nexus::flash />` | Pesan sesi (otomatis di komponen halaman) |
| ~~`datatable`~~, ~~`table-actions`~~ | Deprecated 0.5 → `index-page`, `row-actions` |
| `<x-nexus::stat-card>` `<x-nexus::summary-card>` `<x-nexus::mini-stat>` | KPI |
| `<x-nexus::empty-state title desc icon>` `<x-nexus::skeleton variant>` | Kosong/loading |

**Form**
| Komponen | Pakai untuk |
|---|---|
| `<x-nexus::form-page :action :model :cancel size files>` | **Halaman form** create & edit (kanonik v0.5) |
| `<x-nexus::form.group label name required hint full span>` | Bungkus field (error otomatis dari `name`) |
| `<x-nexus::form.input type name>` `<x-nexus::form.textarea name>` | Input (`old()` → model otomatis) |
| `<x-nexus::form.select name :options>` | Dropdown (mendukung `multiple`) |
| `<x-nexus::form.checkbox name label>` `<x-nexus::form.radio-group name :options inline>` | Pilihan |
| `<x-nexus::form.file name formats max-size preview>` | Unggah berkas + pratinjau |
| `<x-nexus::form-grid cols>` `<x-nexus::form.errors />` | Grid 2/3 kolom (runtuh di HP), ringkasan error |
| `<x-nexus::select-picker multiple :options>` | Multi/live-search (Tom Select) |
| `<x-nexus::toggle name label checked>` | Switch |
| `<x-nexus::search-box name>` `<x-nexus::drop-zone name>` | Search/upload |
| `<x-nexus::colorpicker>` `<x-nexus::wysiwyg name>` | Coloris/Quill |
| `<x-nexus::form-wizard :steps>` + `<x-nexus::wizard-pane>` | Form multi-langkah |
| `<x-nexus::modal-form id title :action>` | Modal berisi form |

**Navigasi & overlay**
| Komponen | Pakai untuk |
|---|---|
| `<x-nexus::page-header title description :breadcrumb>` | Header halaman |
| `<x-nexus::breadcrumb :items>` | Jejak navigasi |
| `<x-nexus::tabs :tabs active>` + `<x-nexus::tab-pane name>` | Tab |
| `<x-nexus::accordion>` + `<x-nexus::accordion-item title open>` | Accordion |
| `<x-nexus::dropdown label align>` | Menu dropdown |
| `<x-nexus::modal id title>` | Modal (trigger `data-modal-open="id"`) |
| `<x-nexus::confirm-dialog>` | Dialog konfirmasi (pasang sekali) |
| `<x-nexus::notif-panel>` + `<x-nexus::notif-item>` | Panel notifikasi |

**Media & visual**
| Komponen | Pakai untuk |
|---|---|
| `<x-nexus::card-chart title type :series :options>` | Chart dalam kartu (ApexCharts) |
| `<x-nexus::chart type :series :options>` | Chart polos |
| `<x-nexus::gallery>` + `<x-nexus::gallery-item thumb title>` | Galeri (+lightbox) |
| `<x-nexus::carousel>` + `<x-nexus::carousel-item>` | Slider scroll-snap |
| `<x-nexus::youtube id>` | Embed YouTube |
| `<x-nexus::map :lat :lng>` `<x-nexus::calendar id>` | Leaflet/FullCalendar |
| `<x-nexus::jumbotron title text>` | Hero banner |
| `<x-nexus::activity>` + `<x-nexus::activity-item color time>` | Feed aktivitas |

## API JS (window)

`Toast.show(msg,type)`, `Modal.open(id)/close()`, `Confirm.show({message,onConfirm})`,
`Sidebar.toggle()`, `Tabs/Accordion/Dropdown/Wizard/Lightbox/Carousel` (auto-init),
`NexusIntegrations.init()` (re-init chart/lib setelah konten dinamis),
`NexusBreakpoint.{sm,md,lg}()`, `StackTable.init(root)` (setelah baris tabel ditambah via AJAX).
