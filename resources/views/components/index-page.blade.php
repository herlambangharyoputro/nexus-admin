{{--
  Index Page — pola halaman daftar (server-side): header + aksi, toolbar cari/filter/reset,
  kolom sortir (?sort=&dir=), empty state (kosong vs tidak cocok), flash, pagination.
  Di bawah 600px setiap baris tabel menjadi KARTU (lihat GUIDELINES §10).

  Usage:
    x-nexus::index-page
        title="Daftar Aset" :count="true" :paginator="$asets"
        :columns="[
            ['label' => 'Aset', 'sort' => 'nama'],
            'Lokasi',
            ['label' => 'Harga', 'sort' => 'harga', 'align' => 'end'],
            ['label' => 'Aksi', 'actions' => true],
        ]"
        search-placeholder="Nama / kode aset…"
        :create-url="route('aset.create')" create-label="Tambah Aset"
        empty="Belum ada data aset.">

        x-slot:filters>
            x-nexus::form.select name="kategori_id" :options="$kategoriOptions"
                placeholder="Semua Kategori" :selected="request('kategori_id')" />
        /x-slot:filters>

        @foreach($asets as $a)
            <tr>
                <td>{{ $a->nama }}</td> ...
                <td>x-nexus::row-actions :view="route('aset.show',$a)" :edit="route('aset.edit',$a)" :delete="route('aset.destroy',$a)" /></td>
            </tr>
        @endforeach
    /x-nexus::index-page>

  Props:
    title, description : judul (h1) & deskripsi kartu
    count             : angka di samping judul; true → $paginator->total()
    paginator         : LengthAwarePaginator / Paginator → pagination (withQueryString otomatis)
                        + deteksi kosong
    rows              : alternatif paginator untuk data tanpa paginasi (deteksi kosong saja)
    columns           : 'Label' | ['label','sort','align'(start|center|end),'class','width','actions'(bool)]
                        Format lama ['Label', 'css-class', 'sort_key'] tetap diterima.
                        Kolom berlabel "Aksi" otomatis dianggap kolom aksi.
    search            : true (default) → kotak cari dalam <form method="GET">
    searchName        : nama parameter (default "search")
    searchPlaceholder : default "Cari…"
    createUrl         : URL tombol primer "Tambah" di header (ikon plus)
    createLabel       : default "Tambah" — konvensi: "Tambah {Resource}"
    empty             : pesan bila memang belum ada data
    emptyFiltered     : pesan bila kosong karena pencarian/filter (+ tautan Reset)
    filtered          : null (auto: ada query selain page/sort/dir/per_page) | true | false
    flash             : true (default) → x-nexus::flash di atas toolbar
    mobile            : cards (default) | scroll — scroll hanya untuk tabel angka/matriks
  Slots:
    default      : baris <tr> tbody
    filters      : kontrol filter tambahan di toolbar (name = parameter query)
    actions      : aksi sekunder di header (Export, Impor, dropdown)
    emptyActions : tombol di empty state (default: tombol Tambah bila createUrl)
    toolbar      : ganti seluruh toolbar
    footer       : di bawah pagination
  Sorting: paket hanya membuat tautan ?sort=key&dir=asc|desc (page di-reset);
  controller WAJIB whitelist kolom (resep di PATTERNS_Nexus.md §1).
--}}
@props([
    'title'             => null,
    'description'       => null,
    'count'             => null,
    'paginator'         => null,
    'rows'              => null,
    'columns'           => [],
    'search'            => true,
    'searchName'        => 'search',
    'searchPlaceholder' => 'Cari…',
    'createUrl'         => null,
    'createLabel'       => 'Tambah',
    'empty'             => 'Belum ada data.',
    'emptyFiltered'     => 'Tidak ada data yang cocok dengan pencarian/filter.',
    'filtered'          => null,
    'flash'             => true,
    'mobile'            => 'cards',
])

@php
    $request = request();

    $cols = collect($columns)->map(function ($c) {
        $base = ['label' => '', 'sort' => null, 'align' => null, 'class' => '', 'width' => null, 'actions' => false];
        if (! is_array($c)) {
            $col = array_merge($base, ['label' => (string) $c]);
        } elseif (array_is_list($c)) {
            // Format lama (override simantara): ['Label', 'css-class', 'sort_key']
            $col = array_merge($base, ['label' => $c[0] ?? '', 'class' => $c[1] ?? '', 'sort' => $c[2] ?? null]);
        } else {
            $col = array_merge($base, $c);
        }
        if (! $col['actions'] && in_array(mb_strtolower(trim($col['label'])), ['aksi', 'actions'], true)) {
            $col['actions'] = true;
        }
        if ($col['actions'] && ! $col['align']) $col['align'] = 'end';
        return $col;
    });

    $sort = $request->query('sort');
    $dir  = $request->query('dir') === 'desc' ? 'desc' : 'asc';

    $ignored  = ['page', 'sort', 'dir', 'per_page'];
    $filtered = $filtered ?? collect($request->query())
        ->except($ignored)
        ->filter(fn ($v) => ! ($v === null || $v === '' || $v === []))
        ->isNotEmpty();

    if ($paginator) {
        $isEmpty = $paginator->isEmpty();
        if (method_exists($paginator, 'withQueryString')) $paginator->withQueryString();
    } elseif (! is_null($rows)) {
        $isEmpty = count($rows) === 0;
    } else {
        $isEmpty = trim((string) $slot) === '';
    }

    if ($count === true) $count = $paginator && method_exists($paginator, 'total') ? $paginator->total() : null;

    $resetUrl  = $request->url();
    $hasHeader = $title || $description || isset($actions) || $createUrl;
    $mobile    = $mobile === 'scroll' ? 'scroll' : 'cards';
