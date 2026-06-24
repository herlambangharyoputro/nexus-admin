{{--
  Form Wizard — form multi-langkah dengan stepper (modul JS Wizard).

  Usage:
    x-nexus::form-wizard :steps="['Akun','Profil','Selesai']" :action="route('register')" submit-label="Daftar">
        x-nexus::wizard-pane>
            x-nexus::form.group label="Email">x-nexus::form.input name="email" type="email" />/x-nexus::form.group>
        /x-nexus::wizard-pane>
        x-nexus::wizard-pane>
            x-nexus::form.group label="Nama">x-nexus::form.input name="name" />/x-nexus::form.group>
        /x-nexus::wizard-pane>
        x-nexus::wizard-pane>Ringkasan & konfirmasi.../x-nexus::wizard-pane>
    /x-nexus::form-wizard>

  Props:
    steps       : array label langkah (jumlah harus = jumlah wizard-pane)
    action      : URL submit
    method      : POST (default) | PUT | PATCH | ...
    submitLabel : teks tombol akhir (default "Kirim")
--}}
@props([
    'steps'       => [],
    'action'      => '#',
    'method'      => 'POST',
    'submitLabel' => 'Kirim',
])

<form {{ $attributes->merge(['class' => 'wizard']) }} data-wizard method="{{ $method === 'GET' ? 'GET' : 'POST' }}" action="{{ $action }}">
    @if($method !== 'GET') @csrf @endif
    @if(!in_array($method, ['GET','POST'])) @method($method) @endif

    <div class="wizard-steps">
        @foreach($steps as $i => $label)
            <div class="wizard-step">
                <div class="wizard-step-num">{{ $i + 1 }}</div>
                <span>{{ $label }}</span>
            </div>
        @endforeach
    </div>

    {{ $slot }}

    <div class="wizard-footer">
        <button type="button" class="btn btn-outline" data-wizard-prev>Sebelumnya</button>
        <button type="button" class="btn btn-primary" data-wizard-next>Berikutnya</button>
        <button type="submit" class="btn btn-primary" data-wizard-submit>{{ $submitLabel }}</button>
    </div>
</form>
