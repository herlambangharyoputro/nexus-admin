# RFC 0.5 — Satu sumber pola Index · Form · Detail (responsive-first)

Status: **DISETUJUI** (default §7 dipakai: kartu di HP, sidebar < 900px, "Simpan Perubahan", judul di kartu) · Target: `v0.5.0` (minor, additive, backward-compatible)
Consumer acuan: `herlambangharyoputro/simantara` (Laravel 12, terkunci di `09e76bb` ≈ v0.1.0).

---

## 0. Ringkasan

1. Tiga komponen halaman baru jadi **satu-satunya** pola: `index-page`, `form-page`, `detail-page`.
2. Form jadi "sadar Laravel": `form.group`/`form.input`/`select`/`textarea` membaca `$errors`,
   `old()` dan nilai model **otomatis** dari prop `name`; satu `_form` untuk create & edit.
3. Kontrak responsive paket: breakpoint 600/900/1200, nol scroll horizontal halaman di 360px,
   target sentuh ≥40px, modal penuh layar di HP, dropdown tidak terpotong.
4. Titik ekstensi layout (brand, nav sidebar, aksi topbar, menu user, `<head>`) supaya consumer
   tidak perlu lagi meng-override `layouts/app`, `partials/topbar`, `partials/sidebar`.
5. Yang tergantikan **di-deprecate, tidak dihapus**: `datatable`, `table-actions`.

---

## 1. Audit

### 1.1 Inventaris komponen yang tumpang-tindih

| Pola | Paket (v0.4) | simantara (override) | Masalah |
|---|---|---|---|
| Halaman index | `datatable` (card + search + table + pagination) | `components/index-page` (14 view) | `datatable` client-side, **hook `data-table-search` tidak pernah diimplementasi di `app.js`** → kotak cari mati. Tidak ada filter, reset, sort server, empty state, flash. simantara memakai server-side (`?search=&sort=&dir=&page=`) — pola yang benar untuk data nyata. |
| Tabel polos | `table` | — | Primitive; dipakai internal. Tetap. |
| Kartu + tabel | `card-table` | — | Untuk ringkasan dashboard, **bukan** halaman index. Tetap, didokumentasikan sebagai bukan pola index. |
| Aksi baris | `table-actions` (ikon view/edit/delete inline) | `components/row-actions` (14 view, tombol ⋮ + dropdown `position:fixed` + JS sendiri) | Dua pola + aturan "≤4 kolom → table-actions, >4 → dropdown" membingungkan agent. Dropdown paket `position:absolute` terpotong oleh `overflow-x:auto` tabel → itulah alasan simantara menulis JS sendiri. |
| Kotak cari | `search-box` (input tanpa form) | inline di `index-page` (`.index-search`) | Dua markup berbeda. |
| Pagination | `pagination` (komponen, URL **tanpa** query string kecuali `withQueryString()`) | `vendor/pagination/nexus.blade.php` + `Paginator::defaultView(...)` | Paket tidak menyediakan view untuk `$paginator->links()`. Tombol 30px, tidak wrap di HP. |
| Flash message | — | ditulis ulang di 13 view + `index-page` | Tidak ada komponen. |
| Layout | `layouts/app` | override: favicon, ±50 baris CSS perbaikan responsive (tabel, card-header wrap, topbar dropdown, pagination), tanpa `confirm-dialog`/`lightbox` | Override menahan upgrade layout paket selamanya. |
| Topbar | `partials/topbar` (data user dummy) | override: `auth()->user()`, lonceng notifikasi, menu profil/pengaturan, logout POST | Paket tidak punya cara menyuntik user/menu tanpa override. |
| Sidebar | `partials/sidebar` (nav contoh) | override: nav aplikasi + logo perusahaan | Wajar (konten aplikasi), tapi kini ikut mengunci markup shell. |
| Komponen dasar | `alert`, `avatar`, `badge`, `button`, `card`, `modal`, `stat-card`, 10 ikon, `toasts` | salinan **v0.1 tanpa modifikasi** hasil `vendor:publish` | Membayangi perbaikan v0.2–v0.4 (ikon alert, badge `danger`, dst.). Bisa langsung dihapus. |

