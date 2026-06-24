{{--
  Halaman Demo — merender komponen Nexus Admin untuk verifikasi visual.
  Daftarkan: Route::view('/nexus-demo', 'nexus::pages.demo');
  (Chart butuh ApexCharts dimuat — lihat CHARTS.md.)
--}}
@extends('nexus::layouts.app')

@section('title', 'Komponen Demo')

@section('content')

<x-nexus::page-header
    title="Galeri Komponen"
    description="Verifikasi visual seluruh komponen Nexus Admin v0.2.0."
    :breadcrumb="[['label'=>'Dashboard','url'=>url('/')], ['label'=>'Demo']]">
    <x-slot:actions>
        <x-nexus::button data-modal-open="demoModal"><x-nexus::icons.plus :size="14" /> Modal</x-nexus::button>
    </x-slot:actions>
</x-nexus::page-header>

{{-- Buttons & badges --}}
<x-nexus::card title="Buttons, Badges, Chips" class="mb-5">
    <div class="d-flex items-center gap-2 mb-4" style="flex-wrap:wrap">
        <x-nexus::button>Primary</x-nexus::button>
        <x-nexus::button variant="outline">Outline</x-nexus::button>
        <x-nexus::button variant="ghost">Ghost</x-nexus::button>
        <x-nexus::button variant="danger">Danger</x-nexus::button>
        <x-nexus::button size="sm">Small</x-nexus::button>
        <x-nexus::button disabled><x-nexus::spinner size="sm" /> Loading</x-nexus::button>
    </div>
    <div class="d-flex items-center gap-2" style="flex-wrap:wrap">
        <x-nexus::badge>Default</x-nexus::badge>
        <x-nexus::badge color="green">Success</x-nexus::badge>
        <x-nexus::badge color="amber">Warning</x-nexus::badge>
        <x-nexus::chip>Filter</x-nexus::chip>
        <x-nexus::chip active>Aktif</x-nexus::chip>
        <x-nexus::kbd>Ctrl</x-nexus::kbd> <x-nexus::kbd>K</x-nexus::kbd>
        <x-nexus::tooltip text="Info tambahan"><x-nexus::badge>Hover saya</x-nexus::badge></x-nexus::tooltip>
    </div>
</x-nexus::card>

{{-- Alerts --}}
<x-nexus::card title="Alerts" class="mb-5">
    <x-nexus::alert type="info" class="mb-2">Pesan informasi.</x-nexus::alert>
    <x-nexus::alert type="success" class="mb-2">Berhasil disimpan.</x-nexus::alert>
    <x-nexus::alert type="warning" class="mb-2">Perhatikan hal ini.</x-nexus::alert>
    <x-nexus::alert type="error" dismissible>Terjadi kesalahan.</x-nexus::alert>
</x-nexus::card>

{{-- Stats --}}
<div class="stats-grid mb-5">
    <x-nexus::stat-card label="Pendapatan" value="Rp 24,8 jt" change="+12%" change-direction="up">
        <x-slot:icon><x-nexus::icons.shopping-bag /></x-slot:icon>
    </x-nexus::stat-card>
    <x-nexus::summary-card label="Pengguna" value="1.247" icon-color="green" change="+8%" change-direction="up">
        <x-slot:icon><x-nexus::icons.users /></x-slot:icon>
    </x-nexus::summary-card>
    <x-nexus::stat-card label="Pesanan" value="356">
        <x-slot:icon><x-nexus::icons.package /></x-slot:icon>
    </x-nexus::stat-card>
    <x-nexus::stat-card label="Pending" value="12">
        <x-slot:icon><x-nexus::icons.bell /></x-slot:icon>
    </x-nexus::stat-card>
</div>

{{-- Forms --}}
<x-nexus::card title="Form" class="mb-5">
    <div class="form-grid">
        <x-nexus::form.group label="Nama" for="d-name" hint="Nama lengkap">
            <x-nexus::form.input id="d-name" name="name" placeholder="Herlambang" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Email" for="d-email" error="Email tidak valid">
            <x-nexus::form.input id="d-email" type="email" placeholder="you@mail.com" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Peran" for="d-role">
            <x-nexus::form.select id="d-role" :options="['admin'=>'Admin','staff'=>'Staff']" placeholder="Pilih" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Warna brand" for="d-color">
            <x-nexus::colorpicker id="d-color" value="#0093b4" />
        </x-nexus::form.group>
        <x-nexus::form.group label="Catatan" full>
            <x-nexus::form.textarea rows="3" placeholder="Tulis catatan..." />
        </x-nexus::form.group>
        <x-nexus::form.group label="Cari" full>
            <x-nexus::search-box placeholder="Cari sesuatu..." />
        </x-nexus::form.group>
    </div>
    <div class="d-flex items-center gap-4 mt-4" style="flex-wrap:wrap">
        <x-nexus::toggle label="Aktifkan notifikasi" :checked="true" />
        <x-nexus::progress :value="64" label color="primary" class="flex-1">Upload</x-nexus::progress>
    </div>
    <div class="mt-4">
        <x-nexus::drop-zone hint="PDF/JPG, maks 2MB" />
    </div>
</x-nexus::card>

{{-- Datatable --}}
<x-nexus::datatable title="Datatable" sortable class="mb-5">
    <x-slot:actions>
        <x-nexus::button size="sm"><x-nexus::icons.plus :size="14" /> Tambah</x-nexus::button>
    </x-slot:actions>
    <x-slot:head>
        <th data-sort="name">Nama</th><th data-sort="email">Email</th><th>Status</th><th style="text-align:right">Aksi</th>
    </x-slot:head>
    @foreach([['Andi','andi@mail.com','Aktif'],['Budi','budi@mail.com','Nonaktif'],['Citra','citra@mail.com','Aktif']] as $r)
        <tr>
            <td>{{ $r[0] }}</td>
            <td>{{ $r[1] }}</td>
            <td><x-nexus::badge :color="$r[2]==='Aktif' ? 'green' : 'blue'">{{ $r[2] }}</x-nexus::badge></td>
            <td><x-nexus::table-actions view="#" edit="#" /></td>
        </tr>
    @endforeach
