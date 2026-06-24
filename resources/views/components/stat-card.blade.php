{{-- 
  Stat Card — KPI block dengan icon, value, label, dan optional change indicator.
  
  Usage 1 — basic:
    x-nexus::stat-card 
        label="Total Pengguna" 
        value="1,247" 
        icon-color="primary">
        x-slot:icon>x-nexus::icons.users />/x-slot:icon>
    /x-nexus::stat-card>

  Usage 2 — dengan change indicator (naik/turun):
    x-nexus::stat-card 
        label="Penjualan" 
        value="Rp 12.4M" 
        icon-color="green"
        change="+12.5%" 
        change-direction="up">
        x-slot:icon>x-nexus::icons.shopping-bag />/x-slot:icon>
    /x-nexus::stat-card>

  Usage di grid 4 kolom:
    <div class="stats-grid">
        x-nexus::stat-card .../>
        x-nexus::stat-card .../>
        x-nexus::stat-card .../>
        x-nexus::stat-card .../>
    </div>

  Props:
    label           : label kecil di bawah value
    value           : nilai utama (string atau angka pre-formatted)
    iconColor       : primary (default) | amber | green | red | purple
                      → kontrol warna icon background (pakai *-soft)
    change          : string optional, mis "+12.5%" atau "-3.2%"
    changeDirection : up | down → kontrol warna change indicator
--}}
@props([
    'label'           => '',
    'value'           => '',
    'iconColor'       => 'primary',
    'change'          => null,
    'changeDirection' => null,
])

<div {{ $attributes->merge(['class' => 'stat-card']) }}>

    <div class="stat-top">
        @isset ($icon)
            <div class="stat-icon bg-{{ $iconColor }}-soft text-{{ $iconColor }}">
                {{ $icon }}
            </div>
        @endisset

        @if ($change && $changeDirection)
            <span class="stat-change {{ $changeDirection }}">
                {{ $change }}
            </span>
        @endif
    </div>

    <div>
        <div class="stat-value">{{ $value }}</div>
        <div class="stat-label">{{ $label }}</div>
    </div>

</div>