### 1.2 Form (temuan yang sudah diukur di simantara)

- 0 pemakaian `x-nexus::form.*`. Alasan yang terbaca dari kode: komponen paket tidak membaca
  `$errors`/`old()` sendiri, sehingga memakai komponen = tetap menulis `:error=… :value="old(…)"`
  per field. Menulis HTML manual terasa sama panjangnya.
- 4 pola tampilan error, 2 pola penanda wajib, ±20 nilai `max-width` kartu, 19× grid inline
  `1fr 1fr` (tidak runtuh di HP), create/edit diduplikasi, 17 view dengan `<style>` sendiri.
- Label tombol tidak seragam: paket "Simpan"/"Perbarui", simantara "Simpan"/"Simpan Perubahan".
- Batal di edit kadang kembali ke **show**, bukan index → perlu prop `cancel` bebas.

### 1.3 Responsive

- Hanya 4 `@media` di CSS paket, dengan 3 nilai berbeda (600, 768, 900) + JS sidebar 768.
- `.modal` di HP tetap kartu 540px − 40px; `.pg-btn` 30px; `.btn` ≈34px (di bawah 40px).
- `.table-controls`, `.page-header`, `.pagination` tidak `flex-wrap` → halaman melebar di 360px.
- `.search-box { max-width:280px }` tidak penuh di HP.

---

## 2. Keputusan kanonik per pola

| Pola | Kanonik v0.5 | Status yang lain |
|---|---|---|
| Halaman index | **`index-page`** (baru, server-side) | `datatable` → **deprecated** (tetap jalan; hook `data-table-search` diperbaiki sebagai bugfix) |
| Aksi baris | **`row-actions`** (baru, ⋮ dropdown anti-terpotong) | `table-actions` → **deprecated** |
| Kotak cari | `search-box` (primitive, dipakai `index-page`) | — |
| Pagination | `pagination` (komponen) + view baru `nexus::pagination.links` untuk `->links()` | — |
| Flash | **`flash`** (baru; dipakai otomatis oleh 3 komponen halaman) | — |
| Halaman form | **`form-page`** (baru) + `form.*` yang diperkaya | `card` + `<form>` manual → tidak dipakai lagi di skeleton |
| Grid form | **`form-grid`** (baru, cols 2/3) | `style="display:grid…"` inline → **dilarang** |
| Halaman detail | **`detail-page`** + **`description-list`/`description-item`** (baru) | pola `d-grid grid-cols-2` di `page-show` diganti |
| Tabel ringkas dashboard | `card-table` | (bukan pola index) |
| Tabel primitive | `table` | — |

---

## 3. API usulan

Konvensi untuk semua komponen baru: header *Usage/Props/Slots*, `@props`, builder `$classes`,
`$attributes->merge` di root, tanpa state DB. Logika kecil yang dipakai lintas komponen
(id dari `name`, kunci dot-notation, cek error) ditaruh di **`src/Support/Field.php`**
(helper statis murni, tanpa state) agar tidak disalin di 8 komponen.

```
Field::key('items[0][nama]')  → 'items.0.nama'     (kunci $errors / old())
Field::id('items[0][nama]')   → 'items-0-nama'     (id input & anchor ringkasan error)
```

### 3.1 INDEX — `<x-nexus::index-page>`

