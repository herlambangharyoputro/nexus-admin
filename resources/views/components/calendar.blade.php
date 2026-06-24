{{--
  Calendar — kontainer kalender (shell FullCalendar; lib di consumer).
  Tidak auto-init (butuh konfigurasi events). Init FullCalendar sendiri
  menargetkan #id — lihat CHARTS.md (bagian Calendar).

  Usage:
    x-nexus::calendar id="kalender" />
    @push('scripts')
      <script>
        new FullCalendar.Calendar(document.getElementById('kalender'), {
          initialView: 'dayGridMonth',
          events: @json($events),
        }).render();
      </script>
    @endpush

  Props:
    id : id elemen (default acak)
--}}
@props(['id' => null])

@php $calId = $id ?? 'cal-' . uniqid(); @endphp

<div id="{{ $calId }}" class="calendar-mount" data-calendar {{ $attributes }}></div>
