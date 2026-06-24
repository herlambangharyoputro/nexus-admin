{{--
  Tab Pane — panel untuk satu tab. Dipakai di dalam x-nexus::tabs>.

  Props:
    name   : key yang cocok dengan key di :tabs
    active : true → panel awal yang tampil
--}}
@props([
    'name',
    'active' => false,
])

<div class="tab-pane {{ $active ? 'active' : '' }}" data-pane="{{ $name }}">
    {{ $slot }}
</div>
