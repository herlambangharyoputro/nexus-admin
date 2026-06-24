{{--
  Card Table — kartu berjudul berisi tabel polos (tanpa search/pagination).
  Pola "card table" untuk ringkasan/daftar pendek di dashboard.

  Usage:
    <x-nexus::card-table title="Transaksi Terbaru">
        <x-slot:action><x-nexus::button variant="ghost" size="sm">Lihat semua</x-nexus::button></x-slot:action>
        <x-slot:head>
            <th>Invoice</th><th>Pelanggan</th><th>Total</th>
        </x-slot:head>
        @foreach($rows as $r)
            <tr><td>{{ $r->no }}</td><td>{{ $r->cust }}</td><td>{{ $r->total }}</td></tr>
        @endforeach
    </x-nexus::card-table>

  Props:
    title    : judul header
    sortable : true → aktifkan data-sort-table
  Slots:
    head     : isi <tr> thead
    action   : aksi di kanan header (opsional)
    default  : baris <tr> tbody
--}}
@props([
    'title'    => null,
    'sortable' => false,
])

<x-nexus::card noBody {{ $attributes }}>
    @if($title || isset($action))
        <div class="card-header">
            @if($title)<div class="card-title">{{ $title }}</div>@endif
            @isset($action)<div>{{ $action }}</div>@endisset
        </div>
    @endif

    <div class="overflow-x-auto">
        <x-nexus::table :sortable="$sortable">
            @isset($head)<x-slot:head>{{ $head }}</x-slot:head>@endisset
            {{ $slot }}
        </x-nexus::table>
    </div>
</x-nexus::card>
