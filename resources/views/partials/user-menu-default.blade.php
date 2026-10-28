{{--
  User Menu (default) — isi dropdown menu user di topbar.
  Dipakai bila aplikasi TIDAK punya resources/views/nexus/user-menu.blade.php.

  Consumer (contoh):
    <a href="{{ route('profil.edit') }}" class="dropdown-item" role="menuitem">x-nexus::icons.user :size="16" /> <span>Profil</span></a>
    <div class="dropdown-divider"></div>
    <form method="POST" action="{{ route('logout') }}">@csrf
        <button type="submit" class="dropdown-item danger" role="menuitem">x-nexus::icons.log-out /> <span>Keluar</span></button>
    </form>
--}}
<a href="{{ \Illuminate\Support\Facades\Route::has('profile.edit') ? route('profile.edit') : url('/profile') }}" class="dropdown-item" role="menuitem">
    <x-nexus::icons.user :size="16" />
    <span>Profil</span>
</a>

<a href="{{ url('/pengaturan') }}" class="dropdown-item" role="menuitem">
    <x-nexus::icons.settings :size="16" />
    <span>Pengaturan</span>
</a>

<div class="dropdown-divider"></div>

@if(\Illuminate\Support\Facades\Route::has('logout'))
    {{-- Logout Laravel = POST + CSRF --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="dropdown-item danger" role="menuitem">
            <x-nexus::icons.log-out />
            <span>Keluar</span>
        </button>
    </form>
@else
    <a href="{{ url('/logout') }}" class="dropdown-item danger" role="menuitem" data-action="logout">
        <x-nexus::icons.log-out />
        <span>Keluar</span>
    </a>
@endif
