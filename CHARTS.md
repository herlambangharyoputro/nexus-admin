# Nexus Admin — Charts & Library Integrations

Komponen chart & widget tertentu adalah **shell**: markup + hook `data-*`.
Lib pihak ketiga **tidak dibundel** (jaga package tetap lean). Consumer memuat
lib lewat CDN/npm, lalu modul `Integrations` di `app.js` meng-init otomatis.

> **Aturan muat:** taruh `<script>` lib **sebelum** `app.js`, **atau** panggil
> `window.NexusIntegrations.init()` setelah lib & konten dimuat (mis. setelah
> AJAX). Init bersifat idempotent (elemen yang sudah ter-init dilewati).

---

## Charts — ApexCharts

### Setup (sekali)

```blade
{{-- sebelum app.js --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
```

Lalu pakai `<x-nexus::chart>` atau `<x-nexus::card-chart>`. Modul `Integrations`
membaca `[data-apexchart]` dan me-render-nya.

### 14 resep tipe chart

Setiap contoh memakai `<x-nexus::card-chart>` (kartu + chart). Untuk chart polos
tanpa kartu, ganti dengan `<x-nexus::chart>` (props sama).

**1. Line**
```blade
<x-nexus::card-chart title="Line" type="line"
    :series="[['name'=>'Penjualan','data'=>[30,40,35,50,49,60,70]]]"
    :options="['xaxis'=>['categories'=>['Sen','Sel','Rab','Kam','Jum','Sab','Min']]]" />
```

**2. Area**
```blade
<x-nexus::card-chart title="Area" type="area"
    :series="[['name'=>'2025','data'=>[31,40,28,51,42,82]]]"
    :options="['xaxis'=>['categories'=>['Jan','Feb','Mar','Apr','Mei','Jun']],'fill'=>['type'=>'gradient']]" />
```

**3. Column (bar vertikal)**
```blade
<x-nexus::card-chart title="Column" type="bar"
    :series="[['name'=>'Omzet','data'=>[44,55,57,56,61,58]]]"
    :options="['xaxis'=>['categories'=>['Jan','Feb','Mar','Apr','Mei','Jun']]]" />
```

**4. Bar (horizontal)**
```blade
<x-nexus::card-chart title="Bar" type="bar"
    :series="[['name'=>'Skor','data'=>[400,430,448,470,540]]]"
    :options="['plotOptions'=>['bar'=>['horizontal'=>true]],'xaxis'=>['categories'=>['A','B','C','D','E']]]" />
```

**5. Pie** — series berupa array angka datar; label di `labels`
```blade
<x-nexus::card-chart title="Pie" type="pie"
    :series="[44,55,13,33]"
    :options="['labels'=>['Tim A','Tim B','Tim C','Tim D']]" />
```

**6. Doughnut**
```blade
<x-nexus::card-chart title="Doughnut" type="donut"
    :series="[44,55,41,17]"
    :options="['labels'=>['Selesai','Proses','Pending','Batal']]" />
```

**7. Radar**
```blade
<x-nexus::card-chart title="Radar" type="radar"
    :series="[['name'=>'Skill','data'=>[80,90,70,85,60,75]]]"
    :options="['xaxis'=>['categories'=>['PHP','JS','SQL','GIS','ML','DevOps']]]" />
```

**8. Polar Area**
```blade
<x-nexus::card-chart title="Polar Area" type="polarArea"
    :series="[14,23,21,17,15]"
    :options="['labels'=>['A','B','C','D','E']]" />
```

**9. Radial (radialBar)**
```blade
<x-nexus::card-chart title="Radial" type="radialBar"
    :series="[70]"
    :options="['labels'=>['Progres']]" />
```

**10. Bubble**
```blade
<x-nexus::card-chart title="Bubble" type="bubble"
    :series="[['name'=>'Wilayah','data'=>[[10,20,30],[20,40,50],[30,25,40]]]]" />
```

