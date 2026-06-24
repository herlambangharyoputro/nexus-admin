{{-- 
  Card — primary content container.
  
  Usage 1 — body-only:
    <x-nexus::card>
        Isi card di sini
    </x-nexus::card>

  Usage 2 — dengan title:
    <x-nexus::card title="Statistik Bulan Ini">
        Isi card
    </x-nexus::card>

  Usage 3 — dengan title + action di header (slot 'header-action'):
    <x-nexus::card title="Pengguna Terbaru">
        <x-slot:headerAction>
            <x-nexus::button variant="ghost" size="sm">Lihat semua</x-nexus::button>
        </x-slot:headerAction>
        Isi card
    </x-nexus::card>

  Usage 4 — fully custom header (slot 'header'):
    <x-nexus::card>
        <x-slot:header>
            <div>Custom header markup</div>
        </x-slot:header>
        Isi card
    </x-nexus::card>

  Props:
    title    : string → render sebagai .card-title di header
    noBody   : true   → skip .card-body wrapper (kalau mau struktur custom)
--}}
@props([
    'title'   => null,
    'noBody'  => false,
])

<div {{ $attributes->merge(['class' => 'card']) }}>

    {{-- Header: full slot atau title+action pattern --}}
    @if (isset($header))
        <div class="card-header">{{ $header }}</div>
    @elseif ($title)
        <div class="card-header">
            <div class="card-title">{{ $title }}</div>
            @isset($headerAction)
                <div>{{ $headerAction }}</div>
            @endisset
        </div>
    @endif

    {{-- Body --}}
    @if ($noBody)
        {{ $slot }}
    @else
        <div class="card-body">{{ $slot }}</div>
    @endif

</div>
