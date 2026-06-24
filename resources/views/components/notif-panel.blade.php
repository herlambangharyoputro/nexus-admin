{{--
  Notif Panel — panel notifikasi (di-toggle tombol bell topbar [data-action="notifications"]).
  PASANG SEKALI (mis. di layout). Isi dengan <x-nexus::notif-item>.

  Usage:
    <x-nexus::notif-panel>
        <x-nexus::notif-item id="1" title="Invoice baru" message="#INV-021 dibuat" time="2 menit" icon-color="primary" unread>
            <x-slot:icon><x-nexus::icons.bell :size="15" /></x-slot:icon>
        </x-nexus::notif-item>
    </x-nexus::notif-panel>

  Props:
    title : judul panel
  Catatan: tombol "Tandai semua" memakai data-notif-mark-all (modul Notif).
--}}
@props(['title' => 'Notifikasi'])

<div class="notif-panel" data-notif-panel style="display:none">
    <div class="notif-header">
        <div class="notif-title">{{ $title }}</div>
        <button type="button" class="notif-clear" data-notif-mark-all>Tandai semua dibaca</button>
    </div>
    <div class="notif-list">
        {{ $slot }}
    </div>
</div>
