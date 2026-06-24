{{--
  Avatar Group — tumpukan avatar yang saling tumpang tindih (.avatar-group .av).

  Usage:
    x-nexus::avatar-group :items="['Andi Pratama','Budi Santoso','Citra Dewi']" :max="3" />
    x-nexus::avatar-group :items="[
        ['name'=>'Andi','src'=>asset('a.jpg')],
        ['name'=>'Budi'],
    ]" />

  Props:
    items : array of (string nama) ATAU ['name'=>..., 'src'=>...]
    max   : tampilkan maksimal N avatar, sisanya jadi "+X" (default 5)
--}}
@props([
    'items' => [],
    'max'   => 5,
])

@php
    $shown = array_slice($items, 0, (int) $max);
    $extra = count($items) - count($shown);
    $ini = function ($name) {
        $p = explode(' ', trim((string) $name));
        return strtoupper(count($p) >= 2
            ? substr($p[0], 0, 1) . substr(end($p), 0, 1)
            : substr($p[0], 0, 2));
    };
@endphp

<div {{ $attributes->merge(['class' => 'avatar-group']) }}>
    @foreach($shown as $it)
        @php
            $name = is_array($it) ? ($it['name'] ?? '') : $it;
            $src  = is_array($it) ? ($it['src'] ?? null) : null;
        @endphp
        <div class="av" style="background: var(--primary); color: #fff" title="{{ $name }}">
            @if($src)
                <img src="{{ $src }}" alt="{{ $name }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">
            @else
                {{ $ini($name) }}
            @endif
        </div>
    @endforeach

    @if($extra > 0)
        <div class="av" style="background: var(--muted); color: #fff" title="{{ $extra }} lainnya">+{{ $extra }}</div>
    @endif

    {{ $slot }}
</div>
