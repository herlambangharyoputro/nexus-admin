{{--
  Card Chart — kartu berjudul berisi chart. Pola "card chart" dashboard.

  Usage:
    x-nexus::card-chart title="Penjualan" subtitle="6 bulan terakhir"
        type="area"
        :series="[['name'=>'2025','data'=>[31,40,28,51,42,82]]]"
        :options="['xaxis'=>['categories'=>['Jan','Feb','Mar','Apr','Mei','Jun']]]" />

    // atau chart kustom via slot:
    x-nexus::card-chart title="Custom">
        x-slot:chart>x-nexus::chart type="bar" :series="$s" />/x-slot:chart>
    /x-nexus::card-chart>

  Props: title, subtitle, type, series, options, height
  Slots: action (kanan header), chart (override chart)
--}}
@props([
    'title'    => null,
    'subtitle' => null,
    'type'     => 'line',
    'series'   => [],
    'options'  => [],
    'height'   => 300,
])

<x-nexus::card noBody {{ $attributes }}>
    @if($title || $subtitle || isset($action))
        <div class="card-header">
            <div>
                @if($title)<div class="card-title">{{ $title }}</div>@endif
                @if($subtitle)<div class="text-xs text-muted mt-1">{{ $subtitle }}</div>@endif
            </div>
            @isset($action)<div>{{ $action }}</div>@endisset
        </div>
    @endif

    <div class="card-body">
        @isset($chart)
            {{ $chart }}
        @else
            <x-nexus::chart :type="$type" :series="$series" :options="$options" :height="$height" />
        @endisset
    </div>
</x-nexus::card>