```blade
<x-nexus::index-page
    title="Daftar Aset"
    :paginator="$asets"
    :columns="[
        ['label' => 'Aset', 'sort' => 'nama'],
        ['label' => 'Kategori', 'sort' => 'kategori'],
        'Lokasi',
        ['label' => 'Harga', 'sort' => 'harga', 'align' => 'end'],
        ['label' => 'Aksi', 'align' => 'end'],
    ]"
    search-placeholder="Nama / kode aset…"
    :create-url="route('aset.create')" create-label="Tambah Aset"
    empty="Belum ada data aset.">

    <x-slot:filters>
        <x-nexus::form.select name="kategori_id" :options="$kategoriOptions"
            placeholder="Semua Kategori" :selected="request('kategori_id')" />
    </x-slot:filters>

    <x-slot:actions>   {{-- aksi sekunder di header, mis. Export/Impor --}}
        <x-nexus::row-actions label="Lainnya">…</x-nexus::row-actions>
    </x-slot:actions>

    @foreach($asets as $a)
        <tr>
            <td>{{ $a->nama }}</td> … 
            <td><x-nexus::row-actions :view="route('aset.show',$a)" :edit="route('aset.edit',$a)"
                     :delete="route('aset.destroy',$a)" /></td>
        </tr>
    @endforeach
</x-nexus::index-page>
```

| Prop | Default | Keterangan |
|---|---|---|
| `title` | `null` | Judul kartu (`<h1 class="card-title">`). |
| `description` | `null` | Teks kecil di bawah judul. |
| `count` | `null` | Angka di samping judul (badge abu). Otomatis `$paginator->total()` bila `count===true`. |
| `paginator` | `null` | `LengthAwarePaginator`. Dipakai untuk pagination (`withQueryString()` otomatis) & deteksi kosong. |
| `rows` | `null` | Alternatif `paginator` bila data tidak dipaginasi (Collection/array) — hanya untuk deteksi kosong. |
| `columns` | `[]` | Item: `'Label'` **atau** `['label'=>…, 'sort'=>key, 'align'=>start\|center\|end, 'class'=>…, 'width'=>…]`. Format lama simantara `['Label','css','sort_key']` **juga diterima** (migrasi mekanis). |
| `search` | `true` | Tampilkan kotak cari (`search-box`) dalam `<form method="GET">`. |
| `searchName` | `'search'` | Nama parameter query. |
| `searchPlaceholder` | `'Cari…'` | |
| `createUrl` / `createLabel` | `null` / `'Tambah'` | Tombol primer di header (ikon plus). |
| `empty` | `'Belum ada data.'` | Pesan saat benar-benar kosong (+ slot `emptyActions`, default tombol Tambah bila `createUrl`). |
| `emptyFiltered` | `'Tidak ada data yang cocok dengan pencarian/filter.'` | Pesan saat kosong **karena** filter; selalu disertai tautan "Reset filter". |
| `filtered` | `null` (auto) | Auto = ada query string selain `page`, `sort`, `dir`, `per_page` yang tidak kosong. (simantara memakai `request()->query()` → salah saat `?page=2`/`?sort=`.) |
| `flash` | `true` | Render `<x-nexus::flash />` di atas tabel. |
| `mobile` | `'cards'` | `<600px`: baris → kartu. `'scroll'` hanya untuk tabel angka/matriks (laporan). |

Slot: `filters`, `actions`, `emptyActions`, `toolbar` (ganti seluruh toolbar), `footer`
(di bawah pagination), default = baris `<tr>`.

**Toolbar**: `<form method="GET">` → search + filters + tombol **Cari** + tautan **Reset**
(muncul hanya bila `filtered`). Parameter `sort`/`dir` dipertahankan via hidden input. Di
`<600px` semua kontrol 100% lebar, tombol Cari/Reset berbagi satu baris.

**Kolom sortir**: `<th aria-sort>` berisi `<a href="?sort=key&dir=asc|desc">` (reset `page`),
ikon ▲/▼/⇅. Paket **tidak** menyortir; controller membaca `sort`/`dir` (resep di GUIDELINES,
whitelist kolom wajib).

