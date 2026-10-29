{{--
  Sidebar Nav (default/contoh) — dipakai bila aplikasi TIDAK punya
  resources/views/nexus/sidebar-nav.blade.php.

  Consumer: buat file itu di aplikasi (milik aplikasi, bukan salinan paket) berisi
  .nav-section-label dan a.nav-item. Contoh:

    <div class="nav-section-label">Menu Utama</div>
    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        x-nexus::icons.home />
        <span class="nav-label">Dashboard</span>
    </a>
--}}
<div class="nav-section-label">Menu Utama</div>

<a href="{{ url('/dashboard') }}"
   class="nav-item {{ request()->is('dashboard') || request()->is('/') ? 'active' : '' }}">
    <x-nexus::icons.home />
    <span class="nav-label">Dashboard</span>
</a>

<a href="{{ url('/pengguna') }}"
   class="nav-item {{ request()->is('pengguna*') ? 'active' : '' }}">
    <x-nexus::icons.users />
    <span class="nav-label">Pengguna</span>
    <span class="nav-badge">3</span>
</a>

<a href="{{ url('/pesanan') }}"
   class="nav-item {{ request()->is('pesanan*') ? 'active' : '' }}">
    <x-nexus::icons.shopping-bag />
    <span class="nav-label">Pesanan</span>
</a>

<a href="{{ url('/nexus-product') }}"
   class="nav-item {{ request()->is('produk*') || request()->is('nexus-product') || request()->is('nexus-product/*') ? 'active' : '' }}">
    <x-nexus::icons.package />
    <span class="nav-label">Produk</span>
</a>

<div class="nav-section-label">Lainnya</div>

<a href="{{ url('/pengaturan') }}"
   class="nav-item {{ request()->is('pengaturan*') ? 'active' : '' }}">
    <x-nexus::icons.settings />
    <span class="nav-label">Pengaturan</span>
</a>

<a href="{{ url('/nexus-demo') }}"
   class="nav-item {{ request()->is('nexus-demo') || request()->is('nexus-demo/*') ? 'active' : '' }}">
    <x-nexus::icons.eye />
    <span class="nav-label">Demo</span>
</a>