**11. Scatter**
```blade
<x-nexus::card-chart title="Scatter" type="scatter"
    :series="[['name'=>'Sampel','data'=>[[1,2],[2,3],[3,5],[4,4],[5,6]]]]" />
```

**12. Heatmap**
```blade
<x-nexus::card-chart title="Heatmap" type="heatmap"
    :series="[
        ['name'=>'Sen','data'=>[['x'=>'08','y'=>10],['x'=>'09','y'=>20],['x'=>'10','y'=>30]]],
        ['name'=>'Sel','data'=>[['x'=>'08','y'=>15],['x'=>'09','y'=>25],['x'=>'10','y'=>12]]],
    ]" />
```

**13. Candlestick** — OHLC
```blade
<x-nexus::card-chart title="Candlestick" type="candlestick"
    :series="[['data'=>[
        ['x'=>'2025-01','y'=>[51,62,49,55]],
        ['x'=>'2025-02','y'=>[55,70,53,68]],
        ['x'=>'2025-03','y'=>[68,72,60,63]],
    ]]]" />
```

**14. Mixed** — tipe per-seri (kosongkan `type` di chart, set di tiap seri)
```blade
<x-nexus::card-chart title="Mixed"
    :series="[
        ['name'=>'Target','type'=>'column','data'=>[44,55,57,56,61]],
        ['name'=>'Realisasi','type'=>'line','data'=>[30,40,45,50,49]],
    ]"
    :options="['xaxis'=>['categories'=>['Jan','Feb','Mar','Apr','Mei']],'stroke'=>['width'=>[0,3]]]" />
```

> Tip warna: ApexCharts memakai paletnya sendiri. Untuk selaras brand, set
> `'colors' => ['#0093b4', ...]` di `:options` (atau warna `[data-theme]` aktif).

---

## Select Picker — Tom Select

```blade
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
```
```blade
<x-nexus::select-picker name="tags" multiple
    :options="['laravel'=>'Laravel','php'=>'PHP','js'=>'JavaScript']"
    :selected="['laravel']" placeholder="Pilih tag" />
```
Tanpa lib → fallback `<select>` biasa.

---

## WYSIWYG — Quill

```blade
<script src="https://cdn.jsdelivr.net/npm/quill/dist/quill.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill/dist/quill.snow.css" rel="stylesheet">
```
```blade
<x-nexus::wysiwyg name="body" :value="old('body', $post->body ?? '')" height="240px" />
```
HTML hasil edit otomatis tersinkron ke input tersembunyi `name="body"`.

---

## Color Picker — Coloris

```blade
<script src="https://cdn.jsdelivr.net/gh/mdbassit/Coloris/dist/coloris.min.js"></script>
<link href="https://cdn.jsdelivr.net/gh/mdbassit/Coloris/dist/coloris.min.css" rel="stylesheet">
```
```blade
<x-nexus::colorpicker name="brand_color" value="#0093b4" />
```

---

## Map — Leaflet

```blade
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link href="https://unpkg.com/leaflet/dist/leaflet.css" rel="stylesheet">
```

**Auto** (peta OSM + marker dari koordinat):
```blade
<x-nexus::map :lat="-7.2575" :lng="112.7521" :zoom="13" height="400px" />
```

**Manual** (kontrol penuh: layer GIS, GeoJSON, dsb):
```blade
<x-nexus::map id="petaWalida" height="480px" />
@push('scripts')
<script>
  const map = L.map('petaWalida').setView([-8.05, 113.95], 12);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
  // L.geoJSON(@json($plots)).addTo(map);
</script>
@endpush
```

---

## Calendar — FullCalendar

```blade
<script src="https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js"></script>
```
```blade
<x-nexus::calendar id="kalender" />
@push('scripts')
<script>
  new FullCalendar.Calendar(document.getElementById('kalender'), {
    initialView: 'dayGridMonth',
    events: @json($events ?? []),
  }).render();
</script>
@endpush
```