**Keputusan mobile `<600px` — dipilih: kartu per baris.** Alasan: tabel index simantara 5–7
kolom dengan kolom Aksi paling kanan; dengan scroll, aksi tersembunyi di luar layar dan butuh
geser horizontal per baris. Implementasi: CSS (`display:block` per `tr`, `td::before{content:attr(data-label)}`)
+ ±15 baris JS yang menyalin teks `<th>` ke `td[data-label]` (consumer tidak perlu menulis
`data-label`; boleh menimpa manual). Kolom pertama = judul kartu (tanpa label), kolom aksi
(`align=end` terakhir) mengapung di kanan atas kartu. Tanpa JS: kartu tetap terbaca, hanya
tanpa label. `mobile="scroll"` disediakan untuk laporan/matriks (scroll **di dalam** kontainer).

### 3.2 INDEX — `<x-nexus::row-actions>`

```blade
<x-nexus::row-actions :view="route('aset.show',$a)" :edit="route('aset.edit',$a)"
    :delete="route('aset.destroy',$a)" delete-confirm="Hapus aset {{ $a->kode }}?">
    <a class="dropdown-item" href="{{ route('aset.qr',$a) }}">Cetak QR</a>   {{-- item tambahan --}}
</x-nexus::row-actions>
```

| Prop | Default | Keterangan |
|---|---|---|
| `view`, `edit` | `null` | URL → item "Detail", "Edit". |
| `delete` | `null` | URL → `<form POST + @method('DELETE') data-confirm>` item "Hapus" (danger, paling bawah, didahului divider). |
| `deleteConfirm` | `'Hapus item ini? Tindakan ini tidak bisa dibatalkan.'` | Pesan `confirm-dialog`. |
| `viewLabel`/`editLabel`/`deleteLabel` | Detail/Edit/Hapus | |
| `label` | `'Aksi'` | `aria-label` tombol ⋮ (dan teks bila `icon=false`). |
| `primaryHref`/`primaryLabel` | — | **Alias kompatibel** dengan override simantara (= `view`/`viewLabel`). |

Tombol pemicu 40×40px di `<900px`. Menu memakai modul `Dropdown` paket yang diperluas
(`data-dropdown="float"`): `position:fixed`, dijepit ke viewport, **membalik ke atas** bila
ruang bawah kurang, tutup saat scroll/resize/Esc. JS ad-hoc simantara tidak diperlukan lagi.

### 3.3 INDEX — pendukung

- `<x-nexus::flash />` — render `session('success'|'error'|'warning'|'info'|'status')` sebagai
  `alert` dismissible. Prop `keys` untuk kustom. Dipakai otomatis oleh 3 komponen halaman.
- `pagination`: tambah `onEachSide` alias `window`, `aria-label` nav, halaman aktif `aria-current`,
  sembunyikan tombol bila 1 halaman (info tetap), `<600px`: info di atas, tombol wrap, 40px.
- View `nexus::pagination.links` → consumer: `Paginator::defaultView('nexus::pagination.links')`
  (menggantikan `vendor/pagination/nexus.blade.php` simantara).

### 3.4 FORM — `<x-nexus::form-page>`

```blade
{{-- lokasi/create.blade.php & lokasi/edit.blade.php → keduanya: @include('lokasi._form') --}}
<x-nexus::form-page
    :title="isset($lokasi) ? 'Edit Lokasi' : 'Tambah Lokasi'"
    :action="isset($lokasi) ? route('lokasi.update', $lokasi) : route('lokasi.store')"
    :model="$lokasi ?? null"
    :cancel="route('lokasi.index')"
    size="md" files>

    <x-nexus::form.group label="Kode" name="kode" required hint="Contoh: GD-A-LT1-R01">
        <x-nexus::form.input name="kode" maxlength="20" />
    </x-nexus::form.group>
    …
</x-nexus::form-page>
```

