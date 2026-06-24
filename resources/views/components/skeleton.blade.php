{{--
  Skeleton — placeholder loading shimmer.

  Usage:
    <x-nexus::skeleton variant="title" />
    <x-nexus::skeleton variant="text" :count="3" />
    <x-nexus::skeleton variant="box" height="120px" />
    <x-nexus::skeleton width="50%" />

  Props:
    variant : text (default) | title | box
    width   : override lebar (mis. "60%")
    height  : override tinggi (mis. "120px")
    count   : jumlah baris (default 1)
    gap     : jarak antar baris saat count>1 (default 8px)
--}}
@props([
    'variant' => 'text',
    'width'   => null,
    'height'  => null,
    'count'   => 1,
    'gap'     => '8px',
])

@php
    $cls = 'skeleton';
    if (in_array($variant, ['text','title','box'])) $cls .= ' skeleton-' . $variant;
    $style = '';
    if ($width)  $style .= 'width: ' . $width . ';';
    if ($height) $style .= 'height: ' . $height . ';';
    $n = max(1, (int) $count);
@endphp

@if($n === 1)
    <div {{ $attributes->merge(['class' => $cls]) }} @if($style) style="{{ $style }}" @endif></div>
@else
    <div {{ $attributes->merge(['class' => 'd-flex flex-col']) }} style="gap: {{ $gap }}">
        @for($i = 0; $i < $n; $i++)
            <div class="{{ $cls }}" @if($style) style="{{ $style }}" @endif></div>
        @endfor
    </div>
@endif
