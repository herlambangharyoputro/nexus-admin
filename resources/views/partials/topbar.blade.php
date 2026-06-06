{{-- 
  Topbar — sticky header.
  
  Variables yang dipakai:
    @section('page-title')    — judul utama breadcrumb (default: 'Dashboard')
    @section('breadcrumb')    — sub-judul setelah / (optional)
    $userName                 — nama user (default: 'Andi Pratama')
    $userRole                 — role user (default: 'Admin')
    $userInitials             — initial untuk avatar (default: auto dari $userName)

  Nanti saat auth ready, ganti default-nya dengan auth()->user().
--}}
@php
    // Default dummy data — nanti override dari controller atau auth()
    $userName     = $userName     ?? 'Andi Pratama';
    $userRole     = $userRole     ?? 'Admin';
    $userInitials = $userInitials ?? collect(explode(' ', trim($userName)))
                        ->map(fn($p) => strtoupper(substr($p, 0, 1)))
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

        {{-- Notifications --}}
        <button class="topbar-btn" data-action="notifications" aria-label="Notifications">
            <x-nexus::icons.bell />
            <span class="notif-dot"></span>
        </button>

        {{-- User Menu (avatar + name + role + dropdown) --}}
        <div class="user-menu" data-user-menu aria-haspopup="true" aria-expanded="false">

            <div class="avatar avatar-sm">{{ $userInitials }}</div>

            <div class="user-menu-info">
                <span class="user-menu-name">{{ $userName }}</span>
                <span class="user-menu-role">{{ $userRole }}</span>
            </div>

            {{-- Dropdown --}}
            <div class="dropdown-menu" data-user-dropdown role="menu">

                <a href="{{ url('/profile') }}" class="dropdown-item" role="menuitem">
                    <x-nexus::icons.user />
                    <span>Profile</span>
                </a>

                <a href="{{ url('/pengaturan') }}" class="dropdown-item" role="menuitem">
                    <x-nexus::icons.settings size="16" />
                    <span>Setting</span>
                </a>

                <div class="dropdown-divider"></div>

                <a href="{{ url('/logout') }}" 
                   class="dropdown-item danger" 
                   role="menuitem"
                   data-action="logout">
                    <x-nexus::icons.log-out />
                    <span>Logout</span>
                </a>

            </div>

        </div>

    </div>

</header>
