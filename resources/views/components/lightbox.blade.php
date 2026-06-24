{{--
  Lightbox — overlay penampil gambar dengan prev/next (modul JS Lightbox).
  PASANG SEKALI (mis. di layout). Otomatis terhubung ke semua [data-lightbox]
  (mis. dari x-nexus::gallery-item>). Navigasi keyboard: ←/→/Esc.

  Usage:
    x-nexus::lightbox />
--}}
<div id="nexusLightbox" class="lightbox" style="display:none">
    <button type="button" class="lightbox-close" data-lb-close aria-label="Tutup">&times;</button>
    <button type="button" class="lightbox-nav prev" data-lb-prev aria-label="Sebelumnya"><x-nexus::icons.chevron-left :size="22" /></button>
    <button type="button" class="lightbox-nav next" data-lb-next aria-label="Berikutnya"><x-nexus::icons.chevron-right :size="22" /></button>
    <div class="lightbox-content">
        <img class="lightbox-img" data-lb-img src="" alt="">
        <div class="lightbox-info">
            <div class="lightbox-title" data-lb-info-title></div>
            <div class="lightbox-sub" data-lb-info-sub></div>
        </div>
    </div>
</div>
