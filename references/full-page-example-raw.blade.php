{{--
  Nexus Product — Demo halaman manajemen produk.

  Fitur:
    - Summary cards (total, aktif, stok rendah, nilai stok)
    - Datatable produk dengan search + sort
    - Modal form tambah produk
    - Modal form edit produk (diisi via JS dari data-* baris)
    - Konfirmasi hapus via Confirm.show() + Toast.show()

  Daftarkan route di consumer app:
    Route::view('/nexus-product', 'nexus::pages.nexus-product');
--}}
@extends('nexus::layouts.app')

@section('title', 'Produk')

@section('content')

@php
$products = [
    ['id'=>1, 'sku'=>'PRD-001', 'name'=>'Laptop Ultrabook 14"',    'category'=>'Elektronik', 'price'=>'Rp 12.500.000', 'stock'=>24, 'status'=>'Aktif'],
    ['id'=>2, 'sku'=>'PRD-002', 'name'=>'Mechanical Keyboard RGB',  'category'=>'Aksesori',   'price'=>'Rp 850.000',    'stock'=>3,  'status'=>'Aktif'],
    ['id'=>3, 'sku'=>'PRD-003', 'name'=>'Monitor IPS 27" 4K',       'category'=>'Elektronik', 'price'=>'Rp 5.200.000',  'stock'=>11, 'status'=>'Aktif'],
    ['id'=>4, 'sku'=>'PRD-004', 'name'=>'Mouse Wireless Ergonomic', 'category'=>'Aksesori',   'price'=>'Rp 320.000',    'stock'=>0,  'status'=>'Nonaktif'],
    ['id'=>5, 'sku'=>'PRD-005', 'name'=>'USB-C Hub 7-in-1',         'category'=>'Aksesori',   'price'=>'Rp 450.000',    'stock'=>2,  'status'=>'Aktif'],
    ['id'=>6, 'sku'=>'PRD-006', 'name'=>'SSD Eksternal 1TB',        'category'=>'Storage',    'price'=>'Rp 1.100.000',  'stock'=>18, 'status'=>'Aktif'],
    ['id'=>7, 'sku'=>'PRD-007', 'name'=>'Webcam Full HD 1080p',     'category'=>'Elektronik', 'price'=>'Rp 680.000',    'stock'=>7,  'status'=>'Aktif'],
    ['id'=>8, 'sku'=>'PRD-008', 'name'=>'Standing Desk Converter',  'category'=>'Furnitur',   'price'=>'Rp 2.750.000',  'stock'=>0,  'status'=>'Nonaktif'],
];
@endphp

{{-- Page Header --}}
<x-nexus::page-header
    title="Produk"
    description="Kelola katalog produk, stok, dan harga.">
    <x-slot:actions>
        <x-nexus::button data-modal-open="modalTambahProduk">
            <x-nexus::icons.plus :size="14" /> Tambah Produk
        </x-nexus::button>
    </x-slot:actions>
</x-nexus::page-header>

{{-- Summary Cards --}}
<div class="stats-grid mb-5">
    <x-nexus::summary-card label="Total Produk" value="8" icon-color="primary">
        <x-slot:icon><x-nexus::icons.package /></x-slot:icon>
    </x-nexus::summary-card>
    <x-nexus::summary-card label="Produk Aktif" value="6" icon-color="green" change="+2" change-direction="up">
        <x-slot:icon><x-nexus::icons.users /></x-slot:icon>
    </x-nexus::summary-card>
    <x-nexus::summary-card label="Stok Rendah" value="3" icon-color="amber">
        <x-slot:icon><x-nexus::icons.bell /></x-slot:icon>
    </x-nexus::summary-card>
    <x-nexus::summary-card label="Nilai Stok" value="Rp 428 jt" icon-color="primary">
        <x-slot:icon><x-nexus::icons.shopping-bag /></x-slot:icon>
    </x-nexus::summary-card>
</div>

<style>
    .data-table thead th { text-align: center !important; }
</style>

{{-- Datatable --}}
<x-nexus::datatable title="Daftar Produk" sortable class="mb-5">
    <x-slot:actions>
        <x-nexus::button variant="outline" size="sm">
            <x-nexus::icons.download :size="14" /> Export
        </x-nexus::button>
    </x-slot:actions>
    <x-slot:head>
        <th data-sort="sku">SKU</th>
        <th data-sort="name">Nama Produk</th>
        <th data-sort="category">Kategori</th>
        <th data-sort="price">Harga</th>
        <th data-sort="stock">Stok</th>
        <th>Status</th>
        <th>Aksi</th>
    </x-slot:head>

    @foreach($products as $p)
    <tr>
        <td><code style="font-size:.8em">{{ $p['sku'] }}</code></td>
        <td>{{ $p['name'] }}</td>
        <td><x-nexus::badge color="blue">{{ $p['category'] }}</x-nexus::badge></td>
        <td>{{ $p['price'] }}</td>
        <td>
            @if($p['stock'] === 0)
                <x-nexus::badge color="danger">Habis</x-nexus::badge>
            @elseif($p['stock'] <= 3)
                <x-nexus::badge color="amber">{{ $p['stock'] }}</x-nexus::badge>
            @else
                <span>{{ $p['stock'] }}</span>
            @endif
        </td>
        <td>
            <x-nexus::badge :color="$p['status']==='Aktif' ? 'green' : 'default'">{{ $p['status'] }}</x-nexus::badge>
        </td>
        <td>
            <div class="d-flex justify-end" style="gap:0">
                <button type="button"
                    class="btn btn-outline btn-icon btn-sm"
                    style="border-radius:var(--radius) 0 0 var(--radius)"
                    aria-label="Edit"
                    data-edit-product
                    data-id="{{ $p['id'] }}"
                    data-sku="{{ $p['sku'] }}"
                    data-name="{{ $p['name'] }}"
                    data-category="{{ $p['category'] }}"
                    data-price="{{ $p['price'] }}"
                    data-stock="{{ $p['stock'] }}"
                    data-status="{{ $p['status'] }}">
                    <x-nexus::icons.edit :size="15" />
                </button>
                <button type="button"
                    class="btn btn-danger btn-icon btn-sm"
                    style="border-radius:0 var(--radius) var(--radius) 0;margin-left:-1px"
                    aria-label="Hapus"
                    data-delete-product
                    data-name="{{ $p['name'] }}">
                    <x-nexus::icons.trash :size="15" />
                </button>
            </div>
        </td>
    </tr>
    @endforeach
