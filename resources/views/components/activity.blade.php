{{--
  Activity — kontainer feed aktivitas. Isi dengan x-nexus::activity-item>.

  Usage:
    x-nexus::card title="Aktivitas Terbaru">
        x-nexus::activity>
            x-nexus::activity-item color="primary" time="2 menit lalu">
                <strong>Andi</strong> membuat invoice baru.
            /x-nexus::activity-item>
            x-nexus::activity-item color="green" time="1 jam lalu">
                Pembayaran <strong>#INV-021</strong> diterima.
            /x-nexus::activity-item>
        /x-nexus::activity>
    /x-nexus::card>
--}}
<div {{ $attributes->merge(['class' => 'activity-feed']) }}>
    {{ $slot }}
</div>
