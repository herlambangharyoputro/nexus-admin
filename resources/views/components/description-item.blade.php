{{--
  Description Item — satu pasangan label–nilai di dalam x-nexus::description-list.

  Usage:
    x-nexus::description-item label="Kode" :value="$aset->kode_aset" />
    x-nexus::description-item label="Alamat" full>{{ $lokasi->alamat }}/x-nexus::description-item>

  Props:
    label : teks label (<dt>)
    value : nilai (<dd>); bila kosong dipakai slot
    full  : true → membentang selebar list
    empty : teks pengganti bila nilai & slot kosong (default "—")
--}}
@props([
    'label' => null,
    'value' => null,
    'full'  => false,
    'empty' => '—',
])

@php
    $hasSlot  = trim((string) $slot) !== '';
    $hasValue = ! is_null($value) && $value !== '';
    if ($value instanceof \BackedEnum) $value = $value->value;
    if ($value instanceof \DateTimeInterface) $value = $value->format($value->format('H:i:s') === '00:00:00' ? 'd/m/Y' : 'd/m/Y H:i');
    if (is_bool($value)) $value = $value ? 'Ya' : 'Tidak';
    $ddClass = 'dl-value' . ($hasSlot || $hasValue ? '' : ' is-empty');
@endphp

<div {{ $attributes->merge(['class' => 'dl-item' . ($full ? ' full' : '')]) }}>
    <dt class="dl-label">{{ $label }}</dt>
    <dd class="{{ $ddClass }}">@if($hasSlot){{ $slot }}@elseif($hasValue){{ $value }}@else{{ $empty }}@endif</dd>
</div>