| Prop | Default | Keterangan |
|---|---|---|
| `action` | (wajib) | URL submit. |
| `method` | `null` → auto | Auto: `PUT` bila `model?->exists`, selain itu `POST`. Eksplisit: `POST\|PUT\|PATCH\|DELETE` → `@method()` (spoofing) + `@csrf` selalu. `GET` → tanpa csrf. |
| `model` | `null` | Model/array untuk nilai awal field (dibaca `form.*` lewat `@aware`). |
| `title`, `description` | `null` | Header kartu (`<h1 class="card-title">`). |
| `size` | `'md'` | `sm` 560px · `md` 760px · `lg` 1040px · `full` — menggantikan ±20 `max-width` bebas. |
| `files` | `false` | `enctype="multipart/form-data"`. |
| `cancel` | `null` | URL tombol **Batal** (tidak dirender bila null). |
| `submitLabel` | auto | `'Simpan'` (create) / `'Simpan Perubahan'` (edit). |
| `cancelLabel` | `'Batal'` | |
| `errorSummary` | `true` | Ringkasan semua error di atas form. |
| `flash` | `true` | `<x-nexus::flash />` di atas form. |

Slot: default (field), `footer` (ganti seluruh footer), `footerStart` (kiri footer, mis. tombol
Hapus pada edit), `aside` (kolom kanan ≥900px, mis. pratinjau/QR; runtuh ke bawah).

Footer: Batal (outline) lalu Simpan (primer) **rata kanan**; `<600px` keduanya lebar penuh
dan Simpan di paling atas (paling dekat jempol setelah field terakhir). Urutan DOM = Simpan,
Batal; desktop ditampilkan dengan `flex-direction: row-reverse`, HP dengan `column`.

**Ringkasan error** = komponen `<x-nexus::form.errors />` (bisa dipakai mandiri):
`role="alert"`, `tabindex="-1"`, judul "Periksa kembali isian berikut:", daftar **semua**
pesan; tiap pesan tautan `#{Field::id(key)}`. JS kecil memfokuskan ringkasan saat halaman
dimuat dengan error (pembaca layar langsung membacakan).

### 3.5 FORM — `form.group` (diperkaya, kompatibel)

| Prop | Baru? | Keterangan |
|---|---|---|
| `name` | **baru** | Kunci error. Error otomatis = `$errors->first(Field::key(name))`. |
| `error` | lama | Bila diisi **menang** atas error otomatis (`:error="$errors->first('x')"` lama tetap jalan). `:error="false"` mematikan error otomatis. |
| `for` | lama | Default baru: `Field::id(name)` bila `name` ada. |
| `label`, `hint`, `required`, `full` | lama | `required` → penanda tunggal `<span class="form-required" aria-hidden="true">*</span>` + teks sr-only "(wajib)". |
| `span` | **baru** | `2`/`3` → membentang N kolom di `form-grid` (runtuh di HP). |

Pesan error: `<p id="{id}-error" class="form-error">`, hint: `<p id="{id}-hint">`. Error
**menggantikan** hint (perilaku lama dipertahankan).

### 3.6 FORM — `form.input` / `form.select` / `form.textarea` (diperkaya, kompatibel)

Nilai yang dirender (urutan prioritas):
1. Ada old input di sesi (`session()->hasOldInput()`) → `old(key)` (field yang dikosongkan
   user **tidak** terisi ulang dari model — jebakan umum `old('x', $model->x)`).
2. Prop `value` / `selected` (nilai lama eksplisit → perilaku lama).
3. `data_get($model, key)` dari `form-page :model` (via `@aware`).

Otomatis: `id` = `Field::id(name)` bila kosong; kelas **`is-invalid`** + `aria-invalid="true"`
bila ada error; `aria-describedby` ke `{id}-error` / `{id}-hint`; `aria-required` bila group
`required` (atribut HTML `required` **tidak** ditambahkan otomatis — validasi browser tetap
pilihan consumer). `type="password"`/`file` tidak pernah diisi ulang. `:old="false"`
mematikan pengisian otomatis. `form.select` mendukung `multiple` (selected array).

### 3.7 FORM — komponen baru

