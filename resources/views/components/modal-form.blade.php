{{--
  Modal Form — modal berisi form (header + body fields + footer submit/cancel).
  Memakai modul Modal yang sudah ada (trigger via data-modal-open="id").

  Usage:
    <x-nexus::button data-modal-open="addUser">Tambah</x-nexus::button>

    <x-nexus::modal-form id="addUser" title="Tambah Pengguna" :action="route('users.store')" method="POST">
        <x-nexus::form.group label="Nama" for="n"><x-nexus::form.input id="n" name="name" /></x-nexus::form.group>
        <x-nexus::form.group label="Email" for="e"><x-nexus::form.input id="e" name="email" type="email" /></x-nexus::form.group>
    </x-nexus::modal-form>

  Props:
    id          : required — target data-modal-open / Modal.open(id)
    title       : judul header
    action      : URL submit
    method      : POST (default) | GET | PUT | PATCH | DELETE (otomatis @method+@csrf)
    submitLabel : teks tombol submit (default "Simpan")
    cancelLabel : teks tombol batal (default "Batal")
    width       : mis. "640px" → override lebar modal
--}}
@props([
    'id',
    'title'       => null,
    'action'      => '#',
    'method'      => 'POST',
    'submitLabel' => 'Simpan',
    'cancelLabel' => 'Batal',
    'width'       => null,
])

<div id="{{ $id }}" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
    <div class="modal" @if($width) style="max-width: {{ $width }}" @endif>
        <div class="modal-header">
            @if($title)<div class="modal-title" id="{{ $id }}-title">{{ $title }}</div>@endif
            <button type="button" class="modal-close" data-modal-close aria-label="Close">&times;</button>
        </div>

        <form method="{{ $method === 'GET' ? 'GET' : 'POST' }}" action="{{ $action }}">
            @if($method !== 'GET') @csrf @endif
            @if(!in_array($method, ['GET','POST'])) @method($method) @endif

            <div class="modal-body">
                {{ $slot }}

                <div class="d-flex justify-end gap-2 mt-6">
                    <button type="button" class="btn btn-outline" data-modal-close>{{ $cancelLabel }}</button>
                    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
