{{--
  Jumbotron — banner/hero untuk header halaman atau ajakan aksi.

  Usage:
    <x-nexus::jumbotron title="Selamat datang, Herlambang 👋" text="Ringkasan aktivitas bisnis Anda hari ini.">
        <x-nexus::button>Mulai</x-nexus::button>
        <x-nexus::button variant="outline">Pelajari</x-nexus::button>
    </x-nexus::jumbotron>

  Props:
    title : judul besar
    text  : deskripsi
  Slot   : tombol aksi (opsional)
--}}
@props([
    'title' => null,
    'text'  => null,
])

<div {{ $attributes->merge(['class' => 'jumbotron']) }}>
    @if($title)<div class="jumbotron-title">{{ $title }}</div>@endif
    @if($text)<div class="jumbotron-text">{{ $text }}</div>@endif
    @if(!$slot->isEmpty())<div class="jumbotron-actions">{{ $slot }}</div>@endif
</div>