```blade
<x-nexus::form.checkbox name="bisa_dipinjam" label="Ya, boleh diajukan peminjaman" />
<x-nexus::form.radio-group name="kondisi" :options="$kondisi" inline />
<x-nexus::form.file name="foto" accept="image/jpeg,image/png" formats="JPG/PNG" max-size="3 MB"
    preview :current="$lokasi?->foto_url" />
```

| Komponen | Props |
|---|---|
| `form.checkbox` | `name`, `label`, `value` (1), `checked` (null→old/model), `unchecked` (null; bila diisi → `<input type=hidden>` agar nilai "0" ikut terkirim), `id`. Target sentuh 40px. |
| `form.radio-group` | `name`, `options` `[value=>label]`, `selected`, `inline` (false), `id`. `role="radiogroup"`; error/aria sama dengan input. |
| `form.file` | `name`, `accept`, `multiple`, `formats` + `maxSize` → hint otomatis "Format JPG/PNG, maks 3 MB." (bila group tidak memberi hint), `preview` (bool, pratinjau gambar terpilih via JS kecil `FilePreview`), `current` (URL gambar tersimpan, tampil di edit). |

`drop-zone` tetap ada (area unggah besar), tidak di-deprecate.

### 3.8 FORM — `<x-nexus::form-grid>`

```blade
<x-nexus::form-grid cols="2">
    <x-nexus::form.group label="Gedung" name="gedung"><x-nexus::form.input name="gedung" /></x-nexus::form.group>
    <x-nexus::form.group label="Lantai" name="lantai"><x-nexus::form.input name="lantai" /></x-nexus::form.group>
</x-nexus::form-grid>
```

`cols` 2 (default) | 3. Runtuh: cols=2 → 1 kolom `<600px`; cols=3 → 2 kolom `<900px`, 1 kolom
`<600px`. Kelas `.form-grid` lama tetap (sudah runtuh di 600).

**Konflik "satu kolom" vs "2 kolom" — keputusan:** default **tetap satu kolom**. `form-grid`
**boleh** hanya bila SEMUA terpenuhi:
1. Field-nya pendek dan **berpasangan secara makna** (Gedung+Lantai, Mulai+Selesai,
   Harga+Jumlah+Satuan, Kota+Kode pos);
2. `form-page size` `md`/`lg` (cols=3 hanya `lg`); tidak pernah di `sm`;
3. textarea, file, radio-group, checkbox panjang **tidak** di dalam grid (atau `full`);
4. urutan baca kiri→kanan = urutan isian, karena di HP runtuh menjadi urutan atas→bawah.
Dilarang: `style="display:grid…"` inline, grid untuk "menghemat tempat" field tak berkaitan.

### 3.9 FORM — label tombol baku

| Konteks | Label |
|---|---|
| Header index (buat) | **Tambah {Resource}** (mis. "Tambah Aset") |
| Submit create | **Simpan** |
| Submit edit | **Simpan Perubahan** (menggantikan "Perbarui") |
| Batal | **Batal** (kembali ke index, atau ke detail bila datang dari detail) |
| Hapus | **Hapus** (danger, dengan `data-confirm`) |
| Kembali di detail | **Kembali** |
| Pengecualian | Boleh `submitLabel` khusus bila aksi berbeda, wajib diawali "Simpan" (mis. "Simpan & Buat QR"). |

### 3.10 DETAIL — `<x-nexus::detail-page>` + description list

```blade
<x-nexus::detail-page :title="$aset->nama" :description="$aset->kode_aset" :back="route('aset.index')">
    <x-slot:actions>
        <x-nexus::button variant="outline" :href="route('aset.edit',$aset)">Edit</x-nexus::button>
        <x-nexus::row-actions :delete="route('aset.destroy',$aset)" label="Aksi lain" />
    </x-slot:actions>

    <x-nexus::description-list cols="2">
        <x-nexus::description-item label="Kategori">{{ $aset->kategori->nama }}</x-nexus::description-item>
        <x-nexus::description-item label="Harga" :value="$aset->harga_format" />
        <x-nexus::description-item label="Deskripsi" full>{{ $aset->deskripsi }}</x-nexus::description-item>
    </x-nexus::description-list>

    <x-slot:aside><img src="{{ $aset->foto_url }}" alt=""></x-slot:aside>

    <x-slot:tabs>
        <x-nexus::tabs :tabs="['riwayat'=>'Riwayat','mutasi'=>'Mutasi']" active="riwayat">…</x-nexus::tabs>
    </x-slot:tabs>
</x-nexus::detail-page>
```

