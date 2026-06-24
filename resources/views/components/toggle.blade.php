{{--
  Toggle — switch on/off (pure CSS, membungkus checkbox).

  Usage:
    x-nexus::toggle name="notif" :checked="true" label="Aktifkan notifikasi" />
    x-nexus::toggle name="dark" value="1" />

  Props:
    name, id : atribut checkbox
    checked  : true → tercentang
    value    : nilai saat tercentang (default 1)
    label    : teks di samping switch (opsional)
--}}
@props([
    'name'    => null,
    'id'      => null,
    'checked' => false,
    'value'   => 1,
    'label'   => null,
])

<label class="d-inline-flex items-center gap-2">
    <span class="toggle-switch">
        <input type="checkbox"
            @if($name) name="{{ $name }}" @endif
            @if($id) id="{{ $id }}" @endif
            value="{{ $value }}"
            @checked($checked)
            {{ $attributes }}>
        <span class="toggle-slider"></span>
    </span>
    @if($label)<span class="text-sm">{{ $label }}</span>@endif
</label>
