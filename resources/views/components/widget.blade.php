{{--
  Widget — panel dashboard (header judul + subtitle + aksi, body, footer opsional).

  Usage:
    <x-nexus::widget title="Penjualan" subtitle="30 hari terakhir">
        <x-slot:action><x-nexus::dropdown label="Periode">...</x-nexus::dropdown></x-slot:action>
        ... isi widget ...
        <x-slot:footer><x-nexus::button variant="ghost" size="sm" full-width>Lihat detail</x-nexus::button></x-slot:footer>
    </x-nexus::widget>

  Props:
    title, subtitle : header
  Slots:
    action  : kanan header
    default : isi body
    footer  : kaki widget (opsional)
--}}
@props([
    'title'    => null,
    'subtitle' => null,
])

<x-nexus::card noBody {{ $attributes }}>
    @if($title || isset($action))
        <div class="card-header">
            <div>
                @if($title)<div class="card-title">{{ $title }}</div>@endif
                @if($subtitle)<div class="text-xs text-muted mt-1">{{ $subtitle }}</div>@endif
            </div>
            @isset($action)<div>{{ $action }}</div>@endisset
        </div>
    @endif

    <div class="card-body">{{ $slot }}</div>

    @isset($footer)
        <div class="card-body pt-0">{{ $footer }}</div>
    @endisset
</x-nexus::card>
