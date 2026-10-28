{{--
  Topbar Actions (default) — tombol di kanan topbar sebelum menu user.
  Dipakai bila aplikasi TIDAK punya resources/views/nexus/topbar-actions.blade.php.
  Tombol notifikasi men-toggle x-nexus::notif-panel bila dipasang.
--}}
<button class="topbar-btn" data-action="notifications" aria-label="Notifikasi">
    <x-nexus::icons.bell />
    <span class="notif-dot"></span>
</button>
