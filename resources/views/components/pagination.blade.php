{{--
  Pagination — info + tombol halaman (.pagination). Dipakai otomatis oleh index-page.

  Usage 1 — dengan Laravel paginator (otomatis):
    x-nexus::pagination :paginator="$users" />

  Usage 2 — manual:
    x-nexus::pagination :current="3" :last="10" :from="21" :to="30" :total="98" base-url="/users" />

  Usage 3 — sebagai view default $paginator->links() (AppServiceProvider::boot):
    Paginator::defaultView('nexus::pagination.links');

  Props:
    paginator : LengthAwarePaginator → semua nilai diturunkan otomatis.
                Paginator sederhana (simplePaginate) → hanya tombol sebelumnya/berikutnya.
    current, last       : halaman aktif & terakhir (mode manual)
    from, to, total     : info "menampilkan X–Y dari Z" (opsional)
    baseUrl             : prefix URL untuk ?page=N (mode manual)
    window              : jumlah nomor di kiri/kanan halaman aktif (default 2; alias onEachSide)
  Bila hanya 1 halaman: info tetap tampil, tombol disembunyikan.
  Di bawah 600px: info di atas, tombol 40px dan boleh turun baris.
--}}
@props([
    'paginator'  => null,
    'current'    => 1,
    'last'       => 1,
    'from'       => null,
    'to'         => null,
    'total'      => null,
    'baseUrl'    => null,
    'window'     => 2,
    'onEachSide' => null,
])

@php
    $simple = false;
    if ($paginator) {
        $current = $paginator->currentPage();
        $from    = $paginator->firstItem();
        $to      = $paginator->lastItem();
        if (method_exists($paginator, 'lastPage')) {
            $last  = $paginator->lastPage();
            $total = $paginator->total();
        } else {
            $simple = true;
            $last   = $paginator->hasMorePages() ? $current + 1 : $current;
        }
    }

    $window = $onEachSide ?? $window;

    $url = function ($p) use ($paginator, $baseUrl) {
        if ($paginator) return $paginator->url($p);
        if ($baseUrl)   return $baseUrl . (str_contains($baseUrl, '?') ? '&' : '?') . 'page=' . $p;
        return '#';
    };

    $start = max(1, $current - $window);
    $end   = min($last, $current + $window);
@endphp

<nav {{ $attributes->merge(['class' => 'pagination']) }} aria-label="Navigasi halaman">
    <div class="pagination-info">
        @if(!is_null($total))
            @if($total > 0)
                Menampilkan {{ $from }}–{{ $to }} dari {{ $total }}
            @else
                Tidak ada data
            @endif
        @else
            Halaman {{ $current }}@unless($simple) dari {{ $last }}@endunless
        @endif
    </div>

    @if($last > 1 || $current > 1)
        <div class="pagination-btns">
            @if($current > 1)
                <a class="pg-btn" href="{{ $url($current - 1) }}" rel="prev" aria-label="Sebelumnya"><x-nexus::icons.chevron-left :size="14" /></a>
            @else
                <button type="button" class="pg-btn" disabled aria-label="Sebelumnya"><x-nexus::icons.chevron-left :size="14" /></button>
            @endif

            @unless($simple)
                @if($start > 1)
                    <a class="pg-btn" href="{{ $url(1) }}">1</a>
                    @if($start > 2)<span class="pg-btn pg-gap" aria-hidden="true">…</span>@endif
                @endif

                @for($p = $start; $p <= $end; $p++)
                    @if($p === $current)
                        <span class="pg-btn active" aria-current="page">{{ $p }}</span>
                    @else
                        <a class="pg-btn" href="{{ $url($p) }}">{{ $p }}</a>
                    @endif
                @endfor

                @if($end < $last)
                    @if($end < $last - 1)<span class="pg-btn pg-gap" aria-hidden="true">…</span>@endif
                    <a class="pg-btn" href="{{ $url($last) }}">{{ $last }}</a>
                @endif
            @endunless

            @if($current < $last)
                <a class="pg-btn" href="{{ $url($current + 1) }}" rel="next" aria-label="Berikutnya"><x-nexus::icons.chevron-right :size="14" /></a>
            @else
                <button type="button" class="pg-btn" disabled aria-label="Berikutnya"><x-nexus::icons.chevron-right :size="14" /></button>
            @endif
        </div>
    @endif
</nav>
