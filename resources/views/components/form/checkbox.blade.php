{{--
  Form Checkbox — satu checkbox berlabel (boolean), sadar $errors & old().

  Usage:
    x-nexus::form.checkbox name="bisa_dipinjam" label="Ya, boleh diajukan peminjaman" />
    x-nexus::form.checkbox name="aktif" label="Aktif" :checked="true" unchecked="0" />

    // di dalam form.group (label grup + error):
    x-nexus::form.group label="Peminjaman" name="bisa_dipinjam">
        x-nexus::form.checkbox name="bisa_dipinjam" label="Ya, boleh diajukan peminjaman" />
    /x-nexus::form.group>

  Props:
    name      : nama input
    id        : default diturunkan dari name
    label     : teks di samping checkbox (area klik ≥40px)
    value     : nilai saat tercentang (default 1)
    checked   : status eksplisit; null (default) → old(name) → model form-page
    unchecked : bila diisi (mis. "0") → hidden input agar nilai ini terkirim saat tidak tercentang
    old       : true (default) → baca old input
--}}
@props([
    'name'      => null,
    'id'        => null,
    'label'     => null,
    'value'     => 1,
    'checked'   => null,
    'unchecked' => null,
    'old'       => true,
])
@aware([
    'model'    => null,
    'hint'     => null,
    'error'    => null,
    'required' => false,
])

@php
    $field   = \Nexus\Admin\Support\Field::class;
    $id      = $id ?: $field::id($name);
    $message = $error === false ? null : ($error ?: $field::error($errors ?? null, $name));

    if ($old && $field::hasOldInput() && $name) {
        // Setelah submit: checkbox yang tidak tercentang tidak ada di old input.
        $oldValue  = $field::hasOld($name) ? request()->old($field::key($name)) : null;
        $isChecked = $oldValue !== null && $oldValue !== (string) $unchecked && $field::selected($value, $oldValue);
    } elseif ($checked !== null) {
        $isChecked = (bool) $checked;
    } else {
        $modelValue = $model !== null && $name ? data_get($model, $field::key($name)) : null;
        $isChecked  = is_bool($modelValue) ? $modelValue : $field::selected($value, $modelValue);
    }

    $describedBy = $message ? $id . '-error' : ($hint ? $id . '-hint' : null);
@endphp

<label {{ $attributes->only('class')->merge(['class' => 'form-check' . ($message ? ' is-invalid' : '')]) }}>
    @if(!is_null($unchecked) && $name)
        <input type="hidden" name="{{ $name }}" value="{{ $unchecked }}">
    @endif
    <input type="checkbox"
        class="form-check-input"
        @if($name) name="{{ $name }}" @endif
        @if($id) id="{{ $id }}" @endif
        value="{{ $value }}"
        @checked($isChecked)
        @if($message) aria-invalid="true" @endif
        @if($describedBy && $id) aria-describedby="{{ $describedBy }}" @endif
        {{ $attributes->except('class') }}>
    @if($label)<span class="form-check-label">{{ $label }}</span>@else{{ $slot }}@endif
</label>
