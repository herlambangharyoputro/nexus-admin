{{--
  Progress — bar progres (.progress-bar > .progress-fill).

  Usage:
    <x-nexus::progress :value="72" />
    <x-nexus::progress :value="40" color="green" label>Upload</x-nexus::progress>

  Props:
    value  : 0–100
    color  : primary (default) | green | amber | red | purple  (→ util bg-*)
    label  : true → tampilkan label + persen di atas bar (teks dari slot)
    height : override tinggi bar (mis. "10px")
--}}
@props([
    'value'  => 0,
    'color'  => 'primary',
    'label'  => false,
    'height' => null,
])

@php $v = max(0, min(100, (float) $value)); @endphp

<div {{ $attributes }}>
    @if($label)
        <div class="d-flex justify-between mb-1 text-xs text-muted">
            <span>{{ $slot->isEmpty() ? 'Progress' : $slot }}</span>
            <span>{{ round($v) }}%</span>
        </div>
    @endif
    <div class="progress-bar" @if($height) style="height: {{ $height }}" @endif
         role="progressbar" aria-valuenow="{{ round($v) }}" aria-valuemin="0" aria-valuemax="100">
        <div class="progress-fill bg-{{ $color }}" style="width: {{ $v }}%"></div>
    </div>
</div>
