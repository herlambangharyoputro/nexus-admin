/* ============================================================
   NEXUS ADMIN — APP JS
   Dependency: jQuery 3.x

   Modules:
     1. Sidebar (collapse + mobile toggle)
     2. Toast system    → window.Toast.show(msg, type)
     3. Modal system    → window.Modal.open(id), window.Modal.close()
     4. Notification    → window.Notif.markRead(id), markAllRead()
     5. Table sort      → auto-init via [data-sort-table]
     6. Dropdown        → auto-init via [data-dropdown]
     7. Tabs            → auto-init via [data-tabs]
     8. Accordion       → auto-init via [data-accordion]
     9. Confirm         → window.Confirm.show({message, onConfirm})
    10. Wizard          → auto-init via [data-wizard]
    11. Lightbox        → auto-init via [data-lightbox] + #nexusLightbox
    12. Carousel        → auto-init via [data-carousel]
    v0.5:
     - Breakpoint      → window.NexusBreakpoint.{sm,md,lg}() (token --bp-*)
     - Dropdown float  → [data-dropdown="float"] (row-actions)
     - Table search    → [data-table-search] (datatable, deprecated)
     - Stack table     → [data-stack-table] (index-page, kartu di HP); StackTable.init(root)
     - Error summary   → [data-error-summary] (form.errors)
     - File preview    → [data-file-preview] (form.file preview)
     ============================================================ */