| Komponen | Props / Slot |
|---|---|
| `detail-page` | `title`, `description`, `back` (URL tombol "Kembali"), `flash` (true). Slot: default (isi kartu utama), `actions` (header kanan; `<600px` wrap ke bawah judul, lebar penuh), `aside` (kartu kanan ≥900px / di bawah di HP), `tabs` (kartu kedua di bawah: riwayat/aktivitas). |
| `description-list` | `cols` 1\|2\|3 (default 2; runtuh seperti form-grid), `layout` `stacked` (label di atas nilai, default) \| `horizontal` (label kiri 40% ≥600px, stacked di HP), `:items="['Label' => nilai]"` (shorthand). Markup `<dl>`. |
| `description-item` | `label`, `value` (atau slot), `full`, `empty` (`'—'` bila nilai kosong/null). `<dt>`/`<dd>`, nilai panjang `overflow-wrap:anywhere`. |

### 3.11 LAYOUT — titik ekstensi (menghapus kebutuhan override shell)

Layout & partial paket memanggil `@includeFirst(['nexus.<slot>', 'nexus::partials.<slot>-default'])`.
Consumer menaruh file **miliknya sendiri** di `resources/views/nexus/` — bukan salinan file paket,
sehingga upgrade paket tidak terbayangi.

| File consumer (opsional) | Isi |
|---|---|
| `nexus/head.blade.php` | favicon, meta, CSS tambahan (juga tersedia `@stack('head')`) |
| `nexus/brand.blade.php` | logo + nama di header sidebar |
| `nexus/sidebar-nav.blade.php` | item navigasi (`.nav-item`, `.nav-section-label`) |
| `nexus/topbar-actions.blade.php` | tombol kanan topbar sebelum menu user (mis. lonceng notifikasi) |
| `nexus/user-menu.blade.php` | item dropdown user (profil, pengaturan, logout) |

Topbar default membaca `auth()->user()` (nama; peran dari `role_label ?? role`), fallback
variabel `$userName/$userRole` (kompatibel), dan **tidak lagi** menampilkan data dummy bila
user login. Layout memasang `confirm-dialog` + `lightbox` seperti sekarang.

---

## 4. Kontrak responsive (akan ditulis di GUIDELINES)

| Token | Nilai | Arti |
|---|---|---|
| `--bp-sm` | **600px** | HP: 1 kolom, tabel → kartu, modal penuh layar, tombol footer lebar penuh |
| `--bp-md` | **900px** | Tablet: sidebar off-canvas, grid 3 → 2, target sentuh 40px |
| `--bp-lg` | **1200px** | Desktop lebar: `form-page lg`, aside detail |

CSS tidak bisa memakai variabel di `@media`, jadi nilai literal dipakai di CSS dan token
`--bp-*` di `tokens.css` adalah sumber kebenaran terdokumentasi + dibaca JS (sidebar).
Media query **hanya** boleh `max-width: 599.98px | 899.98px | 1199.98px` (atau `min-width` 600/900/1200).

Perubahan perilaku yang perlu persetujuan:
- **Sidebar off-canvas dari `<768` menjadi `<900`** (JS membaca `--bp-md`). Di 768–899 konten
  mendapat lebar penuh alih-alih 768−260.
- **`.grid-2` runtuh di `<600`** (dulu `<768`); **`.grid-3`** → 2 kolom `<900`, 1 kolom `<600`.

