{{--
  Carousel Item — satu slide di dalam x-nexus::carousel>.
  Beri lebar via style/class (mis. style="width:280px" atau class="w-full").
--}}
<div {{ $attributes->merge(['class' => 'carousel-slide']) }}>
    {{ $slot }}
</div>
