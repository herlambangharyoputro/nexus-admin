{{--
  Topbar — sticky header.

  Sections:
    @section('page-title')    — judul utama breadcrumb (default: 'Dashboard')
    @section('breadcrumb')    — sub-judul setelah / (opsional)

  Data user (urutan prioritas):
    1. variabel $userName / $userRole / $userInitials (mis. dari View::composer)
    2. auth()->user(): name; peran dari role_label ?? role
    3. contoh 'Andi Pratama' / 'Admin' (hanya bila tamu, untuk halaman demo)

  Titik ekstensi (v0.5) — TIDAK perlu meng-override file ini:
    resources/views/nexus/topbar-actions.blade.php → tombol sebelum menu user
        (default: partials/topbar-actions-default = tombol notifikasi)
    resources/views/nexus/user-menu.blade.php      → isi dropdown menu user
        (default: partials/user-menu-default = Profil, Pengaturan, Keluar)
--}}
@php
    $authUser = auth()->check() ? auth()->user() : null;
    $roleOf   = function ($u) {
        $role = $u->role_label ?? $u->role ?? null;
        if ($role instanceof \BackedEnum) $role = $role->value;
        return is_scalar($role) ? (string) $role : null;
    };

    $userName     = $userName ?? ($authUser->name ?? 'Andi Pratama');
    $userRole     = $userRole ?? ($authUser ? $roleOf($authUser) : 'Admin');
    $userInitials = $userInitials ?? collect(explode(' ', trim($userName)))
                        ->filter()
                        ->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))
                        ->take(2)
                        ->implode('');
@endphp

<header class="topbar">

    {{-- Sidebar toggle (hamburger) --}}
    <button class="topbar-toggle" data-sidebar-toggle aria-label="Toggle sidebar">
        <x-nexus::icons.menu />
    </button>

    {{-- Breadcrumb --}}
    <div class="topbar-breadcrumb">
        @yield('page-title', 'Dashboard')
        @hasSection('breadcrumb')
            <span>/ @yield('breadcrumb')</span>
        @endif
    </div>

    {{-- Right-side actions --}}
    <div class="topbar-right">

        @includeFirst(['nexus.topbar-actions', 'nexus::partials.topbar-actions-default'])

        {{-- User Menu (avatar + name + role + dropdown) --}}
        <div class="user-menu" data-user-menu aria-haspopup="true" aria-expanded="false">

            <div class="avatar avatar-sm">{{ $userInitials }}</div>

            <div class="user-menu-info">
                <span class="user-menu-name">{{ $userName }}</span>
                @if($userRole)<span class="user-menu-role">{{ $userRole }}</span>@endif
            </div>

            {{-- Dropdown --}}
            <div class="dropdown-menu" data-user-dropdown role="menu">
                <div class="dropdown-header">
                    <strong>{{ $userName }}</strong>
                    @if($userRole)<span>{{ $userRole }}</span>@endif
                </div>
                <div class="dropdown-divider"></div>

                @includeFirst(['nexus.user-menu', 'nexus::partials.user-menu-default'])
            </div>

        </div>

    </div>

</header>