</x-nexus::datatable>

{{-- Modal Tambah Produk --}}
<x-nexus::modal-form id="modalTambahProduk" title="Tambah Produk" action="#" submit-label="Simpan Produk" width="600px">
    <div class="form-grid">
        <x-nexus::form.group label="SKU" for="add-sku" required>
            <x-nexus::form.input id="add-sku" name="sku" placeholder="PRD-009" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Nama Produk" for="add-name" required>
            <x-nexus::form.input id="add-name" name="name" placeholder="Nama produk" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Kategori" for="add-category" required>
            <x-nexus::form.select id="add-category" name="category"
                :options="[''=>'Pilih kategori','Elektronik'=>'Elektronik','Aksesori'=>'Aksesori','Storage'=>'Storage','Furnitur'=>'Furnitur']" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Harga (Rp)" for="add-price" required>
            <x-nexus::form.input id="add-price" name="price" type="number" placeholder="0" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Stok" for="add-stock" required>
            <x-nexus::form.input id="add-stock" name="stock" type="number" placeholder="0" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Status" for="add-status">
            <x-nexus::form.select id="add-status" name="status" :options="['Aktif'=>'Aktif','Nonaktif'=>'Nonaktif']" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Deskripsi" for="add-desc" full>
            <x-nexus::form.textarea id="add-desc" name="description" rows="3" placeholder="Deskripsi singkat produk..." />
        </x-nexus::form.group>
    </div>
</x-nexus::modal-form>

{{-- Modal Edit Produk --}}
<x-nexus::modal-form id="modalEditProduk" title="Edit Produk" action="#" method="PUT" submit-label="Perbarui Produk" width="600px">
    <div class="form-grid">
        <x-nexus::form.group label="SKU" for="edit-sku" required>
            <x-nexus::form.input id="edit-sku" name="sku" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Nama Produk" for="edit-name" required>
            <x-nexus::form.input id="edit-name" name="name" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Kategori" for="edit-category" required>
            <x-nexus::form.select id="edit-category" name="category"
                :options="['Elektronik'=>'Elektronik','Aksesori'=>'Aksesori','Storage'=>'Storage','Furnitur'=>'Furnitur']" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Harga (Rp)" for="edit-price" required>
            <x-nexus::form.input id="edit-price" name="price" type="number" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Stok" for="edit-stock" required>
            <x-nexus::form.input id="edit-stock" name="stock" type="number" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Status" for="edit-status">
            <x-nexus::form.select id="edit-status" name="status" :options="['Aktif'=>'Aktif','Nonaktif'=>'Nonaktif']" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Deskripsi" for="edit-desc" full>
            <x-nexus::form.textarea id="edit-desc" name="description" rows="3" placeholder="Deskripsi singkat produk..." />
        </x-nexus::form.group>
    </div>
</x-nexus::modal-form>

{{-- Inline JS --}}
<script>
(function () {
    'use strict';
    function init() {
        document.querySelectorAll('[data-edit-product]').forEach(function (btn) {
            if (btn.dataset.bound) return;
            btn.dataset.bound = '1';
            btn.addEventListener('click', function () {
                var d = btn.dataset;
                var set = function (id, val) {
                    var el = document.getElementById(id);
                    if (el) el.value = val || '';
                };
                set('edit-sku',      d.sku);
                set('edit-name',     d.name);
                set('edit-category', d.category);
                set('edit-price',    (d.price || '').replace(/[^\d]/g, ''));
                set('edit-stock',    d.stock);
                set('edit-status',   d.status);
                if (window.Modal) {
                    window.Modal.open('modalEditProduk');
                } else {
                    var o = document.getElementById('modalEditProduk');
                    if (o) o.classList.add('show');
                }
            });
        });

        document.querySelectorAll('[data-delete-product]').forEach(function (btn) {
            if (btn.dataset.bound) return;
            btn.dataset.bound = '1';
            btn.addEventListener('click', function () {
                var name = btn.dataset.name || 'data ini';
                if (window.Confirm) {
                    window.Confirm.show({
                        message: 'Apakah Anda yakin untuk menghapus data ini?',
                        onConfirm: function () {
                            if (window.Toast) {
                                window.Toast.show('Produk "' + name + '" berhasil dihapus.', 'success');
                            }
                        }
                    });
                }
            });
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
</script>

@endsection
