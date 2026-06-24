{{--
  Chart — shell ApexCharts (lib TIDAK dibundel; load ApexCharts di consumer).
  Render dilakukan modul JS Integrations bila window.ApexCharts tersedia.
  Lihat CHARTS.md untuk setup + 14 resep tipe chart.

  Usage:
    <x-nexus::chart type="line"
        :series="[['name'=>'Penjualan','data'=>[30,40,35,50,49,60]]]"
        :options="['xaxis'=>['categories'=>['Jan','Feb','Mar','Apr','Mei','Jun']]]"
        :height="300" />

  Props:
    type    : line | bar | area | pie | donut | radar | polarArea | radialBar
              | bubble | scatter | heatmap | candlestick | (mixed via series)
    series  : array seri ApexCharts
    options : array opsi ApexCharts tambahan (digabung; key 'chart' ikut di-merge)
    height  : tinggi px (default 300)
    id      : id elemen (opsional)
--}}
@props([
    'type'    => 'line',
    'series'  => [],
    'options' => [],
    'height'  => 300,
    'id'      => null,
])

@php
    $chartOpts = array_merge(
        ['type' => $type, 'height' => $height, 'toolbar' => ['show' => false], 'fontFamily' => 'inherit'],
        $options['chart'] ?? []
    );
    $rest = $options;
    unset($rest['chart']);
    $config = array_merge(['chart' => $chartOpts, 'series' => $series], $rest);
    $mountId = $id ?? 'chart-' . uniqid();
@endphp

<div id="{{ $mountId }}" class="chart-mount" style="min-height: {{ $height }}px" data-apexchart="@json($config)" {{ $attributes }}></div>
