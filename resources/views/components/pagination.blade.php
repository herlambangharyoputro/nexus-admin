{{--
  Pagination — info + tombol halaman (.pagination).

  Usage 1 — dengan Laravel paginator (otomatis):
    x-nexus::pagination :paginator="$users" />

  Usage 2 — manual:
    x-nexus::pagination :current="3" :last="10" :from="21" :to="30" :total="98" base-url="/users" />

  Props:
    paginator : LengthAwarePaginator → semua nilai diturunkan otomatis
    current, last       : halaman aktif & terakhir (mode manual)
    from, to, total     : info "menampilkan X–Y dari Z" (opsional)
    baseUrl             : prefix URL untuk ?page=N (mode manual)
    window              : jumlah nomor di kiri/kanan halaman aktif (default 2)
--}}
@props([
    'paginator' => null,
    'current'   => 1,
    'last'      => 1,
    'from'      => null,
    'to'        => null,
    'total'     => null,
    'baseUrl'   => null,
    'window'    => 2,
])

@php
    if ($paginator) {
        $current = $paginator->currentPage();
        $last    = $paginator->lastPage();
        $from    = $paginator->firstItem();
        $to      = $paginator->lastItem();
        $total   = $paginator->total();
    }

    $url = function ($p) use ($paginator, $baseUrl) {
        if ($paginator) return $paginator->url($p);
        if ($baseUrl)   return $baseUrl . (str_contains($baseUrl, '?') ? '&' : '?') . 'page=' . $p;
        return '#';
    };

    $start = max(1, $current - $window);
    $end   = min($last, $current + $window);
@endphp

<div {{ $attributes->merge(['class' => 'pagination']) }}>
    <div class="pagination-info">
        @if(!is_null($total))
            Menampilkan {{ $from }}–{{ $to }} dari {{ $total }}
        @else
            Halaman {{ $current }} dari {{ $last }}
        @endif
    </div>

    <div class="pagination-btns">
        @if($current > 1)
            <a class="pg-btn" href="{{ $url($current - 1) }}" aria-label="Sebelumnya"><x-nexus::icons.chevron-left :size="14" /></a>
        @else
            <button class="pg-btn" disabled aria-label="Sebelumnya"><x-nexus::icons.chevron-left :size="14" /></button>
        @endif

        @if($start > 1)
            <a class="pg-btn" href="{{ $url(1) }}">1</a>
            @if($start > 2)<button class="pg-btn" disabled>…</button>@endif
        @endif

        @for($p = $start; $p <= $end; $p++)
            <a class="pg-btn {{ $p === $current ? 'active' : '' }}" href="{{ $url($p) }}">{{ $p }}</a>
        @endfor

        @if($end < $last)
            @if($end < $last - 1)<button class="pg-btn" disabled>…</button>@endif
            <a class="pg-btn" href="{{ $url($last) }}">{{ $last }}</a>
        @endif

        @if($current < $last)
            <a class="pg-btn" href="{{ $url($current + 1) }}" aria-label="Berikutnya"><x-nexus::icons.chevron-right :size="14" /></a>
        @else
            <button class="pg-btn" disabled aria-label="Berikutnya"><x-nexus::icons.chevron-right :size="14" /></button>
        @endif
    </div>
</div>
