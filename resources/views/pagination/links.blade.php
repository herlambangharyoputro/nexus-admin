{{--
  View paginator Laravel bergaya Nexus — untuk $paginator->links().

  Pasang sekali di AppServiceProvider::boot():
    \Illuminate\Pagination\Paginator::defaultView('nexus::pagination.links');
    \Illuminate\Pagination\Paginator::defaultSimpleView('nexus::pagination.links');

  Atau per pemanggilan: {{ $items->links('nexus::pagination.links') }}
  Isinya hanya membungkus x-nexus::pagination agar markup tetap satu sumber.
--}}
<x-nexus::pagination :paginator="$paginator" />
