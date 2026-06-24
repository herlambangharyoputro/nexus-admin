{{--
  Empty State — placeholder saat tidak ada data.

  Usage:
    x-nexus::empty-state title="Belum ada pengguna" desc="Tambahkan pengguna pertama Anda." icon="👥">
        x-nexus::button>x-nexus::icons.plus :size="14" /> Tambah/x-nexus::button>
    /x-nexus::empty-state>

  Props:
    icon  : emoji/teks ikon (default 📭)
    title : judul
    desc  : deskripsi (opsional)
  Slot   : tombol aksi (opsional)
--}}
@props([
    'icon'  => '📭',
    'title' => 'Tidak ada data',
    'desc'  => null,
])

<div {{ $attributes->merge(['class' => 'empty-state']) }}>
    <div class="empty-state-icon">{{ $icon }}</div>
    <div class="empty-state-title">{{ $title }}</div>
    @if($desc)<div class="empty-state-desc">{{ $desc }}</div>@endif
    @if(!$slot->isEmpty())
        <div class="mt-4 d-flex justify-center gap-2">{{ $slot }}</div>
    @endif
</div>
