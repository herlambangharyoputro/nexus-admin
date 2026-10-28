{{--
  Sidebar — dashboard navigation (kontrak shell: .sidebar > .sidebar-nav).

  Titik ekstensi (v0.5) — TIDAK perlu meng-override file ini:
    resources/views/nexus/brand.blade.php        → logo + nama (default: partials/brand-default)
    resources/views/nexus/sidebar-nav.blade.php  → item navigasi (default: partials/sidebar-nav-default)
  Active state: request()->routeIs('nama.*') atau request()->is('path*').
--}}
<aside class="sidebar">

    {{-- Header dengan logo --}}
    <div class="sidebar-header">
        @includeFirst(['nexus.brand', 'nexus::partials.brand-default'])
    </div>

    {{-- Nav items --}}
    <nav class="sidebar-nav" aria-label="Navigasi utama">
        @includeFirst(['nexus.sidebar-nav', 'nexus::partials.sidebar-nav-default'])
    </nav>

</aside>
