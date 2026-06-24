{{--
  Breadcrumb — jejak navigasi. Layout nexus::layouts.app menyediakan
  @yield('breadcrumb'), komponen ini mengisinya.

  Usage:
    x-nexus::breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => url('/')],
        ['label' => 'Pengguna', 'url' => route('users.index')],
        ['label' => 'Detail'],
    ]" />

  Props:
    items : array of ['label' => string, 'url' => string|null]
            Item terakhir otomatis jadi current (tanpa link).
--}}
@props(['items' => []])

<nav {{ $attributes->merge(['class' => 'breadcrumb']) }} aria-label="Breadcrumb">
    @foreach($items as $i => $item)
        @php
            $label  = is_array($item) ? ($item['label'] ?? '') : $item;
            $url    = is_array($item) ? ($item['url'] ?? null) : null;
            $isLast = $i === array_key_last($items);
        @endphp

        @if($url && !$isLast)
            <a href="{{ $url }}">{{ $label }}</a>
        @else
            <span class="current" @if($isLast) aria-current="page" @endif>{{ $label }}</span>
        @endif

        @unless($isLast)
            <span class="sep"><x-nexus::icons.chevron-right :size="13" /></span>
        @endunless
    @endforeach

    {{ $slot }}
</nav>
