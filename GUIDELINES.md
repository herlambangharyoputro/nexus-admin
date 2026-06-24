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
`.no-margin`, `.mobile-hidden`, `.show`.

**Pemuatan asset:**

- Muat `app.js` **sekali saja**, di **akhir `<body>`**, **setelah jQuery**.
  (Memuat di `<head>` atau dua kali adalah penyebab umum bug toggle.)
- Modul Sidebar bersifat *idempotent* dan re-query DOM, tapi tetap ikuti aturan
  ini untuk hasil yang pasti.

API JS publik: `window.Sidebar.{toggle, collapse, expand, closeMobile}`,
`window.Modal.{open, close}`, `window.Toast.show(msg, type)`,
`window.Notif.{markRead, markAllRead}`.

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

## 6. Override oleh consumer

Consumer dapat menimpa view mana pun dengan publish `nexus-views`, lalu mengedit
file di `resources/views/vendor/nexus/...`. Karena itu: **jaga kestabilan API
props & class** antar versi minor.

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