@endphp

<div {{ $attributes->merge(['class' => 'page-pattern index-page']) }}>
    <div class="card page-card">
        @if($hasHeader)
            <div class="card-header page-card-header">
                <div class="page-card-heading">
                    @if($title)
                        <h1 class="card-title">{{ $title }}@if(!is_null($count)) <span class="page-card-count">{{ is_numeric($count) ? number_format($count, 0, ',', '.') : $count }}</span>@endif</h1>
                    @endif
                    @if($description)<p class="page-card-desc">{{ $description }}</p>@endif
                </div>
                @if(isset($actions) || $createUrl)
                    <div class="page-card-actions">
                        {{ $actions ?? '' }}
                        @if($createUrl)
                            <a href="{{ $createUrl }}" class="btn btn-primary">
                                <x-nexus::icons.plus :size="14" /> <span>{{ $createLabel }}</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        @if($flash)<x-nexus::flash class="index-flash" />@endif

        @isset($toolbar)
            <div class="index-toolbar">{{ $toolbar }}</div>
        @elseif($search || isset($filters))
            <div class="index-toolbar">
                <form method="GET" action="{{ $resetUrl }}" class="index-filter" role="search">
                    @if($sort)
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="dir" value="{{ $dir }}">
                    @endif
                    @if($search)
                        <x-nexus::search-box class="index-search" :name="$searchName"
                            :placeholder="$searchPlaceholder" :value="$request->query($searchName)" />
                    @endif
                    {{ $filters ?? '' }}
                    <div class="index-filter-buttons">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        @if($filtered)
                            <a href="{{ $resetUrl }}" class="btn btn-outline">Reset</a>
                        @endif
                    </div>
                </form>
            </div>
        @endisset

        @if($isEmpty)
            @if($filtered)
                <x-nexus::empty-state class="index-empty" icon="🔍" :title="$emptyFiltered"
                    desc="Ubah kata kunci atau filter, lalu cari lagi.">
                    <a href="{{ $resetUrl }}" class="btn btn-outline">Reset filter</a>
                </x-nexus::empty-state>
            @else
                <x-nexus::empty-state class="index-empty" icon="📭" :title="$empty">
                    @isset($emptyActions)
                        {{ $emptyActions }}
                    @elseif($createUrl)
                        <a href="{{ $createUrl }}" class="btn btn-primary"><x-nexus::icons.plus :size="14" /> {{ $createLabel }}</a>
                    @endisset
                </x-nexus::empty-state>
            @endif
        @else
            <div class="index-table index-table-{{ $mobile }}">
                <table class="data-table" @if($mobile === 'cards') data-stack-table @endif>
                    @if($cols->isNotEmpty())
                        <thead>
                            <tr>
                                @foreach($cols as $i => $c)
                                    @php
                                        $active = $c['sort'] && $sort === $c['sort'];
                                        $thClass = trim(implode(' ', array_filter([
                                            $c['class'],
                                            $c['align'] ? 'cell-' . $c['align'] : null,
                                            $c['sort'] ? 'th-sortable' : null,
                                            $active ? 'is-sorted' : null,
                                        ])));
                                        $role = $c['actions'] ? 'actions' : ($i === 0 ? 'primary' : null);
                                    @endphp
                                    <th scope="col"
                                        @if($thClass) class="{{ $thClass }}" @endif
                                        @if($c['width']) style="width: {{ $c['width'] }}" @endif
                                        @if($role) data-col-role="{{ $role }}" @endif
                                        @if($c['sort']) aria-sort="{{ $active ? ($dir === 'asc' ? 'ascending' : 'descending') : 'none' }}" @endif>
                                        @if($c['sort'])
                                            <a class="th-sort" href="{{ $request->fullUrlWithQuery(['sort' => $c['sort'], 'dir' => $active && $dir === 'asc' ? 'desc' : 'asc', 'page' => null]) }}">
                                                {{ $c['label'] }}
                                                <span class="sort-ico" aria-hidden="true">{{ $active ? ($dir === 'asc' ? '▲' : '▼') : '⇅' }}</span>
                                            </a>
                                        @else
                                            {{ $c['label'] }}
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                    @endif
                    <tbody>{{ $slot }}</tbody>
                </table>
            </div>
        @endif

        @if($paginator && (! $isEmpty || $paginator->currentPage() > 1))
            <x-nexus::pagination :paginator="$paginator" />
        @endif

        {{ $footer ?? '' }}
    </div>
</div>
