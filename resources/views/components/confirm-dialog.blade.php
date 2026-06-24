{{--
  Confirm Dialog — modal konfirmasi global. PASANG SEKALI (mis. di layout).
  Modul Confirm di app.js mengintersep elemen ber-atribut data-confirm:
    - <form data-confirm="pesan"> ...   → submit ditahan sampai dikonfirmasi
    - <a href data-confirm="pesan">      → navigasi ditahan
    - <button data-confirm-action="url" data-confirm="pesan">

  Usage (sekali di layout, sebelum </body>):
    <x-nexus::confirm-dialog />

  Catatan: id default "nexusConfirm" — jangan diubah (dipakai modul JS).
--}}
<div id="nexusConfirm" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="nexusConfirm-title">
    <div class="modal" style="max-width: 420px">
        <div class="modal-header">
            <div class="modal-title" id="nexusConfirm-title" data-confirm-title>Konfirmasi</div>
            <button type="button" class="modal-close" data-modal-close aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
            <p class="text-muted text-sm" data-confirm-message>Anda yakin?</p>
            <div class="d-flex justify-end gap-2 mt-6">
                <button type="button" class="btn btn-outline" data-modal-close>Batal</button>
                <button type="button" class="btn btn-danger" data-confirm-ok>Ya, lanjutkan</button>
            </div>
        </div>
    </div>
</div>