(function ($) {
  'use strict';

  /* ──────────────────────────────────────────────────────────
     0. CSRF setup untuk Laravel (auto-inject ke semua AJAX)
     ────────────────────────────────────────────────────────── */
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  /* ──────────────────────────────────────────────────────────
     Breakpoint — baca token --bp-sm/--bp-md/--bp-lg dari tokens.css
     (satu sumber kebenaran; fallback 600/900/1200).
     ────────────────────────────────────────────────────────── */
  const Breakpoint = (function () {
    const fallback = { sm: 600, md: 900, lg: 1200 };
    function read(name) {
      const v = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--bp-' + name), 10);
      return isNaN(v) ? fallback[name] : v;
    }
    return {
      sm: function () { return read('sm'); },
      md: function () { return read('md'); },
      lg: function () { return read('lg'); }
    };
  })();

  /* ──────────────────────────────────────────────────────────
     1. Sidebar (collapse desktop + slide mobile)
     ────────────────────────────────────────────────────────── */
  const Sidebar = (function () {
    const STORAGE_KEY = 'nexus.sidebar.collapsed';
    let bound = false;

    // Re-query setiap akses → tahan terhadap urutan load script & DOM yang
    // di-render ulang. (Bug umum: cache $('.sidebar') saat <head> = kosong.)
    function $sidebar() { return $('.sidebar'); }
    function $main()    { return $('.main'); }
    function $overlay() { return $('.sidebar-overlay'); }

    // Breakpoint off-canvas = token --bp-md (tokens.css, default 900px).
    function isMobile() {
      return window.innerWidth < Breakpoint.md();
    }

    function persist(collapsed) {
      try { localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0'); } catch (e) {}
    }
    function wasCollapsed() {
      try { return localStorage.getItem(STORAGE_KEY) === '1'; } catch (e) { return false; }
    }

    function openMobile() {
      $sidebar().removeClass('mobile-hidden');
      $overlay().addClass('show');
      $('body').addClass('overflow-hidden');
    }
    function closeMobile() {
      $sidebar().addClass('mobile-hidden');
      $overlay().removeClass('show');
      $('body').removeClass('overflow-hidden');
    }
    function collapse() {
      $sidebar().addClass('collapsed');
      $main().addClass('expanded');
      persist(true);
    }
    function expand() {
      $sidebar().removeClass('collapsed');
      $main().removeClass('expanded');
      persist(false);
    }

    function toggle() {
      if (isMobile()) {
        if ($sidebar().hasClass('mobile-hidden')) openMobile();
        else closeMobile();
      } else {
        if ($sidebar().hasClass('collapsed')) expand();
        else collapse();
      }
    }

    // Satu sumber kebenaran: sinkronkan kelas dengan ukuran layar saat ini.
    function applyState() {
      if (isMobile()) {
        $sidebar().removeClass('collapsed').addClass('mobile-hidden');
        $main().removeClass('expanded').addClass('no-margin');
        $overlay().removeClass('show');
        $('body').removeClass('overflow-hidden');
      } else {
        $sidebar().removeClass('mobile-hidden');
        $overlay().removeClass('show');
        $('body').removeClass('overflow-hidden');
        $main().removeClass('no-margin');
        if (wasCollapsed()) { $sidebar().addClass('collapsed'); $main().addClass('expanded'); }
        else                { $sidebar().removeClass('collapsed'); $main().removeClass('expanded'); }
      }
    }

    function debounce(fn, wait) {
      let t;
      return function () { clearTimeout(t); t = setTimeout(fn, wait); };
    }

    function init() {
      // Idempotent: cegah double-bind kalau app.js ter-load lebih dari sekali.
      if (bound) { applyState(); return; }
      bound = true;

      applyState();

      // Namespaced + .off() dulu → aman dari binding ganda.
      $(document)
        .off('click.nexusSidebar')
        .on('click.nexusSidebar', '.topbar-toggle, [data-sidebar-toggle]', function (e) {
          e.preventDefault();
          toggle();
        })
        .on('click.nexusSidebar', '.sidebar-overlay', closeMobile)
        .on('click.nexusSidebar', '.sidebar .nav-item', function () {
          if (isMobile()) closeMobile();
        });

      $(window).off('resize.nexusSidebar').on('resize.nexusSidebar', debounce(applyState, 150));
    }

    return { init: init, toggle: toggle, collapse: collapse, expand: expand, closeMobile: closeMobile };
  })();

  /* ──────────────────────────────────────────────────────────
     2. Toast System
     Usage: Toast.show('Saved!', 'success' | 'error' | 'info')
     ────────────────────────────────────────────────────────── */
  const Toast = (function () {
    const CONTAINER_ID = 'toast-container';
    const DURATION = 3200;

    function ensureContainer() {
      let $container = $('#' + CONTAINER_ID);
      if ($container.length === 0) {
        $container = $('<div id="' + CONTAINER_ID + '" class="toast-container"></div>');
        $('body').append($container);
      }
      return $container;
    }

    function show(msg, type) {
      type = type || 'info';
      const $container = ensureContainer();
      const id = 'toast-' + Date.now();

      const $toast = $(
        '<div id="' + id + '" class="toast ' + type + '">' +
          '<span class="toast-msg">' + $('<div>').text(msg).html() + '</span>' +
          '<button class="toast-close" aria-label="Close">&times;</button>' +
        '</div>'
      );

      $container.append($toast);

      // Manual close
      $toast.find('.toast-close').on('click', function () { dismiss($toast); });

      // Auto dismiss
      setTimeout(function () { dismiss($toast); }, DURATION);
    }

    function dismiss($toast) {
      $toast.css({ opacity: 0, transform: 'translateY(10px)' });
      setTimeout(function () { $toast.remove(); }, 200);
    }

    return { show: show };
  })();

  /* ──────────────────────────────────────────────────────────
     3. Modal System
     Usage:
       Modal.open('modalId')
       Modal.close()
       <button data-modal-open="myModal">Open</button>
       <button data-modal-close>Close</button>
     ────────────────────────────────────────────────────────── */
  const Modal = (function () {
    function open(id) {
      const $modal = $('#' + id);
      if ($modal.length === 0) return;
      $modal.addClass('show');
      $('body').addClass('overflow-hidden');
    }

    function close() {
      $('.modal-overlay.show').removeClass('show');
      $('body').removeClass('overflow-hidden');
    }

    function init() {
      // Trigger via data attribute
      $(document).on('click', '[data-modal-open]', function (e) {
        e.preventDefault();
        open($(this).data('modal-open'));
      });

      // Close button
      $(document).on('click', '[data-modal-close]', function (e) {
        e.preventDefault();
        close();
      });

      // Backdrop click (klik area gelap di luar .modal menutup; klik dalam modal tidak)
      $(document).on('click', '.modal-overlay', function (e) {
        if (e.target === e.currentTarget) close();
      });

      // ESC to close
      $(document).on('keydown', function (e) {
        if (e.key === 'Escape') close();
      });
    }

    return { open: open, close: close, init: init };
  })();

  /* ──────────────────────────────────────────────────────────
     4. Notification (mark read)
     Usage:
       Notif.markRead(id) – tandai satu notif
       Notif.markAllRead() – tandai semua
     ────────────────────────────────────────────────────────── */
  const Notif = (function () {
    function markRead(id) {
      $('[data-notif-id="' + id + '"]').removeClass('unread');
      updateBadge();
    }

    function markAllRead() {
      $('[data-notif-id]').removeClass('unread');
      updateBadge();
    }

    function updateBadge() {
      const count = $('[data-notif-id].unread').length;
      const $badge = $('[data-notif-badge]');
      if (count > 0) {
        $badge.text(count).show();
      } else {
        $badge.hide();
      }
    }

    function init() {
      $(document).on('click', '[data-notif-id]', function () {
        markRead($(this).data('notif-id'));
      });
      $(document).on('click', '[data-notif-mark-all]', function (e) {
        e.preventDefault();
        markAllRead();
      });
    }

    return { markRead: markRead, markAllRead: markAllRead, init: init };
  })();

  /* ──────────────────────────────────────────────────────────
     5. Table Sort (client-side, simple)
     Usage:
       <table data-sort-table>
         <thead>
           <tr><th data-sort="id">ID</th> ... </tr>
     ────────────────────────────────────────────────────────── */
  const TableSort = (function () {
    function init() {
      $(document).on('click', '[data-sort-table] th[data-sort]', function () {
        const $th = $(this);
        const $table = $th.closest('table');
        const $tbody = $table.find('tbody');
        const index = $th.index();
        const key = $th.data('sort');
        const currentDir = $th.data('dir') || 'asc';
        const newDir = currentDir === 'asc' ? 'desc' : 'asc';

        // Reset all th dir
        $table.find('th[data-sort]').removeData('dir').removeClass('sort-asc sort-desc');
        $th.data('dir', newDir).addClass('sort-' + newDir);

        const rows = $tbody.find('tr').get();
        rows.sort(function (a, b) {
          const va = $(a).find('td').eq(index).text().trim();
          const vb = $(b).find('td').eq(index).text().trim();
          const numA = parseFloat(va);
          const numB = parseFloat(vb);
          const aIsNum = !isNaN(numA);
          const bIsNum = !isNaN(numB);
          let cmp;
          if (aIsNum && bIsNum) cmp = numA - numB;
          else cmp = va.localeCompare(vb);
          return newDir === 'asc' ? cmp : -cmp;
        });

        $tbody.empty().append(rows);
      });
    }
    return { init: init };
  })();

  /* ──────────────────────────────────────────────────────────
     5b. Table search (client-side) — hook [data-table-search] milik
     datatable (deprecated). Menyaring baris tbody pada kartu yang sama.
     ────────────────────────────────────────────────────────── */
  const TableSearch = (function () {
    function init() {
      $(document).off('input.nexusTs').on('input.nexusTs', '[data-table-search]', function () {
        const q = $(this).val().toLowerCase().trim();
        const $scope = $(this).closest('.card');
        $scope.find('table tbody tr').each(function () {
          $(this).toggle(!q || $(this).text().toLowerCase().indexOf(q) !== -1);
        });
      });
    }
    return { init: init };
  })();

  /* ──────────────────────────────────────────────────────────
     5c. Stack table — tabel index-page di bawah --bp-sm tampil sebagai kartu.
     Salin teks <th> ke td[data-label] (+ data-role primary/actions) supaya
     consumer tidak perlu menulis data-label. data-label manual tidak ditimpa.
     Markup: <table data-stack-table> <th data-col-role="primary|actions">
     ────────────────────────────────────────────────────────── */
  const StackTable = (function () {
    function label(table) {
      const heads = Array.prototype.map.call(table.querySelectorAll('thead th'), function (th) {
        return { text: th.textContent.replace(/[▲▼⇅]/g, '').trim(), role: th.getAttribute('data-col-role') };
      });
      if (!heads.length) return;
      table.querySelectorAll('tbody tr').forEach(function (tr) {
        Array.prototype.forEach.call(tr.children, function (td, i) {
          const h = heads[i];
          if (!h || td.hasAttribute('colspan')) return;
          if (!td.hasAttribute('data-label')) td.setAttribute('data-label', h.text);
          if (h.role && !td.hasAttribute('data-role')) td.setAttribute('data-role', h.role);
        });
      });
    }
    function init(root) {
      (root || document).querySelectorAll('table[data-stack-table]').forEach(label);
    }
    return { init: init, label: label };
  })();

  /* ──────────────────────────────────────────────────────────
     5d. Error summary — fokuskan ringkasan error form saat halaman dimuat
     (x-nexus::form.errors) agar pembaca layar langsung membacanya.
     ────────────────────────────────────────────────────────── */
  const ErrorSummary = (function () {
    function init() {
      const box = document.querySelector('[data-error-summary]');
      if (box) box.focus({ preventScroll: false });
      $(document).off('click.nexusErr').on('click.nexusErr', '[data-error-summary] a[href^="#"]', function (e) {
        const el = document.getElementById(this.getAttribute('href').slice(1));
        if (!el) return;
        e.preventDefault();
        el.scrollIntoView({ block: 'center' });
        el.focus({ preventScroll: true });
      });
    }
    return { init: init };
  })();

  /* ──────────────────────────────────────────────────────────
     5e. File preview — pratinjau gambar untuk x-nexus::form.file preview.
     Markup: [data-file-preview] > [data-file-preview-box] > img[data-file-preview-img] + input[type=file]
     ────────────────────────────────────────────────────────── */
  const FilePreview = (function () {
    function init() {
      $(document).off('change.nexusFp').on('change.nexusFp', '[data-file-preview] input[type="file"]', function () {
        const wrap = this.closest('[data-file-preview]');
        const box = wrap.querySelector('[data-file-preview-box]');
        const img = wrap.querySelector('[data-file-preview-img]');
        const file = this.files && this.files[0];
        if (!box || !img) return;
        if (!file || file.type.indexOf('image/') !== 0) {
          if (img.hasAttribute('data-current')) img.src = img.getAttribute('data-current');
          box.hidden = !img.getAttribute('src');
          return;
        }
        if (!img.hasAttribute('data-current')) img.setAttribute('data-current', img.getAttribute('src') || '');
        img.src = URL.createObjectURL(file);
        box.hidden = false;
      });
    }
    return { init: init };
  })();

  /* ──────────────────────────────────────────────────────────
     6. Topbar action handlers
     ────────────────────────────────────────────────────────── */
  const Topbar = (function () {
    function init() {
      // Notification button — toggle panel kalau <x-nexus::notif-panel/> dipasang,
      // jika tidak, fallback ke toast.
      $(document).on('click', '[data-action="notifications"]', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const $panel = $('[data-notif-panel]');
        if ($panel.length) { $panel.toggle(); }
        else { Toast.show('Notification panel belum diimplementasi', 'info'); }
      });
      $(document).on('click', '[data-notif-panel]', function (e) { e.stopPropagation(); });
      $(document).on('click', function () { $('[data-notif-panel]:visible').hide(); });

      // Logout — confirm sebelum redirect
      $(document).on('click', '[data-action="logout"]', function (e) {
        e.preventDefault();
        const href = $(this).attr('href');
        if (confirm('Yakin ingin keluar?')) {
          // TODO: kalau auth pakai POST logout (Laravel default), 
          // ganti ini dengan form submit + CSRF token
          window.location.href = href;
        }
      });
    }
    return { init: init };
  })();

  /* ──────────────────────────────────────────────────────────
     7. User Menu Dropdown (avatar+name+role di topbar)
     ────────────────────────────────────────────────────────── */
  const UserMenu = (function () {
    function close() {
      $('[data-user-menu]').removeClass('open').attr('aria-expanded', 'false');
      $('[data-user-dropdown]').removeClass('show');
    }

    function toggle($menu) {
      const isOpen = $menu.hasClass('open');
      // Close all first (in case multiple menus exist)
      close();
      if (!isOpen) {
        $menu.addClass('open').attr('aria-expanded', 'true');
        $menu.find('[data-user-dropdown]').addClass('show');
      }
    }

    function init() {
      // Toggle on click
      $(document).on('click', '[data-user-menu]', function (e) {
        // Don't toggle when clicking a dropdown item
        if ($(e.target).closest('[data-user-dropdown]').length) return;
        e.stopPropagation();
        toggle($(this));
      });

      // Close on outside click
      $(document).on('click', function (e) {
        if (!$(e.target).closest('[data-user-menu]').length) {
          close();
        }
      });

      // Close on ESC
      $(document).on('keydown', function (e) {
        if (e.key === 'Escape') close();
      });
    }

    return { init: init, close: close };
  })();

  /* ──────────────────────────────────────────────────────────
     6. Dropdown — toggle .dropdown-menu.show
     Markup: <div class="dropdown" data-dropdown>
               <button data-dropdown-trigger>…</button>
               <div class="dropdown-menu">…</div>
             </div>
     ────────────────────────────────────────────────────────── */
  const Dropdown = (function () {
    function triggerOf(menu) {
      return $(menu).closest('.dropdown').find('[data-dropdown-trigger]').first();
    }
    function close(menu) {
      $(menu).removeClass('show is-floating').css({ left: '', top: '', maxHeight: '' });
      triggerOf(menu).attr('aria-expanded', 'false');
    }
    function closeAll(except) {
      $('.dropdown-menu.show').each(function () {
        if (this !== except) close(this);
      });
    }

    // Mode mengambang (data-dropdown="float", dipakai row-actions): position:fixed
    // agar tidak terpotong kontainer overflow (tabel), dijepit ke viewport,
    // dan membuka ke atas bila ruang di bawah tidak cukup.
    function place(trigger, menu, alignLeft) {
      const pad = 8, gap = 6;
      menu.classList.add('is-floating');
      const r = trigger.getBoundingClientRect();
      const w = menu.offsetWidth, h = menu.offsetHeight;
      const vw = window.innerWidth, vh = window.innerHeight;

      let left = alignLeft ? r.left : r.right - w;
      left = Math.max(pad, Math.min(left, vw - w - pad));

      let top = r.bottom + gap;
      if (top + h > vh - pad) {
        if (r.top - gap - h >= pad) top = r.top - gap - h;      // balik ke atas
        else menu.style.maxHeight = Math.max(120, vh - top - pad) + 'px';
      }
      menu.style.left = left + 'px';
      menu.style.top = top + 'px';
    }

    function closeFloating(e) {
      if (e && e.target && $(e.target).closest('.dropdown-menu.is-floating').length) return;
      $('.dropdown-menu.is-floating.show').each(function () { close(this); });
    }

    function init() {
      $(document)
        .off('.nexusDd')
        .on('click.nexusDd', '[data-dropdown-trigger]', function (e) {
          e.preventDefault();
          e.stopPropagation();
          const $dd = $(this).closest('.dropdown');
          const $menu = $dd.find('.dropdown-menu').first();
          const isOpen = $menu.hasClass('show');
          closeAll($menu[0]);
          if (isOpen) { close($menu[0]); return; }
          $menu.addClass('show');
          $(this).attr('aria-expanded', 'true');
          if ($dd.attr('data-dropdown') === 'float') {
            place(this, $menu[0], $dd.attr('data-align') === 'left');
          }
        })
        .on('click.nexusDd', '.dropdown-menu', function (e) { e.stopPropagation(); })
        .on('click.nexusDd', function () { closeAll(); })
        .on('keydown.nexusDd', function (e) {
          if (e.key !== 'Escape') return;
          const $open = $('.dropdown-menu.show');
          if ($open.length) { triggerOf($open[0]).trigger('focus'); }
          closeAll();
        });

      // Menu mengambang tidak ikut bergeser saat scroll/resize → tutup.
      if (!window.__nexusDdFloatBound) {
        window.__nexusDdFloatBound = true;
        window.addEventListener('scroll', closeFloating, true);
        window.addEventListener('resize', function () { closeFloating(); });
      }
    }
    return { init: init, closeAll: closeAll };
  })();

  /* ──────────────────────────────────────────────────────────
     7. Tabs — switch .settings-tab.active + .tab-pane.active
     Markup: <div data-tabs> … [data-tab="k"] … [data-pane="k"] … </div>
     ────────────────────────────────────────────────────────── */
  const Tabs = (function () {
    function init() {
      $(document).off('click.nexusTab').on('click.nexusTab', '[data-tab]', function () {
        const key = $(this).attr('data-tab');
        const $root = $(this).closest('[data-tabs]');
        $root.find('[data-tab]').removeClass('active');
        $(this).addClass('active');
        $root.find('[data-pane]').removeClass('active');
        $root.find('[data-pane="' + key + '"]').addClass('active');
      });
    }
    return { init: init };
  })();

  /* ──────────────────────────────────────────────────────────
     8. Accordion — toggle .accordion-item.open + max-height
     ────────────────────────────────────────────────────────── */
  const Accordion = (function () {
    function init() {
      $(document).off('click.nexusAcc').on('click.nexusAcc', '[data-accordion-toggle]', function () {
        const $item = $(this).closest('.accordion-item');
        const $acc  = $item.closest('[data-accordion]');
        const multi = $acc.attr('data-accordion-multi') !== undefined;
        const $body = $item.find('.accordion-body').first();
        const isOpen = $item.hasClass('open');

        if (!multi) {
          $acc.find('.accordion-item.open').not($item)
            .removeClass('open')
            .find('.accordion-body').css('max-height', '');
        }
        if (isOpen) {
          $item.removeClass('open');
          $body.css('max-height', '');
        } else {
          $item.addClass('open');
          $body.css('max-height', $body[0].scrollHeight + 'px');
        }
      });
    }
    return { init: init };
  })();

  /* ──────────────────────────────────────────────────────────
     9. Confirm — dialog konfirmasi global (butuh <x-nexus::confirm-dialog/>)
     Intersep: form[data-confirm], a[data-confirm], [data-confirm-action]
     ────────────────────────────────────────────────────────── */
  const Confirm = (function () {
    let pending = null;

    function show(opts) {
      pending = opts || {};
      $('[data-confirm-message]').text(pending.message || 'Anda yakin?');
      $('[data-confirm-title]').text(pending.title || 'Konfirmasi');
      const $ok = $('[data-confirm-ok]');
      $ok.text(pending.okLabel || 'Ya, lanjutkan');
      if (window.Modal) window.Modal.open('nexusConfirm');
    }

    function init() {
      if (!$('#nexusConfirm').length) return; // perlu komponen confirm-dialog

      $(document)
        .off('.nexusConfirm')
        .on('submit.nexusConfirm', 'form[data-confirm]', function (e) {
          if ($(this).data('confirmed')) return;
          e.preventDefault();
          const form = this;
          show({
            message: $(this).attr('data-confirm'),
            onConfirm: function () { $(form).data('confirmed', true); form.submit(); }
          });
        })
        .on('click.nexusConfirm', 'a[data-confirm], [data-confirm-action]', function (e) {
          e.preventDefault();
          const href = $(this).attr('href') || $(this).attr('data-confirm-action');
          show({
            message: $(this).attr('data-confirm'),
            onConfirm: function () { if (href) window.location.href = href; }
          });
        })
        .on('click.nexusConfirm', '[data-confirm-ok]', function () {
          const p = pending;
          if (window.Modal) window.Modal.close();
          pending = null;
          if (p && p.onConfirm) p.onConfirm();
        });
    }
    return { init: init, show: show };
  })();

  /* ──────────────────────────────────────────────────────────
     10. Wizard — navigasi form multi-langkah
     Markup: <form data-wizard> .wizard-step* .wizard-pane* [data-wizard-prev/next/submit] </form>
     ────────────────────────────────────────────────────────── */
  const Wizard = (function () {
    function go($w, idx) {
      const $panes = $w.find('.wizard-pane');
      const $steps = $w.find('.wizard-step');
      idx = Math.max(0, Math.min(idx, $panes.length - 1));

      $panes.removeClass('active').eq(idx).addClass('active');
      $steps.removeClass('active done').each(function (i) {
        if (i < idx) $(this).addClass('done');
        else if (i === idx) $(this).addClass('active');
      });

      $w.data('step', idx);
      const last = idx === $panes.length - 1;
      $w.find('[data-wizard-prev]').prop('disabled', idx === 0);
      $w.find('[data-wizard-next]').toggle(!last);
      $w.find('[data-wizard-submit]').toggle(last);
    }
    function init() {
      $('[data-wizard]').each(function () { go($(this), 0); });
      $(document).off('.nexusWiz')
        .on('click.nexusWiz', '[data-wizard-next]', function () {
          const $w = $(this).closest('[data-wizard]'); go($w, ($w.data('step') || 0) + 1);
        })
        .on('click.nexusWiz', '[data-wizard-prev]', function () {
          const $w = $(this).closest('[data-wizard]'); go($w, ($w.data('step') || 0) - 1);
        });
    }
    return { init: init, go: go };
  })();

  /* ──────────────────────────────────────────────────────────
     11. Lightbox — penampil gambar galeri (butuh <x-nexus::lightbox/>)
     ────────────────────────────────────────────────────────── */
  const Lightbox = (function () {
    let items = [], idx = 0;

    function render() {
      const it = items[idx];
      if (!it) return;
      $('[data-lb-img]').attr('src', it.src || '').attr('alt', it.title || '');
      $('[data-lb-info-title]').text(it.title || '');
      $('[data-lb-info-sub]').text(it.sub || '');
    }
    function open(list, start) {
      items = list || []; idx = start || 0;
      $('#nexusLightbox').css('display', 'flex');
      $('body').addClass('overflow-hidden');
      render();
    }
    function close() {
      $('#nexusLightbox').hide();
      $('body').removeClass('overflow-hidden');
    }
    function go(d) {
      if (!items.length) return;
      idx = (idx + d + items.length) % items.length;
      render();
    }
    function init() {
      if (!$('#nexusLightbox').length) return;
      $(document).off('.nexusLb')
        .on('click.nexusLb', '[data-lightbox]', function () {
          const $g = $(this).closest('[data-gallery]');
          const $all = $g.length ? $g.find('[data-lightbox]') : $('[data-lightbox]');
          const list = $all.map(function () {
            return {
              src:   $(this).attr('data-lb-src'),
              title: $(this).attr('data-lb-title'),
              sub:   $(this).attr('data-lb-sub')
            };
          }).get();
          open(list, $all.index(this));
        })
        .on('click.nexusLb', '[data-lb-close]', close)
        .on('click.nexusLb', '[data-lb-prev]', function () { go(-1); })
        .on('click.nexusLb', '[data-lb-next]', function () { go(1); })
        .on('click.nexusLb', '#nexusLightbox', function (e) { if (e.target.id === 'nexusLightbox') close(); })
        .on('keydown.nexusLb', function (e) {
          if (!$('#nexusLightbox').is(':visible')) return;
          if (e.key === 'Escape') close();
          else if (e.key === 'ArrowLeft') go(-1);
          else if (e.key === 'ArrowRight') go(1);
        });
    }
    return { init: init, open: open, close: close };
  })();

  /* ──────────────────────────────────────────────────────────
     12. Carousel — geser .carousel-track via tombol prev/next
     ────────────────────────────────────────────────────────── */
  const Carousel = (function () {
    function init() {
      $(document).off('.nexusCar')
        .on('click.nexusCar', '[data-carousel-prev]', function () {
          const t = $(this).closest('[data-carousel]').find('.carousel-track')[0];
          if (t) t.scrollBy({ left: -t.clientWidth * 0.8, behavior: 'smooth' });
        })
        .on('click.nexusCar', '[data-carousel-next]', function () {
          const t = $(this).closest('[data-carousel]').find('.carousel-track')[0];
          if (t) t.scrollBy({ left: t.clientWidth * 0.8, behavior: 'smooth' });
        });
    }
    return { init: init };
  })();

  /* ──────────────────────────────────────────────────────────
     13. Integrations — auto-init lib opsional bila global-nya ada.
     Lib TIDAK dibundel. Muat lib-nya SEBELUM app.js, atau panggil
     window.NexusIntegrations.init() setelah lib dimuat.
       • ApexCharts → [data-apexchart]
       • TomSelect  → [data-tomselect]
       • Quill      → [data-quill]   (+ sync ke [data-quill-input])
       • Coloris    → [data-coloris]
       • Leaflet    → [data-map] (auto bila ada data-map-lat/lng)
     ────────────────────────────────────────────────────────── */
  const Integrations = (function () {
    function apex() {
      if (!window.ApexCharts) return;
      $('[data-apexchart]').each(function () {
        if (this.__apex) return;
        try {
          const cfg = JSON.parse(this.getAttribute('data-apexchart'));
          const c = new ApexCharts(this, cfg);
          c.render();
          this.__apex = c;
        } catch (e) { console.error('Nexus chart:', e); }
      });
    }
    function tomselect() {
      if (!window.TomSelect) return;
      $('[data-tomselect]').each(function () {
        if (this.tomselect) return;
        new TomSelect(this, { placeholder: this.getAttribute('data-placeholder') || '' });
      });
    }
    function quill() {
      if (!window.Quill) return;
      $('[data-quill]').each(function () {
        if (this.__quill) return;
        const q = new Quill(this, { theme: 'snow', placeholder: this.getAttribute('data-quill-placeholder') || '' });
        this.__quill = q;
        const $input = $(this).closest('[data-wysiwyg]').find('[data-quill-input]');
        $input.val(q.root.innerHTML);
        q.on('text-change', function () { $input.val(q.root.innerHTML); });
      });
    }
    function coloris() {
      if (!window.Coloris) return;
      try { Coloris({ el: '[data-coloris]' }); } catch (e) {}
    }
    function leaflet() {
      if (!window.L) return;
      $('[data-map]').each(function () {
        if (this.__leaflet) return;
        const lat = parseFloat(this.getAttribute('data-map-lat'));
        const lng = parseFloat(this.getAttribute('data-map-lng'));
        if (isNaN(lat) || isNaN(lng)) return; // perlu koordinat untuk auto-init
        const zoom = parseInt(this.getAttribute('data-map-zoom') || '13', 10);
        const map = L.map(this.id).setView([lat, lng], zoom);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; OpenStreetMap'
        }).addTo(map);
        L.marker([lat, lng]).addTo(map);
        this.__leaflet = map;
      });
    }
    function init() { apex(); tomselect(); quill(); coloris(); leaflet(); }
    return { init: init, apex: apex, tomselect: tomselect, quill: quill, coloris: coloris, leaflet: leaflet };
  })();

  /* ──────────────────────────────────────────────────────────
     Bootstrap on DOM ready
     ────────────────────────────────────────────────────────── */
  $(function () {
    Sidebar.init();
    Modal.init();
    Notif.init();
    TableSort.init();
    TableSearch.init();
    StackTable.init();
    ErrorSummary.init();
    FilePreview.init();
    Topbar.init();
    UserMenu.init();
    Dropdown.init();
    Tabs.init();
    Accordion.init();
    Confirm.init();
    Wizard.init();
    Lightbox.init();
    Carousel.init();
    Integrations.init();
  });

  /* Expose to window for inline calls */
  window.Toast     = Toast;
  window.Modal     = Modal;
  window.Notif     = Notif;
  window.Sidebar   = Sidebar;
  window.UserMenu  = UserMenu;
  window.Dropdown  = Dropdown;
  window.Tabs      = Tabs;
  window.Accordion = Accordion;
  window.Confirm   = Confirm;
  window.Wizard    = Wizard;
  window.Lightbox  = Lightbox;
  window.Carousel  = Carousel;
  window.NexusIntegrations = Integrations;
  window.NexusBreakpoint   = Breakpoint;
  window.StackTable        = StackTable;

})(jQuery);