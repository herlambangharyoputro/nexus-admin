{{-- 
  Sidebar — dashboard navigation.
  Active state: dideteksi via request()->is('path*').
  Kalau pakai named routes, ganti dengan: request()->routeIs('route.name.*')
--}}
<aside class="sidebar">

    {{-- Header dengan logo --}}
    <div class="sidebar-header">
        <div class="logo-icon">NX</div>
        <div class="logo-text">Nexus Admin</div>
    </div>

    {{-- Nav items --}}
    <nav class="sidebar-nav">

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

        <a href="{{ url('/produk') }}"
           class="nav-item {{ request()->is('produk*') ? 'active' : '' }}">
            <x-nexus::icons.package />
            <span class="nav-label">Produk</span>
        </a>

        <div class="nav-section-label">Lainnya</div>

        <a href="{{ url('/pengaturan') }}"
           class="nav-item {{ request()->is('pengaturan*') ? 'active' : '' }}">
            <x-nexus::icons.settings />
            <span class="nav-label">Pengaturan</span>
        </a>

    </nav>

</aside>
