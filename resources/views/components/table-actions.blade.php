{{--
  Table Actions — grup tombol aksi baris (lihat / edit / hapus).

  Usage:
    x-nexus::table-actions
        :view="route('users.show',$u)"
        :edit="route('users.edit',$u)"
        :delete="route('users.destroy',$u)" />

    // aksi tambahan via slot:
    x-nexus::table-actions :edit="$editUrl">
        x-nexus::button variant="ghost" size="sm">Lainnya/x-nexus::button>
    /x-nexus::table-actions>

  Props:
    view, edit    : URL → render tombol ikon (anchor)
    delete        : URL → render form POST+DELETE (butuh @csrf, otomatis)
    deleteConfirm : pesan konfirmasi (hook data-confirm; JS di CP3)

  Catatan: tombol hapus memakai data-confirm; perilaku konfirmasi
  diaktifkan oleh modul Confirm (ditambahkan di CP3). Tanpa JS itu,
  form tetap ter-submit langsung.
--}}
@props([
    'view'          => null,
    'edit'          => null,
    'delete'        => null,
    'deleteConfirm' => 'Hapus item ini? Tindakan ini tidak bisa dibatalkan.',
])

<div {{ $attributes->merge(['class' => 'd-flex gap-1 justify-end']) }}>
    @if($view)
        <a href="{{ $view }}" class="btn btn-ghost btn-icon btn-sm" aria-label="Lihat">
            <x-nexus::icons.eye :size="15" />
        </a>
    @endif

    @if($edit)
        <a href="{{ $edit }}" class="btn btn-outline btn-icon btn-sm" aria-label="Edit">
            <x-nexus::icons.edit :size="15" />
        </a>
    @endif

    @if($delete)
        <form method="POST" action="{{ $delete }}" class="d-inline" data-confirm="{{ $deleteConfirm }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-icon btn-sm" aria-label="Hapus">
                <x-nexus::icons.trash :size="15" />
            </button>
        </form>
    @endif

    {{ $slot }}
</div>