Kontrak yang diuji (Playwright 360/768/1280):
1. `document.documentElement.scrollWidth <= innerWidth` di semua halaman demo.
2. Target sentuh ≥40px `<900px`: `.btn`, `.pg-btn`, `.form-input/select`, `.dropdown-item`,
   `.topbar-btn`, `.modal-close`, checkbox/radio (label area).
3. Modal `<600px` penuh layar (termasuk `modal-form` & `confirm-dialog` yang punya `max-width` inline).
4. Dropdown aksi tidak terpotong oleh kontainer `overflow` dan tidak keluar viewport.
5. Sidebar: off-canvas + overlay `<900`; topbar: breadcrumb ellipsis, menu user/aksi tidak melebar.

---

## 5. Deprecation (tidak ada yang dihapus)

| Komponen / pola | Pengganti | Catatan |
|---|---|---|
| `datatable` | `index-page` | Tetap berfungsi; bug `data-table-search` (tidak ada JS) **diperbaiki** (filter baris client-side). |
| `table-actions` | `row-actions` | Tetap berfungsi. |
| Label "Perbarui" di skeleton | "Simpan Perubahan" | Hanya dokumen/skeleton. |
| Aturan SKILL "≤4 kolom → table-actions, >4 → dropdown" | selalu `row-actions` | Dokumen. |

Penandaan: blok header komponen diawali `@deprecated 0.5.0 — gunakan <x-nexus::…>`; dicatat
di CHANGELOG bagian *Deprecated*; dijadwalkan dihapus paling cepat di 1.0.

---

## 6. Rencana kerja setelah disetujui (commit kecil per langkah)

1. `src/Support/Field.php` + `form.*` diperkaya + `form.checkbox/radio-group/file/errors`, `form-grid`, `form-page`.
2. `flash`, `index-page`, `row-actions`, `pagination` + `pagination.links`, perluasan modul `Dropdown`, perbaikan `data-table-search`.
3. `detail-page`, `description-list`, `description-item`.
4. CSS responsive (token breakpoint, touch target, modal, tabel→kartu, wrap) + titik ekstensi layout/topbar/sidebar.
5. Dokumen: GUIDELINES, PATTERNS, SKILL/SKILL_Nexus, `assets/page-*`, CHANGELOG `[0.5.0]`, `MIGRATION-0.5.md`, halaman demo (normal/error/kosong).
6. Verifikasi: testbench (`require-dev`) + Playwright 360/768/1280 → `docs/screenshots/0.5/`.
7. PR ke `master`. **Tanpa tag/rilis.**

## 7. Pertanyaan terbuka (default yang akan saya pakai bila tidak dijawab)

1. Mobile index: **kartu per baris** (default) — atau scroll di dalam kontainer?
2. Sidebar off-canvas di `<900` (default) — atau tetap `<768`?
3. Label edit: **"Simpan Perubahan"** (default).
4. Komponen halaman memakai judul di dalam kartu (pola simantara) dan `page-header` tetap
   opsional untuk breadcrumb/deskripsi panjang — **default ya**.

---

## 8. Catatan implementasi (setelah persetujuan)

Penyimpangan/penambahan kecil dari usulan di atas, ditemukan saat verifikasi:

- **Tablet (600–899px)**: tabel index tetap tabel (scroll di dalam kontainer) dengan
  **kolom aksi menempel di kanan** (`position: sticky`) agar ⋮ selalu terlihat.
- **Menu mengambang** mengikuti tombolnya saat halaman di-scroll (bukan langsung
  ditutup); ditutup bila tombol keluar layar.
- **Kolom bersortir di HP** tampil sebagai chip di atas kartu (sortir tetap tersedia).
- **Workbench** (`testbench.yaml`, `workbench/routes/web.php`) ditambahkan untuk
  menyajikan halaman demo saat pengembangan paket; tidak dimuat consumer.
- `form.select` placeholder kini dapat dipilih (nilai `""`) — dicatat di CHANGELOG
  *Changed* sebagai risiko kecil.
