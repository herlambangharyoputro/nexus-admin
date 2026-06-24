{{--
  Datatable — komposit: card + kontrol (search + actions) + table + pagination.
  Pola "list/index page" standar Nexus Admin.

  Usage:
    x-nexus::datatable title="Daftar Pengguna" :paginator="$users" sortable>
        x-slot:actions>
            x-nexus::button size="sm">x-nexus::icons.plus :size="14" /> Tambah/x-nexus::button>
        /x-slot:actions>

        x-slot:head>
            <th data-sort="name">Nama</th>
            <th data-sort="email">Email</th>
            <th>Aksi</th>
        /x-slot:head>

        @foreach($users as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>x-nexus::table-actions :edit="route('users.edit',$u)" :delete="route('users.destroy',$u)" /></td>
            </tr>
        @endforeach
    /x-nexus::datatable>

  Props:
    title            : judul di bar kontrol
    search           : true (default) → tampilkan search box (hook data-table-search)
    searchPlaceholder: teks placeholder search
    sortable         : true (default) → aktifkan data-sort-table
    paginator        : LengthAwarePaginator → render pagination otomatis
  Slots:
    head       : isi <tr> untuk <thead>
    default    : baris <tr> tbody
    actions    : tombol di kanan bar kontrol (mis. "Tambah")
    pagination : override pagination manual (kalau tidak pakai :paginator)
--}}
@props([
    'title'             => null,
    'search'            => true,
    'searchPlaceholder' => 'Cari...',
    'sortable'          => true,
    'paginator'         => null,
])

<x-nexus::card noBody {{ $attributes }}>
    @if($title || $search || isset($actions))
        <div class="table-controls px-5 pt-4">
            @if($title)
                <div class="card-title">{{ $title }}</div>
            @endif

            @if($search)
                <div class="search-box">
                    <x-nexus::icons.search :size="15" />
                    <input type="text" placeholder="{{ $searchPlaceholder }}" data-table-search>
                </div>
            @endif

            @isset($actions)
                <div class="table-actions">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="overflow-x-auto">
        <x-nexus::table :sortable="$sortable">
            @isset($head)<x-slot:head>{{ $head }}</x-slot:head>@endisset
            {{ $slot }}
        </x-nexus::table>
    </div>

    @if($paginator)
        <x-nexus::pagination :paginator="$paginator" />
    @elseif(isset($pagination))
        {{ $pagination }}
    @endif
</x-nexus::card>
