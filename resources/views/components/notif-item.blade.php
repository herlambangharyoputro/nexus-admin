{{--
  Notif Item — satu notifikasi di dalam <x-nexus::notif-panel>.

  Props:
    id        : id notifikasi (untuk data-notif-id; klik → tandai dibaca)
    title     : judul
    message   : isi (opsional)
    time      : keterangan waktu (opsional)
    iconColor : primary (default) | amber | green | red | purple
    unread    : true → status belum dibaca (dot + highlight)
  Slot:
    icon      : ikon (opsional)
--}}
@props([
    'id'        => null,
    'title'     => '',
    'message'   => null,
    'time'      => null,
    'iconColor' => 'primary',
    'unread'    => false,
])

<div class="notif-item {{ $unread ? 'unread' : '' }}" @if($id) data-notif-id="{{ $id }}" @endif>
    @isset($icon)
        <div class="notif-icon bg-{{ $iconColor }}-soft text-{{ $iconColor }}">{{ $icon }}</div>
    @endisset
    <div class="notif-body">
        <div class="notif-title">{{ $title }}</div>
        @if($message)<div class="notif-msg">{{ $message }}</div>@endif
        @if($time)<div class="notif-time">{{ $time }}</div>@endif
    </div>
    @if($unread)<span class="notif-unread-dot"></span>@endif
</div>
