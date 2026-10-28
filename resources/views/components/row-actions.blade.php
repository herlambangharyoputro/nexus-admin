{{--
  Row Actions — tombol "⋮" berisi dropdown semua aksi per baris. KANONIK untuk kolom aksi
  (menggantikan table-actions). Menu mengambang (position: fixed): tidak terpotong oleh
  kontainer tabel yang overflow, dijepit ke viewport, dan membuka ke atas bila ruang bawah kurang.

  Usage:
    x-nexus::row-actions
        :view="route('aset.show', $a)"
        :edit="route('aset.edit', $a)"
        :delete="route('aset.destroy', $a)"
        delete-confirm="Hapus aset {{ $a->kode }}?" />

    // item tambahan via slot (disisipkan sebelum Hapus):
    x-nexus::row-actions :edit="route('aset.edit', $a)">
        <a class="dropdown-item" role="menuitem" href="{{ route('aset.qr', $a) }}">Cetak QR</a>
    /x-nexus::row-actions>

    // sebagai menu "Lainnya" di header index-page (tombol berteks):
    x-nexus::row-actions label="Lainnya" :icon="false">
        <a class="dropdown-item" role="menuitem" href="{{ route('aset.export') }}">Export Excel</a>
    /x-nexus::row-actions>

  Props:
    view, edit    : URL → item "Detail", "Edit"
    delete        : URL → form POST + @method('DELETE') + data-confirm (item "Hapus", danger, paling bawah)
    deleteConfirm : pesan konfirmasi (butuh x-nexus::confirm-dialog di layout)
    viewLabel, editLabel, deleteLabel : default Detail | Edit | Hapus
    label         : aria-label tombol (default "Aksi"); teks tombol bila icon=false
    icon          : true (default) → tombol ikon ⋮ ; false → tombol berteks + chevron
    align         : right (default) | left — sisi menu terhadap tombol
    primaryHref, primaryLabel : alias kompatibel override simantara (= view, viewLabel)
--}}
@props([
    'view'          => null,
    'edit'          => null,
    'delete'        => null,
    'deleteConfirm' => 'Hapus item ini? Tindakan ini tidak bisa dibatalkan.',
    'viewLabel'     => 'Detail',
    'editLabel'     => 'Edit',
    'deleteLabel'   => 'Hapus',
    'label'         => 'Aksi',
    'icon'          => true,
    'align'         => 'right',
    'primaryHref'   => null,
    'primaryLabel'  => null,
])

@php
    $view      = $view ?? $primaryHref;
    $viewLabel = $primaryLabel ?? $viewLabel;
    $hasSlot   = ! $slot->isEmpty();
@endphp

<div {{ $attributes->merge(['class' => 'dropdown row-actions']) }} data-dropdown="float" data-align="{{ $align === 'left' ? 'left' : 'right' }}">
    @if($icon)
        <button type="button" class="btn btn-soft btn-icon btn-sm row-actions-trigger"
                data-dropdown-trigger aria-haspopup="menu" aria-expanded="false" aria-label="{{ $label }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="5" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="12" cy="19" r="1.8"/></svg>
        </button>
    @else
        <button type="button" class="btn btn-outline row-actions-trigger"
                data-dropdown-trigger aria-haspopup="menu" aria-expanded="false">
            {{ $label }} <x-nexus::icons.chevron-down :size="14" />
        </button>
    @endif

    <div class="dropdown-menu" role="menu">
        @if($view)
            <a class="dropdown-item" role="menuitem" href="{{ $view }}"><x-nexus::icons.eye :size="16" /> {{ $viewLabel }}</a>
        @endif
        @if($edit)
            <a class="dropdown-item" role="menuitem" href="{{ $edit }}"><x-nexus::icons.edit :size="16" /> {{ $editLabel }}</a>
        @endif

        {{ $slot }}

        @if($delete)
            @if($view || $edit || $hasSlot)<div class="dropdown-divider" role="separator"></div>@endif
            <form method="POST" action="{{ $delete }}" data-confirm="{{ $deleteConfirm }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="dropdown-item danger" role="menuitem"><x-nexus::icons.trash :size="16" /> {{ $deleteLabel }}</button>
            </form>
        @endif
    </div>
</div>