</x-nexus::datatable>

{{-- Tabs & Accordion --}}
<div class="grid-2 mb-5">
    <x-nexus::card title="Tabs">
        <x-nexus::tabs :tabs="['a'=>'Profil','b'=>'Keamanan','c'=>'Tagihan']" active="a">
            <x-nexus::tab-pane name="a" active>Konten profil.</x-nexus::tab-pane>
            <x-nexus::tab-pane name="b">Konten keamanan.</x-nexus::tab-pane>
            <x-nexus::tab-pane name="c">Konten tagihan.</x-nexus::tab-pane>
        </x-nexus::tabs>
    </x-nexus::card>

    <x-nexus::card title="Accordion">
        <x-nexus::accordion>
            <x-nexus::accordion-item title="Apa itu Nexus Admin?" open>Tema admin Laravel berbasis komponen Blade.</x-nexus::accordion-item>
            <x-nexus::accordion-item title="Apakah perlu build step?">Tidak, asset sudah pre-compiled.</x-nexus::accordion-item>
        </x-nexus::accordion>
    </x-nexus::card>
</div>

{{-- Dropdown, Activity, Empty state --}}
<div class="grid-3 mb-5">
    <x-nexus::card title="Dropdown">
        <x-nexus::dropdown label="Aksi">
            <a class="dropdown-item" href="#"><x-nexus::icons.edit :size="16" /> Edit</a>
            <a class="dropdown-item" href="#"><x-nexus::icons.eye :size="16" /> Lihat</a>
            <div class="dropdown-divider"></div>
            <button class="dropdown-item danger"><x-nexus::icons.trash :size="16" /> Hapus</button>
        </x-nexus::dropdown>
    </x-nexus::card>

    <x-nexus::card title="Activity">
        <x-nexus::activity>
            <x-nexus::activity-item color="primary" time="2 mnt">Invoice dibuat.</x-nexus::activity-item>
            <x-nexus::activity-item color="green" time="1 jam">Pembayaran diterima.</x-nexus::activity-item>
        </x-nexus::activity>
    </x-nexus::card>

    <x-nexus::card title="Empty State">
        <x-nexus::empty-state title="Belum ada data" desc="Tambahkan item pertama." icon="📭">
            <x-nexus::button size="sm">Tambah</x-nexus::button>
        </x-nexus::empty-state>
    </x-nexus::card>
</div>

{{-- Skeleton, Avatar group, Mini stat --}}
<div class="grid-3 mb-5">
    <x-nexus::card title="Skeleton">
        <x-nexus::skeleton variant="title" width="60%" />
        <div class="mt-2"><x-nexus::skeleton variant="text" :count="3" /></div>
    </x-nexus::card>
    <x-nexus::card title="Avatar Group">
        <x-nexus::avatar-group :items="['Andi Pratama','Budi Santoso','Citra Dewi','Dewi Lestari','Eka Putra']" :max="4" />
    </x-nexus::card>
    <x-nexus::card title="Mini Stats">
        <x-nexus::mini-stat label="Hari ini" value="42" />
        <x-nexus::mini-stat label="Pending" value="7" color="amber" />
    </x-nexus::card>
</div>

{{-- Jumbotron --}}
<x-nexus::jumbotron title="Selamat datang 👋" text="Ini contoh jumbotron untuk header halaman atau ajakan aksi." class="mb-5">
    <x-nexus::button>Mulai</x-nexus::button>
    <x-nexus::button variant="outline">Pelajari</x-nexus::button>
</x-nexus::jumbotron>

{{-- Gallery & Carousel --}}
<div class="grid-2 mb-5">
    <x-nexus::card title="Gallery (klik → lightbox)">
        <x-nexus::gallery>
            <x-nexus::gallery-item title="Foto 1" meta="2 MB" emoji="🏞️" />
            <x-nexus::gallery-item title="Foto 2" meta="1.4 MB" emoji="🌅" />
            <x-nexus::gallery-item title="Foto 3" meta="3 MB" emoji="🌄" />
        </x-nexus::gallery>
    </x-nexus::card>
    <x-nexus::card title="Carousel">
        <x-nexus::carousel>
            @foreach(['Slide 1','Slide 2','Slide 3','Slide 4'] as $s)
                <x-nexus::carousel-item style="width:200px">
                    <x-nexus::card>{{ $s }}</x-nexus::card>
                </x-nexus::carousel-item>
            @endforeach
        </x-nexus::carousel>
    </x-nexus::card>
</div>

{{-- Chart (butuh ApexCharts) --}}
<x-nexus::card-chart title="Chart (butuh ApexCharts)" subtitle="Lihat CHARTS.md" type="area"
    :series="[['name'=>'2025','data'=>[31,40,28,51,42,82]]]"
    :options="['xaxis'=>['categories'=>['Jan','Feb','Mar','Apr','Mei','Jun']]]"
    class="mb-5" />

{{-- Modal form --}}
<x-nexus::modal-form id="demoModal" title="Contoh Modal Form" action="#">
    <x-nexus::form.group label="Nama" for="m-name"><x-nexus::form.input id="m-name" name="name" /></x-nexus::form.group>
    <div class="mt-4">
        <x-nexus::form.group label="Email" for="m-email"><x-nexus::form.input id="m-email" type="email" /></x-nexus::form.group>
    </div>
</x-nexus::modal-form>

@endsection
