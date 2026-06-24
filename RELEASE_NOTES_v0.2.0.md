# Nexus Admin v0.2.0

Ekspansi besar dari rilis awal: **50+ komponen baru**, **5 palet tema**,
halaman **auth/error/email**, **shell integrasi lib** (chart/peta/editor), dan
**dokumentasi enablement** untuk agent (Cowork). Semua additive & backward-compatible.

## Sorotan
- **Pola halaman siap pakai**: `datatable`, `page-header`, `card-chart`,
  `card-table`, `form-wizard`, `modal-form` — lihat `PATTERNS_Nexus.md`.
- **14 tipe chart** via ApexCharts (shell + resep, tanpa bundel lib) — `CHARTS.md`.
- **5 tema** sekali ganti: `<html data-theme="indigo|emerald|violet|rose|amber">`.
- **Halaman auth/404/email** + layout terpusat `nexus::layouts.auth`.
- **Sidebar di-hardening** (anti double-bind, persist collapsed) & **panel
  notifikasi** kini fungsional.
- **`SKILL.md`** untuk Cowork/agent — quick-reference + aturan + pola halaman.

## Upgrade dari 0.1.0
Tidak ada breaking change. Setelah update:
```bash
php artisan vendor:publish --tag=nexus-assets --force
```
(meng-update CSS/JS termasuk `themes.css`). Komponen lama tetap kompatibel.

Detail lengkap: lihat `CHANGELOG.md`.
