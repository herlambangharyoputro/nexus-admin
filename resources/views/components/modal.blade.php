{{-- 
  Modal — overlay dialog. Triggered via data-modal-open or JS Modal.open(id).
  
  Usage 1 — basic:
    x-nexus::modal id="addUserModal" title="Tambah Pengguna">
        <p>Form di sini...</p>
    /x-nexus::modal>

    <!-- Trigger -->
    x-nexus::button data-modal-open="addUserModal">Tambah Pengguna/x-nexus::button>

  Usage 2 — dengan footer (slot 'footer'):
    x-nexus::modal id="confirmDelete" title="Hapus Item?">
        Aksi ini tidak bisa di-undo.
        x-slot:footer>
            x-nexus::button variant="outline" data-modal-close>Batal/x-nexus::button>
            x-nexus::button variant="danger">Hapus/x-nexus::button>
        /x-slot:footer>
    /x-nexus::modal>

  Usage 3 — open via JS:
    Modal.open('addUserModal');   // di app.js
    Modal.close();

  Props:
    id        : string (required) — untuk targeting via data-modal-open / JS
    title     : string → judul di header
    width     : default 540px (sesuai CSS). Override via inline style kalau perlu.
    noClose   : true → sembunyikan tombol X di header
--}}
@props([
    'id',
    'title'   => null,
    'noClose' => false,
])

<div id="{{ $id }}" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
    <div class="modal" {{ $attributes }}>

        {{-- Header --}}
        @if ($title || ! $noClose)
            <div class="modal-header">
                @if ($title)
                    <div class="modal-title" id="{{ $id }}-title">{{ $title }}</div>
                @endif

                @unless ($noClose)
                    <button type="button" class="modal-close" data-modal-close aria-label="Close">
                        &times;
                    </button>
                @endunless
            </div>
        @endif

        {{-- Body --}}
        <div class="modal-body">
            {{ $slot }}

            @isset ($footer)
                <div class="d-flex justify-end gap-2 mt-6">
                    {{ $footer }}
                </div>
            @endisset
        </div>

    </div>
</div>
