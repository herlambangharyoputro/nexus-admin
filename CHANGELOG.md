# Changelog

Semua perubahan penting pada package ini didokumentasikan di file ini.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/),
dan project ini menganut [Semantic Versioning](https://semver.org/).

## [0.5.0] - Belum dirilis

Satu sumber pola halaman **index · form · detail**, responsive-first. Additive &
backward-compatible; yang tergantikan di-deprecate, tidak dihapus. Panduan upgrade:
`MIGRATION-0.5.md`. Desain: `docs/rfc-0.5.md`.

### Added
- **Pola halaman**: `index-page` (server-side: cari/filter/reset, sortir `?sort=&dir=`
  dengan `aria-sort`, empty state "belum ada data" vs "tidak cocok", flash, pagination
  dengan query string, tabel → kartu di HP), `form-page` (size `sm|md|lg|full`, `@csrf`,
  method spoofing otomatis dari `:model`/`method`, ringkasan error, footer Simpan/Batal),
  `detail-page` (+ slot `actions`, `aside`, `tabs`).
- **Komponen**: `row-actions` (menu ⋮ mengambang), `flash`, `form-grid` (cols 2|3,
  runtuh di HP), `form.checkbox`, `form.radio-group`, `form.file` (hint format/ukuran,
  pratinjau gambar), `form.errors`, `description-list`, `description-item`.
- **Pagination**: view `nexus::pagination.links` untuk `Paginator::defaultView()`;
  komponen `pagination` mendukung `simplePaginate`, `aria-current`, prop `onEachSide`.
- **Titik ekstensi layout** (tanpa override): `resources/views/nexus/{head,brand,
  sidebar-nav,topbar-actions,user-menu}.blade.php`; `@stack('head')` di layout.
- **Helper** `Nexus\Admin\Support\Field` (id/kunci dot-notation/error/nilai awal field).
- **Token** `--bp-sm/--bp-md/--bp-lg` (600/900/1200), `--touch` (40px),
  `--page-sm/md/lg/aside`; utilitas `.sr-only`, `.btn-soft`, `.dropdown-header`.
- **JS**: `NexusBreakpoint`, dropdown `data-dropdown="float"`, `StackTable`,
  `ErrorSummary`, `FilePreview`.
- **Demo**: `nexus::pages.demo-index`, `demo-form`, `demo-detail` (keadaan normal,
  error, kosong); workbench `testbench serve` untuk pengembangan paket.
- **Test**: orchestra/testbench (render komponen, backward-compat) + Playwright
  `tests/browser/responsive.cjs` (360/768/1280); screenshot di `docs/screenshots/0.5/`.
- **Dokumen**: GUIDELINES §9 (anatomi halaman, aturan form-grid, label tombol baku),
  §10 (kontrak responsive), §6 (titik ekstensi); `MIGRATION-0.5.md`.

### Changed
- `form.group`: error otomatis dari prop `name` (prop `error` lama tetap menang;
  `:error="false"` mematikan), `for` diturunkan dari `name`, penanda wajib tunggal
  (+ teks pembaca layar), hint/error kini `<p id="{id}-hint|error">`.
- `form.input`/`form.select`/`form.textarea`: nilai awal otomatis `old()` → `value` →
  model `form-page`; kelas `is-invalid`, `aria-invalid`, `aria-describedby`,
  `aria-required` otomatis; `id` diturunkan dari `name`. Prop lama tetap berlaku.
- `form.select`: opsi `placeholder` bernilai `""` dan **dapat dipilih** (dulu `disabled`),
  agar bisa menjadi filter "Semua …"; mendukung `multiple`.
- Sidebar off-canvas di bawah **900px** (dulu 768px) dan tersembunyi sebelum JS
  berjalan (kelas `.is-open`); `.grid-2` runtuh di bawah 600px (dulu 768px), `.grid-3`
  → 2 kolom di bawah 900px. Semua media query memakai 599.98/899.98/1199.98px.
- < 900px: target sentuh ≥ 40px (`.btn`, `.pg-btn`, input, `.dropdown-item`, dst.).
  < 600px: modal penuh layar, dropdown topbar/notif-panel/toast selebar layar,
  pagination & header kartu menyesuaikan.
- Topbar membaca `auth()->user()`; logout POST bila route `logout` ada; label menu
  user "Profil/Pengaturan/Keluar". Nav & brand contoh dipindah ke `partials/*-default`.
- `.form-hint`/`.form-error` 11px → 12px. `search-box` mendapat `aria-label`.
- Skeleton `assets/page-index|form|show` memakai komponen pola baru; label edit
  "Perbarui" → **"Simpan Perubahan"**.

### Deprecated
- `datatable` → gunakan `index-page`.
- `table-actions` → gunakan `row-actions`.
- Aturan "≤4 kolom → table-actions, >4 → dropdown" (SKILL) → selalu `row-actions`.

### Fixed
- Hook `data-table-search` milik `datatable` tidak pernah diimplementasi di `app.js`;
  kini menyaring baris di browser seperti yang didokumentasikan.
- Tabel lebar, `.page-header`, `.table-controls`, `.pagination`, `.card-header` tidak
  lagi melebarkan halaman di 360px.
- Dropdown di dalam tabel yang di-scroll tidak lagi terpotong (`row-actions`).
- Doc-comment ikon `download` memuat tag komponen literal — dinetralkan.

---

## [0.4.0] - 2026-06-28

Enhancement halaman auth (login & register) — additive & backward-compatible.

### Added
- **Input Group**: pola `.input-group` + `.input-group-text` + `.input-group-btn`
  — gabungkan input dengan addon prefix/suffix menjadi satu field menyatu;
  focus ring via `:focus-within` pada wrapper.
- **Auth CSS**: password input kini pakai `.input-group` + `.input-group-btn`
  untuk tombol show/hide (ikon SVG inline, tanpa lib eksternal).
- **Auth CSS**: `.strength-bars` + `.strength-bar` (weak/fair/good/strong) +
  `.strength-label` — indikator kekuatan password 4-bar animasi.
- **Login page**: flash `session('status')` sebagai alert sukses (post-reset),
  error per-field (`email`, `password`), tombol show/hide password.
- **Register page**: error per-field (`name`, `email`, `password`,
  `password_confirmation`), show/hide password di kedua field sandi,
  strength meter real-time (skor 0–4 berdasar panjang, kapital, angka, simbol).

### Changed
- `pages/auth/login.blade.php` & `pages/auth/register.blade.php` — template
  diperbarui; backward-compatible (struktur section/footer tetap sama).

---

## [0.3.0] - 2026-06-26

Polish UI halaman demo & komponen dasar. Semua additive & backward-compatible.

### Added
- **Badge**: varian `danger` (`<x-nexus::badge color="danger">`) — solid
  `var(--red)` + teks putih, untuk penekanan kuat.
- **Alert**: ikon status (SVG inline) di depan teks per tipe
  (info/success/warning/error) + teks rata tengah. Tombol close dipindah ke
  kelas `.alert-close` (dari inline style).
- **Avatar Group**: prop `size="lg"` (varian avatar besar via `.avatar-group-lg`).

### Changed
- **Card**: `.card-header` kini punya `border-bottom: 1px solid var(--border)`
  + `padding-bottom: var(--s4)`; `.card-title` font-weight `600 → 800`.
- **Form**: `.form-label` font-size `12px → 14px`.
- **Dropdown**: `.dropdown-menu` z-index `250 → 1000` agar tidak tertimpa sidebar
  (`position: fixed`) saat menu meluas ke area sidebar.
- **Demo** (`pages.demo`): `page-header` tanpa breadcrumb; dropdown digabung ke
  kartu "Buttons, Badges, Chips"; semua alert kini dismissible; badge `danger`
  ditampilkan; avatar-group memakai foto + varian `lg`.

## [0.2.0] - 2026-06-24

Ekspansi besar: ~50+ komponen baru, 5 palet tema, halaman auth/error/email,
shell integrasi lib eksternal, dan dokumentasi enablement untuk agent (Cowork).
Semua additive & backward-compatible.

### Added
- **Form**: `form.group`, `form.input`, `form.textarea`, `form.select`.
- **Tabel**: `table`, `pagination` (dukung Laravel paginator), `datatable`
  (komposit search+sort+paginate), `table-actions` (view/edit/delete; delete
  jadi form `POST`+`@method('DELETE')`).
- **Interaktif**: `toggle`, `dropdown`, `tabs`+`tab-pane`, `accordion`+
  `accordion-item`, `modal-form`, `confirm-dialog`, `form-wizard`+`wizard-pane`.
- **Utility**: `progress`, `chip`, `divider`, `kbd`, `spinner`, `empty-state`,
  `skeleton`, `tooltip`, `drop-zone`, `breadcrumb`, `search-box`, `avatar-group`.
- **Komposit & media**: `page-header`, `summary-card`, `card-table`, `widget`,
  `jumbotron`, `mini-stat`, `activity`+`activity-item`, `notif-panel`+
  `notif-item`, `gallery`+`gallery-item`, `lightbox`, `carousel`+
  `carousel-item`, `youtube`.
- **Shell lib eksternal** (tidak dibundel): `chart`, `card-chart` (ApexCharts),
  `select-picker` (Tom Select), `wysiwyg` (Quill), `colorpicker` (Coloris),
  `map` (Leaflet), `calendar` (FullCalendar).
- **Ikon baru**: `edit`, `trash`, `eye`, `plus`, `chevron-left`,
  `chevron-right`, `chevron-down`.
- **Tema**: `themes.css` — 5 palet via `[data-theme]` (indigo, emerald, violet,
  rose, amber). Sidebar tetap navy untuk semua tema.
- **Halaman**: `layouts.auth` (terpusat tanpa sidebar); `pages.auth.login`,
  `pages.auth.register`, `pages.auth.email-confirmation`; `pages.errors.404`;
  `emails.layout` + `emails.sample` (template email table-based inline-style);
  `pages.demo` (galeri komponen).
- **Modul JS**: `Dropdown`, `Tabs`, `Accordion`, `Confirm`, `Wizard`,
  `Lightbox`, `Carousel`, `Integrations` (auto-init ApexCharts/TomSelect/Quill/
  Coloris/Leaflet bila lib-nya dimuat). Semua namespaced & idempotent.
- **Utilitas grid**: `grid-2`, `grid-3` (responsif).
- **Dokumentasi**: `GUIDELINES.md` (kontrak komponen + markup shell + import
  order), `PATTERNS_Nexus.md` (resep halaman), `CHARTS.md` (chart & integrasi
  lib), `SKILL.md` + `SKILL_Nexus.md` (skill untuk Cowork/agent).

### Changed
- **Sidebar (JS) di-hardening**: re-query DOM tiap akses (kebal saat `app.js`
  ter-load di `<head>`), binding namespaced + guard idempotent (kebal load
  ganda), `resize` di-debounce, satu sumber kebenaran `applyState()` (hilangkan
  desync mobile↔desktop), state collapsed persist via `localStorage`. API
  bertambah: `Sidebar.collapse()` / `Sidebar.expand()`.
- **Topbar**: tombol notifikasi kini men-toggle `notif-panel` bila dipasang
  (sebelumnya hanya menampilkan toast "belum diimplementasi").
- **`app.css`**: `@import 'themes.css'` ditambahkan setelah `tokens.css`
  (urutan: tokens → themes → base → utilities → layout → components).

### Fixed
- Scrollbar pada `.sidebar-nav` disembunyikan lintas-browser
  (`scrollbar-width`, `-ms-overflow-style`, `::-webkit-scrollbar`) — tetap
  dapat di-scroll.

## [0.1.0] - 2026-06-07

Rilis awal Nexus Admin sebagai Composer package (presentation layer only).

### Added
- Package scaffold: `composer.json` (`type: library`, MIT), PSR-4 autoload
  `Nexus\Admin\` → `src/`, dependency `php: ^8.2` + `illuminate/support: ^12.0`
  (target Laravel 12).
- `NexusServiceProvider`: `loadViewsFrom` (namespace `nexus`),
  `publishes` tag `nexus-assets`, `publishes` tag `nexus-views`.
  Auto-discovery via `extra.laravel.providers`.
- View namespace `nexus::` untuk seluruh view package (layout, partials,
  components, icons).
- Layout master `nexus::layouts.app` — yields: `title`, `content`,
  `page-title`, `breadcrumb`; stacks: `styles`, `scripts`.
- Partials: `nexus::partials.sidebar` (nav `url()` hardcoded + active state
  via `request()->is()`), `nexus::partials.topbar` (dummy user data +
  dropdown), `nexus::partials.toasts`.
- Anonymous components ber-API (`<x-nexus::...>`): `card`, `stat-card`,
  `button`, `avatar`, `modal`, `alert`, `badge`. Kontrak props/slots
  terdokumentasi di `GUIDELINES.md`.
- Icon set sebagai anonymous components: `<x-nexus::icons.*>`.
- CSS utility classes non-component (dipakai langsung via markup):
  `data-table`, `pagination`, `form-*`, `toggle`, dll.
- Pre-compiled assets (publish via `nexus-assets` → `public/nexus/assets`):
  CSS (`tokens`, `base`, `utilities`, `layout`, `components`, master `app.css`
  dengan import order terkunci), JS (`app.js` + `vendor/jquery-3.7.1.min.js`).
- Override mechanism: `vendor:publish --tag=nexus-views` →
  `resources/views/vendor/nexus` untuk kustomisasi view oleh consumer.

[0.5.0]: https://github.com/herlambangharyoputro/nexus-admin/compare/v0.4.0...HEAD
[0.4.0]: https://github.com/herlambangharyoputro/nexus-admin/releases/tag/v0.4.0
[0.3.0]: https://github.com/herlambangharyoputro/nexus-admin/releases/tag/v0.3.0
[0.2.0]: https://github.com/herlambangharyoputro/nexus-admin/releases/tag/v0.2.0
[0.1.0]: https://github.com/herlambangharyoputro/nexus-admin/releases/tag/v0.1.0