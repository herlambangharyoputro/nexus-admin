{{--
  Summary Card — kartu ringkasan horizontal (ikon kiri, value+label kanan).
  Varian ringkas dari stat-card (yang vertikal). Cocok untuk baris ringkasan.

  Usage:
    <x-nexus::summary-card label="Total Pesanan" value="1.247" icon-color="primary" change="+8%" change-direction="up">
        <x-slot:icon><x-nexus::icons.shopping-bag /></x-slot:icon>
    </x-nexus::summary-card>

  Props:
    label, value    : teks
    iconColor       : primary (default) | amber | green | red | purple
    change          : mis "+8%" (opsional)
    changeDirection : up | down
--}}
@props([
    'label'           => '',
    'value'           => '',
    'iconColor'       => 'primary',
    'change'          => null,
    'changeDirection' => null,
])

<div {{ $attributes->merge(['class' => 'card p-5']) }}>
    <div class="d-flex items-center gap-4">
        @isset($icon)
            <div class="stat-icon bg-{{ $iconColor }}-soft text-{{ $iconColor }}">{{ $icon }}</div>
        @endisset

        <div class="flex-1">
            <div class="stat-value">{{ $value }}</div>
            <div class="stat-label">{{ $label }}</div>
        </div>

        @if($change && $changeDirection)
            <span class="stat-change {{ $changeDirection }}">{{ $change }}</span>
        @endif
    </div>
</div>
