{{--
  Tabs — navigasi tab + panel (switch via app.js modul Tabs).

  Usage:
    x-nexus::tabs :tabs="['profile'=>'Profil','security'=>'Keamanan']" active="profile">
        x-nexus::tab-pane name="profile" active>Konten profil.../x-nexus::tab-pane>
        x-nexus::tab-pane name="security">Konten keamanan.../x-nexus::tab-pane>
    /x-nexus::tabs>

  Props:
    tabs   : array [key => label]
    active : key tab aktif (default: tab pertama)
--}}
@props([
    'tabs'   => [],
    'active' => null,
])

@php $active = $active ?? array_key_first($tabs); @endphp

<div {{ $attributes->merge(['class' => 'tabs']) }} data-tabs>
    <div class="settings-tabs">
        @foreach($tabs as $key => $label)
            <button type="button" class="settings-tab {{ $key === $active ? 'active' : '' }}" data-tab="{{ $key }}">{{ $label }}</button>
        @endforeach
    </div>
    {{ $slot }}
</div>
