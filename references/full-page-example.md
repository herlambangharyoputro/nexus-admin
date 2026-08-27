# Referensi: Halaman Index Lengkap (contoh "Produk")

File asli: `pages/nexus-product.blade.php` (disalin apa adanya di
`full-page-example-raw.blade.php` di folder yang sama — buka file itu untuk
lihat kode lengkapnya).

Baca referensi ini kalau butuh **konteks tambahan** di luar yang sudah dicakup
skeleton `assets/page-index.blade.php` — terutama dua pola berikut yang TIDAK
ada di skeleton default karena sifatnya opsional per-resource:

## 1. Summary cards di atas tabel
Halaman ini menambahkan blok `stats-grid` berisi beberapa `<x-nexus::summary-card>`
sebelum datatable — dipakai kalau resource punya angka ringkasan yang berguna
ditampilkan (total item, stok menipis, dll). Bukan wajib ada di semua halaman index.

## 2. Modal untuk edit cepat (pengecualian pola "form = halaman terpisah")
Halaman ini memakai `<x-nexus::modal-form>` untuk edit-cepat beberapa field,
BERBEDA dari default proyek (form create/edit = halaman terpisah, lihat
`assets/page-form.blade.php`). Ini valid dipakai HANYA kalau:
- Field yang diedit sedikit (≤4 field), dan
- User secara eksplisit minta interaksi cepat tanpa pindah halaman.

Kalau tidak ada permintaan eksplisit soal itu, tetap pakai `page-form.blade.php`
(halaman terpisah) sebagai default, sesuai Aturan #3 di SKILL.md.

## Catatan konsistensi
Halaman ini memakai tombol edit/delete manual (bukan komponen
`<x-nexus::table-actions>`). Ini bukan pola yang harus ditiru — skeleton
`page-index.blade.php` sudah distandarkan memakai `<x-nexus::table-actions>`
sebagai default resmi. Anggap halaman ini sebagai referensi ide fitur
(summary card, modal), bukan referensi struktur baris tabel.
