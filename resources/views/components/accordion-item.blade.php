{{--
  Accordion Item — satu panel di dalam x-nexus::accordion>.

  Props:
    title : judul header
    open  : true → terbuka saat awal
--}}
@props([
    'title' => null,
    'open'  => false,
])

<div class="accordion-item {{ $open ? 'open' : '' }}">
    <button type="button" class="accordion-header" data-accordion-toggle>
        <span>{{ $title }}</span>
        <x-nexus::icons.chevron-down :size="16" class="accordion-chevron" />
    </button>
    <div class="accordion-body" @if($open) style="max-height:none" @endif>
        <div class="accordion-body-inner">{{ $slot }}</div>
    </div>
</div>
