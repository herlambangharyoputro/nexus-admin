{{--
  Dropdown — menu klik (toggle .show via app.js modul Dropdown).

  Usage 1 — trigger default:
    <x-nexus::dropdown label="Aksi">
        <a class="dropdown-item" href="#"><x-nexus::icons.edit :size="16" /> Edit</a>
        <div class="dropdown-divider"></div>
        <button class="dropdown-item danger"><x-nexus::icons.trash :size="16" /> Hapus</button>
    </x-nexus::dropdown>

  Usage 2 — trigger custom (WAJIB beri data-dropdown-trigger):
    <x-nexus::dropdown align="left">
        <x-slot:trigger>
            <button class="btn btn-icon btn-ghost" data-dropdown-trigger><x-nexus::icons.menu /></button>
        </x-slot:trigger>
        ...
    </x-nexus::dropdown>

  Props:
    label : teks trigger default
    align : right (default) | left
--}}
@props([
    'label' => 'Menu',
    'align' => 'right',
])

<div {{ $attributes->merge(['class' => 'dropdown']) }} data-dropdown>
    @isset($trigger)
        {{ $trigger }}
    @else
        <button type="button" class="btn btn-outline btn-sm" data-dropdown-trigger>
            {{ $label }} <x-nexus::icons.chevron-down :size="14" />
        </button>
    @endisset

    <div class="dropdown-menu {{ $align === 'left' ? 'left' : '' }}">
        {{ $slot }}
    </div>
</div>
