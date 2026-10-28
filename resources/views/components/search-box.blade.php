{{--
  Search Box — input pencarian ber-ikon (.search-box). Dipakai index-page di toolbar.

  Usage:
    x-nexus::search-box name="q" placeholder="Cari pengguna..." :value="request('q')" />

  Props:
    name        : nama input
    placeholder : teks placeholder
    value       : nilai awal
    label       : aria-label input (default: placeholder)
--}}
@props([
    'name'        => null,
    'placeholder' => 'Cari...',
    'value'       => null,
    'label'       => null,
])

<div {{ $attributes->merge(['class' => 'search-box']) }}>
    <x-nexus::icons.search :size="15" />
    <input type="search"
        @if($name) name="{{ $name }}" @endif
        placeholder="{{ $placeholder }}"
        aria-label="{{ $label ?? rtrim($placeholder, '.…') }}"
        @if(!is_null($value)) value="{{ $value }}" @endif>
</div>
