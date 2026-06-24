{{--
  Activity Item — satu baris feed (dot warna + teks + waktu).

  Props:
    color : primary (default) | amber | green | red | purple → warna dot (bg-*)
    time  : keterangan waktu (opsional)
--}}
@props([
    'color' => 'primary',
    'time'  => null,
])

<div class="activity-item">
    <div class="activity-dot bg-{{ $color }}"></div>
    <div class="activity-content">
        <div class="activity-text">{{ $slot }}</div>
        @if($time)<div class="activity-time">{{ $time }}</div>@endif
    </div>
</div>
