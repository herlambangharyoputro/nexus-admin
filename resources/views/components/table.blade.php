{{--
  Table — wrapper <table class="data-table"> dengan slot head + body.

  Usage:
    x-nexus::table sortable>
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
    /x-nexus::table>

  Props:
    sortable : true → tambahkan data-sort-table (client-side sort via app.js).
               Pasangkan dengan data-sort="key" pada <th>.
--}}
@props([
    'sortable' => false,
])

<table {{ $attributes->merge(['class' => 'data-table']) }} @if($sortable) data-sort-table @endif>
    @isset($head)
        <thead><tr>{{ $head }}</tr></thead>
    @endisset
    <tbody>{{ $slot }}</tbody>
</table>
